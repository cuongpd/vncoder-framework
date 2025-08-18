<?php

namespace VnCoder\Models;

class RunConsole
{
    public static function sendCommand($controller, $action = 'index')
    {
        $actionData = VnConfig::getCommandData();
        if (!$actionData) {
            VnConfig::clearConsoleData();
            $actionData = [
                'controller' => $controller,
                'action' => $action,
                'active' => false
            ];
            VnConfig::setCommandData($actionData);
        }
    }

    public static function activeCommand()
    {
        $actionData = VnConfig::getCommandData();
        if(isset($actionData['controller'])){
            $actionData['active'] = true;
            VnConfig::setCommandData($actionData);
        }
    }

    public static function getCommandData()
    {
        return VnConfig::getCommandData();
    }

    public static function getMessage($finish = false)
    {
        if($finish){
            $message = VnConfig::getConsoleLog('console-log');
            self::clearConsoleData();
            return ['status' => 1, 'message' => $message];
        }else{
            $message = VnConfig::getConsoleLog('console-runtime');
            if (!$message) {
                $message = 'Đang xử lý tác vụ...';
            }
            return ['status' => 0, 'message' => $message];
        }
    }

    public static function setConsoleLogs($message)
    {
        VnConfig::setConsoleLogs($message);
    }

    public static function clearConsole()
    {
        VnConfig::clearConsoleData(false);
    }

    public static function clearConsoleData()
    {
        VnConfig::clearConsoleData(true);
    }

}
