<?php

namespace App\Support;

class FicheSteHelper
{
    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'nom_societe' => '',
            'nom_gerant' => '',
            'contact' => '',
            'ville' => '',
            'whatsapp' => '',
            'email' => '',
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, string>
     */
    public static function normalize(array $row): array
    {
        $defaults = self::defaults();

        return [
            'nom_societe' => trim((string) ($row['nom_societe'] ?? $defaults['nom_societe'])),
            'nom_gerant' => trim((string) ($row['nom_gerant'] ?? $defaults['nom_gerant'])),
            'contact' => trim((string) ($row['contact'] ?? $defaults['contact'])),
            'ville' => trim((string) ($row['ville'] ?? $defaults['ville'])),
            'whatsapp' => trim((string) ($row['whatsapp'] ?? $defaults['whatsapp'])),
            'email' => trim((string) ($row['email'] ?? $defaults['email'])),
        ];
    }

    public static function get(): array
    {
        return self::normalize(AppStore::getConfig('fiche_ste'));
    }

    public static function save(array $row): array
    {
        $normalized = self::normalize($row);
        AppStore::putConfig('fiche_ste', $normalized);

        return $normalized;
    }
}
