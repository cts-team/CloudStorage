<?php


namespace CoverCMS\CloudStorage;


use CoverCMS\CloudStorage\Qiniu\Bucket;
use CoverCMS\CloudStorage\Qiniu\Resource;

class QiniuAdapter
{
    protected $ak;

    protected $sk;

    protected $bucketManger;

    protected $resourceManager;

    public function __construct(string $ak, string $sk)
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->bucketManger = new Bucket($this->ak, $this->sk);
        $this->resourceManager = new Resource($this->ak, $this->sk);
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