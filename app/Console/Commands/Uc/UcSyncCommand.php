<?php

namespace App\Console\Commands\Uc;

use App\Common\Console\BaseCommand;
use App\Common\Tools\CustomException;
use App\Services\Uc\UcAdgroupService;
use App\Services\Uc\UcCampaignService;
use App\Services\Uc\UcCreativeService;

class UcSyncCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'uc:sync {--type=} {--account_ids=} {--status=} {--multi_chunk_size=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步UC信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @throws \App\Common\Tools\CustomException
     * 处理
     */
    public function handle(){
        $param = $this->option();

        if(empty($param['type'])){
            throw new CustomException([
                'code' => 'NO_TYPE_PARAM',
                'message' => 'type 必传',
            ]);
        }

        // 账户
        if(!empty($param['account_ids'])){
            $param['account_ids'] = explode(",", $param['account_ids']);
        }

        $service = $this->getServices($param['type']);

        $option = ['log' => true];
        $this->lockRun(
            [$service, 'sync'],
            'uc|sync|'.$param['type'],
            3600 * 3,
            $option,
            $param
        );
    }



    public function getServices($type){
        switch ($type){
            case 'adgroup':
                echo "同步推广组\n";
                $service = new UcAdgroupService();
                break;
            case 'campaign':
                echo "同步推广计划\n";
                $service = new UcCampaignService();
                break;
            case 'creative':
                echo "同步创意\n";
                $service = new UcCreativeService();
                break;
            default:
                throw new CustomException([
                    'code' => 'TYPE_PARAM_INVALID',
                    'message' => 'type 无效',
                ]);
        }
       return $service;
    }
}
