<?php

use App\Jobs\DeleteExpiredTokensJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonAccept;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};

return Application::configure(basePath: dirname(__DIR__))->withRouting(
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('api')->group(base_path('routes/api.php'));
        Route::middleware('api', 'auth:api')->group(base_path('routes/auth.php'));
    }
)->withMiddleware(function (Middleware $middleware) {
    $middleware->append([
        ForceJsonAccept::class,
    ]);
})->withExceptions(function (Exceptions $exceptions) {
    //
})->withSchedule(function (Schedule $schedule) {
    $schedule->job(new DeleteExpiredTokensJob())->everyFiveMinutes();
})->create();
