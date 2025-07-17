<?php

namespace VnCoder\Backend\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleController extends BackendController
{

    public function Index_Action(){
        return redirect(backend('core-console/run'));
    }
    public function Run_Action(){
        $this->metaData->title = 'Run Console';
        $this->setData['listCommand'] = $this->getListCommand();
        return $this->views('admin.console');
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