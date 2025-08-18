<?php

namespace VnCoder\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

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
        $process = new Process(['ps', '-p', $pid]);
        $process->run();
        return $process->isSuccessful() && str_contains($process->getOutput(), (string) $pid);
    }

    private function getLastQueueListenerPID()
    {
        if (! file_exists(storage_path($this->pidQueue))) {
            return false;
        }
        return get_contents(storage_path($this->pidQueue));
    }

    private function saveQueueListenerPID($pid)
    {
        put_contents(storage_path($this->pidQueue), $pid);
    }

    private function startQueueListener()
    {
        return exec('php ' . base_path() . '/artisan queue:work --timeout=60 --sleep=5 --tries=3 > /dev/null & echo $!');
    }
}
