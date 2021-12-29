<?php

namespace App\Enums;

class RemarkStatusEnum
{
    const DELETE = 'DELETE';

    /**
     * @var string
     * 名称
     */
    static public $name = '备注状态';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::DELETE, 'name' => '删除'],
    ];
}
