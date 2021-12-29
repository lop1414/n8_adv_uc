<?php

namespace App\Console;

use App\Common\Console\Queue\QueueClickCommand;
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

        // 队列
        QueueClickCommand::class
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

        // 百度同步任务
        $schedule->command('uc:sync --type=adgroup')->cron('*/20 * * * *');
        $schedule->command('uc:sync --type=campaign')->cron('*/20 * * * *');
        $schedule->command('uc:sync --type=creative_template')->cron('*/20 * * * *');
        $schedule->command('uc:sync --type=creative')->cron('*/20 * * * *');
    }
}
