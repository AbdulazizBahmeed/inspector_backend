<?php

namespace App\Exceptions;

use BadMethodCallException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
// use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
        if (!config('app.debug')) {
            $this->renderable(function (NotFoundHttpException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'the URL is wrong',
                    ], 404);
                }
            });
            $this->renderable(function (AuthenticationException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'the user is unauthenticated',
                    ], 401);
                }
            });

            $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'the request method is wrong',
                    ], 405);
                }
            });


            $this->renderable(function (Exception $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'some error happened in the server please try again later',
                    ], 500);
                }
            });
        }
    }
}
