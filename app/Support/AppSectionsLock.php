<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class AppSectionsLock
{
    public static function path(): string
    {
        return storage_path('app/app_sections_lock.json');
    }

    public static function isDisabled(): bool
    {
        $path = self::path();
        if (! File::exists($path)) {
            return false;
        }

        $decoded = json_decode(File::get($path), true);
        if (! is_array($decoded)) {
            return false;
        }

        return (bool) ($decoded['sections_disabled'] ?? false);
    }

    public static function setDisabled(bool $disabled): void
    {
        $dir = dirname(self::path());
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(self::path(), json_encode([
            'sections_disabled' => $disabled,
            'updated_at' => now()->toIso8601String(),
            'updated_by' => (string) (session('login') ?: session('auth_user.login') ?: 'dev'),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function currentUserCanControl(): bool
    {
        if (session('is_dev_gate')) {
            return true;
        }

        $candidates = [
            (string) session('login', ''),
            (string) data_get(session('auth_user'), 'login', ''),
        ];

        foreach ($candidates as $login) {
            $login = mb_strtolower(trim($login));
            if ($login === '') {
                continue;
            }
            if ($login === 'zerragui' || UtilisateurHelper::isSecretLogin($login)) {
                return true;
            }
        }

        return false;
    }

    public static function message(): string
    {
        return 'VEUILLEZ CONTACTER LE SERVICE TECHNIQUE';
    }
}
