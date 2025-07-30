<?php

namespace VnCoder\Backend\Controllers;
use Illuminate\Http\Request;
use VnCoder\Models\RunConsole;

class ConsoleController extends BackendController
{

    public function Index_Action(){
        return redirect(backend('core-console/run'));
    }
    public function Run_Action(){
        $this->metaData->title = 'Run Console';
        $this->setData['isConsoleRunning'] = RunConsole::isConsoleRunning();
        $this->setData['listCommand'] =  $this->getListCommand();
        $currentCommand = RunConsole::getCommand();
        $this->setData['currentCommand'] = 'php artisan run ' . ($currentCommand['controller'] ?? '') . ' ' . ($currentCommand['action'] ?? '');
        return $this->views('admin.console');
    }

    public function Run_Action_Submit(Request $request){
        $command = trim($request->input('command', ''));
        if($command && !RunConsole::isConsoleRunning()){
            @list($controller, $action) = explode(' ', $command, 2);
            if($controller) RunConsole::sendCommand($controller, $action);
        }
        return redirect(backend('core-console/run'));
    }

    public function Run_Data_Action(){
        $isConsoleRunning = RunConsole::isConsoleRunning();
        if(!$isConsoleRunning){
            RunConsole::removeCommand();
            return $this->toJsonData('');
        }
        $message = RunConsole::getMessage();
        return $message ? $this->toJsonData($message) : $this->toJsonError('Đang chạy lệnh, vui lòng đợi trong giây lát!');
    }

    protected function getListCommand(){
        $commands = [];
        foreach (glob(COMMAND_PATH . '*Command.php') as $filePath) {
            $fileName = basename($filePath, '.php');
            $controller = toKebabCase(str_replace('Command', '', $fileName));
            $content = get_contents($filePath);
            if (preg_match_all('/public function (\w+)_Action\s*\(/', $content, $matches)) {
                foreach ($matches[1] as $actionRaw) {
                    $action = ($actionRaw === 'index') ? '' : toKebabCase($actionRaw);
                    $commands[] = trim($controller . ' ' . $action);
                }
            }
        }
        return $commands;
    }

}