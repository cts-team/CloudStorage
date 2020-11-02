<?php


namespace CoverCMS\CloudStorage\Baidu;


use CoverCMS\CloudStorage\ResultHandler;

class Bucket extends Base
{
    public function all()
    {
        $response = $this->sdk->listBuckets();
        $data = json_decode(json_encode($response), true);

        return new ResultHandler($data['buckets'], $response);
    }
}