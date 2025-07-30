<?php

namespace VnCoder\Models;

define('CONSOLE_PATH_FILE', STORAGE_PATH . 'logs/console.json');

class RunConsole
{

    public static function sendCommand($controller, $action = 'index')
    {
        self::setCommandData($controller, $action, false );
        self::deleteLogCommand($controller . '__' . $action);
    }

    public static function removeCommand(){
        return VnConfig::where('type', 'console')->where('name', 'console')->delete();
    }

    public static function getCommandData()
    {
        $data = VnConfig::where('type', 'console')->where('name', 'console')->first();
        if($data){
            return json_decode($data->data, true);
        }
        return [];
    }

    public static function setCommandActive(){
        $data = self::getCommandData();
        if($data && isset($data['controller']) && isset($data['action'])) {
            self::setCommandData($data['controller'], $data['action'], true );
        }
    }

    public static function getMessage()
    {
        $command = self::getCommandData();
        $commandKey = $command['controller'] . '__' . $command['action'];
        $message = self::getLogCommand($commandKey);
        if($message){
            self::removeCommand();
            self::deleteLogCommand($commandKey);
        }
        return $message;
    }

    public static function setData($data){
        return VnConfig::updateOrCreate(['type' => 'console', 'name' => 'console'], ['type' => 'console', 'name' => 'console', 'data' => json_encode($data)]);
    }

    public static function getData(){
        $data = VnConfig::where('type', 'console')->where('name', 'console')->first();
        if($data){
            return json_decode($data->data, true);
        }
        return [];
    }

    public static function removeData(){
        return VnConfig::where('type', 'console')->where('name', 'console')->delete();
    }

    public static function logCommand($command, $message){
        $key = 'console-data-'. $command;
        VnConfig::updateOrCreate(['type' => 'console', 'name' => $key], ['type' => 'console', 'name' => $key, 'data' => $message]);
    }

    public static function deleteLogCommand($command){
        $key = 'console-data-'. $command;
        return VnConfig::where('type', 'console')->where('name', $key)->delete();
    }

    public static function getLogCommand($command){
        $key = 'console-data-'. $command;
        $data = VnConfig::where('type', 'console')->where('name', $key)->first();
        if($data){
            return $data->data;
        }
        return '';
    }


    public static function setCommandData($controller, $action, $status = false){
        $data = [
            'controller' => $controller,
            'action' => $action,
            'time' => time(),
            'active' => $status
        ];
        return VnConfig::updateOrCreate(['type' => 'console', 'name' => 'console'], ['type' => 'console', 'name' => 'console', 'data' => json_encode($data)]);
    }






}
