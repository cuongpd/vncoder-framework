<?php

namespace VnCoder\Backend\Controllers;

use VnCoder\Backend\Controllers\BackendController;
use VnCoder\Backend\Models\VnLogs;

class LogsController extends BackendController
{
    protected VnLogs $logs;
    protected string $logsUrl = '';

    public function __construct()
    {
        parent::__construct();
        $this->logs = new VnLogs();
        $this->logsUrl =  backend('core-logs');
    }

    public function Index_Action()
    {
        $this->metaData->title = 'Xem lỗi ghi nhận trên hệ thống';
        $folderFiles = [];
        if (getParam('f')) {
            $this->logs->setFolder(decrypt(getParam('f')));
            $folderFiles = $this->logs->getFolderFiles(true);
        }
        if (getParam('l')) {
            $this->logs->setFile(decrypt(getParam('l')));
        }
        if ($early_return = $this->earlyReturn()) {
            return $early_return;
        }

        $logs = $this->logs->all();
        $this->setData['folders'] = $this->logs->getFolders();
        $this->setData['current_folder'] = $this->logs->getFolderName();
        $this->setData['folder_files'] = $folderFiles;
        $this->setData['files'] = $this->logs->getFiles(true);

        $current_file = $this->logs->getFileName();
        $this->setData['current_file'] = $current_file;
        $this->setData['standardFormat'] = true;

        if (is_array($logs)) {
            $firstLog = reset($logs);
            if ($firstLog && !$firstLog['context']) {
                $this->setData['standardFormat'] = false;
            }
        }

        $this->setData['logs'] = $logs;
        $this->setData['logs_url'] = $this->logsUrl;
        $this->setData['logs_data'] = encrypt($current_file);

        $this->initDataTable();
        $this->header('<style>.stack-info{font-size:9px;text-align: initial} .td-top{vertical-align:top !important;}</style>');
        return $this->views('admin.logs');
    }

    private function earlyReturn()
    {
        if (getParam('f')) {
            $this->logs->setFolder(decrypt(getParam('f')));
        }
        if (getParam('dl')) {
            return $this->download($this->pathFromInput('dl'));
        } elseif (getParam('clean')) {
            flash_message('Bạn đã dọn dẹp xong Logs hệ thống!', 'info');
            app('files')->put($this->pathFromInput('clean'), '');
            return $this->redirect($this->logsUrl);
        } elseif (getParam('del')) {
            flash_message('Bạn đã xóa thành công toàn bộ Logs hệ thống!', 'info');
            app('files')->delete($this->pathFromInput('del'));
            return $this->redirect($this->logsUrl);
        } elseif (getParam('reset')) {
            $files = ($this->logs->getFolderName()) ? $this->logs->getFolderFiles(true) : $this->logs->getFiles(true);
            foreach ($files as $file) {
                try {
                    app('files')->delete($this->logs->pathToLogFile($file));
                } catch (\Exception $e) {
                }
            }
            flash_message('Dữ liệu Logs hệ thống đã bị reset!', 'info');
            return $this->redirect($this->logsUrl);
        }
        return false;
    }

    private function pathFromInput($input_string): string
    {
        try {
            return $this->logs->pathToLogFile(decrypt(getParam($input_string)));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }
        return app('redirect')->to($to);
    }

    private function download($data)
    {
        return response()->download($data);
    }
}
