<?php

namespace App\Services\Uc;

use App\Models\Uc\UcCreativeTemplateModel;


class UcCreativeTemplateService extends UcService
{

    public function syncItem($subAccount){

        $params = $this->getAdgroupParamByAccount($subAccount);

        $saveData = [];
        $list = $this->sdk->multiGetCreativeTemplate($params);

        foreach ($list as $item){
            // 计划不存在处理
//            $this->handleAdgroupIdNotExists($item);

            $accountName = isset($item['req']['param']['header']['target'])
                ? $item['req']['param']['header']['target']
                : $item['req']['param']['header']['username'];

            $account = $this->getAccountByName($accountName);

            $adgroupId = $item['req']['param']['body']['adGroupId'];
            foreach ($item['data']['body']['adGroupCreativeTemplates'] as $creative){
                $creative['creativeTemplateContent'] = json_decode($creative['creativeTemplateContent'] ,true);

                $saveData[] = [
                    'id'                => $creative['creativeTemplateId'],
                    'account_id'        => $account['account_id'],
                    'adgroup_id'        => $adgroupId,
                    'name'              => $creative['creativeTemplateName'],
                    'style_type'        => $creative['creativeTemplateStyleType'],
                    'extends'           => json_encode($creative),
                    'remark_status'     => '',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            }
        }
        if(empty($saveData)) return;
        $this->batchSave(UcCreativeTemplateModel::class,$saveData);
    }
}
