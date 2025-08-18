<?php

namespace VnCoder\Controllers;

use Illuminate\Support\Facades\Artisan;
use VnCoder\Models\RunConsole;

class VnCommand
{
    protected bool $runInConsole = true;
    protected array $tableKey = [];
    public bool $runInQueue = false;
    public string $command = "";
    protected string $output = "";

    public function __construct()
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        ob_implicit_flush(true);
    }

    public function runInQueue()
    {
        $this->runInQueue = true;
        $this->print("Run command : " . $this->command);
    }

    public function saveCommandLog(){
        if(!$this->runInQueue) return;
        RunConsole::setConsoleLogs($this->output);
    }

    protected function runComand($command){
        $this->print("Running command: $command");
        $runComand = 'run ' . trim($command) . ($this->runInQueue ? ' --queue' : '');
        Artisan::call($runComand);
        $this->sleep(1);
    }

    protected function info(...$message)
    {
        foreach ($message as $msg) {
            $this->print($msg);
        }
    }

    protected function print($data = null)
    {
        if (is_array($data) || is_object($data)) {
            var_dump($data);
            $this->output .= print_r($data, true);
            $this->endLine();
        } else {
            $this->echo($data);
        }
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }

    protected function printTable($data = [], $key = "table-data")
    {
        if (empty($data) ) {
            return;
        }
        if (!isset($this->tableKey[$key])) {
            $this->tableKey[$key] = $key;
            $keys = array_keys($data);
            $capitalizedKeys = array_map(function($key) {
                return ucfirst($key);
            }, $keys);
            $this->echo(implode("\t", $capitalizedKeys));
            $this->endLine();
        }
        $this->echo(implode("\t", array_values($data)));
    }

    protected function echo($info)
    {
        echo $info. "\n";
        $this->output .= $info . "\n";
    }

    protected function endLine()
    {
        echo "\n";
    }

    protected function sleep($time = 5){
        sleep($time);
    }

    public function Index_Action()
    {
        $this->print('OK');
    }

    protected function exec($command)
    {
        $output = exec($command);
        $this->print($output);
    }

    protected function showNotify($message = '')
    {
        $this->exec("osascript -e 'display notification \"$message\" with title \"VnCoder Notify\"'");
    }

    protected function separator(){
        echo str_repeat('=', 80) . "\n";
    }

    protected function get($url, $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    protected function post($url, $data = [], $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    protected function VnCommandInit(){}

}