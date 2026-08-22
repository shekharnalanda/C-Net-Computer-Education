<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$environmentPath = dirname(__DIR__).'/.env';

if (is_readable($environmentPath)) {
    foreach (file($environmentPath, FILE_IGNORE_NEW_LINES) ?: [] as $environmentLine) {
        $environmentLine = trim($environmentLine);

        if ($environmentLine === '' || str_starts_with($environmentLine, '#') || ! str_contains($environmentLine, '=')) {
            continue;
        }

        [$environmentKey, $environmentValue] = explode('=', $environmentLine, 2);
        $environmentKey = trim($environmentKey);
        $environmentValue = trim($environmentValue);

        if (! preg_match('/^[A-Z_][A-Z0-9_]*$/i', $environmentKey)) {
            continue;
        }

        if (strlen($environmentValue) >= 2) {
            $firstCharacter = $environmentValue[0];
            $lastCharacter = $environmentValue[strlen($environmentValue) - 1];

            if (($firstCharacter === '"' && $lastCharacter === '"') || ($firstCharacter === "'" && $lastCharacter === "'")) {
                $environmentValue = substr($environmentValue, 1, -1);
            }
        }

        if (! array_key_exists($environmentKey, $_ENV) && getenv($environmentKey) === false) {
            $_ENV[$environmentKey] = $environmentValue;
            $_SERVER[$environmentKey] = $environmentValue;
            @putenv($environmentKey.'='.$environmentValue);
        }
    }
}

$application = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->alias(['admin' => \App\Http\Middleware\AdminMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$application->loadEnvironmentFrom('.cnet-environment-already-loaded');

return $application;
