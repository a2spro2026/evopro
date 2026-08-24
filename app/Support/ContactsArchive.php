<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class ContactsArchive
{
    public static function folder(): string
    {
        return base_path('contacts-archive');
    }

    /**
     * @return list<array{nom: string, telephone: string, source: string, ref: string, ville: string}>
     */
    public static function collect(?array $data = null): array
    {
        $data = $data ?? AppStore::all();
        $contacts = [];
        $seen = [];

        foreach ($data['relances'] ?? [] as $row) {
            self::pushContact($contacts, $seen, [
                'nom' => trim((string) ($row['nom_complet'] ?? '')),
                'telephone' => trim((string) ($row['telephone'] ?? '')),
                'source' => 'relance',
                'ref' => trim((string) ($row['ref'] ?? '')),
                'ville' => trim((string) ($row['ville'] ?? '')),
            ]);
        }

        foreach ($data['clients'] ?? [] as $row) {
            self::pushContact($contacts, $seen, [
                'nom' => trim((string) ($row['nom'] ?? '')),
                'telephone' => trim((string) ($row['contact'] ?? '')),
                'source' => 'client',
                'ref' => trim((string) ($row['ref'] ?? '')),
                'ville' => trim((string) ($row['ville'] ?? '')),
            ]);
        }

        foreach ($data['whatsapp_messages'] ?? [] as $row) {
            self::pushContact($contacts, $seen, [
                'nom' => trim((string) ($row['nom_complet'] ?? '')),
                'telephone' => trim((string) ($row['telephone'] ?? '')),
                'source' => 'whatsapp',
                'ref' => '',
                'ville' => '',
            ]);
        }

        usort($contacts, fn ($a, $b) => strcasecmp($a['nom'], $b['nom']) ?: strcasecmp($a['telephone'], $b['telephone']));

        return $contacts;
    }

    /**
     * @return list<array{nom: string, telephone: string, source: string, ref: string, ville: string}>
     */
    public static function export(?array $data = null): array
    {
        $contacts = self::collect($data);
        $dir = self::folder();

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(
            $dir.'/contacts.json',
            json_encode([
                'exported_at' => now()->toIso8601String(),
                'count' => count($contacts),
                'contacts' => $contacts,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $csvLines = ["nom;telephone;source;ref;ville"];
        foreach ($contacts as $contact) {
            $csvLines[] = implode(';', array_map(
                fn ($value) => str_replace(';', ',', (string) $value),
                [$contact['nom'], $contact['telephone'], $contact['source'], $contact['ref'], $contact['ville']]
            ));
        }

        File::put($dir.'/contacts.csv', implode("\n", $csvLines)."\n");

        return $contacts;
    }

    /**
     * @param  list<array{nom: string, telephone: string, source: string, ref: string, ville: string}>  $contacts
     * @param  array<string, true>  $seen
     * @param  array{nom: string, telephone: string, source: string, ref: string, ville: string}  $contact
     */
    private static function pushContact(array &$contacts, array &$seen, array $contact): void
    {
        $nom = $contact['nom'];
        $telephone = $contact['telephone'];

        if ($nom === '' && ($telephone === '' || $telephone === '—')) {
            return;
        }

        $key = self::phoneKey($telephone) ?: mb_strtolower($nom);
        if ($key === '' || isset($seen[$key])) {
            return;
        }

        $seen[$key] = true;
        $contacts[] = $contact;
    }

    private static function phoneKey(string $telephone): string
    {
        $digits = self::normalizePhoneDigits($telephone);

        return $digits;
    }

    /**
     * @return list<array{nom: string, telephone: string, source: string, ref: string, ville: string}>
     */
    public static function readExported(): array
    {
        $path = self::folder().'/contacts.json';

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded['contacts'] ?? null)) {
            return [];
        }

        return array_values($decoded['contacts']);
    }

    /**
     * @return list<array{nom: string, telephone: string, source: string, ref: string, ville: string}>
     */
    public static function phonesFromData(?array $data = null): array
    {
        return self::collect($data);
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public static function importPhonesFromData(?array $data = null): array
    {
        $data = $data ?? AppStore::all();
        $relances = AppStore::get('relances');
        $existingPhones = collect($relances)
            ->map(fn ($row) => self::normalizePhoneDigits((string) ($row['telephone'] ?? '')))
            ->filter()
            ->values()
            ->all();

        $today = now()->format('d/m/Y');
        $created = 0;
        $skipped = 0;

        foreach (self::phonesFromData($data) as $contact) {
            $telephone = trim((string) ($contact['telephone'] ?? ''));
            $digits = self::normalizePhoneDigits($telephone);

            if ($digits === '') {
                continue;
            }

            if (in_array($digits, $existingPhones, true)) {
                $skipped++;

                continue;
            }

            $n = count($relances) + 1;
            $relances[] = [
                'id' => uniqid('rel_', true),
                'date' => $today,
                'ref' => 'REL-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
                'nom_complet' => trim((string) ($contact['nom'] ?? '')),
                'telephone' => $telephone,
                'ville' => trim((string) ($contact['ville'] ?? '')),
                'titre_projet' => '',
                'description' => '',
                'vendeur' => '',
                'envoye' => 'lien',
                'statue' => 'a_voir',
                'a_rappeler' => 'non',
                'date_rappel' => '../../2026',
                'client_id' => null,
                'from_archive' => true,
                'import_source' => trim((string) ($contact['source'] ?? '')),
            ];

            $existingPhones[] = $digits;
            $created++;
        }

        if ($created > 0) {
            AppStore::put('relances', $relances);
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public static function importPhonesToRelances(): array
    {
        $snapshotPath = storage_path('app/evopro_data.production.json');

        if (File::exists($snapshotPath)) {
            $decoded = json_decode(File::get($snapshotPath), true);

            if (is_array($decoded)) {
                return self::importPhonesFromData($decoded);
            }
        }

        return self::importPhonesFromData([
            'relances' => array_map(
                fn ($contact) => [
                    'nom_complet' => $contact['nom'] ?? '',
                    'telephone' => $contact['telephone'] ?? '',
                    'ref' => $contact['ref'] ?? '',
                    'ville' => $contact['ville'] ?? '',
                ],
                self::readExported()
            ),
        ]);
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public static function importRelancesToProspections(): array
    {
        $prospections = AppStore::get('prospections');
        $existingPhones = collect($prospections)
            ->map(fn ($row) => self::normalizePhoneDigits((string) ($row['telephone'] ?? '')))
            ->filter()
            ->values()
            ->all();

        $today = now()->format('d/m/Y');
        $created = 0;
        $skipped = 0;

        foreach (AppStore::get('relances') as $relance) {
            $telephone = trim((string) ($relance['telephone'] ?? ''));
            $digits = self::normalizePhoneDigits($telephone);

            if ($digits === '') {
                continue;
            }

            if (in_array($digits, $existingPhones, true)) {
                $skipped++;

                continue;
            }

            $prospections[] = [
                'id' => uniqid('pros_', true),
                'date' => trim((string) ($relance['date'] ?? '')) ?: $today,
                'commercial' => trim((string) ($relance['vendeur'] ?? '')),
                'telephone' => $telephone,
                'nom_prospect' => trim((string) ($relance['nom_complet'] ?? '')),
                'ville' => trim((string) ($relance['ville'] ?? '')),
                'projet' => trim((string) ($relance['titre_projet'] ?? '')),
                'remarque' => '',
                'statue' => 'en_attente',
                'date_rappel' => '',
                'from_relance_import' => true,
            ];

            $existingPhones[] = $digits;
            $created++;
        }

        if ($created > 0) {
            AppStore::put('prospections', $prospections);
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public static function importClientsFromProduction(): array
    {
        $path = storage_path('app/evopro_data.production.json');

        if (! File::exists($path)) {
            return ['created' => 0, 'skipped' => 0];
        }

        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded)) {
            return ['created' => 0, 'skipped' => 0];
        }

        $clients = AppStore::get('clients');
        $existingIds = collect($clients)->pluck('id')->filter()->flip()->all();
        $created = 0;
        $skipped = 0;

        foreach ($decoded['clients'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = (string) ($row['id'] ?? '');

            if ($id !== '' && isset($existingIds[$id])) {
                $skipped++;

                continue;
            }

            $clients[] = self::normalizeClientRow($row);

            if ($id !== '') {
                $existingIds[$id] = true;
            }

            $created++;
        }

        if ($created > 0) {
            AppStore::put('clients', $clients);
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    /**
     * @return array<string, mixed>
     */
    public static function normalizeClientRow(array $row): array
    {
        $titre = trim((string) ($row['titre_projet'] ?? $row['activite'] ?? ''));

        return [
            'id' => $row['id'] ?? uniqid('cli_', true),
            'date' => trim((string) ($row['date'] ?? '')) ?: now()->format('d/m/Y'),
            'ref' => trim((string) ($row['ref'] ?? '')),
            'nom' => trim((string) ($row['nom'] ?? '')),
            'titre_projet' => $titre,
            'delai_travail' => self::formatDelaiTravail($row['delai_travail'] ?? ''),
            'budget' => (float) ($row['budget'] ?? 0),
            'ville' => trim((string) ($row['ville'] ?? '')),
            'contact' => trim((string) ($row['contact'] ?? '')),
            'activite' => $titre,
            'solde' => (float) ($row['solde'] ?? 0),
        ];
    }

    public static function formatDelaiTravail(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $core = trim((string) preg_replace('/\s*(jrs?|jours?)\s*$/iu', '', $value));
        if ($core === '') {
            return '';
        }

        return $core.' JRS';
    }

    public static function isDateRappelDue(?string $date): bool
    {
        $date = trim((string) $date);
        if (! preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $parts)) {
            return false;
        }

        $dueAt = mktime(0, 0, 0, (int) $parts[2], (int) $parts[1], (int) $parts[3]);

        return $dueAt !== false && $dueAt <= strtotime('today');
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function transferProspectionToClient(array $prospection): ?array
    {
        $nom = trim((string) ($prospection['nom_prospect'] ?? ''));
        $telephone = trim((string) ($prospection['telephone'] ?? ''));
        $phoneKey = self::normalizePhoneDigits($telephone);

        if ($nom === '' && $phoneKey === '') {
            return null;
        }

        $clients = AppStore::get('clients');
        $index = collect($clients)->search(function ($client) use ($phoneKey, $nom) {
            if ($phoneKey !== '' && self::normalizePhoneDigits((string) ($client['contact'] ?? '')) === $phoneKey) {
                return true;
            }

            return $nom !== '' && mb_strtolower(trim((string) ($client['nom'] ?? ''))) === mb_strtolower($nom);
        });

        if ($index !== false) {
            $clients[$index]['contact'] = $telephone !== '' ? $telephone : ($clients[$index]['contact'] ?? '');
            $clients[$index]['ville'] = trim((string) ($prospection['ville'] ?? '')) ?: ($clients[$index]['ville'] ?? '');
            $clients[$index]['titre_projet'] = trim((string) ($prospection['projet'] ?? '')) ?: ($clients[$index]['titre_projet'] ?? '');
            $clients[$index]['activite'] = $clients[$index]['titre_projet'];
            $clients[$index]['prospection_id'] = $prospection['id'] ?? ($clients[$index]['prospection_id'] ?? '');
            AppStore::put('clients', $clients);

            return $clients[$index];
        }

        $n = count($clients) + 1;
        $client = self::normalizeClientRow([
            'id' => uniqid('cli_', true),
            'date' => trim((string) ($prospection['date'] ?? '')) ?: now()->format('d/m/Y'),
            'ref' => 'CLI-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
            'nom' => $nom !== '' ? $nom : 'Prospect',
            'contact' => $telephone,
            'ville' => trim((string) ($prospection['ville'] ?? '')),
            'titre_projet' => trim((string) ($prospection['projet'] ?? '')),
            'delai_travail' => '',
            'budget' => 0,
        ]);
        $client['prospection_id'] = $prospection['id'] ?? '';

        $clients[] = $client;
        AppStore::put('clients', $clients);

        return $client;
    }

    private static function normalizePhoneDigits(string $telephone): string
    {
        $digits = preg_replace('/\D+/', '', $telephone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '212') && strlen($digits) >= 11) {
            $digits = '0'.substr($digits, 3);
        }

        if (strlen($digits) === 9 && preg_match('/^[5-8]\d{8}$/', $digits)) {
            $digits = '0'.$digits;
        }

        return $digits;
    }
}
