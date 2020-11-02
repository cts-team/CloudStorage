<?php


namespace CoverCMS\CloudStorage\Qiniu;


use CoverCMS\CloudStorage\ResultHandler;

class Bucket extends Base
{
    public function all(array $options = [])
    {
        $buckets = $this->bucketManager->listbuckets();

        return new ResultHandler($buckets);
    }
}