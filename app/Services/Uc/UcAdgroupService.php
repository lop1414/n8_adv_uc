<?php

namespace App\Services\Uc;



use App\Models\Uc\UcAdgroupModel;

class UcAdgroupService extends UcService
{


    public function syncItem($subAccount){
        $accountNames = [];
        foreach ($subAccount as $account){
            $this->setAccountMap($account);
            $accountNames[] = $account['name'];
        }

        $list = $this->sdk->multiGetAdgroup($accountNames);
        $saveData = [];
        foreach ($list as $item){

            $accountName = isset($item['req']['param']['header']['target'])
                ? $item['req']['param']['header']['target']
                : $item['req']['param']['header']['username'];

            $account = $this->getAccountByName($accountName);
            foreach ($item['data']['body']['adGroupTypes'] as $campaign){
                $saveData[] = [
                    'id'                => $campaign['id'],
                    'account_id'        => $account['account_id'],
                    'name'              => $campaign['name'],
                    'objective_type'    => $campaign['objectiveType'],
                    'paused'            => $campaign['paused'],
                    'budget'            => $campaign['budget'] * 100,
                    'remark_status'     => '',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            }
        }

        if(empty($saveData)) return;
        $this->batchSave(UcAdgroupModel::class,$saveData);
    }


}
