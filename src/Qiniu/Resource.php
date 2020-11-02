<?php


namespace CoverCMS\CloudStorage\Qiniu;


use CoverCMS\CloudStorage\ResourceInterface;
use Qiniu\Http\Error;
use function Qiniu\base64_urlSafeEncode;
use function Qiniu\crc32_data;

class Resource extends Base implements ResourceInterface
{
    protected $bucket;

    protected $upToken;

    protected $upHost;

    private $currentUrl;

    public function setBucket(string $bucket)
    {
        $this->bucket = $bucket;
        $this->upToken = $this->auth->uploadToken($this->bucket);
        $this->upHost = $this->config->getUpHost($this->auth->getAccessKey(), $bucket);
        return $this;
    }

    public function upload(string $key, string $content, array $options = [])
    {
        if (is_file($content)) {
            return $this->uploadManager->putFile($this->upToken, $key, $content, $options);
        }

        return $this->uploadManager->put($this->upToken, $key, $content, $options);
    }

    public function initMultipartUpload(string $key, array $options = []): string
    {
        return '';
    }

    public function uploadPart(string $key, string $content, int $partNumber, string $uploadId)
    {
        $blockSize = strlen($content);
        $crc = crc32_data($content);
        $response = $this->makeBlock($content, $blockSize);
        $ret = null;
        if ($response->ok() && $response->json() != null) {
            $ret = $response->json();
        }

        if ($response->needRetry() || !isset($ret['crc32']) || $crc != $ret['crc32']) {
            $response = $this->makeBlock($content, $blockSize);
            $ret = $response->json();
        }

        if (!$response->ok() || !isset($ret['crc32']) || $crc != $ret['crc32']) {
            return array(null, new Error($this->currentUrl, $response));
        }

        return $ret;
    }

    /**
     * 组合分片文件
     *
     * @param $path
     * @param array $uploadParts
     * @param null $uploadId
     * @param null $bucket
     * @return array
     */
    public function completeMultipartUpload(string $key, string $uploadId, array $parts)
    {
        $ctxs = array_column($parts, 'ctx');
        $size = array_sum(array_column($parts, 'offset'));
        $body = implode(',', $ctxs);
        $url = $this->fileUrl($key, $size);
        $response = $this->post($url, $body);
        if ($response->needRetry()) {
            $response = $this->post($url, $body);
        }
        if (!$response->ok()) {
            return array(null, new Error($this->currentUrl, $response));
        }
        return array($response->json(), null);
    }

    public function download(string $key, string $localPath, array $options = [])
    {
    }

    /**
     * 判断文件是否存在
     *
     * @param string $key
     * @param array $options
     * @return bool
     */
    public function isExist(string $key, array $options = []): bool
    {
        list($fileInfo, $err) = $this->bucketManager->stat($this->bucket, $key);

        return $err === null;
    }

    /**
     * 获取文件元信息
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function meta(string $key, array $options = [])
    {
        list($fileInfo, $err) = $this->bucketManager->stat($this->bucket, $key);

        return $fileInfo;
    }

    public function fetch(string $url, string $key = null)
    {
        $this->bucketManager->fetch($url, $this->bucket, $key);
    }

    public function remove($key, array $options = [])
    {
        $ops = $this->bucketManager->buildBatchDelete($this->bucket, (array)$key);
        $this->bucketManager->batch($ops);
    }

    /**
     * 创建块
     *
     * @param $block
     * @param $blockSize
     * @return \Qiniu\Http\Response
     */
    private function makeBlock($block, $blockSize)
    {
        $upHost = $this->config->getUpHost($this->auth->getAccessKey(), $this->bucket);

        $url = $upHost . '/mkblk/' . $blockSize;
        return $this->post($url, $block);
    }

    private function fileUrl(string $key, int $size)
    {
        $url = $this->upHost . '/mkfile/' . $size;
        $url .= '/mimeType/' . base64_urlSafeEncode('application/octet-stream');
        $url .= '/key/' . base64_urlSafeEncode($key);
        $url .= '/fname/' . base64_urlSafeEncode($key);
        if (!empty($this->params)) {
            foreach ($this->params as $k => $v) {
                $v = base64_urlSafeEncode($v);
                $url .= "/$k/$v";
            }
        }

        return $url;
    }

    private function post($url, $data)
    {
        $this->currentUrl = $url;
        $headers = array('Authorization' => 'UpToken ' . $this->upToken);
        return \Qiniu\Http\Client::post($url, $data, $headers);
    }
}