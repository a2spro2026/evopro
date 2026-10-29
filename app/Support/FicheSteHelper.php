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
            'adresse' => '',
            'ville' => '',
            'whatsapp' => '',
            'email' => '',
            'rc' => '',
            'identifiant_fiscal' => '',
            'cnss' => '',
            'patente' => '',
            'ice' => '',
            'statue' => '',
            'activite' => '',
            'fixe' => '',
            'logo' => '',
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
            'adresse' => trim((string) ($row['adresse'] ?? $defaults['adresse'])),
            'ville' => trim((string) ($row['ville'] ?? $defaults['ville'])),
            'whatsapp' => trim((string) ($row['whatsapp'] ?? $defaults['whatsapp'])),
            'email' => trim((string) ($row['email'] ?? $defaults['email'])),
            'rc' => trim((string) ($row['rc'] ?? $defaults['rc'])),
            'identifiant_fiscal' => trim((string) ($row['identifiant_fiscal'] ?? $defaults['identifiant_fiscal'])),
            'cnss' => trim((string) ($row['cnss'] ?? $defaults['cnss'])),
            'patente' => trim((string) ($row['patente'] ?? $defaults['patente'])),
            'ice' => trim((string) ($row['ice'] ?? $defaults['ice'])),
            'statue' => trim((string) ($row['statue'] ?? $defaults['statue'])),
            'activite' => trim((string) ($row['activite'] ?? $defaults['activite'])),
            'fixe' => trim((string) ($row['fixe'] ?? $defaults['fixe'])),
            'logo' => trim((string) ($row['logo'] ?? $defaults['logo'])),
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
