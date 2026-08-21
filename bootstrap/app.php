<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
        $middleware->alias(['admin' => \App\Http\Middleware\AdminMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() < 500) {
                return null;
            }

            $message = preg_replace(
                '/(password|pwd)\s*[=:]\s*[^\s,;]+/i',
                '$1=[hidden]',
                $exception->getMessage()
            );
            $message = e(mb_substr((string) $message, 0, 1200));
            $location = e(str_replace(base_path().'/', '', $exception->getFile()));
            $line = (int) $exception->getLine();

            $html = <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>C-Net Laravel diagnostic</title>
<style>
body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}
main{max-width:780px;margin:8vh auto;padding:32px;background:#fff;border-radius:18px;box-shadow:0 12px 40px #17324d22}
h1{color:#075cab}code,pre{background:#f3f6f9;border-radius:8px}pre{padding:16px;white-space:pre-wrap;word-break:break-word;border-left:4px solid #f59e0b}
</style>
</head>
<body><main>
<h1>C-Net Laravel setup needs attention</h1>
<p>The safe diagnostic below hides password values.</p>
<pre>{$message}</pre>
<p><strong>Location:</strong> <code>{$location}:{$line}</code></p>
</main></body></html>
HTML;

            return response($html, 503, ['Content-Type' => 'text/html; charset=UTF-8']);
        });
    })->create();

$application->loadEnvironmentFrom('.cnet-environment-already-loaded');

return $application;
