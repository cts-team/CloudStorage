<?php


namespace CoverCMS\CloudStorage\Baidu;


use CoverCMS\CloudStorage\ResourceInterface;

class Resource extends Base implements ResourceInterface
{
    protected $bucket;

    public function setBucket(string $bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function upload(string $key, string $content, array $options = [])
    {
        if (is_file($content)) {
            $result = $this->sdk->putObjectFromFile($this->bucket, $key, $content, $options);
        } else {
            $result = $this->sdk->putObjectFromString($this->bucket, $key, $content, $options);
        }
        return json_decode(json_encode($result), true);
    }

    public function initMultipartUpload(string $key, array $options = []): string
    {
        $response = $this->sdk->initiateMultipartUpload($this->bucket, $key);
        return $response->uploadId;
    }

    public function uploadPart(string $key, string $content, int $partNumber, string $uploadId)
    {
        $md5 = base64_encode(md5($content, true));
        $response = $this->sdk->uploadPart($this->bucket, $key, $uploadId, $partNumber, strlen($content), $md5, $content);
        return $response->metadata['etag'];
    }

    public function completeMultipartUpload(string $key, string $uploadId, array $parts)
    {
        $result = $this->sdk->completeMultipartUpload($this->bucket, $key, $uploadId, $parts);

        return json_decode(json_encode($result), true);
    }

    public function download(string $key, string $localPath)
    {
        $this->sdk->getObjectToFile($this->bucket, $key, $localPath);
    }

    public function remove($key, array $options = [])
    {
        $key = (array)$key;
        foreach ($key as $item) {
            $this->sdk->deleteObject($this->bucket, $item, $options);
        }
    }

    public function isExist(string $key, array $options = []): bool
    {
        try {
            $this->sdk->getObjectMetadata($this->bucket, $key);
        } catch (\BaiduBce\Exception\BceBaseException $e) {
            if ($e->getStatusCode() == 404) {
                return false;
            }
        }
        return true;
    }

    public function meta(string $key, array $options = [])
    {
        return $this->sdk->getObjectMetadata($this->bucket, $key);
    }
}