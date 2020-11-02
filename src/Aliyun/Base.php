<?php


namespace CoverCMS\CloudStorage\Aliyun;

require __DIR__ . '/../../sdk/aliyun-oss-php-sdk-2.4.1/autoload.php';

use OSS\OssClient;

class Base
{
    protected $ak;

    protected $sk;

    protected $endPoint;

    protected $sdk;

    public function __construct(string $ak, string $sk, string $endPoint)
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->endPoint = $endPoint;
        $this->sdk = new OssClient($this->ak, $this->sk, $this->endPoint);
    }
}