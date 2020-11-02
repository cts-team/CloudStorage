<?php


namespace CoverCMS\CloudStorage\Qcloud;

use Qcloud\Cos\Client;

class Base
{
    protected $ak;

    protected $sk;

    protected $sdk;

    public function __construct(string $ak, string $sk, string $region)
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->sdk = new Client([
            'region' => $region,
            'schema' => 'http',
            'credentials' => [
                'secretId' => $this->ak,
                'secretKey' => $this->sk
            ]
        ]);
    }
}