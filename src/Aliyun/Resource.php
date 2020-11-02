<?php


namespace CoverCMS\CloudStorage\Aliyun;


use CoverCMS\CloudStorage\ResourceInterface;
use OSS\OssClient;

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
            return $this->sdk->uploadFile($this->bucket, $key, $content, $options);
        } else {
            return $this->sdk->putObject($this->bucket, $key, $content, $options);
        }
    }

    /**
     * 返回uploadId字符串
     *
     * @param string $key
     * @param array $options
     * @return string 例如：B069EEAC745A4784BC47D52189335E65
     * @throws \OSS\Core\OssException
     */
    public function initMultipartUpload(string $key, array $options = []): string
    {
        return $this->sdk->initiateMultipartUpload($this->bucket, $key, $options);
    }

    /**
     * 返回ETag字符串
     *
     * @param string $key
     * @param string $content
     * @param int $partNumber
     * @param string $uploadId
     * @return string 例如： 467C21E2C846ECBD0880A320DCDE450F
     * @throws \OSS\Core\OssException
     */
    public function uploadPart(string $key, string $content, int $partNumber, string $uploadId)
    {
        $handle = fopen('php://temp', 'w');

        fwrite($handle, $content);
        rewind($handle);

        return $this->sdk->uploadPart($this->bucket, $key, $uploadId, [
            OssClient::OSS_FILE_UPLOAD => $handle,
            OssClient::OSS_PART_NUM => $partNumber
        ]);
    }

    /**
     * @param string $key
     * @param string $uploadId
     * @param array $parts
     * @return array
     * @throws \OSS\Core\OssException
     */
    public function completeMultipartUpload(string $key, string $uploadId, array $parts)
    {
        return $this->sdk->completeMultipartUpload($this->bucket, $key, $uploadId, $parts);
    }

    /**
     * @param string $key
     * @param string $localPath
     */
    public function download(string $key, string $localPath)
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localPath
        );

        $this->sdk->getObject($this->bucket, $key, $options);
    }

    public function remove($key, array $options = [])
    {
        $this->sdk->deleteObjects($this->bucket, (array)$key, $options);
    }

    /**
     * @param string $key
     * @param array $options
     * @return bool
     */
    public function isExist(string $key, array $options = []): bool
    {
        return $this->sdk->doesObjectExist($this->bucket, $key);
    }

    public function meta(string $key, array $options = [])
    {
        return $this->sdk->getObjectMeta($this->bucket, $key);
    }
}