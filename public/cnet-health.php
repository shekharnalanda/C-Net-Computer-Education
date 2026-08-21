<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');
$root = dirname(__DIR__);
$checks = [];

function env_values(string $path): array
{
    $values = [];
    if (!is_readable($path)) {
        return $values;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $value = trim($value);
        if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
            $value = substr($value, 1, -1);
        }
        $values[trim($key)] = $value;
    }
    return $values;
}

function add_check(array &$checks, string $name, bool $ok, string $detail): void
{
    $checks[] = [$name, $ok, $detail];
}

$envPath = $root.'/.env';
$env = env_values($envPath);
add_check($checks, '.env readable', is_readable($envPath), is_readable($envPath) ? 'Yes' : 'No');
add_check($checks, 'PHP version', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION);
add_check($checks, 'Composer vendor', is_file($root.'/vendor/autoload.php'), is_file($root.'/vendor/autoload.php') ? 'Present' : 'Missing');
add_check($checks, 'Storage writable', is_writable($root.'/storage'), is_writable($root.'/storage') ? 'Yes' : 'No');
add_check($checks, 'Cache writable', is_writable($root.'/bootstrap/cache'), is_writable($root.'/bootstrap/cache') ? 'Yes' : 'No');

$dbResult = 'Not tested';
$dbOk = false;
$tableResult = 'Unavailable';
try {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $database = $env['DB_DATABASE'] ?? '';
    $username = $env['DB_USERNAME'] ?? '';
    $password = $env['DB_PASSWORD'] ?? '';
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $dbOk = true;
    $dbResult = 'Connected as '.$username.' to '.$database;
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $tableResult = $tables ? implode(', ', $tables) : 'Database is empty';
} catch (Throwable $exception) {
    $dbResult = preg_replace('/(password|pwd)\s*[=:]\s*[^\s,;]+/i', '$1=[hidden]', $exception->getMessage());
}
add_check($checks, 'Database connection', $dbOk, $dbResult);
add_check($checks, 'Database tables', $dbOk && $tableResult !== 'Database is empty', $tableResult);

$rows = '';
foreach ($checks as [$name, $ok, $detail]) {
    $status = $ok ? 'PASS' : 'FAIL';
    $class = $ok ? 'pass' : 'fail';
    $rows .= '<tr><td>'.htmlspecialchars($name).'</td><td class="'.$class.'">'.$status.'</td><td><code>'.htmlspecialchars($detail).'</code></td></tr>';
}

echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>C-Net Health Check</title><style>body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}main{max-width:1000px;margin:6vh auto;padding:30px;background:white;border-radius:18px;box-shadow:0 12px 40px #17324d22}h1{color:#075cab}table{width:100%;border-collapse:collapse}th,td{padding:12px;text-align:left;border-bottom:1px solid #dce6ef}.pass{color:#08783e;font-weight:bold}.fail{color:#b42318;font-weight:bold}code{white-space:pre-wrap;word-break:break-word}</style></head><body><main><h1>C-Net standalone health check</h1><p>This page does not load Laravel and never displays the database password.</p><table><thead><tr><th>Check</th><th>Status</th><th>Detail</th></tr></thead><tbody>'.$rows.'</tbody></table></main></body></html>';
