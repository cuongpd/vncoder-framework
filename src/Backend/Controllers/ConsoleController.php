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
        $this->setData['listCommand'] =  $this->getListCommand();
        $currentCommand = RunConsole::getCommandData();
        $this->setData['isConsoleRunning'] = $currentCommand ?? false;
        $this->setData['currentCommand'] = 'php artisan run ' . ($currentCommand['controller'] ?? '') . ' ' . ($currentCommand['action'] ?? '');
        return $this->views('admin.console');
    }

    public function Run_Action_Submit(Request $request){
        $command = trim($request->input('command', ''));
        $currentCommand = RunConsole::getCommandData();
        if($command && !$currentCommand){
            @list($controller, $action) = explode(' ', $command, 2);
            if($controller) RunConsole::sendCommand($controller, $action);
        }
        return redirect(backend('core-console/run'));
    }

    public function Run_Data_Action(){
        $currentCommand = RunConsole::getCommandData();
        if(!$currentCommand){
            RunConsole::removeCommand();
            return $this->toJsonData('');
        }
        $active = $currentCommand['active'] ?? false;
        if($active){
            $message = RunConsole::getMessage();
            if($message){
                if($message == '[__NA__]') $message = 'Đã xử lý xong lệnh!';
                return $this->toJsonData($message);
            }
            return $this->toJsonError('Đang chạy lệnh, vui lòng đợi trong giây lát!...');
        }else{
            return $this->toJsonError('Lệnh đang trong hàng đợi, vui lòng đợi trong giây lát!...');
        }
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