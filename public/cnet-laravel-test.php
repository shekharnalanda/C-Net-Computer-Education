<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '256M');

$GLOBALS['cnet_step'] = 'PHP script started';

function cnet_safe($value)
{
    $value = preg_replace('/(password|pwd)\s*[=:]\s*[^\s,;]+/i', '$1=[hidden]', (string) $value);
    return htmlspecialchars(substr($value, 0, 1800), ENT_QUOTES, 'UTF-8');
}

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        http_response_code(503);
        echo '<!doctype html><html><body style="font-family:Arial;padding:30px"><h1>PHP fatal error captured</h1>';
        echo '<p><strong>Last completed step:</strong> '.cnet_safe($GLOBALS['cnet_step']).'</p>';
        echo '<pre style="white-space:pre-wrap;background:#f3f6f9;padding:16px">'.cnet_safe($error['message']).'</pre>';
        echo '<p><strong>File:</strong> '.cnet_safe($error['file']).':'.(int) $error['line'].'</p></body></html>';
    }
});

$root = dirname(__DIR__);
$steps = array();

try {
    $GLOBALS['cnet_step'] = 'Loading Composer autoload';
    require $root.'/vendor/autoload.php';
    $steps[] = 'Composer autoload: PASS';

    $GLOBALS['cnet_step'] = 'Creating Laravel application';
    $app = require $root.'/bootstrap/app.php';
    $steps[] = 'Laravel application creation: PASS';

    $GLOBALS['cnet_step'] = 'Bootstrapping Laravel console kernel';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    $steps[] = 'Laravel bootstrap: PASS';

    $GLOBALS['cnet_step'] = 'Connecting to database';
    Illuminate\Support\Facades\DB::connection()->getPdo();
    $steps[] = 'Database connection: PASS';

    $GLOBALS['cnet_step'] = 'Querying users';
    $steps[] = 'Users: '.App\Models\User::query()->count();

    $GLOBALS['cnet_step'] = 'Querying courses';
    $courses = App\Models\Course::query()->get();
    $steps[] = 'Courses: '.$courses->count();

    $GLOBALS['cnet_step'] = 'Rendering home view';
    view('home', array('courses' => $courses))->render();
    $steps[] = 'Home view render: PASS';

    $GLOBALS['cnet_step'] = 'All checks completed';
    $result = '<h2 style="color:green">All Laravel checks passed</h2>';
} catch (Throwable $exception) {
    $GLOBALS['cnet_step'] = 'Throwable captured';
    $result = '<h2 style="color:#b42318">Laravel exception captured</h2><pre style="white-space:pre-wrap;background:#f3f6f9;padding:16px">'.cnet_safe($exception->getMessage()).'</pre><p>'.cnet_safe($exception->getFile()).':'.(int) $exception->getLine().'</p>';
}

$list = '';
foreach ($steps as $step) {
    $list .= '<li><code>'.cnet_safe($step).'</code></li>';
}
echo '<!doctype html><html><head><meta charset="utf-8"><title>C-Net Laravel Fatal Test</title></head><body style="font-family:Arial;padding:30px;background:#eef4fb"><main style="max-width:900px;margin:auto;background:white;padding:30px;border-radius:18px"><h1>C-Net Laravel test</h1>'.$result.'<ol>'.$list.'</ol></main></body></html>';
