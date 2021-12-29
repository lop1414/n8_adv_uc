<?php

namespace App\Sdks\Uc\Traits;


trait Creative
{
    /**
     * @param $params
     * @return mixed
     * 并发获取创意ID
     */
    public function multiGetCreative($params){
        $url = $this->getUrl('api/creative/getCreativeByCreativeId');
        $reqParams = [];
        foreach ($params as $item){
            if(!empty($item['creative_ids'])){
                $reqParams[] = [
                    'body' => [
                        'creativeIds'   => $item['creative_ids']
                    ],
                    'header' =>  [
                        'target' => $item['account_name']
                    ]
                ];
            }
        }

        return $this->multiGet($url,$reqParams);
    }


    /**
     * @param $params
     * @return mixed
     * 并发获取创意ID
     */
    public function multiGetCreativeId($params){
        $url = $this->getUrl('api/creative/getCreativeIdByCampaignId');
        $reqParams = [];
        foreach ($params as $item){
            if(!empty($item['campaign_ids'])){
                    $reqParams[] = [
                        'body' => [
                            'campaignIds'   => $item['campaign_ids']
                        ],
                        'header' =>  [
                            'target' => $item['account_name']
                        ]
                    ];
            }
        }

        return $this->multiGet($url,$reqParams);
    }

    /**
     * @param $params
     * @return mixed
     * 并发获取创意样式模板
     */
    public function multiGetCreativeTemplate($params){
        $url = $this->getUrl('api/creative/getCreativeTemplates');
        $reqParams = [];
        foreach ($params as $item){
            if(!empty($item['adgroup_ids'])){
                foreach ($item['adgroup_ids'] as $adgroupId){
                    $reqParams[] = [
                        'body' => [
                            'adGroupId'    => $adgroupId
                        ],
                        'header' =>  [
                            'target' => $item['account_name']
                        ]
                    ];
                }
            }
        }

        return $this->multiGet($url,$reqParams);
    }

}
