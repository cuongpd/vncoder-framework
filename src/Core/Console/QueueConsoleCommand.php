<?php
namespace VnCoder\Core\Console;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use VnCoder\Models\RunConsole;

class QueueConsoleCommand extends Command
{
    protected $signature = 'queue:console';
    protected $description = "Automatically run commands when the admin console is accessed";

    public function handle()
    {
        $consoleCommand = RunConsole::getCommand();
        if ($consoleCommand) {
            logData('console', "Call command 'php artisan run {$consoleCommand}'");
            if(RunConsole::checkRunning($consoleCommand)){
                logData('console', "Command 'php artisan run {$consoleCommand}' is already running. Skipped.");
                return;
            }else{
                RunConsole::setRunning($consoleCommand);
                try {
                    $artisanCommand = 'run ' . $consoleCommand;
                    Artisan::call($artisanCommand);
                    RunConsole::clearRunning($consoleCommand);
                } catch (\Exception $e) {
                    logData('console', "Error running command '{$consoleCommand}': " . $e->getMessage());
                    RunConsole::clearRunning($consoleCommand);
                } finally {
                    RunConsole::clearRunning($consoleCommand);
                }
            }

        }

    }
}