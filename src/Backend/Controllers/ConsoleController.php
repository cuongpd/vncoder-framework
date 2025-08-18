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
        if(isset($currentCommand['controller'])){
            $this->setData['isConsoleRunning'] = true;
            $this->setData['currentCommand'] = 'php artisan run ' . $currentCommand['controller'] . ' ' . ($currentCommand['action'] ?? '');
        }else{
            $this->setData['isConsoleRunning'] = false;
            $this->setData['currentCommand'] = '';
        }
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
        if(isset($currentCommand['controller'])){
            if($currentCommand['active'] ?? false){
                $messageData = RunConsole::getMessage(false);
                return response()->json($messageData);
            }else{
                return response()->json(['message' => 'Lệnh đang trong hàng đợi, vui lòng đợi trong giây lát!...', 'status' => 0]);
            }
        }else{
            $messageData = RunConsole::getMessage(true);
            return response()->json($messageData);
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