<?php


namespace CoverCMS\CloudStorage\Baidu;

require __DIR__.'/../../sdk/bce-php-sdk-0.9.8/BaiduBce.phar';
//require __DIR__.'/../../sdk/bce-php-sdk-0.9.8/BaiduBce.phar/src/BaiduBce/Services/Bos/BosClient.php';

use BaiduBce\BceClientConfigOptions;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Services\Bos\BosClient;

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

        $this->sdk = new BosClient([
            'credentials' => array(
                'accessKeyId' => $this->ak,
                'secretAccessKey' => $this->sk,
//                'sessionToken' => 'your session token'
            ),
            'endpoint' => 'http://' . $this->endPoint,
        ]);
    }
}