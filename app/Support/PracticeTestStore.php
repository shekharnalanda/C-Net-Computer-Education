<?php

namespace App\Support;

use Illuminate\Support\Str;

class PracticeTestStore
{
    private static function testsPath(): string { return storage_path('app/cnet-practice-tests.json'); }
    private static function attemptsPath(): string { return storage_path('app/cnet-practice-attempts.json'); }

    public static function all(): array
    {
        $items = self::read(self::testsPath());
        usort($items, fn(array $a,array $b): int => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
        return $items;
    }

    public static function find(string $id): ?array
    {
        foreach (self::all() as $item) if (($item['id'] ?? '') === $id) return $item;
        return null;
    }

    public static function add(array $data): array
    {
        $items = self::all();
        $test = array_merge($data, ['id'=>(string) Str::uuid(),'is_active'=>true,'created_at'=>now()->toIso8601String()]);
        array_unshift($items,$test); self::write(self::testsPath(),$items); return $test;
    }

    public static function toggle(string $id): bool
    {
        $items=self::all(); $changed=false;
        foreach($items as &$item) if(($item['id']??'')===$id){$item['is_active']=!($item['is_active']??true);$changed=true;break;}
        unset($item); if($changed) self::write(self::testsPath(),$items); return $changed;
    }

    public static function remove(string $id): bool
    {
        $items=self::all(); $before=count($items);
        $items=array_values(array_filter($items,fn(array $item):bool=>($item['id']??'')!==$id));
        if($before===count($items)) return false;
        self::write(self::testsPath(),$items);
        $attempts=array_values(array_filter(self::attempts(),fn(array $row):bool=>($row['test_id']??'')!==$id));
        self::write(self::attemptsPath(),$attempts); return true;
    }

    public static function attempts(): array
    {
        $items=self::read(self::attemptsPath());
        usort($items,fn(array $a,array $b):int=>strcmp($b['submitted_at']??'',$a['submitted_at']??''));
        return $items;
    }

    public static function attemptsForStudent(string $studentId): array
    {
        return array_values(array_filter(self::attempts(),fn(array $row):bool=>($row['student_id']??'')===$studentId));
    }

    public static function recordAttempt(array $data): array
    {
        $items=self::attempts();
        $attempt=array_merge($data,['id'=>(string) Str::uuid(),'submitted_at'=>now()->toIso8601String()]);
        array_unshift($items,$attempt); self::write(self::attemptsPath(),$items); return $attempt;
    }

    private static function read(string $path): array
    {
        $items=is_readable($path)?json_decode((string)file_get_contents($path),true):[];
        return is_array($items)?array_values($items):[];
    }

    private static function write(string $path,array $items): void
    {
        file_put_contents($path,json_encode($items,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),LOCK_EX);
    }
}
