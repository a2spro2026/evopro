<?php

use App\Support\AppStore;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$requireAuth = function () {
    if (! session()->has('auth_user')) {
        return redirect('/')
            ->withErrors(['login' => 'Veuillez vous connecter avec un utilisateur enregistré.']);
    }

    return null;
};

Route::get('/', function () {
    if (session()->has('auth_user')) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('/dashboard', function () use ($requireAuth) {
    if ($redirect = $requireAuth()) {
        return $redirect;
    }

    AppStore::hydrateFromSessionIfEmpty();

    $clients = AppStore::get('clients');
    $projets = AppStore::get('projets');

    $resolveStatut = function (array $projet): string {
        $statut = $projet['statut'] ?? '';

        if (in_array($statut, ['actif', 'attente', 'annule', 'execute'], true)) {
            return $statut;
        }

        return (float) ($projet['montant_paye'] ?? 0) > 0 ? 'actif' : 'attente';
    };

    $projets = collect($projets)
        ->map(function ($projet) use ($resolveStatut) {
            $projet['statut'] = $resolveStatut($projet);

            return $projet;
        })
        ->all();

    $soldesParClient = collect($projets)
        ->groupBy('client')
        ->map(fn ($items) => $items->sum(fn ($p) => (float) ($p['solde'] ?? 0)));

    $clients = collect($clients)
        ->map(function ($client) use ($soldesParClient) {
            $nom = $client['nom'] ?? '';
            $client['solde'] = $soldesParClient->get($nom, 0);

            return $client;
        })
        ->all();

    $paiements = AppStore::get('paiements');

    $tresorerieParProjet = collect($paiements)
        ->reverse()
        ->filter(fn ($p) => ! empty($p['projet_id']))
        ->groupBy('projet_id')
        ->map(fn ($items) => $items->first()['tresorerie'] ?? '');

    $projets = collect($projets)
        ->map(function ($projet) use ($tresorerieParProjet) {
            $projet['tresorerie'] = $tresorerieParProjet->get($projet['id'] ?? '', $projet['tresorerie'] ?? '');

            return $projet;
        })
        ->all();

    $projetsCollection = collect($projets);

    $dashboardCounts = [
        'actif' => $projetsCollection->where('statut', 'actif')->count(),
        'attente' => $projetsCollection->where('statut', 'attente')->count(),
        'annule' => $projetsCollection->where('statut', 'annule')->count(),
        'execute' => $projetsCollection->where('statut', 'execute')->count(),
    ];

    $paiementsCollection = collect($paiements);
    $sumTresorerie = function (string $nom) use ($paiementsCollection): float {
        return (float) $paiementsCollection
            ->filter(fn ($p) => mb_strtolower(trim((string) ($p['tresorerie'] ?? ''))) === mb_strtolower($nom))
            ->sum(fn ($p) => (float) ($p['increment_paye'] ?? $p['montant_paye'] ?? 0));
    };

    $menuSections = [
        [
            'key' => 'dashboard',
            'label' => 'Tableau de Bord',
            'items' => [
                ['key' => 'dashboard', 'label' => 'Tableau de Bord'],
            ],
        ],
        [
            'key' => 'client',
            'label' => 'Client',
            'items' => [
                ['key' => 'fiche-client', 'label' => 'Fiche Client'],
                ['key' => 'fiche-relance', 'label' => 'Relance'],
            ],
        ],
        [
            'key' => 'projet',
            'label' => 'Projets',
            'items' => [
                ['key' => 'fiche-projet', 'label' => 'Fiche Projet'],
                ['key' => 'fiche-evolution', 'label' => 'Evolution Travaux'],
            ],
        ],
        [
            'key' => 'paiement',
            'label' => 'Paiement',
            'items' => [
                ['key' => 'fiche-paiement', 'label' => 'Fiche Paiement'],
            ],
        ],
        [
            'key' => 'charges',
            'label' => 'Charges',
            'items' => [
                ['key' => 'charges', 'label' => 'Charges'],
            ],
        ],
        [
            'key' => 'suivie',
            'label' => 'Suivie Monétaire',
            'items' => [
                ['key' => 'suivie', 'label' => 'Suivie Monétaire'],
            ],
        ],
        [
            'key' => 'rapports',
            'label' => 'Rapports',
            'items' => [
                ['key' => 'rapports', 'label' => 'Rapports'],
            ],
        ],
        [
            'key' => 'configuration',
            'label' => 'Configuration',
            'items' => [
                ['key' => 'fiche-utilisateur', 'label' => 'Utilisateur'],
                ['key' => 'fiche-autorisation', 'label' => 'Autorisation'],
                ['key' => 'fiche-whatsapp', 'label' => 'WhatsApp'],
            ],
        ],
    ];

    $allPermissionKeys = collect($menuSections)
        ->flatMap(fn ($section) => collect($section['items'])->pluck('key'))
        ->unique()
        ->values()
        ->all();

    $authUser = session('auth_user', []);
    $authUserId = (string) ($authUser['id'] ?? '');
    $authUserStatue = (string) ($authUser['statue'] ?? '');
    $autorisationUser = collect(AppStore::get('autorisations'))
        ->first(fn ($a) => (string) ($a['utilisateur_id'] ?? '') === $authUserId);

    if ($authUserStatue === 'admin' || ! $autorisationUser) {
        $userPermissions = $allPermissionKeys;
    } else {
        $userPermissions = array_values(array_intersect(
            $allPermissionKeys,
            array_map('strval', $autorisationUser['permissions'] ?? [])
        ));
    }

    $relances = AppStore::get('relances');
    $relancesCleaned = false;
    foreach ($relances as &$relanceRow) {
        foreach (['nom_complet', 'ville', 'titre_projet', 'description'] as $placeholderField) {
            if (trim((string) ($relanceRow[$placeholderField] ?? '')) === 'À compléter') {
                $relanceRow[$placeholderField] = '';
                $relancesCleaned = true;
            }
        }
        $vendeur = trim((string) ($relanceRow['vendeur'] ?? ''));
        $legacyCommercial = trim((string) ($relanceRow['commercial'] ?? ''));
        if ($vendeur === '' && $legacyCommercial !== '') {
            $relanceRow['vendeur'] = $legacyCommercial;
            $relancesCleaned = true;
        }
        $dr = trim((string) ($relanceRow['date_rappel'] ?? ''));
        if ($dr === '') {
            $relanceRow['date_rappel'] = '../../2026';
            $relancesCleaned = true;
        } elseif (! empty($relanceRow['from_import']) && $dr === trim((string) ($relanceRow['date'] ?? ''))) {
            // Auto-filled at import (same day as creation) → empty day/month, year 2026
            $relanceRow['date_rappel'] = '../../2026';
            $relancesCleaned = true;
        }
    }
    unset($relanceRow);
    if ($relancesCleaned) {
        AppStore::put('relances', $relances);
    }

    return view('dashboard', [
        'clients' => $clients,
        'projets' => $projets,
        'paiements' => $paiements,
        'utilisateurs' => AppStore::get('utilisateurs'),
        'evolutions' => AppStore::get('evolutions'),
        'relances' => $relances,
        'autorisations' => AppStore::get('autorisations'),
        'whatsappMessages' => collect(AppStore::get('whatsapp_messages'))
            ->sortByDesc(fn ($m) => (string) ($m['sent_at_iso'] ?? $m['sent_at'] ?? ''))
            ->values()
            ->take(80)
            ->all(),
        'whatsappConfig' => (static function () {
            $cfg = WhatsApp::config();
            $cfg['has_token'] = trim((string) ($cfg['access_token'] ?? '')) !== '';
            $cfg['access_token'] = $cfg['has_token'] ? '********' : '';
            $cfg['api_ready'] = WhatsApp::hasApiCredentials(array_merge($cfg, [
                'access_token' => $cfg['has_token'] ? 'x' : '',
            ])) && ! empty($cfg['actif']) && ! empty($cfg['messages_actifs']);

            return $cfg;
        })(),
        'menuSections' => $menuSections,
        'userPermissions' => $userPermissions,
        'authUserStatue' => $authUserStatue,
        'authUserNom' => (string) ($authUser['nom_complet'] ?? ''),
        'isVendeur' => $authUserStatue === 'vendeur',
        'vendeurs' => collect(AppStore::get('utilisateurs'))
            ->filter(fn ($u) => ($u['statue'] ?? '') === 'vendeur')
            ->pluck('nom_complet')
            ->merge(collect($relances)->pluck('vendeur'))
            ->merge(collect($relances)->pluck('commercial'))
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique(fn ($n) => mb_strtolower($n))
            ->sort()
            ->values()
            ->all(),
        'dashboardCounts' => array_merge($dashboardCounts, [
            'confirme' => collect($relances)->where('statue', 'confirme')->count(),
        ]),
        'revenuAyda' => $sumTresorerie('ayda'),
        'revenuBrahim' => $sumTresorerie('brahim'),
        'totalSolde' => $projetsCollection->sum(fn ($p) => (float) ($p['solde'] ?? 0)),
        'paiementTotalBudgets' => (float) $paiementsCollection
            ->groupBy(fn ($p) => $p['projet_id'] ?? ('row-'.($p['id'] ?? '')))
            ->sum(fn ($items) => (float) ($items->first()['budget'] ?? 0)),
        'paiementTotalMontants' => (float) $paiementsCollection
            ->sum(fn ($p) => (float) ($p['increment_paye'] ?? 0)),
        'paiementTotalSoldes' => (float) $paiementsCollection
            ->groupBy(fn ($p) => $p['projet_id'] ?? ('row-'.($p['id'] ?? '')))
            ->sum(fn ($items) => (float) ($items->last()['solde'] ?? 0)),
    ]);
})->name('dashboard');

$handleConnexion = function (Request $request) {
    $data = $request->validate([
        'statue' => ['required', 'string', 'in:admin,manager,comptable,vendeur,stock'],
        'login' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $statue = $data['statue'];
    $login = trim($data['login']);
    $password = $data['password'];
    $utilisateurs = AppStore::get('utilisateurs');

    // Premier accès : crée le premier utilisateur avec les identifiants saisis.
    if ($utilisateurs === []) {
        $utilisateurs[] = [
            'id' => uniqid('usr_', true),
            'date' => now()->format('d/m/Y'),
            'nom_complet' => $login,
            'statue' => $statue,
            'login' => $login,
            'password' => $password,
        ];
        AppStore::put('utilisateurs', $utilisateurs);
    }

    $user = collect($utilisateurs)->first(function ($u) use ($statue, $login, $password) {
        return ($u['statue'] ?? '') === $statue
            && mb_strtolower(trim((string) ($u['login'] ?? ''))) === mb_strtolower($login)
            && (string) ($u['password'] ?? '') === (string) $password;
    });

    if (! $user) {
        return back()
            ->withErrors(['login' => 'Statue, login ou mot de passe incorrect.'])
            ->withInput($request->only('statue', 'login'));
    }

    $request->session()->regenerate();
    session([
        'auth_user' => [
            'id' => $user['id'] ?? '',
            'login' => $user['login'] ?? $login,
            'statue' => $user['statue'] ?? $statue,
            'nom_complet' => $user['nom_complet'] ?? '',
        ],
        'login' => $user['login'] ?? $login,
        'statue' => $user['statue'] ?? $statue,
    ]);

    return redirect()->route('dashboard');
};

Route::post('/connexion', $handleConnexion)->name('connexion');
Route::post('/', $handleConnexion);

Route::post('/deconnexion', function () {
    session()->forget(['login', 'statue', 'auth_user']);
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::get('/devis/{token}', function (string $token) {
    if (! preg_match('/^[A-Za-z0-9_-]{16,80}$/', $token)) {
        abort(404);
    }

    $path = storage_path('app/devis/'.$token.'.pdf');
    if (! is_file($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="devis-evopro.pdf"',
    ]);
})->name('devis.show');

Route::middleware('auth.user')->group(function () {

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

Route::post('/clients', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'ref' => ['required', 'string'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'contact' => ['required', 'string', 'max:255'],
        'activite' => ['required', 'string', 'max:255'],
    ]);

    $clients = AppStore::get('clients');
    $clients[] = [
        'id' => uniqid('cli_', true),
        'date' => $data['date'],
        'ref' => $data['ref'],
        'nom' => $data['nom'],
        'ville' => $data['ville'],
        'contact' => $data['contact'],
        'activite' => $data['activite'],
        'solde' => 0,
    ];

    AppStore::put('clients', $clients);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_client', true);
})->name('clients.store');

Route::put('/clients/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'contact' => ['required', 'string', 'max:255'],
        'activite' => ['required', 'string', 'max:255'],
    ]);

    $clients = AppStore::get('clients');
    $index = collect($clients)->search(fn ($client) => ($client['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_client', true);
    }

    $ancienNom = $clients[$index]['nom'] ?? '';
    $nouveauNom = $data['nom'];

    $clients[$index]['date'] = $data['date'];
    $clients[$index]['nom'] = $nouveauNom;
    $clients[$index]['ville'] = $data['ville'];
    $clients[$index]['contact'] = $data['contact'];
    $clients[$index]['activite'] = $data['activite'];

    if ($ancienNom !== $nouveauNom) {
        $projets = collect(AppStore::get('projets'))
            ->map(function ($projet) use ($ancienNom, $nouveauNom) {
                if (($projet['client'] ?? '') === $ancienNom) {
                    $projet['client'] = $nouveauNom;
                }

                return $projet;
            })
            ->all();

        $paiements = collect(AppStore::get('paiements'))
            ->map(function ($paiement) use ($ancienNom, $nouveauNom) {
                if (($paiement['client'] ?? '') === $ancienNom) {
                    $paiement['client'] = $nouveauNom;
                }

                return $paiement;
            })
            ->all();

        AppStore::putMany(['projets' => $projets, 'paiements' => $paiements]);
    }

    AppStore::put('clients', $clients);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_client', true);
})->name('clients.update');

Route::delete('/clients/{id}', function (string $id) {
    $clients = collect(AppStore::get('clients'))
        ->reject(fn ($client) => ($client['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('clients', $clients);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_client', true);
})->name('clients.destroy');

Route::post('/relances', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'ref' => ['required', 'string'],
        'nom_complet' => ['required', 'string', 'max:255'],
        'telephone' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:2000'],
        'vendeur' => ['nullable', 'string', 'max:255'],
        'commercial' => ['nullable', 'string', 'max:255'],
        'envoye' => ['required', 'string', 'in:lien,conception'],
        'statue' => ['required', 'string', 'in:confirme,a_voir,inj'],
        'a_rappeler' => ['required', 'string', 'in:oui,non'],
        'date_rappel' => ['nullable', 'string', 'regex:/^(\d{2}|\.\.)\/(\d{2}|\.\.)\/\d{4}$/'],
    ]);

    if (($data['a_rappeler'] ?? '') !== 'oui') {
        $data['date_rappel'] = '../../2026';
    } elseif (empty($data['date_rappel'])) {
        $data['date_rappel'] = '../../2026';
    }

    $clientId = null;
    if ($data['statue'] === 'confirme') {
        $clients = AppStore::get('clients');
        $nom = trim($data['nom_complet']);
        $existing = collect($clients)->first(
            fn ($c) => mb_strtolower(trim((string) ($c['nom'] ?? ''))) === mb_strtolower($nom)
        );

        if ($existing) {
            $clientId = $existing['id'] ?? null;
        } else {
            $clientId = uniqid('cli_', true);
            $clients[] = [
                'id' => $clientId,
                'date' => $data['date'],
                'ref' => 'CLI-'.str_pad((string) (count($clients) + 1), 4, '0', STR_PAD_LEFT),
                'nom' => $nom,
                'ville' => $data['ville'],
                'contact' => $data['telephone'],
                'activite' => $data['titre_projet'],
                'solde' => 0,
            ];
            AppStore::put('clients', $clients);
        }
    }

    $relances = AppStore::get('relances');
    $relances[] = [
        'id' => uniqid('rel_', true),
        'date' => $data['date'],
        'ref' => $data['ref'],
        'nom_complet' => $data['nom_complet'],
        'telephone' => $data['telephone'],
        'ville' => $data['ville'],
        'titre_projet' => $data['titre_projet'],
        'description' => $data['description'],
        'vendeur' => trim((string) ($data['vendeur'] ?? $data['commercial'] ?? '')),
        'budget' => 0.0,
        'envoye' => $data['envoye'],
        'statue' => $data['statue'],
        'a_rappeler' => $data['a_rappeler'],
        'date_rappel' => $data['date_rappel'],
        'client_id' => $clientId,
    ];

    AppStore::put('relances', $relances);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.store');

Route::post('/relances/import', function (Request $request) {
    $data = $request->validate([
        'telephones' => ['required', 'array', 'min:1', 'max:200'],
        'telephones.*' => ['required', 'string', 'max:40'],
    ]);

    $relances = AppStore::get('relances');
    $today = now()->format('d/m/Y');
    $existingPhones = collect($relances)
        ->map(function ($r) {
            $digits = preg_replace('/\D+/', '', (string) ($r['telephone'] ?? '')) ?? '';
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
        })
        ->filter()
        ->values()
        ->all();
    $created = 0;
    $skipped = 0;

    foreach ($data['telephones'] as $telephone) {
        $phone = trim((string) $telephone);
        if ($phone === '') {
            continue;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '212') && strlen($digits) >= 11) {
            $digits = '0'.substr($digits, 3);
        }
        if (strlen($digits) === 9 && preg_match('/^[5-8]\d{8}$/', $digits)) {
            $digits = '0'.$digits;
        }

        if ($digits !== '' && in_array($digits, $existingPhones, true)) {
            $skipped++;
            continue;
        }

        $n = count($relances) + 1;
        $relances[] = [
            'id' => uniqid('rel_', true),
            'date' => $today,
            'ref' => 'REL-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
            'nom_complet' => '',
            'telephone' => $phone,
            'ville' => '',
            'titre_projet' => '',
            'description' => '',
            'vendeur' => '',
            'budget' => 0.0,
            'envoye' => 'lien',
            'statue' => 'a_voir',
            'a_rappeler' => 'non',
            'date_rappel' => '../../2026',
            'client_id' => null,
            'from_import' => true,
        ];
        if ($digits !== '') {
            $existingPhones[] = $digits;
        }
        $created++;
    }

    AppStore::put('relances', $relances);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true)
        ->with('import_relance_count', $created);
})->name('relances.import');

Route::put('/relances/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom_complet' => ['required', 'string', 'max:255'],
        'telephone' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:2000'],
        'vendeur' => ['nullable', 'string', 'max:255'],
        'commercial' => ['nullable', 'string', 'max:255'],
        'envoye' => ['required', 'string', 'in:lien,conception'],
        'statue' => ['required', 'string', 'in:confirme,a_voir,inj'],
        'a_rappeler' => ['required', 'string', 'in:oui,non'],
        'date_rappel' => ['nullable', 'string', 'regex:/^(\d{2}|\.\.)\/(\d{2}|\.\.)\/\d{4}$/'],
    ]);

    if (($data['a_rappeler'] ?? '') !== 'oui') {
        $data['date_rappel'] = '../../2026';
    } elseif (empty($data['date_rappel'])) {
        $data['date_rappel'] = '../../2026';
    }

    $relances = AppStore::get('relances');
    $index = collect($relances)->search(fn ($r) => ($r['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_relance', true);
    }

    $relances[$index]['date'] = $data['date'];
    $relances[$index]['nom_complet'] = $data['nom_complet'];
    $relances[$index]['telephone'] = $data['telephone'];
    $relances[$index]['ville'] = $data['ville'];
    $relances[$index]['titre_projet'] = $data['titre_projet'];
    $relances[$index]['description'] = $data['description'];
    $relances[$index]['vendeur'] = trim((string) ($data['vendeur'] ?? $data['commercial'] ?? ''));
    $relances[$index]['envoye'] = $data['envoye'];
    $relances[$index]['statue'] = $data['statue'];
    $relances[$index]['a_rappeler'] = $data['a_rappeler'];
    $relances[$index]['date_rappel'] = $data['date_rappel'];

    if ($data['statue'] === 'confirme' && empty($relances[$index]['client_id'])) {
        $clients = AppStore::get('clients');
        $nom = trim($data['nom_complet']);
        $existing = collect($clients)->first(
            fn ($c) => mb_strtolower(trim((string) ($c['nom'] ?? ''))) === mb_strtolower($nom)
        );

        if ($existing) {
            $relances[$index]['client_id'] = $existing['id'] ?? null;
        } else {
            $clientId = uniqid('cli_', true);
            $clients[] = [
                'id' => $clientId,
                'date' => $relances[$index]['date'] ?? now()->format('d/m/Y'),
                'ref' => 'CLI-'.str_pad((string) (count($clients) + 1), 4, '0', STR_PAD_LEFT),
                'nom' => $nom,
                'ville' => $data['ville'],
                'contact' => $data['telephone'],
                'activite' => $data['titre_projet'],
                'solde' => 0,
            ];
            AppStore::put('clients', $clients);
            $relances[$index]['client_id'] = $clientId;
        }
    }

    AppStore::put('relances', $relances);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.update');

Route::patch('/relances/{id}/statue', function (Request $request, string $id) {
    $data = $request->validate([
        'statue' => ['required', 'string', 'in:confirme,a_voir,inj'],
    ]);

    $relances = AppStore::get('relances');
    $index = collect($relances)->search(fn ($r) => ($r['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_relance', true);
    }

    $relances[$index]['statue'] = $data['statue'];

    if ($data['statue'] === 'confirme' && empty($relances[$index]['client_id'])) {
        $clients = AppStore::get('clients');
        $nom = trim((string) ($relances[$index]['nom_complet'] ?? ''));
        $existing = collect($clients)->first(
            fn ($c) => mb_strtolower(trim((string) ($c['nom'] ?? ''))) === mb_strtolower($nom)
        );

        if ($existing) {
            $relances[$index]['client_id'] = $existing['id'] ?? null;
        } else {
            $clientId = uniqid('cli_', true);
            $clients[] = [
                'id' => $clientId,
                'date' => $relances[$index]['date'] ?? now()->format('d/m/Y'),
                'ref' => 'CLI-'.str_pad((string) (count($clients) + 1), 4, '0', STR_PAD_LEFT),
                'nom' => $nom,
                'ville' => $relances[$index]['ville'] ?? '',
                'contact' => $relances[$index]['telephone'] ?? '—',
                'activite' => $relances[$index]['titre_projet'] ?? '',
                'solde' => 0,
            ];
            AppStore::put('clients', $clients);
            $relances[$index]['client_id'] = $clientId;
        }
    }

    AppStore::put('relances', $relances);

    if ($request->boolean('from_dashboard')) {
        return redirect()->route('dashboard');
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.statue');

Route::patch('/relances/{id}/date-rappel', function (Request $request, string $id) {
    $data = $request->validate([
        'date_rappel' => ['required', 'string', 'regex:/^(\d{2}|\.\.)\/(\d{2}|\.\.)\/\d{4}$/'],
    ]);

    $relances = AppStore::get('relances');
    $index = collect($relances)->search(fn ($r) => ($r['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_relance', true);
    }

    $relances[$index]['date_rappel'] = $data['date_rappel'];
    AppStore::put('relances', $relances);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'id' => $id,
            'date_rappel' => $data['date_rappel'],
        ]);
    }

    if ($request->boolean('from_dashboard')) {
        return redirect()->route('dashboard');
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.date-rappel');

Route::patch('/relances/{id}/envoye', function (Request $request, string $id) {
    $data = $request->validate([
        'envoye' => ['required', 'string', 'in:lien,conception'],
    ]);

    $relances = AppStore::get('relances');
    $index = collect($relances)->search(fn ($r) => ($r['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_relance', true);
    }

    $relances[$index]['envoye'] = $data['envoye'];
    AppStore::put('relances', $relances);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'id' => $id,
            'envoye' => $data['envoye'],
        ]);
    }

    if ($request->boolean('from_dashboard')) {
        return redirect()->route('dashboard');
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.envoye');

Route::patch('/relances/{id}/a-rappeler', function (Request $request, string $id) {
    $data = $request->validate([
        'a_rappeler' => ['required', 'string', 'in:oui,non'],
    ]);

    $relances = AppStore::get('relances');
    $index = collect($relances)->search(fn ($r) => ($r['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_relance', true);
    }

    $relances[$index]['a_rappeler'] = $data['a_rappeler'];
    AppStore::put('relances', $relances);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'id' => $id,
            'a_rappeler' => $data['a_rappeler'],
        ]);
    }

    if ($request->boolean('from_dashboard')) {
        return redirect()->route('dashboard');
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.a-rappeler');

Route::patch('/relances/{id}/inline', function (Request $request, string $id) {
    $data = $request->validate([
        'field' => ['required', 'string', 'in:telephone,nom_complet,titre_projet,description,a_rappeler,ville,date,vendeur,commercial'],
        'value' => ['nullable'],
    ]);

    $field = $data['field'];
    $value = $data['value'];

    if ($field === 'date') {
        $value = trim((string) ($value ?? ''));
        if (! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return response()->json(['ok' => false, 'message' => 'Date invalide'], 422);
        }
    } elseif ($field === 'vendeur' || $field === 'commercial') {
        $value = trim((string) ($value ?? ''));
        if (mb_strlen($value) > 255) {
            return response()->json(['ok' => false, 'message' => 'Trop long'], 422);
        }
        $field = 'vendeur';
    } elseif ($field === 'a_rappeler') {
        $value = (string) $value;
        if (! in_array($value, ['oui', 'non'], true)) {
            return response()->json(['ok' => false, 'message' => 'Valeur invalide'], 422);
        }
    } elseif ($field === 'telephone') {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return response()->json(['ok' => false, 'message' => 'Champ requis'], 422);
        }
        if (mb_strlen($value) > 255) {
            return response()->json(['ok' => false, 'message' => 'Trop long'], 422);
        }
    } else {
        $value = trim((string) ($value ?? ''));
        if (mb_strlen($value) > ($field === 'description' ? 2000 : 255)) {
            return response()->json(['ok' => false, 'message' => 'Trop long'], 422);
        }
    }

    $relances = AppStore::get('relances');
    $index = collect($relances)->search(fn ($r) => ($r['id'] ?? '') === $id);

    if ($index === false) {
        return response()->json(['ok' => false, 'message' => 'Introuvable'], 404);
    }

    $relances[$index][$field] = $value;
    AppStore::put('relances', $relances);

    return response()->json([
        'ok' => true,
        'id' => $id,
        'field' => $field,
        'value' => $value,
    ]);
})->name('relances.inline');

Route::delete('/relances/{id}', function (string $id) {
    $relances = collect(AppStore::get('relances'))
        ->reject(fn ($r) => ($r['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('relances', $relances);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_relance', true);
})->name('relances.destroy');

Route::post('/relances/bulk-destroy', function (Request $request) {
    $data = $request->validate([
        'ids' => ['required', 'array', 'min:1'],
        'ids.*' => ['required', 'string', 'max:80'],
    ]);

    $ids = collect($data['ids'])->map(fn ($id) => (string) $id)->unique()->values()->all();
    $relances = collect(AppStore::get('relances'))
        ->reject(fn ($r) => in_array((string) ($r['id'] ?? ''), $ids, true))
        ->values()
        ->all();

    AppStore::put('relances', $relances);

    return response()->json([
        'ok' => true,
        'deleted' => count($ids),
        'remaining' => count($relances),
    ]);
})->name('relances.bulk-destroy');

Route::post('/utilisateurs', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string'],
        'nom_complet' => ['required', 'string', 'max:255'],
        'statue' => ['required', 'string', 'in:admin,manager,comptable,vendeur,stock'],
        'login' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'max:255'],
    ]);

    $utilisateurs = AppStore::get('utilisateurs');
    $utilisateurs[] = [
        'id' => uniqid('usr_', true),
        'date' => $data['date'],
        'nom_complet' => $data['nom_complet'],
        'statue' => $data['statue'],
        'login' => $data['login'],
        'password' => $data['password'],
    ];

    AppStore::put('utilisateurs', $utilisateurs);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_utilisateur', true);
})->name('utilisateurs.store');

Route::put('/utilisateurs/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'nom_complet' => ['required', 'string', 'max:255'],
        'statue' => ['required', 'string', 'in:admin,manager,comptable,vendeur,stock'],
        'login' => ['required', 'string', 'max:255'],
        'password' => ['nullable', 'string', 'max:255'],
    ]);

    $utilisateurs = AppStore::get('utilisateurs');
    $index = collect($utilisateurs)->search(fn ($u) => ($u['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_utilisateur', true);
    }

    $utilisateurs[$index]['nom_complet'] = $data['nom_complet'];
    $utilisateurs[$index]['statue'] = $data['statue'];
    $utilisateurs[$index]['login'] = $data['login'];

    if (! empty($data['password'])) {
        $utilisateurs[$index]['password'] = $data['password'];
    }

    AppStore::put('utilisateurs', $utilisateurs);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_utilisateur', true);
})->name('utilisateurs.update');

Route::delete('/utilisateurs/{id}', function (string $id) {
    $utilisateurs = collect(AppStore::get('utilisateurs'))
        ->reject(fn ($u) => ($u['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('utilisateurs', $utilisateurs);

    $autorisations = collect(AppStore::get('autorisations'))
        ->reject(fn ($a) => (string) ($a['utilisateur_id'] ?? '') === (string) $id)
        ->values()
        ->all();
    AppStore::put('autorisations', $autorisations);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_utilisateur', true);
})->name('utilisateurs.destroy');

Route::post('/autorisations', function (Request $request) {
    $menuSections = [
        'dashboard', 'fiche-client', 'fiche-relance', 'fiche-projet', 'fiche-evolution',
        'fiche-paiement', 'charges', 'suivie', 'rapports', 'fiche-utilisateur', 'fiche-autorisation',
        'fiche-whatsapp',
    ];

    $data = $request->validate([
        'utilisateur_id' => ['required', 'string'],
        'permissions' => ['nullable', 'array'],
        'permissions.*' => ['string', 'in:'.implode(',', $menuSections)],
    ]);

    $utilisateur = collect(AppStore::get('utilisateurs'))
        ->first(fn ($u) => (string) ($u['id'] ?? '') === (string) $data['utilisateur_id']);

    if (! $utilisateur) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_autorisation', true)
            ->withErrors(['utilisateur_id' => 'Utilisateur introuvable.']);
    }

    $permissions = array_values(array_unique($data['permissions'] ?? []));
    $autorisations = AppStore::get('autorisations');
    $index = collect($autorisations)->search(
        fn ($a) => (string) ($a['utilisateur_id'] ?? '') === (string) $data['utilisateur_id']
    );

    $row = [
        'id' => $index === false
            ? uniqid('aut_', true)
            : ($autorisations[$index]['id'] ?? uniqid('aut_', true)),
        'utilisateur_id' => $data['utilisateur_id'],
        'utilisateur_nom' => $utilisateur['nom_complet'] ?? '',
        'utilisateur_login' => $utilisateur['login'] ?? '',
        'permissions' => $permissions,
    ];

    if ($index === false) {
        $autorisations[] = $row;
    } else {
        $autorisations[$index] = $row;
    }

    AppStore::put('autorisations', $autorisations);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_autorisation', true);
})->name('autorisations.store');

Route::put('/autorisations/{id}', function (Request $request, string $id) {
    $menuSections = [
        'dashboard', 'fiche-client', 'fiche-relance', 'fiche-projet', 'fiche-evolution',
        'fiche-paiement', 'charges', 'suivie', 'rapports', 'fiche-utilisateur', 'fiche-autorisation',
        'fiche-whatsapp',
    ];

    $data = $request->validate([
        'utilisateur_id' => ['required', 'string'],
        'permissions' => ['nullable', 'array'],
        'permissions.*' => ['string', 'in:'.implode(',', $menuSections)],
    ]);

    $utilisateur = collect(AppStore::get('utilisateurs'))
        ->first(fn ($u) => (string) ($u['id'] ?? '') === (string) $data['utilisateur_id']);

    if (! $utilisateur) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_autorisation', true);
    }

    $autorisations = AppStore::get('autorisations');
    $index = collect($autorisations)->search(fn ($a) => ($a['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_autorisation', true);
    }

    // Si on change d'utilisateur, éviter les doublons.
    $other = collect($autorisations)->search(
        fn ($a, $i) => $i !== $index && (string) ($a['utilisateur_id'] ?? '') === (string) $data['utilisateur_id']
    );
    if ($other !== false) {
        unset($autorisations[$other]);
        $autorisations = array_values($autorisations);
        $index = collect($autorisations)->search(fn ($a) => ($a['id'] ?? '') === $id);
    }

    $autorisations[$index]['utilisateur_id'] = $data['utilisateur_id'];
    $autorisations[$index]['utilisateur_nom'] = $utilisateur['nom_complet'] ?? '';
    $autorisations[$index]['utilisateur_login'] = $utilisateur['login'] ?? '';
    $autorisations[$index]['permissions'] = array_values(array_unique($data['permissions'] ?? []));

    AppStore::put('autorisations', $autorisations);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_autorisation', true);
})->name('autorisations.update');

Route::delete('/autorisations/{id}', function (string $id) {
    $autorisations = collect(AppStore::get('autorisations'))
        ->reject(fn ($a) => ($a['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('autorisations', $autorisations);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_autorisation', true);
})->name('autorisations.destroy');

Route::post('/projets', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'ref' => ['required', 'string'],
        'nom' => ['required', 'string', 'max:255'],
        'designation' => ['required', 'string', 'max:255'],
        'client' => ['required', 'string', 'max:255'],
        'delai' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'numeric', 'min:0'],
        'avance' => ['nullable', 'numeric', 'min:0'],
        'statut' => ['required', 'string', 'in:actif,attente,annule,execute'],
    ]);

    $budget = (float) $data['budget'];
    $avance = (float) ($data['avance'] ?? 0);
    $solde = $budget - $avance;

    $projets = AppStore::get('projets');
    $projets[] = [
        'id' => uniqid('prj_', true),
        'date' => $data['date'],
        'ref' => $data['ref'],
        'nom' => $data['nom'],
        'designation' => $data['designation'],
        'client' => $data['client'],
        'delai' => $data['delai'],
        'budget' => $budget,
        'montant_paye' => $avance,
        'solde' => $solde,
        'statut' => $data['statut'],
    ];

    AppStore::put('projets', $projets);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_projet', true);
})->name('projets.store');

Route::post('/evolutions', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:2000'],
        'pull' => ['required', 'string', 'in:oui,non'],
    ]);

    $evolutions = AppStore::get('evolutions');
    $evolutions[] = [
        'id' => uniqid('evo_', true),
        'date' => $data['date'],
        'titre_projet' => $data['titre_projet'],
        'description' => $data['description'],
        'pull' => $data['pull'],
    ];

    AppStore::put('evolutions', $evolutions);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.store');

Route::put('/evolutions/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:2000'],
        'pull' => ['required', 'string', 'in:oui,non'],
    ]);

    $evolutions = AppStore::get('evolutions');
    $index = collect($evolutions)->search(fn ($e) => ($e['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_evolution', true);
    }

    $evolutions[$index]['date'] = $data['date'];
    $evolutions[$index]['titre_projet'] = $data['titre_projet'];
    $evolutions[$index]['description'] = $data['description'];
    $evolutions[$index]['pull'] = $data['pull'];

    AppStore::put('evolutions', $evolutions);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.update');

Route::patch('/evolutions/{id}/pull', function (Request $request, string $id) {
    $data = $request->validate([
        'pull' => ['required', 'string', 'in:oui,non'],
    ]);

    $evolutions = AppStore::get('evolutions');
    $index = collect($evolutions)->search(fn ($e) => ($e['id'] ?? '') === $id);

    if ($index !== false) {
        $evolutions[$index]['pull'] = $data['pull'];
        AppStore::put('evolutions', $evolutions);
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.pull');

Route::delete('/evolutions/{id}', function (string $id) {
    $evolutions = collect(AppStore::get('evolutions'))
        ->reject(fn ($e) => ($e['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('evolutions', $evolutions);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.destroy');

Route::put('/projets/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom' => ['required', 'string', 'max:255'],
        'designation' => ['required', 'string', 'max:255'],
        'client' => ['required', 'string', 'max:255'],
        'delai' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'numeric', 'min:0'],
        'statut' => ['required', 'string', 'in:actif,attente,annule,execute'],
    ]);

    $projets = AppStore::get('projets');
    $index = collect($projets)->search(fn ($projet) => ($projet['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_projet', true);
    }

    $budget = (float) $data['budget'];
    $montantPaye = (float) ($projets[$index]['montant_paye'] ?? 0);

    $projets[$index]['date'] = $data['date'];
    $projets[$index]['nom'] = $data['nom'];
    $projets[$index]['designation'] = $data['designation'];
    $projets[$index]['client'] = $data['client'];
    $projets[$index]['delai'] = $data['delai'];
    $projets[$index]['budget'] = $budget;
    $projets[$index]['statut'] = $data['statut'];
    $projets[$index]['solde'] = $budget - $montantPaye;

    AppStore::put('projets', $projets);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_projet', true);
})->name('projets.update');

Route::patch('/projets/{id}/statut', function (Request $request, string $id) {
    $data = $request->validate([
        'statut' => ['required', 'string', 'in:actif,attente,annule,execute'],
    ]);

    $projets = AppStore::get('projets');
    $index = collect($projets)->search(fn ($projet) => ($projet['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_projet', true);
    }

    $projets[$index]['statut'] = $data['statut'];
    AppStore::put('projets', $projets);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_projet', true);
})->name('projets.statut');

Route::delete('/projets/{id}', function (string $id) {
    $projets = collect(AppStore::get('projets'))
        ->reject(fn ($projet) => ($projet['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('projets', $projets);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_projet', true);
})->name('projets.destroy');

Route::post('/paiements', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'ref' => ['required', 'string'],
        'projet_id' => ['required', 'string'],
        'client' => ['required', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'numeric', 'min:0'],
        'montant_paye' => ['required', 'numeric', 'min:0'],
        'type_reg' => ['required', 'string', 'max:255'],
        'bnq' => ['required', 'string', 'max:255'],
        'tresorerie' => ['required', 'string', 'max:255'],
    ]);

    $projets = AppStore::get('projets');
    $projetIndex = collect($projets)->search(fn ($p) => ($p['id'] ?? '') === $data['projet_id']);

    if ($projetIndex === false) {
        return redirect()->route('dashboard')->with('open_fiche_paiement', true);
    }

    $incrementPaye = (float) $data['montant_paye'];
    $tresorerie = $data['tresorerie'];
    $budget = (float) $data['budget'];
    $ancienPaye = (float) ($projets[$projetIndex]['montant_paye'] ?? 0);
    $nouveauPaye = $ancienPaye + $incrementPaye;
    $solde = $budget - $nouveauPaye;

    $projets[$projetIndex]['montant_paye'] = $nouveauPaye;
    $projets[$projetIndex]['solde'] = $solde;

    $paiements = AppStore::get('paiements');
    $paiements[] = [
        'id' => uniqid('pay_', true),
        'date' => $data['date'],
        'ref' => $data['ref'],
        'projet_id' => $data['projet_id'],
        'titre_projet' => $data['titre_projet'],
        'client' => $data['client'],
        'budget' => $budget,
        'montant_paye' => $nouveauPaye,
        'increment_paye' => $incrementPaye,
        'tresorerie' => $tresorerie,
        'type_reg' => $data['type_reg'],
        'bnq' => $data['bnq'],
        'solde' => $solde,
    ];

    AppStore::putMany(['projets' => $projets, 'paiements' => $paiements]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_paiement', true);
})->name('paiements.store');

Route::put('/paiements/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'montant_paye' => ['required', 'numeric', 'min:0'],
        'type_reg' => ['required', 'string', 'max:255'],
        'bnq' => ['required', 'string', 'max:255'],
        'tresorerie' => ['required', 'string', 'max:255'],
    ]);

    $paiements = AppStore::get('paiements');
    $index = collect($paiements)->search(fn ($paiement) => ($paiement['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_paiement', true);
    }

    $paiement = $paiements[$index];
    $ancienIncrement = (float) ($paiement['increment_paye'] ?? 0);
    $nouvelIncrement = (float) $data['montant_paye'];
    $projetId = $paiement['projet_id'] ?? '';

    $projets = AppStore::get('projets');
    $projetIndex = collect($projets)->search(fn ($p) => ($p['id'] ?? '') === $projetId);

    if ($projetIndex !== false) {
        $budget = (float) ($projets[$projetIndex]['budget'] ?? 0);
        $payeProjet = (float) ($projets[$projetIndex]['montant_paye'] ?? 0);
        $nouveauPaye = max(0, $payeProjet - $ancienIncrement + $nouvelIncrement);
        $solde = $budget - $nouveauPaye;

        $projets[$projetIndex]['montant_paye'] = $nouveauPaye;
        $projets[$projetIndex]['solde'] = $solde;

        $paiements[$index]['montant_paye'] = $nouveauPaye;
        $paiements[$index]['solde'] = $solde;
        AppStore::put('projets', $projets);
    }

    $paiements[$index]['date'] = $data['date'];
    $paiements[$index]['increment_paye'] = $nouvelIncrement;
    $paiements[$index]['type_reg'] = $data['type_reg'];
    $paiements[$index]['bnq'] = $data['bnq'];
    $paiements[$index]['tresorerie'] = $data['tresorerie'];

    AppStore::put('paiements', $paiements);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_paiement', true);
})->name('paiements.update');

Route::delete('/paiements/{id}', function (string $id) {
    $paiements = AppStore::get('paiements');
    $paiement = collect($paiements)->firstWhere('id', $id);

    if ($paiement) {
        $projets = AppStore::get('projets');
        $projetIndex = collect($projets)->search(fn ($p) => ($p['id'] ?? '') === ($paiement['projet_id'] ?? ''));

        if ($projetIndex !== false) {
            $increment = (float) ($paiement['increment_paye'] ?? 0);
            $budget = (float) ($projets[$projetIndex]['budget'] ?? 0);
            $nouveauPaye = max(0, (float) ($projets[$projetIndex]['montant_paye'] ?? 0) - $increment);
            $projets[$projetIndex]['montant_paye'] = $nouveauPaye;
            $projets[$projetIndex]['solde'] = $budget - $nouveauPaye;
            AppStore::put('projets', $projets);
        }
    }

    $paiements = collect($paiements)
        ->reject(fn ($p) => ($p['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('paiements', $paiements);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_paiement', true);
})->name('paiements.destroy');

Route::put('/whatsapp/settings', function (Request $request) {
    $data = $request->validate([
        'actif' => ['nullable', 'in:0,1'],
        'mode' => ['required', 'string', 'in:lien,api'],
        'indicatif' => ['required', 'string', 'max:6'],
        'numero_business' => ['nullable', 'string', 'max:40'],
        'access_token' => ['nullable', 'string', 'max:2000'],
        'phone_number_id' => ['nullable', 'string', 'max:120'],
        'messages_actifs' => ['nullable', 'in:0,1'],
        'appels_actifs' => ['nullable', 'in:0,1'],
        'message_defaut' => ['nullable', 'string', 'max:1000'],
        'template_name' => ['nullable', 'string', 'max:120'],
        'template_lang' => ['nullable', 'string', 'max:12'],
    ]);

    $current = WhatsApp::config();
    $token = trim((string) ($data['access_token'] ?? ''));
    if ($token === '' || $token === '********') {
        $token = (string) ($current['access_token'] ?? '');
    }

    AppStore::putConfig('whatsapp', [
        'actif' => ($data['actif'] ?? '0') === '1',
        'mode' => ($token !== '' && trim((string) ($data['phone_number_id'] ?? '')) !== '') ? 'api' : $data['mode'],
        'indicatif' => preg_replace('/\D+/', '', $data['indicatif']) ?: '212',
        'numero_business' => trim((string) ($data['numero_business'] ?? '')),
        'access_token' => $token,
        'phone_number_id' => trim((string) ($data['phone_number_id'] ?? '')),
        'messages_actifs' => ($data['messages_actifs'] ?? '0') === '1',
        'appels_actifs' => ($data['appels_actifs'] ?? '0') === '1',
        'message_defaut' => trim((string) ($data['message_defaut'] ?? '')),
        'template_name' => trim((string) ($data['template_name'] ?? '')),
        'template_lang' => trim((string) ($data['template_lang'] ?? 'fr')) ?: 'fr',
    ]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_whatsapp', true)
        ->with('whatsapp_saved', true);
})->name('whatsapp.settings.update');

Route::post('/whatsapp/send', function (Request $request) {
    $data = $request->validate([
        'telephone' => ['required', 'string', 'max:40'],
        'message' => ['nullable', 'string', 'max:1000'],
    ]);

    $result = WhatsApp::sendMessage($data['telephone'], (string) ($data['message'] ?? ''));

    return response()->json($result, ($result['ok'] ?? false) ? 200 : 422);
})->name('whatsapp.send');

Route::post('/whatsapp/call', function (Request $request) {
    $cfg = WhatsApp::config();

    if (! ($cfg['actif'] ?? false)) {
        return response()->json(['ok' => false, 'message' => 'WhatsApp est désactivé.'], 422);
    }

    if (! ($cfg['appels_actifs'] ?? false)) {
        return response()->json(['ok' => false, 'message' => 'Les appels WhatsApp sont désactivés.'], 422);
    }

    $data = $request->validate([
        'telephone' => ['required', 'string', 'max:40'],
    ]);

    $url = WhatsApp::callUrl($data['telephone'], $cfg['indicatif'] ?? '212');
    if (! $url) {
        return response()->json(['ok' => false, 'message' => 'Numéro de téléphone invalide.'], 422);
    }

    return response()->json([
        'ok' => true,
        'message' => 'Ouverture de WhatsApp pour appeler.',
        'url' => $url,
    ]);
})->name('whatsapp.call');

Route::post('/whatsapp/messages/log', function (Request $request) {
    $data = $request->validate([
        'telephone' => ['required', 'string', 'max:40'],
        'message' => ['nullable', 'string', 'max:1000'],
        'relance_id' => ['nullable', 'string', 'max:80'],
        'nom_complet' => ['nullable', 'string', 'max:255'],
    ]);

    $phone = trim($data['telephone']);
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if ($digits === '') {
        return response()->json(['ok' => false, 'message' => 'Numéro invalide.'], 422);
    }

    $nom = trim((string) ($data['nom_complet'] ?? ''));
    $relanceId = trim((string) ($data['relance_id'] ?? ''));
    if ($relanceId !== '') {
        $relance = collect(AppStore::get('relances'))->firstWhere('id', $relanceId);
        if ($relance) {
            if ($nom === '') {
                $nom = (string) ($relance['nom_complet'] ?? '');
            }
            $relancePhone = trim((string) ($relance['telephone'] ?? ''));
            if ($relancePhone !== '') {
                $phone = $relancePhone;
                $digits = preg_replace('/\D+/', '', $phone) ?? $digits;
            }
        }
    }

    $messages = AppStore::get('whatsapp_messages');
    $now = now();
    $row = [
        'id' => uniqid('wam_', true),
        'relance_id' => $relanceId !== '' ? $relanceId : null,
        'telephone' => $phone,
        'telephone_digits' => $digits,
        'nom_complet' => $nom,
        'message' => trim((string) ($data['message'] ?? '')),
        'sent_at' => $now->format('d/m/Y H:i'),
        'sent_at_iso' => $now->toIso8601String(),
        'unread' => true,
        'type' => 'out',
    ];
    array_unshift($messages, $row);
    $messages = array_slice(array_values($messages), 0, 200);
    AppStore::put('whatsapp_messages', $messages);

    $unread = collect($messages)->where('unread', true)->count();

    return response()->json([
        'ok' => true,
        'item' => $row,
        'unread' => $unread,
        'messages' => array_slice($messages, 0, 80),
    ]);
})->name('whatsapp.messages.log');

Route::post('/whatsapp/messages/read', function (Request $request) {
    $data = $request->validate([
        'id' => ['nullable', 'string', 'max:80'],
        'all' => ['nullable', 'boolean'],
    ]);

    $messages = AppStore::get('whatsapp_messages');
    $markAll = (bool) ($data['all'] ?? false);
    $id = (string) ($data['id'] ?? '');

    foreach ($messages as &$msg) {
        if ($markAll || ($id !== '' && (string) ($msg['id'] ?? '') === $id)) {
            $msg['unread'] = false;
        }
    }
    unset($msg);

    AppStore::put('whatsapp_messages', array_values($messages));
    $unread = collect($messages)->where('unread', true)->count();

    return response()->json([
        'ok' => true,
        'unread' => $unread,
        'messages' => array_slice($messages, 0, 80),
    ]);
})->name('whatsapp.messages.read');

Route::post('/whatsapp/devis', function (Request $request) {
    $data = $request->validate([
        'pdf' => ['required', 'file', 'mimes:pdf', 'max:8192'],
        'telephone' => ['required', 'string', 'max:40'],
        'nom_complet' => ['nullable', 'string', 'max:255'],
        'titre' => ['nullable', 'string', 'max:255'],
        'caption' => ['nullable', 'string', 'max:1000'],
        'relance_id' => ['nullable', 'string', 'max:80'],
    ]);

    $dir = storage_path('app/devis');
    if (! is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $token = bin2hex(random_bytes(16));
    $filename = $token.'.pdf';
    $absolutePath = $dir.DIRECTORY_SEPARATOR.$filename;
    $request->file('pdf')->move($dir, $filename);

    $url = url('/devis/'.$token);
    $titre = trim((string) ($data['titre'] ?? ''));
    $safeName = $titre !== ''
        ? ('Devis_EvoPro_'.preg_replace('/[^\p{L}\p{N}\-_]+/u', '_', $titre).'.pdf')
        : 'Devis_EvoPro.pdf';
    $caption = trim((string) ($data['caption'] ?? ''));
    if ($caption === '') {
        $caption = 'Devis EvoPro'.($titre !== '' ? ' — '.$titre : '');
    }

    $send = WhatsApp::sendDocument(
        $data['telephone'],
        $absolutePath,
        $safeName,
        $caption
    );

    $preview = 'Devis PDF'.($titre !== '' ? ' — '.$titre : '');
    $messages = AppStore::get('whatsapp_messages');
    $now = now();
    $row = [
        'id' => uniqid('wam_', true),
        'relance_id' => trim((string) ($data['relance_id'] ?? '')) ?: null,
        'telephone' => trim($data['telephone']),
        'telephone_digits' => preg_replace('/\D+/', '', $data['telephone']) ?? '',
        'nom_complet' => trim((string) ($data['nom_complet'] ?? '')),
        'message' => ($send['ok'] ?? false) ? ($preview.' (envoyé)') : ($preview.' '.$url),
        'sent_at' => $now->format('d/m/Y H:i'),
        'sent_at_iso' => $now->toIso8601String(),
        'unread' => true,
        'type' => 'devis',
        'devis_url' => $url,
        'sent' => (bool) ($send['ok'] ?? false),
    ];
    array_unshift($messages, $row);
    AppStore::put('whatsapp_messages', array_slice(array_values($messages), 0, 200));

    if (! ($send['ok'] ?? false)) {
        return response()->json([
            'ok' => false,
            'sent' => false,
            'needs_api' => (bool) ($send['needs_api'] ?? false),
            'message' => $send['message'] ?? 'Échec de l’envoi WhatsApp.',
            'url' => $url,
            'item' => $row,
            'unread' => collect($messages)->where('unread', true)->count(),
            'messages' => array_slice($messages, 0, 80),
        ], 422);
    }

    return response()->json([
        'ok' => true,
        'sent' => true,
        'message' => $send['message'] ?? 'Devis envoyé.',
        'url' => $url,
        'token' => $token,
        'item' => $row,
        'unread' => collect($messages)->where('unread', true)->count(),
        'messages' => array_slice($messages, 0, 80),
    ]);
})->name('whatsapp.devis.upload');

});