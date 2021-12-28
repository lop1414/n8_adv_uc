<?php

namespace App\Enums\Uc;

class UcSyncTypeEnum
{
    const CAMPAIGN = 'CAMPAIGN';
    const ADGROUP = 'ADGROUP';
    const CREATIVE = 'CREATIVE';

    /**
     * @var string
     * 名称
     */
    static public $name = '百度同步类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::CAMPAIGN, 'name' => '推广计划'],
        ['id' => self::ADGROUP, 'name' => '推广单元'],
        ['id' => self::CREATIVE, 'name' => '创意'],
    ];
}
