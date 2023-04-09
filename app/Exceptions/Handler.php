<?php

namespace App\Exceptions;

use Throwable;
use App\Ultainfinity\Ultainfinity;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    use Ultainfinity;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (request()->expectsJson() && app()->environment('production')) {

            if ($exception instanceof ModelNotFoundException) {
                return $this->AppResponse('failed', 'Data not found', 400);
            }

            if ($exception instanceof ApiErrorException) {
                return $this->AppResponse('failed', 'Something went wrong trying to connect to Stripe');
            }

            if ($exception instanceof NotFoundHttpException) {
                return $this->AppResponse('failed', 'Oh you hit the wrong route', 404);
            }

            if ($exception instanceof HttpException) {
                return $this->AppResponse('failed', $exception->getMessage(), $exception->getStatusCode());
            }

            if (
                $exception instanceof TypeError ||
                $exception instanceof QueryException ||
                $exception instanceof InvalidArgumentException ||
                $exception instanceof FatalError ||
                $exception instanceof ErrorException
            ) {
                return $this->AppResponse('failed', 'Something is not just right, please check your data', 400);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->AppResponse('failed', 'Oh maybe you hit the wrong http method', 405);
            }

            if ($exception instanceof AuthenticationException) {
                return $this->AppResponse('failed', 'Not authenticated', 401);
            }

            // if ($exception instanceof ValidationException) {
            //     return $this->AppError('failed', array_values($exception->errors())[0][0], 400);
            // }

            // return $this->AppException($exception);
        }

        return parent::render($request, $exception);
    }
}
