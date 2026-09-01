<?php

namespace App\Support;

class ProjetHelper
{
    public const MODES = ['Vir', 'Esp', 'Chq', 'Vers'];

    public const PARTS = [10, 15, 20, 30, 50];

    /**
     * @param  list<array<string, mixed>>|null  $projets
     */
    public static function nextRef(?array $projets = null, ?int $year = null): string
    {
        $projets = $projets ?? AppStore::get('projets');
        $year = $year ?? (int) date('Y');

        $max = 0;
        foreach ($projets as $projet) {
            $ref = (string) ($projet['ref'] ?? '');
            if (preg_match('/^PR(\d+)\/'.$year.'$/', $ref, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return 'PR'.str_pad((string) ($max + 1), 2, '0', STR_PAD_LEFT).'/'.$year;
    }

    public static function computeSolde(float $budget, float $avance): float
    {
        return round(max(0, $budget - $avance), 2);
    }

    public static function resolveStatut(float $avance, ?string $existing = null): string
    {
        if ($existing === 'annule') {
            return 'annule';
        }

        return $avance > 0 ? 'actif' : 'attente';
    }

  /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function normalizeRow(array $row): array
    {
        $budget = (float) ($row['budget'] ?? 0);
        $avance = (float) ($row['avance'] ?? $row['montant_paye'] ?? 0);

        return [
            'id' => (string) ($row['id'] ?? uniqid('prj_', true)),
            'date' => (string) ($row['date'] ?? date('d/m/Y')),
            'ref' => (string) ($row['ref'] ?? ''),
            'commercial' => trim((string) ($row['commercial'] ?? '')),
            'titre_projet' => trim((string) ($row['titre_projet'] ?? $row['nom'] ?? '')),
            'nom_client' => trim((string) ($row['nom_client'] ?? $row['client'] ?? '')),
            'ville' => trim((string) ($row['ville'] ?? '')),
            'contact' => trim((string) ($row['contact'] ?? '')),
            'budget' => $budget,
            'avance' => $avance,
            'montant_paye' => $avance,
            'mode' => self::normalizeMode((string) ($row['mode'] ?? 'Vir')),
            'solde' => self::computeSolde($budget, $avance),
            'part_commercial' => self::normalizePart($row['part_commercial'] ?? 10),
            'statut' => self::resolveStatut($avance, $row['statut'] ?? null),
            'prospection_id' => $row['prospection_id'] ?? null,
        ];
    }

    public static function normalizeMode(string $mode): string
    {
        $mode = ucfirst(strtolower(trim($mode)));

        return in_array($mode, self::MODES, true) ? $mode : 'Vir';
    }

    public static function normalizePart(mixed $part): int
    {
        $part = (int) $part;

        return in_array($part, self::PARTS, true) ? $part : 10;
    }

    /**
     * @param  list<array<string, mixed>>|null  $prospections
     * @return array<string, mixed>|null
     */
    public static function findProspectionByPhone(string $phone, ?array $prospections = null): ?array
    {
        $digits = ProspectionHelper::normalizePhoneDigits($phone);
        if ($digits === '') {
            return null;
        }

        foreach ($prospections ?? AppStore::get('prospections') as $row) {
            if (ProspectionHelper::normalizePhoneDigits((string) ($row['telephone'] ?? '')) === $digits) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>|null  $clients
     * @return array<string, mixed>|null
     */
    public static function findClientByPhone(string $phone, ?array $clients = null): ?array
    {
        $digits = ProspectionHelper::normalizePhoneDigits($phone);
        if ($digits === '') {
            return null;
        }

        foreach ($clients ?? AppStore::get('clients') as $row) {
            if (ProspectionHelper::normalizePhoneDigits((string) ($row['contact'] ?? '')) === $digits) {
                return $row;
            }
        }

        return null;
    }
}
