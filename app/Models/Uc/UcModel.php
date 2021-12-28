<?php

namespace App\Models\Uc;

use App\Common\Helpers\Functions;
use App\Common\Models\BaseModel;

class UcModel extends BaseModel
{
    /**
     * @param $query
     * 数据授权
     */
    public function scopeWithPermission($query){
        $adminUserInfo = Functions::getGlobalData('admin_user_info');
        $table = $this->getTable();
        if(!$adminUserInfo['is_admin']){
            $query->whereRaw("
                    {$table}.account_id IN (
                        SELECT account_id FROM uc_accounts
                            WHERE admin_id = {$adminUserInfo['admin_user']['id']}
                    )
                ");
        }
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联账户
     */
    public function baidu_account(){
        return $this->hasOne('App\Models\Uc\UcAccountModel', 'account_id', 'account_id');
    }

}
