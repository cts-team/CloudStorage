<?php


namespace CoverCMS\CloudStorage\Qiniu;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

require __DIR__ . '/../../sdk/qiniu-sdk-7.3.0/autoload.php';

class Base
{
    protected $auth;

    protected $bucketManager;

    protected $uploadManager;

    protected $config;

    public function __construct(string $ak, string $sk)
    {
        $this->auth = new Auth($ak, $sk);
        $this->config = new Config();
        $this->bucketManager = new BucketManager($this->auth, $this->config);
        $this->uploadManager = new UploadManager($this->config);
    }
}