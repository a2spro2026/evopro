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
            'aliases' => collect((array) ($row['aliases'] ?? []))
                ->map(fn ($alias) => mb_strtolower(trim((string) $alias)))
                ->filter()
                ->unique()
                ->values()
                ->all(),
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

        foreach ((array) ($user['aliases'] ?? []) as $alias) {
            $alias = mb_strtolower(trim((string) $alias));
            if ($alias !== '') {
                $keys[] = $alias;
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * @param  array<string, mixed>  $previousUser
     * @param  array<string, mixed>  $updatedUser
     * @return array<string, mixed>
     */
    public static function mergeCommercialAliases(array $previousUser, array $updatedUser): array
    {
        if (self::normalizeStatue($updatedUser['statue'] ?? '') !== 'commercial') {
            return $updatedUser;
        }

        $aliases = collect((array) ($updatedUser['aliases'] ?? []))
            ->merge((array) ($previousUser['aliases'] ?? []))
            ->merge(self::commercialIdentityKeys($previousUser))
            ->map(fn ($alias) => mb_strtolower(trim((string) $alias)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $updatedUser['aliases'] = $aliases;

        return $updatedUser;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $authUser
     */
    public static function rowBelongsToCommercial(array $row, array $authUser): bool
    {
        $authId = trim((string) ($authUser['id'] ?? ''));
        $rowUserId = trim((string) ($row['commercial_user_id'] ?? ''));

        if ($authId !== '' && $rowUserId !== '' && $authId === $rowUserId) {
            return true;
        }

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
     * @param  array<int, array<string, mixed>>  $commercialUsers
     * @return array<string, mixed>|null
     */
    public static function findCommercialUserForProspectionRow(array $row, array $commercialUsers): ?array
    {
        $rowUserId = trim((string) ($row['commercial_user_id'] ?? ''));
        if ($rowUserId !== '') {
            foreach ($commercialUsers as $user) {
                if (($user['id'] ?? '') === $rowUserId) {
                    return $user;
                }
            }
        }

        foreach ($commercialUsers as $user) {
            if (self::rowBelongsToCommercial($row, $user)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $user
     */
    public static function prospectionRowMatchesCommercialUser(array $row, array $user): bool
    {
        $authId = trim((string) ($user['id'] ?? ''));
        $rowUserId = trim((string) ($row['commercial_user_id'] ?? ''));

        if ($authId !== '' && $rowUserId !== '' && $authId === $rowUserId) {
            return true;
        }

        return self::rowBelongsToCommercial($row, $user);
    }

    /**
     * @param  array<string, mixed>  $previousUser
     * @param  array<string, mixed>  $updatedUser
     */
    public static function syncProspectionsAfterCommercialUpdate(array $previousUser, array $updatedUser): void
    {
        if (self::normalizeStatue($updatedUser['statue'] ?? '') !== 'commercial') {
            return;
        }

        $userId = trim((string) ($updatedUser['id'] ?? ''));
        if ($userId === '') {
            return;
        }

        $label = trim((string) ($updatedUser['nom_complet'] ?? ''));
        $rows = AppStore::get('prospections');
        $changed = false;

        foreach ($rows as $index => $row) {
            $matchesPrevious = self::prospectionRowMatchesCommercialUser($row, $previousUser);
            $matchesUpdated = self::prospectionRowMatchesCommercialUser($row, $updatedUser);

            if (! $matchesPrevious && ! $matchesUpdated) {
                continue;
            }

            $rows[$index]['commercial_user_id'] = $userId;
            if ($label !== '') {
                $rows[$index]['commercial'] = $label;
            }
            $changed = true;
        }

        if ($changed) {
            AppStore::put('prospections', $rows);
        }
    }

    public static function repairProspectionCommercialLinks(): void
    {
        $commercialUsers = collect(self::normalizeAll(AppStore::get('utilisateurs')))
            ->filter(fn ($user) => self::isCommercial($user))
            ->values()
            ->all();

        if ($commercialUsers === []) {
            return;
        }

        $rows = AppStore::get('prospections');
        $changed = false;
        $unmatchedStrings = [];

        foreach ($rows as $index => $row) {
            $user = self::findCommercialUserForProspectionRow($row, $commercialUsers);
            if ($user !== null) {
                $userId = trim((string) ($user['id'] ?? ''));
                if ($userId !== '' && ($row['commercial_user_id'] ?? '') !== $userId) {
                    $rows[$index]['commercial_user_id'] = $userId;
                    $changed = true;
                }

                continue;
            }

            $commercial = mb_strtolower(trim((string) ($row['commercial'] ?? '')));
            if ($commercial !== '') {
                $unmatchedStrings[$commercial] = true;
            }
        }

        $unmatchedStrings = array_keys($unmatchedStrings);
        $unmatchedUsers = collect($commercialUsers)
            ->filter(function ($user) use ($rows) {
                foreach ($rows as $row) {
                    if (self::prospectionRowMatchesCommercialUser($row, $user)) {
                        return false;
                    }
                }

                return true;
            })
            ->values()
            ->all();

        if (count($unmatchedStrings) === 1 && count($unmatchedUsers) === 1) {
            $commercial = $unmatchedStrings[0];
            $user = $unmatchedUsers[0];
            $userId = trim((string) ($user['id'] ?? ''));

            if ($userId !== '') {
                $aliases = collect((array) ($user['aliases'] ?? []))
                    ->push($commercial)
                    ->map(fn ($alias) => mb_strtolower(trim((string) $alias)))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                foreach ($commercialUsers as $i => $commercialUser) {
                    if (($commercialUser['id'] ?? '') === $userId) {
                        $commercialUsers[$i]['aliases'] = $aliases;
                        break;
                    }
                }

                $utilisateurs = self::normalizeAll(AppStore::get('utilisateurs'));
                foreach ($utilisateurs as $i => $utilisateur) {
                    if (($utilisateur['id'] ?? '') === $userId) {
                        $utilisateurs[$i]['aliases'] = $aliases;
                        break;
                    }
                }
                AppStore::put('utilisateurs', $utilisateurs);

                foreach ($rows as $index => $row) {
                    if (mb_strtolower(trim((string) ($row['commercial'] ?? ''))) === $commercial) {
                        $rows[$index]['commercial_user_id'] = $userId;
                        $changed = true;
                    }
                }
            }
        }

        if ($changed) {
            AppStore::put('prospections', $rows);
        }
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
