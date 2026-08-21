<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '256M');

cnet_mark('PHP script started');

function cnet_safe($value)
{
    $value = preg_replace('/(password|pwd)\s*[=:]\s*[^\s,;]+/i', '$1=[hidden]', (string) $value);
    return htmlspecialchars(substr($value, 0, 1800), ENT_QUOTES, 'UTF-8');
}

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        @file_put_contents(dirname(__DIR__).'/storage/logs/cnet-bootstrap-step.json', json_encode(array(
            'step' => isset($GLOBALS['cnet_step']) ? $GLOBALS['cnet_step'] : 'unknown',
            'time' => date('c'),
            'error' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
        )));
        http_response_code(503);
        echo '<!doctype html><html><body style="font-family:Arial;padding:30px"><h1>PHP fatal error captured</h1>';
        echo '<p><strong>Last completed step:</strong> '.cnet_safe($GLOBALS['cnet_step']).'</p>';
        echo '<pre style="white-space:pre-wrap;background:#f3f6f9;padding:16px">'.cnet_safe($error['message']).'</pre>';
        echo '<p><strong>File:</strong> '.cnet_safe($error['file']).':'.(int) $error['line'].'</p></body></html>';
    }
});

$root = dirname(__DIR__);
$stepLog = $root.'/storage/logs/cnet-bootstrap-step.json';

function cnet_mark($step)
{
    $GLOBALS['cnet_step'] = $step;
    $payload = array('step' => $step, 'time' => date('c'), 'error' => null);
    @file_put_contents(dirname(__DIR__).'/storage/logs/cnet-bootstrap-step.json', json_encode($payload));
}

cnet_mark('PHP script started');
$steps = array();

try {
    cnet_mark('Loading Composer autoload');
    require $root.'/vendor/autoload.php';
    $steps[] = 'Composer autoload: PASS';

    cnet_mark('Creating Laravel application');
    $app = require $root.'/bootstrap/app.php';
    $steps[] = 'Laravel application creation: PASS';

    $bootstrappers = array(
        'Load environment' => Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        'Load configuration' => Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        'Handle exceptions' => Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        'Register facades' => Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        'Register providers' => Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        'Boot providers' => Illuminate\Foundation\Bootstrap\BootProviders::class,
    );

    foreach ($bootstrappers as $label => $bootstrapper) {
        cnet_mark('Bootstrap stage: '.$label);
        $app->bootstrapWith(array($bootstrapper));
        $steps[] = $label.': PASS';
    }

    cnet_mark('Connecting to database');
    Illuminate\Support\Facades\DB::connection()->getPdo();
    $steps[] = 'Database connection: PASS';

    cnet_mark('Querying users');
    $steps[] = 'Users: '.App\Models\User::query()->count();

    cnet_mark('Querying courses');
    $courses = App\Models\Course::query()->get();
    $steps[] = 'Courses: '.$courses->count();

    cnet_mark('Rendering home view');
    view('home', array('courses' => $courses))->render();
    $steps[] = 'Home view render: PASS';

    cnet_mark('All checks completed');
    $result = '<h2 style="color:green">All Laravel checks passed</h2>';
} catch (Throwable $exception) {
    cnet_mark('Throwable captured');
    $result = '<h2 style="color:#b42318">Laravel exception captured</h2><pre style="white-space:pre-wrap;background:#f3f6f9;padding:16px">'.cnet_safe($exception->getMessage()).'</pre><p>'.cnet_safe($exception->getFile()).':'.(int) $exception->getLine().'</p>';
}

$list = '';
foreach ($steps as $step) {
    $list .= '<li><code>'.cnet_safe($step).'</code></li>';
}
echo '<!doctype html><html><head><meta charset="utf-8"><title>C-Net Laravel Fatal Test</title></head><body style="font-family:Arial;padding:30px;background:#eef4fb"><main style="max-width:900px;margin:auto;background:white;padding:30px;border-radius:18px"><h1>C-Net Laravel test</h1>'.$result.'<ol>'.$list.'</ol></main></body></html>';
