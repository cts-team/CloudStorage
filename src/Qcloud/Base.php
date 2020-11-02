<?php


namespace CoverCMS\CloudStorage\Qcloud;

require __DIR__ . '/../../sdk/cos-php-sdk-v5/vendor/autoload.php';

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