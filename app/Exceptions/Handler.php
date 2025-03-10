<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AccessDeniedHttpException ||
            $exception instanceof AuthorizationException) {
            
            \Log::error('403 error: ' . $exception->getMessage() . ' - Current user: ' . 
                (auth()->check() ? auth()->user()->email : 'Not logged in') . 
                ' - URL: ' . $request->fullUrl());
        }
        
        return parent::render($request, $exception);
    }
}