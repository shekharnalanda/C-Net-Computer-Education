<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Throwable;

class HomeController extends Controller
{
    public function index()
    {
        if (app()->bound('cnet.setup_error')) {
            return $this->setupErrorResponse((string) app('cnet.setup_error'));
        }

        try {
            $courses = Course::where('is_active', true)->orderBy('sort_order')->orderBy('title')->get();

            return view('home', compact('courses'));
        } catch (Throwable $exception) {
            return $this->setupErrorResponse($exception->getMessage());
        }
    }

    private function setupErrorResponse(string $message)
    {
        $safeMessage = preg_replace(
            '/(password|pwd)\s*[=:]\s*[^\s,;]+/i',
            '$1=[hidden]',
            $message
        );
        $safeMessage = e(mb_substr((string) $safeMessage, 0, 1200));
        $environment = e(app()->environment());

        $html = <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>C-Net setup needs attention</title>
    <style>
        body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}
        main{max-width:760px;margin:8vh auto;padding:32px;background:#fff;border-radius:18px;box-shadow:0 12px 40px #17324d22}
        h1{color:#075cab} code,pre{background:#f3f6f9;border-radius:8px}
        pre{padding:16px;white-space:pre-wrap;word-break:break-word;border-left:4px solid #f59e0b}
        li{margin:8px 0}
    </style>
</head>
<body><main>
    <h1>C-Net setup needs attention</h1>
    <p>The application is online, but Laravel could not complete database setup.</p>
    <p><strong>Environment:</strong> <code>{$environment}</code></p>
    <p><strong>Safe diagnostic:</strong></p>
    <pre>{$safeMessage}</pre>
    <p>Check that <code>APP_ENV=production</code>, the database name/user/password are correct, the database user has all privileges, and <code>ADMIN_PASSWORD</code> is not the placeholder value.</p>
</main></body></html>
HTML;

        return response($html, 503, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
