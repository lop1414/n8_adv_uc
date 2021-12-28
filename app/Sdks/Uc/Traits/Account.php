<?php

namespace App\Sdks\Uc\Traits;

use App\Common\Tools\CustomException;

trait Account
{
    /**
     * @var
     * 账户id
     */
    protected $accountId;

    /**
     * @var
     * 账户名称
     */
    protected $accountName;

    /**
     * @var
     * 目标账户名称
     */
    protected $targetAccountName;

    /**
     * @param $accountId
     * @return bool
     * 设置账户id
     */
    public function setAccountId($accountId){
        $this->accountId = $accountId;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取账户id
     */
    public function getAccountId(){
        if(is_null($this->accountId)){
            throw new CustomException([
                'code' => 'NOT_FOUND_ACCOUNT_ID',
                'message' => '尚未设置账户id',
            ]);
        }
        return $this->accountId;
    }



    /**
     * @param $name
     * @return bool
     * 设置账户名称
     */
    public function setAccountName($name){
        $this->accountName = $name;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取账户名称
     */
    public function getAccountName(){
        if(is_null($this->accountName)){
            throw new CustomException([
                'code' => 'NOT_FOUND_ACCOUNT_NAME',
                'message' => '尚未设置账户名称',
            ]);
        }
        return $this->accountName;
    }


    /**
     * @param $name
     * @return bool
     * 设置目标账户名称
     */
    public function setTargetAccountName($name){
        $this->targetAccountName = $name;
        return true;
    }


    /**
     * @return mixed
     * 获取目标账户名称
     */
    public function getTargetAccountName(){
        return $this->targetAccountName;
    }



    /**
     * @return mixed
     * 获取子账户账户
     */
    public function getSubAccount(){
        $url = $this->getUrl('api/account/getChildrenAccountByAccountId');

        return $this->authRequest($url, [], 'POST');
    }

}
