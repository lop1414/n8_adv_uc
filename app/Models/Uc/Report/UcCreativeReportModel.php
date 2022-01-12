<?php

namespace App\Models\Uc\Report;

use Illuminate\Support\Facades\DB;

class UcCreativeReportModel extends UcReportModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'uc_creative_reports';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';



    /**
     * @param $query
     * @return mixed
     * 计算
     */
    public function scopeCompute($query){
        return $query->select(DB::raw("
                SUM(`consume`) `consume`,
                SUM(`click`) `click`,
                SUM(`srch`) `srch`,
                SUM(`binding_conversion`) `binding_conversion`,
                ROUND(SUM(`consume` / 100) / SUM(`srch`) * 1000, 2) `srch_cost`,
                ROUND(SUM(`consume` / 100) / SUM(`click`), 2) `click_cost`,
                CONCAT(ROUND(SUM(`click`) / SUM(`srch`) * 100, 2), '%') `click_rate`,
                ROUND(SUM(`consume` / 100) / SUM(`binding_conversion`), 2) `binding_conversion_cost`,
                CONCAT(ROUND(SUM(`binding_conversion`) / SUM(`click`) * 100, 2), '%') `binding_conversion_rate`
            "));
    }
}
