<?php


namespace CoverCMS\CloudStorage;


use CoverCMS\CloudStorage\Qcloud\Bucket;
use CoverCMS\CloudStorage\Qcloud\Resource;

class QcloudAdapter
{
    protected $ak;

    protected $sk;

    protected $region;

    protected $bucketManger;

    protected $resourceManager;

    public function __construct(string $ak, string $sk, string $region)
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->region = $region;
        $this->bucketManger = new Bucket($this->ak, $this->sk, $this->region);
        $this->resourceManager = new Resource($this->ak, $this->sk, $this->region);
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