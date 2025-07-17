<?php

namespace VnCoder\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class LaravelCollector extends DataCollector implements Renderable
{

    public function collect()
    {
        return [
            "version" => app()->version()
        ];
    }

    public function getName()
    {
        return 'laravel';
    }

    public function getWidgets()
    {
        return [
            "version" => [
                "icon" => "github",
                "tooltip" => "Laravel Version",
                "map" => "laravel.version",
                "default" => ""
            ],
        ];
    }
}
