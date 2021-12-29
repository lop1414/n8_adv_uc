<?php

namespace App\Sdks\Uc\Traits;


trait Adgroup
{


    /**
     * @param $accountNames
     * @return mixed
     * 并发获取信息流计划信息
     */
    public function multiGetAdgroup($accountNames){
        $url = $this->getUrl('/api/adgroup/getAllAdGroup');

        $params = [];
        foreach ($accountNames as $accountName){
            $params[] = [
                'header' =>  [
                    'target' => $accountName
                ]
            ];
        }

        return $this->multiGet($url,$params);
    }


}
