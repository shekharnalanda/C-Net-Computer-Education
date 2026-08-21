<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    $autoload = __DIR__.'/../vendor/autoload.php';

    if (! file_exists($autoload)) {
        throw new RuntimeException('Composer vendor folder is missing or was not extracted in the repository root.');
    }

    require $autoload;

    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (Throwable $exception) {
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');

    $message = preg_replace(
        '/(password|pwd)\s*[=:]\s*[^\s,;]+/i',
        '$1=[hidden]',
        $exception->getMessage()
    );
    $message = htmlspecialchars(mb_substr((string) $message, 0, 1200), ENT_QUOTES, 'UTF-8');
    $file = htmlspecialchars(str_replace(dirname(__DIR__).'/', '', $exception->getFile()), ENT_QUOTES, 'UTF-8');
    $line = (int) $exception->getLine();

    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>C-Net startup diagnostic</title>
<style>
body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}
main{max-width:780px;margin:8vh auto;padding:32px;background:#fff;border-radius:18px;box-shadow:0 12px 40px #17324d22}
h1{color:#075cab}code,pre{background:#f3f6f9;border-radius:8px}pre{padding:16px;white-space:pre-wrap;word-break:break-word;border-left:4px solid #f59e0b}
</style>
</head>
<body><main>
<h1>C-Net startup needs attention</h1>
<p>Laravel stopped before the home page could load. The diagnostic below hides password values.</p>
<pre>{$message}</pre>
<p><strong>Location:</strong> <code>{$file}:{$line}</code></p>
</main></body></html>
HTML;
}
