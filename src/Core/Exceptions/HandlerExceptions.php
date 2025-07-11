<?php

namespace VnCoder\Core\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HandlerExceptions extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return response(view("core::page.page-404"), 404);
        }
        return parent::render($request, $e);
    }
}
