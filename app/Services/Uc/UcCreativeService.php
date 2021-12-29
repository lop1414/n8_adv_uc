<?php

namespace App\Services\Uc;

use App\Models\Uc\UcCreativeModel;


class UcCreativeService extends UcService
{

    public function syncItem($subAccount){

        $params = $this->getCampaignParamByAccount($subAccount);

        $saveData = [];
        $list = $this->sdk->multiGetCreativeId($params);

        foreach ($list as $item){
            // 计划不存在处理
//            $this->handleAdgroupIdNotExists($item);

            $accountName = isset($item['req']['param']['header']['target'])
                ? $item['req']['param']['header']['target']
                : $item['req']['param']['header']['username'];

            $account = $this->getAccountByName($accountName);

            $campaignCreativeIds = $item['data']['body']['campaignCreativeIds'] ?? [];
            foreach ($campaignCreativeIds as $campaignCreativeId){
                $creativeList = $this->sdk->multiGetCreative([[
                    'creative_ids' => $campaignCreativeId['creativeIds'],
                    'account_name' => $accountName,
                ]]);

                foreach ($creativeList as $creatives) {
                    foreach ($creatives['data']['body']['creativeTypes'] as $creative) {
                        $saveData[] = [
                            'id' => $creative['id'],
                            'account_id' => $account['account_id'],
                            'campaign_id' => $creative['campaignId'],
                            'style' => $creative['style'],
                            'style_type' => $creative['styleType'],
                            'show_mode' => $creative['showMode'],
                            'paused' => $creative['paused'],
                            'state' => $creative['state'],
                            'extends' => json_encode($creative),
                            'remark_status' => '',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
        }
        if(empty($saveData)) return;
        $this->batchSave(UcCreativeModel::class,$saveData);
    }
}
