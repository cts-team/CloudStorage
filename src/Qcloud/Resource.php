<?php


namespace CoverCMS\CloudStorage\Qcloud;


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
        $body = $content;
        if (is_file($content)) {
            $body = fopen($content, 'rb');
        }

        return $this->sdk->putObject(array_merge([
            'Bucket' => $this->bucket, //格式：BucketName-APPID
            'Key' => $key,
            'Body' => $body,
        ], $options))->toArray();
    }

    public function initMultipartUpload(string $key, array $options = []): string
    {
        return $this->sdk->createMultipartUpload(array_merge([
            'Bucket' => $this->bucket, //格式：BucketName-APPID
            'Key' => $key,
        ], $options))['UploadId'];
    }

    public function uploadPart(string $key, string $content, int $partNumber, string $uploadId)
    {
        return $this->sdk->uploadPart(array(
            'Bucket' => $this->bucket, //格式：BucketName-APPID
            'Key' => $key,
            'Body' => $content,
            'UploadId' => $uploadId, //UploadId 为对象分块上传的 ID，在分块上传初始化的返回参数里获得
            'PartNumber' => $partNumber, //PartNumber 为分块的序列号，COS 会根据携带序列号合并分块
        ))['ETag'];
    }

    public function completeMultipartUpload(string $key, string $uploadId, array $parts)
    {
        return $this->sdk->completeMultipartUpload([
            'Bucket' => $this->bucket, //格式：BucketName-APPID
            'Key' => $key,
            'UploadId' => $uploadId,
            'Parts' => $parts,
        ])->toArray();
    }

    public function download(string $key, string $localPath, array $options = [])
    {
        $config = array_merge([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SaveAs' => $localPath
        ], $options);

        return $this->sdk->getObject($config);
    }

    public function isExist(string $key, array $options = []): bool
    {
        return $this->sdk->doesObjectExist($this->bucket, $key);
    }

    public function remove($key, array $options = [])
    {
        foreach ((array)$key as $item) {
            $this->sdk->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $item
            ]);
        }
    }

    public function meta(string $key, array $options = [])
    {
        return $this->sdk->headObject(array_merge([
            'Bucket' => $this->bucket, //格式：BucketName-APPID
            'Key' => $key,
        ], $options))->toArray();
    }
}