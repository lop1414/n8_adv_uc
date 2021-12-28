<?php

namespace App\Services\Uc;

use App\Common\Enums\StatusEnum;
use App\Models\Uc\UcAccountModel;


class UcAccountService extends UcService
{

    protected $account;


    public function read($accountId){
        if(empty($this->account[$accountId])){
            $this->account[$accountId] = (new UcAccountModel())->find($accountId);
        }
        return $this->account[$accountId];
    }


    public function syncSubAccount(){
        $data = $this->sdk->getSubAccount();
        foreach ($data['childrenAccounts'] as $item){
            $info = (new UcAccountModel())->where('account_id',$item['id'])->first();
            if(empty($info)){
                $info = new UcAccountModel();
                $info->status = StatusEnum::ENABLE;
                $info->token = '';
                $info->password = '';
            }
            $info->account_id = $item['id'];
            $info->name = $item['name'];
            $info->parent_id = $this->manageAccount['account_id'];
            $info->admin_id = 0;
            $info->extends = [
                'remark' => $item['remark']
            ];
            $info->save();
        }
    }


    /**
     * @param $subAccount
     * 同步账户
     */
    public function syncItem($subAccount){}
}
