<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class AppStore
{
    private const KEYS = ['clients', 'projets', 'paiements', 'utilisateurs', 'evolutions'];

    public static function path(): string
    {
        return storage_path('app/evopro_data.json');
    }

    public static function all(): array
    {
        $path = self::path();

        if (! File::exists($path)) {
            return self::defaults();
        }

        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded)) {
            return self::defaults();
        }

        $data = self::defaults();

        foreach (self::KEYS as $key) {
            if (isset($decoded[$key]) && is_array($decoded[$key])) {
                $data[$key] = array_values($decoded[$key]);
            }
        }

        return $data;
    }

    public static function get(string $key, array $default = []): array
    {
        if (! in_array($key, self::KEYS, true)) {
            return $default;
        }

        $data = self::all();

        return $data[$key] ?? $default;
    }

    public static function put(string $key, array $value): void
    {
        if (! in_array($key, self::KEYS, true)) {
            return;
        }

        $data = self::all();
        $data[$key] = array_values($value);
        self::write($data);
    }

    /**
     * @param  array<string, array>  $pairs
     */
    public static function putMany(array $pairs): void
    {
        $data = self::all();

        foreach ($pairs as $key => $value) {
            if (! in_array($key, self::KEYS, true) || ! is_array($value)) {
                continue;
            }

            $data[$key] = array_values($value);
        }

        self::write($data);
    }

    /**
     * Import session-backed data for any empty store keys.
     */
    public static function hydrateFromSessionIfEmpty(): void
    {
        $data = self::all();
        $imported = false;

        foreach (self::KEYS as $key) {
            if (! empty($data[$key])) {
                continue;
            }

            $fromSession = session($key, []);
            if (is_array($fromSession) && $fromSession !== []) {
                $data[$key] = array_values($fromSession);
                $imported = true;
            }
        }

        if ($imported) {
            self::write($data);
        }
    }

    private static function defaults(): array
    {
        return [
            'clients' => [],
            'projets' => [],
            'paiements' => [],
            'utilisateurs' => [],
            'evolutions' => [],
        ];
    }

    private static function write(array $data): void
    {
        $dir = dirname(self::path());

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(
            self::path(),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
