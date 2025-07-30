<?php

namespace VnCoder\Models;

class RunConsole
{
    const COMMAND_CACHE_KEY = 'command_cache_key';
    const COMMAND_CACHE_RUNTIME = 'command_cache_runtime';

    public static function sendCommand($controller, $action = 'index')
    {
        cache(self::COMMAND_CACHE_KEY, $controller . ' ' . $action, 360);
        cache(self::COMMAND_CACHE_RUNTIME, $controller . '__' . $action, 360);
        self::setMessage($controller . '__' . $action, '');
    }

    public static function removeCommand(){
        cache(self::COMMAND_CACHE_KEY, '');
    }

    public static function getCommand()
    {
        return cache(self::COMMAND_CACHE_KEY);
    }

    public static function isConsoleRunning()
    {
        $cache = cache(self::COMMAND_CACHE_KEY);
        return $cache != '';
    }

    public static function getMessage()
    {
        $message = '';
//        if(self::isConsoleRunning()){
//            return $message;
//        }
        $command = cache(self::COMMAND_CACHE_RUNTIME);
        if($command){
            $message = get_contents(storage_path('logs/console-'.md5($command).'.log'));
            if($message){
                cache(self::COMMAND_CACHE_RUNTIME, '');
            }
        }
        return $message;
    }

    public static function setMessage($command, $message)
    {
        $filePath = storage_path('logs/console-'.md5($command).'.log');
        file_put_contents($filePath, $message);
    }

    public static function checkRunning($command)
    {
        $lockKey = 'console_lock_' . md5($command);
        return cache($lockKey, false);
    }

    public static function setRunning($command)
    {
        $lockKey = 'console_lock_' . md5($command);
        cache($lockKey, 'run', 3600); // Lock for 1 hour
    }

    public static function clearRunning($command)
    {
        $lockKey = 'console_lock_' . md5($command);
        cache($lockKey, '', -1);
        self::removeCommand();
    }

}
