<?php

namespace App\Exceptions;

use App\Util\Logger\Logger;
use App\Util\Logger\Type\LogLevel;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use PhpTypes\Exception\ConstraintException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DomainException;

class Handler extends ExceptionHandler
{
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render the caught exception.
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        $rendered = parent::render($request, $exception);

        $responseData = [
            'error' => [
                'code' => $rendered->getStatusCode(),
                'message' => $exception->getMessage()
            ]
        ];

        if ($exception instanceof ConstraintException) {
            $responseData['error']['code'] = Response::HTTP_BAD_REQUEST;
        } else if ($exception instanceof DomainException) {
            $responseData['error']['code'] = Response::HTTP_BAD_REQUEST;
        } else if ($exception instanceof ValidationException) {
            $responseData['error']['message'] = 'Validation Failure';
            $responseData['error']['errors'] = $exception->errors();
        }

        Logger::log(LogLevel::ERROR, "Exception caught by handler.  " .
            "Code: {$responseData['error']['code']}, Message: {$responseData['error']['message']}");

        return response()->json($responseData, $responseData['error']['code']);
    }
}
