<?php

namespace App\Exceptions;

use App\Traits\JsonExceptionHandlerTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Spatie\QueryBuilder\Exceptions\InvalidQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use JsonExceptionHandlerTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            if ($e instanceof AuthenticationException) {
                return $this->unauthenticatedError($e);
            }

            if ($e instanceof ModelNotFoundException) {
                return $this->modelNotFound($e);
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->routeNotFound();
            }

            if ($e instanceof RequestException) {
                return $this->httpClientError($e);
            }

            if ($e instanceof AuthorizationException) {
                return $this->authorizationError($e);
            }

            if ($e instanceof ThrottleRequestsException) {
                return $this->throttleRequestsError($e);
            }

            if ($e instanceof InvalidQuery) {
                return $this->invalidSpatieQueryError($e);
            }
        }

        return parent::render($request, $e);
    }
}
