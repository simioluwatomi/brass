<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait JsonExceptionHandlerTrait
{
    /**
     * Returns JSON response for model not found exception.
     *
     * @param Throwable $exception
     *
     * @throws \ReflectionException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound(Throwable $exception)
    {
        /** @var ModelNotFoundException $exception */
        $model = new ReflectionClass($exception->getModel());

        return $this->respondWithError(
            $model->getShortName().' not found.',
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Returns JSON response for Eloquent model not found exception.
     *
     * @param Throwable $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function httpClientError(Throwable $exception)
    {
        return $this->respondWithError(
            "{$exception->getMessage()}",
            $exception->getCode()
        );
    }

    /**
     * Returns JSON response for Eloquent model not found exception.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function routeNotFound()
    {
        return $this->respondWithError(
            'The requested URI is invalid.',
            Response::HTTP_NOT_FOUND
        );
    }

    protected function validationError(Throwable $exception)
    {
        /* @var $exception ValidationException */
        return $this->respondWithError(
            $exception->validator->errors()->first(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    protected function authorizationError(Throwable $exception)
    {
        return $this->respondWithError(
            $exception->getMessage(),
            Response::HTTP_FORBIDDEN
        );
    }

    protected function unauthenticatedError(Throwable $exception)
    {
        return $this->respondWithError(
            $exception->getMessage(),
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Returns JSON response for idempotent header exception.
     *
     * @param Throwable $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function idempotentHeaderError(Throwable $exception)
    {
        return $this->respondWithError(
            "{$exception->getMessage()}",
            $exception->getCode()
        );
    }

    /**
     * Returns JSON response for invalid query exception.
     *
     * @param Throwable $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidSpatieQueryError(Throwable $exception)
    {
        return $this->respondWithError(
            "{$exception->getMessage()}",
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Returns json response error.
     *
     * @param       $message
     * @param mixed $statusCode
     * @param mixed $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message, $statusCode, $headers = [])
    {
        return response()->json([
            'status'      => 'fail',
            'status_code' => $statusCode,
            'error'       => [
                'message' => $message,
            ],
        ], $statusCode, $headers);
    }

    protected function throttleRequestsError(Throwable $exception)
    {
        return $this->respondWithError(
            "{$exception->getMessage()}",
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }
}
