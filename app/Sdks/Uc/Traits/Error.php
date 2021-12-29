<?php

namespace App\Sdks\Uc\Traits;

trait Error
{
    /**
     * @return array
     * 获取返回映射
     */
    public function getCodeMessageMap(){
        return [
            // 成功返回
            0 => '成功',

            // 系统相关
            800     => '系统错误',
            8303    => '没有权限操作该用户',
            8401    => '缺少token信息',
            8402    => 'token不合法',
            8403    => 'token已删除',
            8408    => '没有权限使用该token',
            8603    => '请求头信息不合法',
            8611    => '请求超时，请稍后再试',

        ];
    }


    /**
     * @param $result
     * @return bool
     * 有计划不存在的错误
     */
    public function hasCampaignFeedIdNotExists($result){

        $failures = $result['header']['failures'];
        foreach ($failures as $item){
            if($this->isCampaignFeedIdNotExistsByCode($item['code'])){
                return true;
            }
        }

        return false;
    }


    /**
     * @param $code
     * @return bool
     * 是否计划不存在的错误
     */
    public function isCampaignFeedIdNotExistsByCode($code){
        $errorCodes = [
            912401411, // 计划不存在
        ];

        if(in_array($code, $errorCodes)){
            return true;
        }

        return false;
    }


}
