<?php

namespace App\Sdks\Uc\Traits;


trait Campaign
{



    /**
     * @param $params
     * @return mixed
     * 并发获取推广组
     */
    public function multiGetCampaign($params){
        $url = $this->getUrl('api/adgroup/getAllAdGroup');


        $reqParams = [];
        foreach ($params as $item){
            $reqParams[] = [
                'header' =>  [
                    'target' => $item['account_name']
                ]
            ];
        }

        return $this->multiGet($url,$reqParams);
    }

}
