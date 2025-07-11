<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Container\Container;

class FilesCollector extends DataCollector implements Renderable
{
    protected Container $app;
    protected string $basePath;

    public function __construct(Container $app = null)
    {
        $this->app = $app;
        $this->basePath = base_path();
    }

    public function collect()
    {
        $files = $this->getIncludedFiles();
        $compiled = $this->getCompiledFiles();

        $included = [];
        $alreadyCompiled = [];
        foreach ($files as $file) {
            if (str_contains($file, 'vendor/vncoder/framework/debugbar') || str_contains($file, 'vendor/php-debugbar/php-debugbar/src')) {
                continue;
            } elseif (!in_array($file, $compiled)) {
                $included[] = ['message' => "'" . $this->stripBasePath($file) . "',",'is_string' => true];
            } else {
                $alreadyCompiled[] = ['message' => "* '" . $this->stripBasePath($file) . "',", 'is_string' => true];
            }
        }
        $messages = array_merge($included, $alreadyCompiled);

        return [
            'messages' => $messages,
            'count' => count($included),
        ];
    }

    /**
     * Get the files included on load.
     *
     * @return array
     */
    protected function getIncludedFiles()
    {
        return get_included_files();
    }

    /**
     * Get the files that are going to be compiled, so they aren't as important.
     *
     * @return array
     */
    protected function getCompiledFiles()
    {
        if ($this->app && class_exists('Illuminate\Foundation\Console\OptimizeCommand')) {
            $reflector = new \ReflectionClass('Illuminate\Foundation\Console\OptimizeCommand');
            $path = dirname($reflector->getFileName()) . '/Optimize/config.php';

            if (file_exists($path)) {
                $app = $this->app;
                $core = require $path;
                return array_merge($core, $app['config']['compile']);
            }
        }
        return [];
    }

    /**
     * Remove the basePath from the paths, so they are relative to the base
     *
     * @param $path
     * @return string
     */
    protected function stripBasePath($path)
    {
        return ltrim(str_replace($this->basePath, '', $path), '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $name = $this->getName();
        return [
            "$name" => [
                "icon" => "files-o",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.messages",
                "default" => "{}"
            ],
            "$name:badge" => [
                "map" => "$name.count",
                "default" => "null"
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'files';
    }
}
