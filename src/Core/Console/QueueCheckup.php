<?php

namespace VnCoder\Core\Console;

use Illuminate\Console\Command;

class QueueCheckup extends Command
{
    protected $signature = 'queue:checkup';
    protected $description = 'Ensure that the queue listener is running.';
    protected string $pidQueue = 'framework/cache/queue.pid';

    public function handle()
    {
        if (! $this->isQueueListenerRunning()) {
            $this->comment('Queue listener is being started...');
            $pid = $this->startQueueListener();
            $this->saveQueueListenerPID($pid);
        }
        $this->comment('Queue listener is running...');
    }

    private function isQueueListenerRunning()
    {
        if (! $pid = $this->getLastQueueListenerPID()) {
            return false;
        }
        $process = exec("ps -p $pid -opid=,cmd=");
        return ! empty($process);
    }

    private function getLastQueueListenerPID()
    {
        if (! file_exists(storage_path($this->pidQueue))) {
            return false;
        }
        return file_get_contents(storage_path($this->pidQueue));
    }

    private function saveQueueListenerPID($pid)
    {
        file_put_contents(storage_path($this->pidQueue), $pid);
    }

    private function startQueueListener()
    {
        return exec('php ' . base_path() . '/artisan queue:work --timeout=60 --sleep=5 --tries=3 > /dev/null & echo $!');
    }
}
