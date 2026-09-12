<?php

namespace App\Support;

class NumerosEntrantsHelper
{
    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function normalizeRow(array $row): array
    {
        $telephoneRaw = trim((string) ($row['telephone'] ?? ''));
        $telephone = ProspectionHelper::normalizePhoneDisplay($telephoneRaw) ?: $telephoneRaw;
        $digits = ProspectionHelper::normalizePhoneDigits($telephone);
        $statue = trim((string) ($row['statue'] ?? 'nouveau'));

        if (! in_array($statue, ['nouveau', 'reparti'], true)) {
            $statue = 'nouveau';
        }

        return [
            'id' => trim((string) ($row['id'] ?? '')) ?: uniqid('in_', true),
            'date' => trim((string) ($row['date'] ?? '')) ?: now()->format('d/m/Y'),
            'telephone' => $telephone,
            'telephone_digits' => $digits,
            'source' => trim((string) ($row['source'] ?? 'manuel')) ?: 'manuel',
            'statue' => $statue,
            'commercial' => trim((string) ($row['commercial'] ?? '')),
            'commercial_user_id' => trim((string) ($row['commercial_user_id'] ?? '')),
            'prospection_id' => trim((string) ($row['prospection_id'] ?? '')),
            'reparti_at' => trim((string) ($row['reparti_at'] ?? '')),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  list<string>  $telephones
     * @return array{created: int, skipped: int, rows: list<array<string, mixed>>}
     */
    public static function addNumbers(array $rows, array $telephones, string $source = 'manuel', ?string $date = null): array
    {
        $existing = collect($rows)
            ->map(fn ($row) => ProspectionHelper::normalizePhoneDigits((string) ($row['telephone'] ?? $row['telephone_digits'] ?? '')))
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

            $digits = ProspectionHelper::normalizePhoneDigits($telephone);
            if ($digits === '') {
                $skipped++;

                continue;
            }

            if ($existing->has($digits)) {
                $skipped++;

                continue;
            }

            $row = self::normalizeRow([
                'telephone' => $telephone,
                'source' => $source,
                'date' => $date,
                'statue' => 'nouveau',
            ]);

            $rows[] = $row;
            $newRows[] = $row;
            $existing->put($digits, true);
            $created++;
        }

        if ($created > 0) {
            AppStore::put('numeros_entrants', $rows);
        }

        return ['created' => $created, 'skipped' => $skipped, 'rows' => $newRows];
    }

    /**
     * @param  array<int, array<string, mixed>>  $inbox
     * @param  list<string>  $ids
     * @return array{distributed: int, skipped: int, rows: list<array<string, mixed>>, inbox: list<array<string, mixed>>}
     */
    public static function distributeToCommercial(array $inbox, array $ids, string $commercial, ?string $date = null): array
    {
        $ids = array_values(array_filter(array_map('strval', $ids)));
        $idSet = array_fill_keys($ids, true);

        $toDistribute = [];
        foreach ($inbox as $row) {
            $id = (string) ($row['id'] ?? '');
            if ($id === '' || ! isset($idSet[$id])) {
                continue;
            }
            if (($row['statue'] ?? '') === 'reparti') {
                continue;
            }
            $telephone = trim((string) ($row['telephone'] ?? ''));
            if ($telephone !== '') {
                $toDistribute[] = $telephone;
            }
        }

        if ($toDistribute === []) {
            return ['distributed' => 0, 'skipped' => count($ids), 'rows' => [], 'inbox' => $inbox];
        }

        $prospections = AppStore::get('prospections');
        $result = ProspectionHelper::appendNumbersForCommercial(
            $prospections,
            $commercial,
            $toDistribute,
            $date
        );

        $createdByPhone = [];
        foreach ($result['rows'] as $prosRow) {
            $digits = ProspectionHelper::normalizePhoneDigits((string) ($prosRow['telephone'] ?? ''));
            if ($digits !== '') {
                $createdByPhone[$digits] = $prosRow;
            }
        }

        $commercialName = ProspectionHelper::resolveCommercialName($commercial);
        $commercialUserId = ProspectionHelper::resolveCommercialUserId($commercial) ?? '';
        $now = now()->format('d/m/Y H:i');
        $distributed = 0;
        $skipped = 0;

        foreach ($inbox as &$row) {
            $id = (string) ($row['id'] ?? '');
            if ($id === '' || ! isset($idSet[$id])) {
                continue;
            }

            if (($row['statue'] ?? '') === 'reparti') {
                $skipped++;

                continue;
            }

            $digits = ProspectionHelper::normalizePhoneDigits((string) ($row['telephone'] ?? ''));
            $prosRow = $createdByPhone[$digits] ?? null;

            // Already existed in prospection: still mark as distributed to commercial.
            if ($prosRow === null && $digits !== '') {
                $existingPros = collect(AppStore::get('prospections'))->first(
                    fn ($p) => ProspectionHelper::normalizePhoneDigits((string) ($p['telephone'] ?? '')) === $digits
                        && mb_strtolower(trim((string) ($p['commercial'] ?? ''))) === mb_strtolower($commercialName)
                );
                if (is_array($existingPros)) {
                    $prosRow = $existingPros;
                }
            }

            $row['statue'] = 'reparti';
            $row['commercial'] = $commercialName;
            $row['commercial_user_id'] = $commercialUserId;
            $row['prospection_id'] = (string) ($prosRow['id'] ?? '');
            $row['reparti_at'] = $now;
            $distributed++;
        }
        unset($row);

        AppStore::put('numeros_entrants', $inbox);

        return [
            'distributed' => $distributed,
            'skipped' => $skipped + (int) ($result['skipped'] ?? 0),
            'rows' => $result['rows'],
            'inbox' => $inbox,
        ];
    }
}
