<?php

namespace App\Sdks\Uc\Traits;


trait Creative
{

    /**
     * @param $params
     * @return mixed
     * 并发获取信息流原生创意信息
     */
    public function multiGetCreative($params){
        $url = $this->getUrl('api/creative/getCreativeByCampaignId');

        $reqParams = [];
        foreach ($params as $item){
            $reqParams[] = [
                'body' => [
                    'campaignIds'    => $item['campaign_ids']
                ],
                'header' =>  [
                    'target' => $item['account_name']
                ]
            ];
        }

        return $this->multiGet($url,$reqParams);
    }

}
