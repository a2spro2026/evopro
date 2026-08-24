<?php

namespace App\Support;

class UtilisateurHelper
{
    public const STATUES = ['administrateur', 'assistante', 'commercial'];

    /**
     * @return array<string, string>
     */
    public static function statueLabels(): array
    {
        return [
            'administrateur' => 'Administrateur',
            'assistante' => 'Assistante',
            'commercial' => 'Commercial',
        ];
    }

    public static function normalizeStatue(?string $statue): string
    {
        $statue = mb_strtolower(trim((string) $statue));

        return match ($statue) {
            'admin', 'administrateur' => 'administrateur',
            'assistante' => 'assistante',
            'commercial', 'vendeur', 'manager' => 'commercial',
            default => in_array($statue, self::STATUES, true) ? $statue : 'commercial',
        };
    }

    public static function statueLabel(?string $statue): string
    {
        $normalized = self::normalizeStatue($statue);

        return self::statueLabels()[$normalized] ?? ucfirst($normalized);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function normalizeRow(array $row, int $index = 0): array
    {
        $ref = trim((string) ($row['ref'] ?? ''));
        if ($ref === '') {
            $ref = 'USR-'.str_pad((string) max(1, $index + 1), 4, '0', STR_PAD_LEFT);
        }

        return [
            'id' => $row['id'] ?? uniqid('usr_', true),
            'ref' => $ref,
            'date' => trim((string) ($row['date'] ?? '')) ?: now()->format('d/m/Y'),
            'nom_complet' => trim((string) ($row['nom_complet'] ?? '')),
            'ville' => trim((string) ($row['ville'] ?? '')),
            'statue' => self::normalizeStatue($row['statue'] ?? ''),
            'login' => trim((string) ($row['login'] ?? '')),
            'password' => (string) ($row['password'] ?? ''),
            'suspendu' => (bool) ($row['suspendu'] ?? false),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeAll(array $rows): array
    {
        return collect($rows)
            ->values()
            ->map(fn ($row, $index) => self::normalizeRow($row, $index))
            ->all();
    }

    public static function isCommercial(array $user): bool
    {
        if ($user['suspendu'] ?? false) {
            return false;
        }

        return self::normalizeStatue($user['statue'] ?? '') === 'commercial';
    }

    public static function canManageProspectionCommercial(?string $statue): bool
    {
        return in_array(self::normalizeStatue($statue), ['administrateur', 'assistante'], true);
    }

    public static function isAdministrateur(?string $statue): bool
    {
        return self::normalizeStatue($statue) === 'administrateur';
    }

    public static function isAssistante(?string $statue): bool
    {
        return self::normalizeStatue($statue) === 'assistante';
    }

    public static function isCommercialRole(?string $statue): bool
    {
        return self::normalizeStatue($statue) === 'commercial';
    }

    public static function defaultPanel(?string $statue): string
    {
        return match (self::normalizeStatue($statue)) {
            'assistante', 'commercial' => 'prospection',
            default => 'dashboard',
        };
    }

    /**
     * @param  array<string, mixed>  $user
     * @return list<string>
     */
    public static function commercialIdentityKeys(array $user): array
    {
        $keys = [];

        foreach (['nom_complet', 'login'] as $field) {
            $value = mb_strtolower(trim((string) ($user[$field] ?? '')));
            if ($value !== '') {
                $keys[] = $value;
            }
        }

        $nom = trim((string) ($user['nom_complet'] ?? ''));
        if ($nom !== '') {
            $parts = preg_split('/\s+/u', mb_strtolower($nom)) ?: [];
            if (($parts[0] ?? '') !== '') {
                $keys[] = $parts[0];
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $authUser
     */
    public static function rowBelongsToCommercial(array $row, array $authUser): bool
    {
        $rowCommercial = mb_strtolower(trim((string) ($row['commercial'] ?? '')));
        if ($rowCommercial === '') {
            return false;
        }

        foreach (self::commercialIdentityKeys($authUser) as $key) {
            if ($rowCommercial === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $authUser
     */
    public static function assertCanAccessProspectionRow(array $row, array $authUser): void
    {
        $statue = self::normalizeStatue($authUser['statue'] ?? '');
        if ($statue === 'commercial' && ! self::rowBelongsToCommercial($row, $authUser)) {
            abort(403, 'Accès refusé à cette prospection.');
        }
    }
}
