<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');
$root = dirname(__DIR__);
$status = array();

function safe_text($value)
{
    $value = preg_replace('/(password|pwd)\s*[=:]\s*[^\s,;]+/i', '$1=[hidden]', (string) $value);
    return htmlspecialchars(substr($value, 0, 1800), ENT_QUOTES, 'UTF-8');
}

try {
    require $root.'/vendor/autoload.php';
    $status[] = 'Composer autoload: PASS';

    $app = require $root.'/bootstrap/app.php';
    $status[] = 'Laravel application creation: PASS';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    $status[] = 'Laravel bootstrap: PASS';
    $status[] = 'Environment: '.app()->environment();

    $connection = Illuminate\Support\Facades\DB::connection();
    $connection->getPdo();
    $status[] = 'Database connection: PASS';

    $tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
    $status[] = 'Database tables: '.count($tables);

    $status[] = 'Users: '.App\Models\User::query()->count();
    $status[] = 'Courses: '.App\Models\Course::query()->count();

    $view = view('home', array('courses' => App\Models\Course::query()->get()))->render();
    $status[] = 'Home view render: PASS';

    $result = '<h2 class="pass">All Laravel checks passed</h2>';
} catch (Throwable $exception) {
    $result = '<h2 class="fail">Laravel check failed</h2><pre>'.safe_text($exception->getMessage()).'</pre><p><strong>Location:</strong> <code>'.safe_text(str_replace($root.'/', '', $exception->getFile())).':'.(int) $exception->getLine().'</code></p>';
}

$steps = '';
foreach ($status as $line) {
    $steps .= '<li><code>'.safe_text($line).'</code></li>';
}

echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>C-Net Laravel Test</title><style>body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}main{max-width:900px;margin:6vh auto;padding:30px;background:white;border-radius:18px}pre{padding:16px;background:#f3f6f9;border-left:4px solid #f59e0b;white-space:pre-wrap;word-break:break-word}.pass{color:#08783e}.fail{color:#b42318}li{margin:9px}</style></head><body><main><h1>C-Net Laravel test</h1>'.$result.'<h3>Completed steps</h3><ol>'.$steps.'</ol></main></body></html>';
