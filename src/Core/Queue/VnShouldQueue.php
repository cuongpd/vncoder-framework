<?php

namespace VnCoder\Core\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VnShouldQueue implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

}