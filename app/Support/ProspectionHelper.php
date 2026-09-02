<?php

namespace App\Support;

class ProspectionHelper
{
    /**
     * @return list<string>
     */
    public static function extractPhoneNumbers(string $text): array
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(['–', '—', '−'], '-', $text);
        $text = preg_replace('/[Oo]/u', '0', $text) ?? $text;
        $text = preg_replace('/(?<=[\d\s\-+])[Il|]/u', '1', $text) ?? $text;

        $found = [];

        $patterns = [
            '/(?:\+212|00212|212)[\s\-]*[567]\d(?:[\s\-]?\d){7}/u',
            '/(?<!\d)0[567]\d(?:[\s\-]?\d){7}(?!\d)/u',
            '/(?<!\d)[567]\d(?:[\s\-]?\d){7}(?!\d)/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[0] as $match) {
                    $normalized = self::normalizePhoneDisplay($match);
                    if ($normalized !== '') {
                        $found[] = $normalized;
                    }
                }
            }
        }

        if (preg_match_all('/[\d\s\-+().]{9,22}/u', $text, $blocks)) {
            foreach ($blocks[0] as $block) {
                if (! preg_match('/[567]/', $block)) {
                    continue;
                }

                $digits = preg_replace('/\D+/', '', $block) ?? '';
                $digitLen = strlen($digits);

                if ($digitLen < 9 || $digitLen > 12) {
                    continue;
                }

                if (str_starts_with($digits, '212') && $digitLen !== 12) {
                    continue;
                }

                if (! str_starts_with($digits, '212') && ! in_array($digitLen, [9, 10], true)) {
                    continue;
                }

                $normalized = self::normalizePhoneDisplay($digits);
                if ($normalized !== '') {
                    $found[] = $normalized;
                }
            }
        }

        return array_values(array_unique($found));
    }

    public static function normalizePhoneDigits(string $telephone): string
    {
        $digits = preg_replace('/\D+/', '', $telephone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '212') && strlen($digits) >= 12) {
            $digits = '0'.substr($digits, 3);
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return $digits;
        }

        if (strlen($digits) === 9 && in_array($digits[0] ?? '', ['5', '6', '7'], true)) {
            return '0'.$digits;
        }

        return $digits;
    }

    public static function normalizePhoneDisplay(string $telephone): string
    {
        $digits = self::normalizePhoneDigits($telephone);

        if (strlen($digits) !== 10 || ! str_starts_with($digits, '0')) {
            return trim($telephone) !== '' && strlen(preg_replace('/\D+/', '', $telephone) ?? '') >= 9
                ? trim($telephone)
                : '';
        }

        return substr($digits, 0, 4).' '.substr($digits, 4, 2).' '.substr($digits, 6, 2).' '.substr($digits, 8, 2);
    }

    public static function resolveCommercialName(string $commercial): string
    {
        $commercial = trim($commercial);
        if ($commercial === '') {
            return '';
        }

        $user = self::resolveCommercialUser($commercial);

        return trim((string) ($user['nom_complet'] ?? $commercial));
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function resolveCommercialUser(string $commercial): ?array
    {
        $commercial = trim($commercial);
        if ($commercial === '') {
            return null;
        }

        $users = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
        $commercialUsers = collect($users)
            ->filter(fn ($user) => UtilisateurHelper::isCommercial($user))
            ->values()
            ->all();

        $probe = ['commercial' => $commercial];

        return UtilisateurHelper::findCommercialUserForProspectionRow($probe, $commercialUsers);
    }

    public static function resolveCommercialUserId(string $commercial): ?string
    {
        $user = self::resolveCommercialUser($commercial);
        $id = trim((string) ($user['id'] ?? ''));

        return $id !== '' ? $id : null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function createRelanceRow(array $data): array
    {
        $statue = trim((string) ($data['statue'] ?? 'en_attente'));

        return [
            'id' => uniqid('pros_', true),
            'date' => trim((string) ($data['date'] ?? '')) ?: now()->format('d/m/Y'),
            'commercial' => self::resolveCommercialName((string) ($data['commercial'] ?? '')),
            'commercial_user_id' => self::resolveCommercialUserId((string) ($data['commercial'] ?? '')),
            'telephone' => self::normalizePhoneDisplay((string) ($data['telephone'] ?? '')) ?: trim((string) ($data['telephone'] ?? '')),
            'nom_prospect' => trim((string) ($data['nom_prospect'] ?? '')),
            'ville' => trim((string) ($data['ville'] ?? '')),
            'projet' => trim((string) ($data['projet'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'remarque' => trim((string) ($data['remarque'] ?? '')),
            'statue' => in_array($statue, ['valide', 'en_attente', 'annule', 'reporte'], true) ? $statue : 'en_attente',
            'date_rappel' => trim((string) ($data['date_rappel'] ?? '')),
            'from_commercial_import' => (bool) ($data['from_commercial_import'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function createRow(string $commercial, string $telephone, ?string $date = null): array
    {
        return self::createRelanceRow([
            'commercial' => $commercial,
            'telephone' => $telephone,
            'date' => $date,
            'from_commercial_import' => true,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{created: int, skipped: int, rows: list<array<string, mixed>>}
     */
    public static function appendNumbersForCommercial(array $rows, string $commercial, array $telephones, ?string $date = null): array
    {
        $commercial = self::resolveCommercialName($commercial);
        $commercialUserId = self::resolveCommercialUserId($commercial);
        $commercialKey = mb_strtolower(trim($commercial));
        $existing = collect($rows)
            ->filter(function ($row) use ($commercialKey, $commercialUserId) {
                if ($commercialUserId !== null && ($row['commercial_user_id'] ?? '') === $commercialUserId) {
                    return true;
                }

                return mb_strtolower(trim((string) ($row['commercial'] ?? ''))) === $commercialKey;
            })
            ->map(fn ($row) => self::normalizePhoneDigits((string) ($row['telephone'] ?? '')))
            ->filter()
            ->flip();

        $created = 0;
        $skipped = 0;
        $newRows = [];

        foreach ($telephones as $telephone) {
            $telephone = trim((string) $telephone);
            if ($telephone === '') {
                continue;
            }

            $digits = self::normalizePhoneDigits($telephone);
            if ($digits === '') {
                $skipped++;

                continue;
            }

            if ($existing->has($digits)) {
                $skipped++;

                continue;
            }

            $row = self::createRow($commercial, $telephone, $date);
            $rows[] = $row;
            $newRows[] = $row;
            $existing->put($digits, true);
            $created++;
        }

        if ($created > 0) {
            AppStore::put('prospections', $rows);
        }

        return ['created' => $created, 'skipped' => $skipped, 'rows' => $newRows];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function migrateDescriptionFields(array $rows): array
    {
        $changed = false;

        foreach ($rows as &$row) {
            if (array_key_exists('description', $row)) {
                continue;
            }

            $row['description'] = trim((string) ($row['remarque'] ?? ''));
            $row['remarque'] = '';
            $changed = true;
        }
        unset($row);

        if ($changed) {
            AppStore::put('prospections', $rows);
        }

        return $rows;
    }
}
