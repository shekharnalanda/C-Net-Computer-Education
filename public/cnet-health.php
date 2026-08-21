<?php
header('Content-Type: text/html; charset=UTF-8');

$root = dirname(__DIR__);
$items = array();

function cnet_env($path)
{
    $values = array();
    $lines = is_readable($path) ? file($path, FILE_IGNORE_NEW_LINES) : array();
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || substr($line, 0, 1) === '#' || strpos($line, '=') === false) continue;
        $parts = explode('=', $line, 2);
        $value = trim($parts[1]);
        if (strlen($value) >= 2) {
            $first = substr($value, 0, 1);
            $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) $value = substr($value, 1, -1);
        }
        $values[trim($parts[0])] = $value;
    }
    return $values;
}

function add_item(&$items, $name, $ok, $detail)
{
    $items[] = array($name, (bool) $ok, (string) $detail);
}

add_item($items, 'PHP version', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION);
$required = array('pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'ctype', 'fileinfo');
foreach ($required as $extension) add_item($items, 'Extension '.$extension, extension_loaded($extension), extension_loaded($extension) ? 'Loaded' : 'Missing');

$autoload = $root.'/vendor/autoload.php';
add_item($items, 'Composer autoload', is_file($autoload), is_file($autoload) ? 'Present; size '.filesize($autoload).' bytes; modified '.date('Y-m-d H:i:s', filemtime($autoload)) : 'Missing');
$real = $root.'/vendor/composer/autoload_real.php';
$realText = is_readable($real) ? file_get_contents($real) : '';
$fingerprint = preg_match('/ComposerAutoloaderInit([a-f0-9]+)/', $realText, $match) ? $match[1] : 'not found';
add_item($items, 'Vendor fingerprint', $fingerprint === 'd8833d036110596d4b10e0cca9e6bbd3', $fingerprint);

$installedPath = $root.'/vendor/composer/installed.json';
$laravelVersion = 'not found';
if (is_readable($installedPath)) {
    $installed = json_decode(file_get_contents($installedPath), true);
    $packages = isset($installed['packages']) ? $installed['packages'] : $installed;
    if (is_array($packages)) foreach ($packages as $package) {
        if (isset($package['name']) && $package['name'] === 'laravel/framework') $laravelVersion = isset($package['pretty_version']) ? $package['pretty_version'] : $package['version'];
    }
}
add_item($items, 'Laravel package', $laravelVersion !== 'not found', $laravelVersion);

$envPath = $root.'/.env';
$env = cnet_env($envPath);
add_item($items, '.env readable', is_readable($envPath), is_readable($envPath) ? 'Yes' : 'No');
add_item($items, 'APP_KEY', !empty($env['APP_KEY']), !empty($env['APP_KEY']) ? 'Set' : 'Missing');
add_item($items, 'APP_ENV', isset($env['APP_ENV']) && $env['APP_ENV'] === 'production', isset($env['APP_ENV']) ? $env['APP_ENV'] : 'Missing');
add_item($items, 'ADMIN_PASSWORD', !empty($env['ADMIN_PASSWORD']) && $env['ADMIN_PASSWORD'] !== 'change-this-before-seeding', !empty($env['ADMIN_PASSWORD']) && $env['ADMIN_PASSWORD'] !== 'change-this-before-seeding' ? 'Set securely' : 'Missing or placeholder');

try {
    $host = isset($env['DB_HOST']) ? $env['DB_HOST'] : '127.0.0.1';
    $port = isset($env['DB_PORT']) ? $env['DB_PORT'] : '3306';
    $database = isset($env['DB_DATABASE']) ? $env['DB_DATABASE'] : '';
    $username = isset($env['DB_USERNAME']) ? $env['DB_USERNAME'] : '';
    $password = isset($env['DB_PASSWORD']) ? $env['DB_PASSWORD'] : '';
    $pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$database.';charset=utf8mb4', $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    add_item($items, 'Direct database', true, 'Connected to '.$database.' as '.$username);
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    add_item($items, 'Database tables', count($tables) > 0, count($tables).' tables: '.implode(', ', $tables));
} catch (Exception $exception) {
    add_item($items, 'Direct database', false, preg_replace('/(password|pwd)\s*[=:]\s*[^\s,;]+/i', '$1=[hidden]', $exception->getMessage()));
}

add_item($items, 'Storage writable', is_writable($root.'/storage/framework'), is_writable($root.'/storage/framework') ? 'Yes' : 'No');
add_item($items, 'Cache writable', is_writable($root.'/bootstrap/cache'), is_writable($root.'/bootstrap/cache') ? 'Yes' : 'No');

$rows = '';
foreach ($items as $item) {
    $class = $item[1] ? 'pass' : 'fail';
    $status = $item[1] ? 'PASS' : 'FAIL';
    $rows .= '<tr><td>'.htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8').'</td><td class="'.$class.'">'.$status.'</td><td><code>'.htmlspecialchars($item[2], ENT_QUOTES, 'UTF-8').'</code></td></tr>';
}
echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>C-Net Full Health</title><style>body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}main{max-width:1050px;margin:4vh auto;padding:30px;background:#fff;border-radius:18px}table{width:100%;border-collapse:collapse}th,td{padding:11px;text-align:left;border-bottom:1px solid #ddd}.pass{color:#08783e;font-weight:bold}.fail{color:#b42318;font-weight:bold}code{white-space:pre-wrap;word-break:break-word}</style></head><body><main><h1>C-Net full server health</h1><p>No Laravel code is executed and passwords are never displayed.</p><table><tr><th>Check</th><th>Status</th><th>Detail</th></tr>'.$rows.'</table></main></body></html>';
