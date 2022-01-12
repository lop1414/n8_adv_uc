<?php

namespace App\Models\Uc\Report;

use App\Models\Uc\UcModel;

class UcReportModel extends UcModel
{
    /**
     * @var bool
     * 关闭自动更新时间戳
     */
    public $timestamps= false;

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
     * @param $value
     * @return float|int
     * 属性访问器
     */
    public function getConsumeAttribute($value)
    {
        return $value / 100;
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setConsumeAttribute($value)
    {
        $this->attributes['consume'] = $value * 100;
    }
}
