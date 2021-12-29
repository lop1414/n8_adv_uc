<?php

namespace App\Services\Uc;

use App\Models\Uc\UcCreativeModel;


class UcCreativeService extends UcService
{

    public function syncItem($subAccount){

        $params = $this->getAdgroupParamByAccount($subAccount);

        $saveData = [];
        $list = $this->sdk->multiGetCreative($params);

        foreach ($list as $item){
            // 计划不存在处理
//            $this->handleAdgroupIdNotExists($item);

            $accountName = isset($item['req']['param']['header']['target'])
                ? $item['req']['param']['header']['target']
                : $item['req']['param']['header']['username'];

            $account = $this->getAccountByName($accountName);

            foreach ($item['data']['body']['adGroupCreativeTemplates'] as $creative){
                $creative['creativeTemplateContent'] = json_decode($creative['creativeTemplateContent'] ,true);

//                $saveData[] = [
//                    'id'                => $creative['creativeFeedId'],
//                    'account_id'        => $account['account_id'],
//                    'adgroup_id'        => $creative['adgroupFeedId'],
//                    'name'              => $creative['creativeFeedName'],
//                    'materialstyle'     => $creative['materialstyle'],
//                    'pause'             => $creative['pause'],
//                    'status'            => $creative['status'],
//                    'idea_type'         => $creative['ideaType'],
//                    'show_mt'           => $creative['showMt'] ?? 0,
//                    'addtime'           => date('Y-m-d H:i:s',strtotime($creative['addtime'])),
//                    'extends'           => json_encode($creative),
//                    'remark_status'     => '',
//                    'created_at'        => date('Y-m-d H:i:s'),
//                    'updated_at'        => date('Y-m-d H:i:s'),
//                ];
            }
        }
        dd($list);
        if(empty($saveData)) return;
        $this->batchSave(UcCreativeModel::class,$saveData);
    }
}
