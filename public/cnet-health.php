<?php
header('Content-Type: text/html; charset=UTF-8');

$root = dirname(__DIR__);
$items = array();
$items[] = array('PHP version', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION.' (Laravel requires PHP 8.2 or newer)');
$items[] = array('Composer vendor', file_exists($root.'/vendor/autoload.php'), file_exists($root.'/vendor/autoload.php') ? 'Present' : 'Missing');
$items[] = array('.env readable', is_readable($root.'/.env'), is_readable($root.'/.env') ? 'Yes' : 'No');
$items[] = array('Storage writable', is_writable($root.'/storage'), is_writable($root.'/storage') ? 'Yes' : 'No');
$items[] = array('Cache writable', is_writable($root.'/bootstrap/cache'), is_writable($root.'/bootstrap/cache') ? 'Yes' : 'No');

$rows = '';
foreach ($items as $item) {
    $class = $item[1] ? 'pass' : 'fail';
    $status = $item[1] ? 'PASS' : 'FAIL';
    $rows .= '<tr><td>'.htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8').'</td><td class="'.$class.'">'.$status.'</td><td><code>'.htmlspecialchars($item[2], ENT_QUOTES, 'UTF-8').'</code></td></tr>';
}

echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>C-Net Health Check</title><style>body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}main{max-width:900px;margin:6vh auto;padding:30px;background:#fff;border-radius:18px}table{width:100%;border-collapse:collapse}th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd}.pass{color:#08783e;font-weight:bold}.fail{color:#b42318;font-weight:bold}</style></head><body><main><h1>C-Net server health</h1><table><tr><th>Check</th><th>Status</th><th>Detail</th></tr>'.$rows.'</table></main></body></html>';
