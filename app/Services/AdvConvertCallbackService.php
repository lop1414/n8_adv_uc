<?php

namespace App\Services;

use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Common\Services\ConvertCallbackService;

class AdvConvertCallbackService extends ConvertCallbackService
{
    /**
     * @param $item
     * @return bool
     * @throws CustomException
     * 回传
     */
    protected function callback($item){
        $productType  = $item->extends->convert->n8_union_user->product_type ?? ProductTypeEnums::KYY;
        $convertAt  = isset($item->extends->convert->convert_at) ? strtotime($item->extends->convert->convert_at) : 0;
        $eventTypeMap = $this->getEventTypeMap($productType);
        if(!isset($eventTypeMap[$item->convert_type])){
            // 无映射
            throw new CustomException([
                'code' => 'UNDEFINED_EVENT_TYPE_MAP',
                'message' => '未定义的事件类型映射',
                'log' => true,
                'data' => [
                    'item' => $item,
                ],
            ]);
        }

        // 关联点击
        if(empty($item->click)){
            throw new CustomException([
                'code' => 'NOT_FOUND_CONVERT_CLICK',
                'message' => '找不到该转化对应点击',
                'log' => true,
                'data' => [
                    'item' => $item,
                ],
            ]);
        }

        $eventType = $eventTypeMap[$item->convert_type];

        //付费金额
        $payAmount = 0;
        if(!empty($payAmount)){
            $payAmount =  $item->extends->amount;
        }

        $this->runCallback($item->click,$eventType,$convertAt,$payAmount);

        return true;
    }



    public function runCallback($click,$eventType,$convertAt,$payAmount = 0){
        if(!empty($click->callback_url)){
            return $this->callbackUrl($click, $eventType,$convertAt, $payAmount);

        }else{
            return $this->callbackParam($click, $eventType,$convertAt, $payAmount);

        }

    }






    /**
     * @param $click
     * @param $eventType
     * @param int $convertAt
     * @param int $payAmount
     * @return bool
     * 转化回调地址 回传
     */
    public function callbackUrl($click, $eventType, $convertAt = 0,$payAmount = 0){

        $callbackUrl = $click->callback_url;
        $callbackUrl .= '&type='.$eventType;
        $callbackUrl .= '&oaid='.$click->oaid;
        $click->os == 0 && $callbackUrl .= '&idfa='.$click->muid;
        $click->os == 1 && $callbackUrl .= '&imei_sum='.$click->muid;
        $convertAt > 0 && $callbackUrl .= '&event_time='.$convertAt;
        // 回传金额
        if($payAmount > 0){
            $callbackUrl .= '&money='.$payAmount;
        }
        file_get_contents($callbackUrl);
        return true;
    }



    /**
     * @param $click
     * @param $eventType
     * @param int $convertAt
     * @param int $payAmount
     * @throws CustomException
     * 回传参数回传
     */
    public function callbackParam($click, $eventType, $convertAt = 0,$payAmount = 0){
        throw new CustomException([
            'code' => 'CONVERT_CALLBACK_ERROR',
            'message' => '转化回传失败 - 回传链接回传',
            'log' => true,
            'data' => [
                'click_id' => $click->id ?? 0,
                'event_type' => $eventType,
                'pay_amount' => $payAmount,
                'covert_at'  => $convertAt
            ],
        ]);
    }



    /**
     * @return array
     * 获取事件映射
     */
    public function getEventTypeMap($productType){
        if($productType == ProductTypeEnums::H5){
            return [
                ConvertTypeEnum::ACTIVATION => 1,
                ConvertTypeEnum::REGISTER => 27,
                ConvertTypeEnum::FOLLOW => 65,
                ConvertTypeEnum::PAY => 66,
            ];
        }

        if($productType == ProductTypeEnums::KYY){
            return [
                ConvertTypeEnum::ACTIVATION => 1,
                ConvertTypeEnum::REGISTER => 27,
                ConvertTypeEnum::ADD_DESKTOP => 68,
                ConvertTypeEnum::PAY => 69,
            ];
        }

        return [];
    }


    /**
     * @param $url
     * @param array $data
     * @return bool|string
     */
    public function curlPost($url , $data = []){

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);

        if(stripos($url, 'https://') === 0){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        }

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $output = curl_exec($ch);

        curl_close($ch);

        return json_decode($output, true);

    }



    /**
     * @param $click
     * @return array|void
     */
    public function filterClickData($click){
        return [
            'id' => $click['id'],
            'campaign_id' => $click['campaign_id'],
            'ad_id' => $click['adgroup_id'],
            'creative_id' => $click['creative_id'],
            'click_at' => $click['click_at'],
        ];
    }
}
