<?php

namespace App\Sdks\Uc\Traits;

use App\Common\Tools\CustomException;

trait AccountPassword
{
    /**
     * @var
     * 账户密码
     */
    protected $accountPassword;


    /**
     * @param $password
     * @return bool
     * 设置账户密码
     */
    public function setAccountPassword($password){
        $this->accountPassword = $password;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取账户id
     */
    public function getAccountPassword(){
        if(is_null($this->accountPassword)){
            throw new CustomException([
                'code' => 'NOT_FOUND_ACCOUNT_PASSWORD',
                'message' => '尚未设置账户密码',
            ]);
        }
        return $this->accountPassword;
    }


}
