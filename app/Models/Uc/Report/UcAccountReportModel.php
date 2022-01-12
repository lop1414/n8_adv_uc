<?php

namespace App\Models\Uc\Report;

class UcAccountReportModel extends UcReportModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'uc_account_reports';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
