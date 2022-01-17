<?php

namespace App\Models\Uc;



class UcAdgroupModel extends UcModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'uc_adgroups';


    /**
     * @var array
     * 批量更新忽略字段
     */
    protected $updateIgnoreFields = [
        'created_at'
    ];



    protected $fillable = [
        'account_id',
        'name',
        'objective_type',
        'paused',
        'budget',
        'remark_status',
    ];


    /**
     * 属性访问器
     * @param $value
     * @return mixed
     */
    public function getBudgetAttribute($value){
        return $value / 100;
    }

    /**
     * 属性修饰器
     * @param $value
     */
    public function setBudgetAttribute($value){
        $this->attributes['budget'] = $value * 100;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联推广账户
     */
    public function uc_account(){
        return $this->hasOne('App\Models\Uc\UcAccountModel', 'account_id', 'account_id');
    }

}
