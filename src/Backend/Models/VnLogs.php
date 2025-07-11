<?php

namespace VnCoder\Backend\Models;

class VnLogs
{
    protected string $file = "";
    protected string $folder = "";

    private string | array $storage_path;
    public const MAX_FILE_SIZE = 52428800;
    private Level $level;
    private Pattern $pattern;

    public function __construct()
    {
        $this->level = new Level();
        $this->pattern = new Pattern();
        $this->storage_path = storage_path('logs');
    }

    public function setFolder(string $folder)
    {
        if (app('files')->exists($folder)) {
            $this->folder = $folder;
        }
        if ($this->storage_path) {
            $logsPath = $this->storage_path . '/' . $folder;
            if (app('files')->exists($logsPath)) {
                $this->folder = $folder;
            }
        }
    }

    public function setFile(string $file)
    {
        try {
            $file = $this->pathToLogFile($file);
        } catch (\Exception $e) {
            logData('vnlogs', $e->getMessage());
        }

        if (app('files')->exists($file)) {
            $this->file = $file;
        }
    }

    public function pathToLogFile(string $file)
    {
        if (app('files')->exists($file)) {
            return $file;
        }
        if (is_array($this->storage_path)) {
            foreach ($this->storage_path as $folder) {
                if (app('files')->exists($folder . '/' . $file)) {
                    $file = $folder . '/' . $file;
                    break;
                }
            }
            return $file;
        }

        $logsPath = $this->storage_path;
        $logsPath .= ($this->folder) ? '/' . $this->folder : '';
        $file = $logsPath . '/' . $file;
        if (dirname($file) !== $logsPath) {
            throw new \Exception('No such log file');
        }
        return $file;
    }

    public function getFolderName()
    {
        return $this->folder;
    }

    public function getFileName()
    {
        return basename($this->file);
    }

    public function all()
    {
        $log = array();

        if (!$this->file) {
            $log_file = (!$this->folder) ? $this->getFiles() : $this->getFolderFiles();
            if (!count($log_file)) {
                return [];
            }
            $this->file = $log_file[0];
        }

        if (app('files')->size($this->file) > self::MAX_FILE_SIZE) {
            return null;
        }

        $file = app('files')->get($this->file);

        preg_match_all($this->pattern->getPattern('logs'), $file, $headings);

        if (!is_array($headings)) {
            return $log;
        }

        $log_data = preg_split($this->pattern->getPattern('logs'), $file);

        if ($log_data[0] < 1) {
            array_shift($log_data);
        }

        foreach ($headings as $h) {
            for ($i = 0, $j = count($h); $i < $j; $i++) {
                foreach ($this->level->all() as $level) {
                    if (strpos(strtolower($h[$i]), '.' . $level) || strpos(strtolower($h[$i]), $level . ':')) {
                        preg_match($this->pattern->getPattern('current_log', 0) . $level . $this->pattern->getPattern('current_log', 1), $h[$i], $current);
                        if (!isset($current[4])) {
                            continue;
                        }

                        $log[] = array(
                            'context' => $current[3],
                            'level' => $level,
                            'folder' => $this->folder,
                            'level_class' => $this->level->cssClass($level),
                            'level_img' => $this->level->img($level),
                            'date' => $current[1],
                            'text' => $current[4],
                            'in_file' => $current[5] ?? null,
                            'stack' => preg_replace("/^\n*/", '', $log_data[$i])
                        );
                    }
                }
            }
        }

        if (empty($log)) {
            $lines = explode(PHP_EOL, $file);
            $log = [];

            foreach ($lines as $key => $line) {
                $log[] = [
                    'context' => '',
                    'level' => '',
                    'folder' => '',
                    'level_class' => '',
                    'level_img' => '',
                    'date' => $key + 1,
                    'text' => $line,
                    'in_file' => null,
                    'stack' => '',
                ];
            }
        }

        return array_reverse($log);
    }

    public function getFolders()
    {
        $folders = glob($this->storage_path . '/*', GLOB_ONLYDIR);
        if (is_array($this->storage_path)) {
            foreach ($this->storage_path as $value) {
                $folders = array_merge(
                    $folders,
                    glob($value . '/*', GLOB_ONLYDIR)
                );
            }
        }

        if (is_array($folders)) {
            foreach ($folders as $k => $folder) {
                $folders[$k] = basename($folder);
            }
        }
        return array_values($folders);
    }

    public function getFolderFiles($basename = false)
    {
        return $this->getFiles($basename, $this->folder);
    }

    public function getFiles($basename = false, $folder = '')
    {
        $pattern = '*.log';
        $files = glob(
            $this->storage_path . '/' . $folder . '/' . $pattern,
            preg_match($this->pattern->getPattern('files'), $pattern) ? GLOB_BRACE : 0
        );
        if (is_array($this->storage_path)) {
            foreach ($this->storage_path as $value) {
                $files = array_merge(
                    $files,
                    glob(
                        $value . '/' . $folder . '/' . $pattern,
                        preg_match($this->pattern->getPattern('files'), $pattern) ? GLOB_BRACE : 0
                    )
                );
            }
        }

        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return array_values($files);
    }
}


class Level
{
    private array $levels_classes = [
        'debug' => 'info',
        'info' => 'info',
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'critical' => 'danger',
        'alert' => 'danger',
        'emergency' => 'danger',
        'processed' => 'info',
        'failed' => 'warning',
    ];

    private array $levels_imgs = [
        'debug' => 'info-circle',
        'info' => 'info-circle',
        'notice' => 'info-circle',
        'warning' => 'exclamation-triangle',
        'error' => 'exclamation-triangle',
        'critical' => 'exclamation-triangle',
        'alert' => 'exclamation-triangle',
        'emergency' => 'exclamation-triangle',
        'processed' => 'info-circle',
        'failed' => 'exclamation-triangle'
    ];

    public function all()
    {
        return array_keys($this->levels_imgs);
    }

    public function img($level)
    {
        return $this->levels_imgs[$level];
    }

    public function cssClass($level)
    {
        return $this->levels_classes[$level];
    }
}


class Pattern
{
    private array $patterns = [
        'logs' => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            ': (.*?)( in .*?:[0-9]+)?$/i'
        ],
        'files' => '/\{.*?\,.*?\}/i',
    ];

    public function all()
    {
        return array_keys($this->patterns);
    }

    public function getPattern($pattern, $position = null)
    {
        if ($position !== null) {
            return $this->patterns[$pattern][$position];
        }
        return $this->patterns[$pattern];
    }
}
