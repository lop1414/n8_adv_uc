<?php

namespace App\Enums\Uc;

class UcCampaignStatusEnum
{
    const CAMPAIGN_STATUS_DELIVERY_OK = 0;
    const CAMPAIGN_STATUS_DISABLE = 1;
    const CAMPAIGN_STATUS_BUDGET_EXCEED = 2;
    const CAMPAIGN_STATUS_NO_SCHEDULE = 3;
    const CAMPAIGN_STATUS_AUDIT = 4;
    const CAMPAIGN_STATUS_AUDIT_DENY = 5;
    const CAMPAIGN_STATUS_STYLE_OFFLINE = 6;
    const CAMPAIGN_STATUS_PRE_OFFLINE_BUDGET = 7;
    const CAMPAIGN_STATUS_WAIT_AUDIT = 8;
    const CAMPAIGN_STATUS_SOURCE_MATERIAL_ERROR = 9;


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
        ['id' => self::CAMPAIGN_STATUS_DELIVERY_OK, 'name' => '推广中'],
        ['id' => self::CAMPAIGN_STATUS_DISABLE, 'name' => '推广暂停'],
        ['id' => self::CAMPAIGN_STATUS_BUDGET_EXCEED, 'name' => '推广计划预算不足'],
        ['id' => self::CAMPAIGN_STATUS_NO_SCHEDULE, 'name' => '不在推广周期'],
        ['id' => self::CAMPAIGN_STATUS_AUDIT, 'name' => '审核中'],
        ['id' => self::CAMPAIGN_STATUS_AUDIT_DENY, 'name' => '审核拒绝'],
        ['id' => self::CAMPAIGN_STATUS_STYLE_OFFLINE, 'name' => '样式下线'],
        ['id' => self::CAMPAIGN_STATUS_PRE_OFFLINE_BUDGET, 'name' => '预算即将不足'],
        ['id' => self::CAMPAIGN_STATUS_WAIT_AUDIT, 'name' => '待审核'],
        ['id' => self::CAMPAIGN_STATUS_SOURCE_MATERIAL_ERROR, 'name' => '关联素材异常'],

    ];
}
