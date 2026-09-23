<?php

namespace App\Support;

class ProspectionHelper
{
    public const PAGE_SIZE = 50;

    /**
     * @return list<string>
     */
    public static function extractPhoneNumbers(string $text): array
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = str_replace(['–', '—', '−', '•', '·', '|'], ['-', '-', '-', ' ', ' ', ' '], $text);
        // OCR confusions on digit-like glyphs
        $text = preg_replace('/[OoQ]/u', '0', $text) ?? $text;
        $text = preg_replace('/(?<=[\d\s\-+(])[Il|](?=[\d\s\-+)])/u', '1', $text) ?? $text;
        $text = preg_replace('/(?<=\d)[Bb](?=\d)/u', '8', $text) ?? $text;
        $text = preg_replace('/(?<=\d)[Ss](?=\d)/u', '5', $text) ?? $text;

        $found = [];

        $patterns = [
            '/(?:\+212|00212|212)[\s\-_.]*[567]\d(?:[\s\-_.]?\d){7}/u',
            '/(?<!\d)0[567]\d(?:[\s\-_.]?\d){7}(?!\d)/u',
            '/(?<!\d)[567]\d(?:[\s\-_.]?\d){7}(?!\d)/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[0] as $match) {
                    $normalized = self::normalizePhoneDisplay($match);
                    if ($normalized !== '' && self::isValidMoroccoMobile($normalized)) {
                        $found[] = $normalized;
                    }
                }
            }
        }

        // Line-by-line: recover numbers OCR split with spaces/newlines
        foreach (preg_split('/\R+/', $text) ?: [] as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $digits = preg_replace('/\D+/', '', $line) ?? '';
            $digitLen = strlen($digits);

            if (in_array($digitLen, [9, 10, 12], true)) {
                $normalized = self::normalizePhoneDisplay($digits);
                if ($normalized !== '' && self::isValidMoroccoMobile($normalized)) {
                    $found[] = $normalized;
                }
            }

            // Several numbers on one line separated by spaces
            if (preg_match_all('/(?:\+?212|0)?[567]\d[\d\s\-_.]{7,16}/u', $line, $lineMatches)) {
                foreach ($lineMatches[0] as $match) {
                    $normalized = self::normalizePhoneDisplay($match);
                    if ($normalized !== '' && self::isValidMoroccoMobile($normalized)) {
                        $found[] = $normalized;
                    }
                }
            }
        }

        if (preg_match_all('/[\d\s\-+()._]{8,24}/u', $text, $blocks)) {
            foreach ($blocks[0] as $block) {
                if (! preg_match('/[567]/', $block)) {
                    continue;
                }

                $digits = preg_replace('/\D+/', '', $block) ?? '';
                $digitLen = strlen($digits);

                if ($digitLen < 9 || $digitLen > 13) {
                    continue;
                }

                // Prefer exact mobile lengths to avoid glued false positives
                if (! in_array($digitLen, [9, 10, 12, 13], true)) {
                    continue;
                }

                $normalized = self::normalizePhoneDisplay($digits);
                if ($normalized !== '' && self::isValidMoroccoMobile($normalized)) {
                    $found[] = $normalized;
                }
            }
        }

        return array_values(array_unique($found));
    }

    public static function isValidMoroccoMobile(string $telephone): bool
    {
        $digits = self::normalizePhoneDigits($telephone);

        return strlen($digits) === 10
            && str_starts_with($digits, '0')
            && in_array($digits[1] ?? '', ['5', '6', '7'], true);
    }

    public static function normalizePhoneDigits(string $telephone): string
    {
        $digits = preg_replace('/\D+/', '', $telephone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00212')) {
            $digits = substr($digits, 2);
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
            return '';
        }

        if (! in_array($digits[1] ?? '', ['5', '6', '7'], true)) {
            return '';
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
        $page = (int) ($data['page'] ?? 1);
        $num = (int) ($data['num'] ?? 0);

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
            'statue' => in_array($statue, ['valide', 'confirme', 'en_attente', 'annule', 'reporte'], true) ? $statue : 'en_attente',
            'date_rappel' => trim((string) ($data['date_rappel'] ?? '')),
            'from_commercial_import' => (bool) ($data['from_commercial_import'] ?? false),
            'page' => max(1, $page),
            'num' => max(0, $num),
            'import_batch_id' => trim((string) ($data['import_batch_id'] ?? '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function createRow(
        string $commercial,
        string $telephone,
        ?string $date = null,
        int $page = 1,
        int $num = 0,
        string $batchId = '',
        string $nomProspect = ''
    ): array {
        return self::createRelanceRow([
            'commercial' => $commercial,
            'telephone' => $telephone,
            'date' => $date,
            'nom_prospect' => $nomProspect,
            'from_commercial_import' => true,
            'page' => $page,
            'num' => $num,
            'import_batch_id' => $batchId,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public static function rowsForCommercial(array $rows, string $commercial, ?string $commercialUserId = null): array
    {
        $commercial = self::resolveCommercialName($commercial);
        $commercialUserId ??= self::resolveCommercialUserId($commercial);
        $commercialKey = mb_strtolower(trim($commercial));

        return collect($rows)
            ->filter(function ($row) use ($commercialKey, $commercialUserId) {
                if ($commercialUserId !== null && ($row['commercial_user_id'] ?? '') === $commercialUserId) {
                    return true;
                }

                return mb_strtolower(trim((string) ($row['commercial'] ?? ''))) === $commercialKey;
            })
            ->values()
            ->all();
    }

    public static function nextPageForCommercial(array $rows, string $commercial, ?string $commercialUserId = null): int
    {
        $owned = self::rowsForCommercial($rows, $commercial, $commercialUserId);
        $maxPage = 0;

        foreach ($owned as $row) {
            $maxPage = max($maxPage, (int) ($row['page'] ?? 0));
        }

        return $maxPage + 1;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  list<string>  $telephones
     * @return array{created: int, skipped: int, rows: list<array<string, mixed>>, pages: list<int>, page_from: int, page_to: int}
     */
    public static function appendNumbersForCommercial(
        array $rows,
        string $commercial,
        array $telephones,
        ?string $date = null,
        bool $forceNewPage = true,
        string $nomProspect = ''
    ): array {
        $commercial = self::resolveCommercialName($commercial);
        $commercialUserId = self::resolveCommercialUserId($commercial);
        $existing = collect(self::rowsForCommercial($rows, $commercial, $commercialUserId))
            ->map(fn ($row) => self::normalizePhoneDigits((string) ($row['telephone'] ?? '')))
            ->filter()
            ->flip();

        $uniquePhones = [];
        $skipped = 0;

        foreach ($telephones as $telephone) {
            $telephone = trim((string) $telephone);
            if ($telephone === '') {
                continue;
            }

            $display = self::normalizePhoneDisplay($telephone);
            $digits = self::normalizePhoneDigits($display !== '' ? $display : $telephone);
            if ($digits === '' || ! self::isValidMoroccoMobile($digits)) {
                $skipped++;

                continue;
            }

            if ($existing->has($digits) || isset($uniquePhones[$digits])) {
                $skipped++;

                continue;
            }

            $uniquePhones[$digits] = $display !== '' ? $display : self::normalizePhoneDisplay($digits);
        }

        if ($uniquePhones === []) {
            return [
                'created' => 0,
                'skipped' => $skipped,
                'rows' => [],
                'pages' => [],
                'page_from' => 0,
                'page_to' => 0,
            ];
        }

        $page = $forceNewPage
            ? self::nextPageForCommercial($rows, $commercial, $commercialUserId)
            : max(1, self::nextPageForCommercial($rows, $commercial, $commercialUserId) - 1);

        if (! $forceNewPage) {
            $owned = self::rowsForCommercial($rows, $commercial, $commercialUserId);
            $lastPage = 0;
            $countOnLast = 0;
            foreach ($owned as $row) {
                $p = (int) ($row['page'] ?? 1);
                if ($p > $lastPage) {
                    $lastPage = $p;
                    $countOnLast = 1;
                } elseif ($p === $lastPage) {
                    $countOnLast++;
                }
            }
            if ($lastPage === 0 || $countOnLast >= self::PAGE_SIZE) {
                $page = $lastPage + 1;
            } else {
                $page = $lastPage;
            }
        }

        $batchId = uniqid('batch_', true);
        $created = 0;
        $newRows = [];
        $pagesUsed = [];
        $numOnPage = 0;

        if (! $forceNewPage) {
            $owned = self::rowsForCommercial($rows, $commercial, $commercialUserId);
            foreach ($owned as $row) {
                if ((int) ($row['page'] ?? 0) === $page) {
                    $numOnPage = max($numOnPage, (int) ($row['num'] ?? 0));
                }
            }
        }

        $pageFrom = $page;

        foreach ($uniquePhones as $digits => $display) {
            if ($numOnPage >= self::PAGE_SIZE) {
                $page++;
                $numOnPage = 0;
            }

            $numOnPage++;
            $row = self::createRow($commercial, $display, $date, $page, $numOnPage, $batchId, $nomProspect);
            if ($commercialUserId !== null) {
                $row['commercial_user_id'] = $commercialUserId;
            }

            $rows[] = $row;
            $newRows[] = $row;
            $existing->put($digits, true);
            $pagesUsed[$page] = true;
            $created++;
        }

        if ($created > 0) {
            AppStore::put('prospections', $rows);
        }

        $pagesList = array_keys($pagesUsed);
        sort($pagesList);

        return [
            'created' => $created,
            'skipped' => $skipped,
            'rows' => $newRows,
            'pages' => $pagesList,
            'page_from' => $pageFrom,
            'page_to' => $page,
        ];
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

    /**
     * Backfill page + num (1..50) per commercial for legacy rows.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function migratePageFields(array $rows): array
    {
        $needs = false;
        foreach ($rows as $row) {
            if (! isset($row['page']) || ! isset($row['num']) || (int) ($row['num'] ?? 0) < 1) {
                $needs = true;
                break;
            }
        }

        if (! $needs) {
            return $rows;
        }

        $grouped = [];
        foreach ($rows as $index => $row) {
            $key = trim((string) ($row['commercial_user_id'] ?? ''));
            if ($key === '') {
                $key = mb_strtolower(trim((string) ($row['commercial'] ?? ''))) ?: '_none_';
            }
            $grouped[$key][] = $index;
        }

        foreach ($grouped as $indexes) {
            usort($indexes, function ($a, $b) use ($rows) {
                $pa = (int) ($rows[$a]['page'] ?? 0);
                $pb = (int) ($rows[$b]['page'] ?? 0);
                if ($pa !== $pb && $pa > 0 && $pb > 0) {
                    return $pa <=> $pb;
                }

                $da = (string) ($rows[$a]['date'] ?? '');
                $db = (string) ($rows[$b]['date'] ?? '');
                $cmp = strcmp($da, $db);
                if ($cmp !== 0) {
                    return $cmp;
                }

                return strcmp((string) ($rows[$a]['id'] ?? ''), (string) ($rows[$b]['id'] ?? ''));
            });

            $page = 1;
            $num = 0;
            foreach ($indexes as $index) {
                if ($num >= self::PAGE_SIZE) {
                    $page++;
                    $num = 0;
                }
                $num++;
                $rows[$index]['page'] = $page;
                $rows[$index]['num'] = $num;
                if (! isset($rows[$index]['import_batch_id'])) {
                    $rows[$index]['import_batch_id'] = '';
                }
            }
        }

        AppStore::put('prospections', $rows);

        return $rows;
    }
}
