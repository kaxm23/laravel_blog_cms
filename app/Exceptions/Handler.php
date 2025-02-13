<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $this->renderable(function (QueryException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Database error occurred',
                    'error' => app()->environment('local') ? $e->getMessage() : 'Internal Server Error'
                ], 500);
            }
        });
    }
}