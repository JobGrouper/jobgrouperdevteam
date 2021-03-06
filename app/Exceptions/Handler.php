<?php

namespace App\Exceptions;

use Exception;
use Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        ValidationException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        //return parent::report($e);
	Log::error('Error: ' . $e->getMessage() . ' :: ' . $e->getFile() . ' (' . $e->getLine() . ')');
	Log::error('Trace: ' . $e->getTraceAsString());
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
	$render_500 = true;

	// CASES WHERE WE DO NOT WANT 500 ERROR THROWN
        if(env('APP_DEBUG')) {
	   $render_500 = false;
        }

	if ($this->isHttpException($e)) {
	  $render_500 = false;
	}

	if ($e instanceof ValidationException) {
	  $render_500 = false;
	}

	if ($e instanceof AuthorizationException) {
	  $render_500 = false;
	}

	if ($e instanceof ModelNotFoundException) {
	  $render_500 = false;
	}

	// Render 500 page
	if ($render_500) { 
	  $e = new \Symfony\Component\HttpKernel\Exception\HttpException(500);
	}

        return parent::render($request, $e);
        //return response()->json(['message' => $e->getMessage()]);
    }
}
