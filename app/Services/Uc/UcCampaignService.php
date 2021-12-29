<?php

namespace App\Services\Uc;



use App\Models\Uc\UcCampaignModel;

class UcCampaignService extends UcService
{


    public function syncItem($subAccount){

        $params = $this->getAdgroupParamByAccount($subAccount);

        $saveData = [];
        $list = $this->sdk->multiGetCampaign($params);

        foreach ($list as $item){
            // 计划不存在处理
//            $this->handleAdgroupIdNotExists($item);

            $accountName = isset($item['req']['param']['header']['target'])
                ? $item['req']['param']['header']['target']
                : $item['req']['param']['header']['username'];

            $account = $this->getAccountByName($accountName);

            foreach ($item['data']['body']['adGroupCampaigns'] as $adgroup){
                foreach ($adgroup['campaignTypes'] as $campaign){
                    $saveData[] = [
                        'id'                => $campaign['id'],
                        'account_id'        => $account['account_id'],
                        'adgroup_id'        => $campaign['adGroupId'],
                        'name'              => $campaign['name'],
                        'type'              => $campaign['type'],
                        'paused'            => $campaign['paused'],
                        'opt_target'        => $campaign['optTarget'],
                        'delivery'          => $campaign['delivery'],
                        'delivery_mode'     => $campaign['deliveryMode'],
                        'budget'            => $campaign['budget'] * 100,
                        'charge_type'       => $campaign['chargeType'],
                        'enable_anxt'       => $campaign['enableAnxt'],
                        'anxt_status'       => $campaign['anxtStatus'],
                        'show_mode'         => $campaign['showMode'],
                        'extends'           => json_encode($campaign),
                        'remark_status'     => '',
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }
        if(empty($saveData)) return;
        $this->batchSave( UcCampaignModel::class,$saveData);
    }
}
