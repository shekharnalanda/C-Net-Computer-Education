<?php

namespace App\Support;

use Illuminate\Support\Str;

class CertificateStore
{
    private static function path(): string
    {
        return storage_path('app/cnet-certificates.json');
    }

    public static function all(): array
    {
        $items = is_readable(self::path()) ? json_decode((string) file_get_contents(self::path()), true) : [];
        $items = is_array($items) ? array_values($items) : [];
        usort($items, fn (array $a, array $b): int => strcmp(($b['issue_date'] ?? '').($b['created_at'] ?? ''), ($a['issue_date'] ?? '').($a['created_at'] ?? '')));
        return $items;
    }

    public static function find(string $id): ?array
    {
        return collect(self::all())->firstWhere('id', $id);
    }

    public static function findByCode(string $code): ?array
    {
        return collect(self::all())->first(fn (array $item): bool => strtoupper($item['verification_code'] ?? '') === strtoupper(trim($code)));
    }

    public static function add(array $data): array
    {
        $items = self::all();
        do {
            $code = 'CNET-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
        } while (collect($items)->contains('verification_code', $code));
        $item = array_merge($data, [
            'id' => (string) Str::uuid(),
            'certificate_no' => 'CNET-CERT-'.now()->format('ymd').'-'.strtoupper(Str::random(5)),
            'verification_code' => $code,
            'created_at' => now()->toIso8601String(),
        ]);
        array_unshift($items, $item);
        self::write($items);
        return $item;
    }

    public static function remove(string $id): bool
    {
        $items = self::all();
        $before = count($items);
        $items = array_values(array_filter($items, fn (array $item): bool => ($item['id'] ?? '') !== $id));
        if ($before === count($items)) return false;
        self::write($items);
        return true;
    }

    private static function write(array $items): void
    {
        file_put_contents(self::path(), json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
