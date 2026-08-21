<?php

namespace App\Support;

use Illuminate\Support\Str;

class AdmissionStore
{
    private static function path(): string
    {
        return storage_path('app/cnet-admissions.json');
    }

    public static function all(): array
    {
        $items = is_readable(self::path()) ? json_decode((string) file_get_contents(self::path()), true) : [];
        $items = is_array($items) ? array_values($items) : [];
        usort($items, fn (array $a, array $b) => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

        return $items;
    }

    public static function add(array $data): array
    {
        $items = self::all();
        $item = array_merge($data, [
            'id' => (string) Str::uuid(),
            'application_no' => 'CNET-'.now()->format('ymd').'-'.strtoupper(Str::random(5)),
            'status' => 'pending',
            'created_at' => now()->toIso8601String(),
        ]);
        array_unshift($items, $item);
        self::write($items);

        return $item;
    }

    public static function updateStatus(string $id, string $status): bool
    {
        $items = self::all();
        $changed = false;
        foreach ($items as &$item) {
            if (($item['id'] ?? '') === $id) {
                $item['status'] = $status;
                $item['updated_at'] = now()->toIso8601String();
                $changed = true;
                break;
            }
        }
        unset($item);
        if ($changed) self::write($items);

        return $changed;
    }

    public static function remove(string $id): bool
    {
        $items = self::all();
        $before = count($items);
        $items = array_values(array_filter($items, fn (array $item) => ($item['id'] ?? '') !== $id));
        if (count($items) === $before) return false;
        self::write($items);

        return true;
    }

    private static function write(array $items): void
    {
        file_put_contents(self::path(), json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
