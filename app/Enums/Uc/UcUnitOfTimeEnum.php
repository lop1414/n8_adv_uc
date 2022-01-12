<?php

namespace App\Enums\Uc;

class UcUnitOfTimeEnum
{

    const HOUR = 0;
    const DAY = 1;
    const MONTH = 2;
    const SUM = 3;
    const WEEK = 4;



    /**
     * @var string
     * 名称
     */
    static public $name = 'UC广告计划投放状态';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::HOUR, 'name' => '小时'],
        ['id' => self::DAY, 'name' => '日报'],
        ['id' => self::MONTH, 'name' => '月报'],
        ['id' => self::SUM, 'name' => '汇总'],
        ['id' => self::WEEK, 'name' => '周报'],

    ];
}
