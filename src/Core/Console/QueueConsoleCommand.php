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
        $consoleCommand = RunConsole::getCommandData();
        $controller = $consoleCommand['controller'] ?? '';
        $action = $consoleCommand['action'] ?? '';
        $active = $consoleCommand && isset($consoleCommand['active']) ? $consoleCommand['active'] : false;
        $artisanCommand = $action ?  'run ' . $controller . ' ' . $action . ' --queue' : 'run ' . $controller . ' --queue';
        logData('console', "Call command 'php artisan run {$artisanCommand}'");
        if($controller && !$active){
            RunConsole::activeCommand();
            try {
                Artisan::call($artisanCommand);
                logData('console', "Command 'php artisan run {$artisanCommand}' executed successfully.");
            } catch (\Exception $e) {
                logData('console', "Error running command '{$consoleCommand}': " . $e->getMessage());
            } finally {
                RunConsole::clearConsole();
            }
        }else{
            logData('console', "Command 'php artisan run {$artisanCommand}' is already running. Skipped.");
        }

    }
}