<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('cnet:audit', function () {
    $audit = \App\Support\ProductionAuditService::run();
    $this->newLine();
    $this->info('C-Net automatic production audit');
    $this->table(
        ['Group', 'Check', 'Status', 'Detail'],
        collect($audit['checks'])->map(fn (array $check): array => [
            $check['group'], $check['name'], strtoupper($check['status']), $check['detail'],
        ])->all()
    );
    $this->newLine();
    $this->line("PASS: {$audit['passed']}  WARN: {$audit['warnings']}  FAIL: {$audit['failed']}");
    if (! $audit['ready']) {
        $this->error('AUTOMATIC AUDIT FAILED — deployment requires attention.');
        return 1;
    }
    $this->info('AUTOMATIC AUDIT PASSED — C-Net is ready.');
    return 0;
})->purpose('Run all C-Net production readiness checks');
