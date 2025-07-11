<?php

namespace VnCoder\Core\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class QueueSchedule extends ConsoleKernel
{
    protected $commands = [];

    public function schedule(Schedule $schedule)
    {
        $schedule->command('queue:checkup')->everyTwoMinutes();
        $schedulePath = ADMIN_PATH . 'schedule.php';
        if(file_exists($schedulePath)){
            require $schedulePath;
        }
    }
}
