<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Http\Request;

class BaseController
{
    protected LaravelDebugbar $debugbar;

    public function __construct(Request $request, LaravelDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;

        if ($request->hasSession()) {
            $request->session()->reflash();
        }
    }
}
