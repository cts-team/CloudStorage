<?php


namespace CoverCMS\CloudStorage\Aliyun;


use CoverCMS\CloudStorage\ResultHandler;
use OSS\Core\OssException;
use OSS\OssClient;

class Bucket extends Base
{
    /**
     * @param array $options
     * @return ResultHandler
     * @throws OssException
     */
    public function all(array $options = [])
    {
        $bucketListInfo = $this->sdk->listBuckets($options);
        $bucketList = $bucketListInfo->getBucketList();

        foreach ($bucketList as $bucket) {
            $result[] = [
                'location' => $bucket->getLocation(),
                'name' => $bucket->getName(),
                'createDate' => $bucket->getCreateDate(),
                'storageClass' => $bucket->getStorageClass(),
                'extranetEndpoint' => $bucket->getExtranetEndpoint(),
                'intranetEndpoint' => $bucket->getIntranetEndpoint(),
                'region' => $bucket->getRegion()
            ];
        }

        return new ResultHandler($result ?? [], $bucketList);
    }

    /**
     * 创建存储空间
     *
     * @param string $bucketName
     */
    public function create(string $bucketName): void
    {
        $options = [
            OssClient::OSS_STORAGE => OssClient::OSS_STORAGE_STANDARD
        ];
        // 设置存储空间的权限为公共读，默认是私有读写。
        $this->sdk->createBucket($bucketName, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE, $options);
    }

    /**
     * @param string $bucketName
     * @return bool
     * @throws OssException
     */
    public function isExist(string $bucketName)
    {
        $res = $this->sdk->doesBucketExist($bucketName);

        return $res === true;
    }
}