<?php

namespace App\Sdks\Uc;



use App\Sdks\Uc\Traits\AccountFeed;
use App\Sdks\Uc\Traits\AdgroupFeed;
use App\Sdks\Uc\Traits\CampaignFeed;
use App\Sdks\Uc\Traits\CreativeFeed;
use App\Sdks\Uc\Traits\Image;
use App\Sdks\Uc\Traits\ReportFeed;
use App\Sdks\Uc\Traits\Error;
use App\Sdks\Uc\Traits\MultiRequest;
use App\Sdks\Uc\Traits\OcpcToken;
use App\Sdks\Uc\Traits\Token;
use App\Sdks\Uc\Traits\Account;
use App\Sdks\Uc\Traits\AccountPassword;
use App\Sdks\Uc\Traits\Request;
use App\Sdks\Uc\Traits\Video;

class Uc
{
    use Account;
    use AccountPassword;
    use Token;
    use OcpcToken;
    use Request;
    use MultiRequest;
    use Error;
    use AccountFeed;
    use CampaignFeed;
    use AdgroupFeed;
    use CreativeFeed;
    use ReportFeed;
    use Image;
    use Video;

    /**
     * 公共接口地址
     */
    const BASE_URL = 'https://e.uc.cn/shc';




    /**
     * Uc constructor.
     * @param $accountName
     * @param $password
     * @param $token
     */
    public function __construct($accountName,$password,$token){
        $this->setAccountName($accountName);
        $this->setAccountPassword($password);
        $this->setToken($token);
    }

    /**
     * @param $uri
     * @return string
     * 获取请求地址
     */
    public function getUrl($uri){
        return self::BASE_URL .'/'. ltrim($uri, '/');
    }

    /**
     * @param string $path
     * @return string
     * 获取 sdk 路径
     */
    public function getSdkPath($path = ''){
        $path = rtrim($path, '/');
        $sdkPath = rtrim(__DIR__ .'/'. $path, '/');
        return $sdkPath;
    }


    /**
     * @param $file
     * @return string
     * 获取 素材的md5
     */
    public function getMaterialMd5($file){
        if($fp = fopen($file,"rb", 0))
        {
            $gambar = fread($fp,filesize($file));
            fclose($fp);

            return md5($gambar);
        }
    }

}
