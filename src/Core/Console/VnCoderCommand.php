<?php

namespace VnCoder\Core\Console;

use Illuminate\Console\Command;
use JetBrains\PhpStorm\NoReturn;

class VnCoderCommand extends Command
{
    protected $signature = 'run {controller} {action?} {--queue}';
    protected $description = "Lumen Command : php artisan run {run-command} {action?}";

    #[NoReturn]
    public function handle()
    {
        if(php_sapi_name() === 'cli' || defined('STDIN')){
            if (PHP_OS_FAMILY === 'Windows') {
                system('cls');
            } else {
                system('clear');
            }
        }
        $controller = $this->argument('controller');
        if (!$controller) {
            $this->error("Error: Permission denied");
            exit();
        }
        $action = $this->argument('action');
        $command = $controller . ($action ? '__' . $action : '');
        $runInQueue = $this->option('queue');

        if (preg_match('/^\d/', $controller)) {
            $controller = 'N'. $controller;
        }
        if (preg_match('/^\d/', $action)) {
            $action = 'N'. $action;
        }

        $controller = str_replace('-', ' ', $controller);
        $controllerName = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller))) . 'Command';

        if(!$action){
            $actionName = "Index_Action";
        }else{
            $action = str_replace('-', ' ', $action);
            $actionName = str_replace(' ', '_', ucwords(str_replace('-', ' ', $action))) . '_Action';
        }

        $commandController = 'App\\Admin\\Command\\' . $controllerName;

        if (class_exists($commandController)) {
            $commandClass = app()->make($commandController);
            if (!method_exists($commandClass, 'VnCommandInit')) {
                $this->error("Please extends \VnCommand to call : $commandController");
            }else{
                if (!method_exists($commandClass, $actionName)) {
                    $this->error("Method $actionName not active in class $commandController");
                }else{
                    $commandClass->command = $command;
                    if($runInQueue){
                        $commandClass->runInQueue();
                    }
                    $commandClass->$actionName();
                    $commandClass->saveCommandLog();
                }
            }
        }else{
            $this->error("Command class $commandController not found.");
            // Create Command
            $command_file = COMMAND_PATH. $controllerName. '.php';
            $command_code = <<<EOF
<?php

namespace App\Admin\Command;
use VnCoder\Controllers\VnCommand;

class __CONTROLLER__ extends VnCommand{

    public function __METHOD__()
    {

    }
}

EOF;
            if (!file_exists($command_file)) {
                $command_code = str_replace(['__CONTROLLER__', '__METHOD__'], [$controllerName, $actionName], $command_code);
                file_put_contents($command_file, $command_code);
                $this->comment("Command created in $command_file");
            } else {
                $this->comment("Please check file $command_file");
            }
            exit();
        }
    }
}
