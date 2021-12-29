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

}
