<?php

namespace App\Console;

use App\Common\Console\ConvertCallbackCommand;
use App\Common\Console\Queue\QueueClickCommand;
use App\Common\Helpers\Functions;
use App\Console\Commands\SyncChannelCampaignCommand;
use App\Console\Commands\Uc\Report\UcSyncAccountReportCommand;
use App\Console\Commands\Uc\Report\UcSyncCreativeReportCommand;
use App\Console\Commands\Uc\UcSyncCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UcSyncCommand::class,
        SyncChannelCampaignCommand::class,

        // 队列
        QueueClickCommand::class,
        // 转化回传
        ConvertCallbackCommand::class,
        // UC
        UcSyncAccountReportCommand::class,
        UcSyncCreativeReportCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 队列
        $schedule->command('queue:click')->cron('* * * * *');

        // 同步渠道-推广计划
        $schedule->command('sync_channel_campaign --date=today')->cron('*/2 * * * *');

        // 转化上报
        $schedule->command('convert_callback')->cron('* * * * *');

        //同步任务
        $schedule->command('uc:sync --type=adgroup')->cron('*/20 * * * *');
        $schedule->command('uc:sync --type=campaign')->cron('*/20 * * * *');
        $schedule->command('uc:sync --type=creative')->cron('*/20 * * * *');

        // 正式
        if(Functions::isProduction()){

            // UC账户报表同步
            $schedule->command('uc:sync_account_report --date=today --has_history_cost=1 --key_suffix=has_history_cost')->cron('*/2 * * * *');
            $schedule->command('uc:sync_account_report --date=today')->cron('15 * * * *');
            $schedule->command('uc:sync_account_report --date=yesterday --key_suffix=yesterday')->cron('25-30 10 * * *');

            // 巨量创意报表同步
            $schedule->command('uc:sync_creative_report --date=today --run_by_account_cost=1')->cron('*/2 * * * *');
            $schedule->command('uc:sync_creative_report --date=yesterday --key_suffix=yesterday')->cron('10-15 9,14 * * *');

        }
    }
}
