<?php

namespace VnCoder\Models;

define('CONSOLE_PATH_FILE', STORAGE_PATH . 'logs/console.json');

class RunConsole
{

    public static function sendCommand($controller, $action = 'index')
    {
        $data = [
            'controller' => $controller,
            'action' => $action,
            'time' => time(),
            'active' => false
        ];
        put_contents(CONSOLE_PATH_FILE, json_encode($data));
        self::setMessage($controller . '__' . $action, '');
    }

    public static function removeCommand(){
        if(is_file(CONSOLE_PATH_FILE)){
            @unlink(CONSOLE_PATH_FILE);
        }

    }

    public static function getCommand()
    {
        return json_content(CONSOLE_PATH_FILE);
    }

    public static function setCommandActive(){
        $data = self::getCommand();
        if ($data) {
            $data['active'] = true;
            put_contents(CONSOLE_PATH_FILE, json_encode($data));
        }
    }

    public static function isConsoleRunning()
    {
        $data = json_content(CONSOLE_PATH_FILE);
        return !empty($data) && isset($data['controller']) && isset($data['action']);
    }

    public static function getMessage()
    {
        $command = self::getCommand();
        $commandLog = storage_path('logs/console-'.$command['controller'].'__'.$command['action'].'.log');
        $message = get_contents($commandLog);
        if($message){
            self::removeCommand();
            unlink($commandLog);
        }
        return $message;
    }

    public static function setMessage($command, $message)
    {
        $filePath = storage_path('logs/console-'.$command.'.log');
        put_contents($filePath, $message);
    }


}
