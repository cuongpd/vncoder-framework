<?php

namespace VnCoder\Core\Traits;
use Illuminate\Support\Str;

/**
 *
 * @method static \Illuminate\Database\Eloquent\Model|static creating(\Illuminate\Events\QueuedClosure|callable|array|string $callback) Register a creating model event with the dispatcher.
 */

trait CreateUuidTrait
{
    public static function bootCreateUuidTrait(): void
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

}