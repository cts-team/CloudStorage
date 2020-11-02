<?php


namespace CoverCMS\CloudStorage;


use CoverCMS\CloudStorage\Baidu\Bucket;
use CoverCMS\CloudStorage\Baidu\Resource;

class BaiduAdapter
{
    protected $ak;

    protected $sk;

    protected $endPoint;

    protected $bucketManger;

    protected $resourceManager;

    public function __construct(string $ak, string $sk, string $endPoint)
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->endPoint = $endPoint;
        $this->bucketManger = new Bucket($this->ak, $this->sk, $this->endPoint);
        $this->resourceManager = new Resource($this->ak, $this->sk, $this->endPoint);
    }

    /**
     * @return Bucket
     */
    public function getBucketManager()
    {
        return $this->bucketManger;
    }

    /**
     * @param string $bucket
     * @return ResourceInterface
     */
    public function getResourceManager(string $bucket): ResourceInterface
    {
        return $this->resourceManager->setBucket($bucket);
    }
}