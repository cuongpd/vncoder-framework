<?php

namespace VnCoder\Models;

class RunConsole
{
    public static function sendCommand($controller, $action = 'index')
    {
        $data = self::getCommandData();
        if (!$data) {
            VnConfig::deleteConsoleData();
            $new = [
                'controller' => $controller,
                'action' => $action,
                'active' => false
            ];
            VnConfig::setConsoleData('console', $new);
        }
    }

    public static function activeCommand()
    {
        $data = self::getCommandData();
        if (!$data) {
            return false;
        }
        $data['active'] = true;
        return VnConfig::setConsoleData('console', $data);
    }

    public static function getCommandData()
    {
        return VnConfig::getConsoleData('console');
    }

    public static function setData($data)
    {
        return VnConfig::setConsoleData('console', $data);
    }

    public static function deleteData()
    {
        return VnConfig::deleteConsoleData();
    }

    public static function getData()
    {
        return self::getCommandData();
    }

    public static function setCommandRuntime($message)
    {
        return VnConfig::setConsoleData('console-runtime', $message, false);
    }

    public static function getCommandRuntime()
    {
        return VnConfig::getConsoleData('console-runtime', false);
    }

    public static function removeCommand()
    {
        self::deleteConfigData('console');
        self::deleteConfigData('console-runtime');
    }

    public static function setCommandLog($message)
    {
        self::setCommandRuntime($message);
        $log = self::getCommandLog();
        $newLog = $log ? ($log . "\n" . $message) : $message;
        return VnConfig::setConsoleData('console-log', $newLog, false);
    }

    public static function getCommandLog()
    {
        return VnConfig::getConsoleData('console-log', false);
    }

    public static function deleteCommandLog()
    {
        return self::deleteConfigData('console-log');
    }

    public static function getMessage($finish = false)
    {
        if ($finish) {
            $message = self::getCommandLog();
            VnConfig::deleteConsoleData();
            return ['status' => 1, 'message' => $message];
        }

        $message = self::getCommandRuntime() ?: 'Đang xử lý tác vụ...';
        return ['status' => 0, 'message' => $message];
    }

    protected static function deleteConfigData($name)
    {
        return VnConfig::deleteConsole($name);
    }
}
