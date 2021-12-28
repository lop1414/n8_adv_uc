<?php

namespace App\Sdks\Uc\Traits;

use App\Common\Tools\CustomException;

trait Token
{
    /**
     * @var
     * access token
     */
    protected $token;

    /**
     * @param $token
     * @return bool
     * 设置  token
     */
    public function setToken($token){
        $this->token = $token;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取 access token
     */
    public function getToken(){
        if(is_null($this->token)){
            throw new CustomException([
                'code' => 'NOT_FOUND_TOKEN',
                'message' => '尚未设置token',
            ]);
        }
        return $this->token;
    }
}
