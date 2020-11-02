<?php


namespace CoverCMS\CloudStorage\Qcloud;


use CoverCMS\CloudStorage\ResultHandler;

class Bucket extends Base
{
    public function all()
    {
        $buckets = $this->sdk->listBuckets();

        return new ResultHandler($buckets->toArray(), $buckets);
    }
}