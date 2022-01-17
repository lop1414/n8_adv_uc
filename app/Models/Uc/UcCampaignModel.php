<?php

namespace App\Models\Uc;


class UcCampaignModel extends UcModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'uc_campaigns';


    /**
     * @var array
     * 批量更新忽略字段
     */
    protected $updateIgnoreFields = [
        'created_at'
    ];



    protected $fillable = [
        'account_id',
        'adgroup_id',
        'name',
        'type',
        'paused',
        'opt_target',
        'delivery',
        'budget',
        'charge_type',
        'bids',
        'enable_anxt',
        'extends',
        'remark_status'
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联推广账户
     */
    public function uc_account(){
        return $this->hasOne('App\Models\Uc\UcAccountModel', 'account_id', 'account_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联推广组
     */
    public function uc_adgroup(){
        return $this->hasOne('App\Models\Uc\UcAdgroupModel', 'id', 'adgroup_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联推广单元扩展模型 一对一
     */
    public function uc_campaign_extends(){
        return $this->hasOne('App\Models\Uc\UcCampaignExtendModel', 'campaign_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 关联渠道模型 一对一
     */
    public function channel_campaign(){
        return $this->hasOne('App\Models\ChannelCampaignModel', 'campaign_id', 'id');
    }


}
