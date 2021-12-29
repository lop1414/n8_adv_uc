<?php

namespace App\Sdks\Uc\Traits;


trait Creative
{

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
