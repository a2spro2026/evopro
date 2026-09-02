<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class CommercialPresenceHelper
{
    private const TIMEOUT_SECONDS = 12;

    private static function path(): string
    {
        return storage_path('app/commercial_presence.json');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function all(): array
    {
        $path = self::path();
        if (! File::exists($path)) {
            return [];
        }

        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, array<string, mixed>>  $data
     */
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

  /**
     * @param  array<string, mixed>  $authUser
     */
    public static function commercialKey(array $authUser): string
    {
        $id = trim((string) ($authUser['id'] ?? ''));
        if ($id !== '') {
            return mb_strtolower($id);
        }

        return mb_strtolower(trim((string) ($authUser['nom_complet'] ?? $authUser['login'] ?? '')));
    }

    /**
     * @param  array<string, mixed>  $authUser
     */
    public static function markOnline(array $authUser): void
    {
        $key = self::commercialKey($authUser);
        if ($key === '') {
            return;
        }

        $data = self::all();
        $data[$key] = [
            'nom_complet' => trim((string) ($authUser['nom_complet'] ?? '')),
            'status' => 'online',
            'last_seen' => time(),
            'session_id' => session()->getId(),
        ];
        self::write($data);
    }

    /**
     * @param  array<string, mixed>  $authUser
     */
    public static function markOffline(array $authUser): void
    {
        $key = self::commercialKey($authUser);
        if ($key === '') {
            return;
        }

        $data = self::all();
        $data[$key] = [
            'nom_complet' => trim((string) ($authUser['nom_complet'] ?? $data[$key]['nom_complet'] ?? '')),
            'status' => 'offline',
            'last_seen' => time(),
            'session_id' => null,
        ];
        self::write($data);
    }

    /**
     * @param  array<string, mixed>  $authUser
     */
    public static function heartbeat(array $authUser): void
    {
        self::markOnline($authUser);
    }

    /**
     * @return list<array{key: string, nom_complet: string, status: string}>
     */
    public static function statusesForManager(): array
    {
        $data = self::all();
        $now = time();
        $changed = false;

        $commercials = collect(UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs')))
            ->filter(fn ($u) => UtilisateurHelper::isCommercial($u))
            ->values();

        $result = [];

        foreach ($commercials as $user) {
            $key = self::commercialKey($user);
            $row = $data[$key] ?? $data[mb_strtolower(trim((string) ($user['nom_complet'] ?? '')))] ?? null;
            $status = 'offline';

            if (is_array($row)) {
                $lastSeen = (int) ($row['last_seen'] ?? 0);
                $isFresh = ($now - $lastSeen) <= self::TIMEOUT_SECONDS;

                if (($row['status'] ?? '') === 'online' && $isFresh) {
                    $status = 'online';
                } else {
                    $status = 'offline';
                    if (($row['status'] ?? '') === 'online') {
                        $data[$key]['status'] = 'offline';
                        $changed = true;
                    }
                }
            }

            $result[] = [
                'key' => $key,
                'nom_complet' => (string) ($user['nom_complet'] ?? ''),
                'status' => $status,
            ];
        }

        if ($changed) {
            self::write($data);
        }

        return $result;
    }
}
