<?php

namespace App\Models\Uc;


class UcAccountModel extends UcModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'uc_accounts';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'account_id';



    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;




    protected $fillable = [
        'account_id',
        'name',
        'token',
        'rebate',
        'password',
        'parent_id',
        'status',
        'admin_id',
        'extends',
    ];


    /**
     * 属性访问器
     * @param $value
     * @return mixed
     */
    public function getExtendsAttribute($value){
        return json_decode($value);
    }

    /**
     * 属性修饰器
     * @param $value
     */
    public function setExtendsAttribute($value){
        $this->attributes['extends'] = json_encode($value);
    }


    /**
     * 产品
     */
    public function manageAccount(){
        return $this->hasOne('App\Models\Uc\UcAccountModel', 'account_id', 'parent_id');
    }

}
