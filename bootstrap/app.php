<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/* Creates separate file for errors */
$app->configureMonologUsing(function ($monolog) {
    $formatter = new \Monolog\Formatter\LineFormatter(null, null, true, true);

    $info_stream = new \Monolog\Handler\StreamHandler( realpath(__DIR__.'/../') . '/storage/logs/laravel.log', \Monolog\Logger::INFO, false);
    $info_stream->setFormatter($formatter);
    $monolog->pushHandler($info_stream);

    $debug_stream = new \Monolog\Handler\StreamHandler( realpath(__DIR__.'/../') . '/storage/logs/laravel.log', \Monolog\Logger::DEBUG, false);
    $debug_stream->setFormatter($formatter);
    $monolog->pushHandler($debug_stream);

    $error_stream = new \Monolog\Handler\StreamHandler( realpath(__DIR__.'/../') . '/storage/logs/error.log', \Monolog\Logger::ERROR, false);
    $error_stream->setFormatter($formatter);
    $monolog->pushHandler($error_stream);

});

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
