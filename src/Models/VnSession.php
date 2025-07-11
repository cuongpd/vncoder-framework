<?php

namespace VnCoder\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class VnSession
{
    protected array $attributes = [];

    public function start()
    {
        if (! $this->has('__token')) {
            $this->regenerateToken();
        }
    }

    public function regenerateToken()
    {
        $this->put('__token', Str::random(40));
    }

    public function put($key, $value = null)
    {
        if (! is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            Arr::set($this->attributes, $arrayKey, $arrayValue);
        }
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    public function has($key)
    {
        return ! collect(is_array($key) ? $key : func_get_args())->contains(function ($key) {
            return is_null($this->get($key));
        });
    }
}
