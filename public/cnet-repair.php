<?php
header('Content-Type: text/html; charset=UTF-8');

$root = dirname(__DIR__);
$directories = array(
    $root.'/storage',
    $root.'/storage/app',
    $root.'/storage/framework',
    $root.'/storage/framework/cache',
    $root.'/storage/framework/cache/data',
    $root.'/storage/framework/sessions',
    $root.'/storage/framework/views',
    $root.'/storage/logs',
    $root.'/bootstrap/cache',
);

$results = array();

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
    @chmod($directory, 0755);
    $results[] = array(str_replace($root.'/', '', $directory), is_dir($directory) && is_writable($directory));
}

$clearPatterns = array(
    $root.'/bootstrap/cache/*.php',
    $root.'/storage/framework/views/*.php',
);

foreach ($clearPatterns as $pattern) {
    foreach (glob($pattern) ?: array() as $file) {
        if (basename($file) !== '.gitignore') {
            @unlink($file);
        }
    }
}

$cacheRoot = $root.'/storage/framework/cache/data';
if (is_dir($cacheRoot)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isFile()) {
            @unlink($item->getPathname());
        } elseif ($item->isDir()) {
            @rmdir($item->getPathname());
        }
    }
}

$rows = '';
$allPassed = true;
foreach ($results as $result) {
    $allPassed = $allPassed && $result[1];
    $rows .= '<tr><td>'.htmlspecialchars($result[0], ENT_QUOTES, 'UTF-8').'</td><td style="color:'.($result[1] ? '#08783e' : '#b42318').';font-weight:bold">'.($result[1] ? 'FIXED' : 'FAILED').'</td></tr>';
}

echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>C-Net Repair</title><style>body{margin:0;background:#eef4fb;color:#17324d;font-family:Arial,sans-serif}main{max-width:800px;margin:6vh auto;padding:30px;background:#fff;border-radius:18px}table{width:100%;border-collapse:collapse}td,th{padding:11px;border-bottom:1px solid #ddd;text-align:left}</style></head><body><main><h1>C-Net Laravel repair</h1><h2 style="color:'.($allPassed ? '#08783e' : '#b42318').'">'.($allPassed ? 'Permissions and caches repaired' : 'Some folders could not be repaired').'</h2><table><tr><th>Folder</th><th>Status</th></tr>'.$rows.'</table><p>Generated Laravel configuration, view and application cache files were cleared. Sessions were preserved.</p><p><a href="/">Open website</a></p></main></body></html>';
