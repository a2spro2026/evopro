<?php

use App\Support\AppStore;
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

    return view('dashboard', [
        'clients' => $clients,
        'projets' => $projets,
        'paiements' => $paiements,
        'utilisateurs' => AppStore::get('utilisateurs'),
        'evolutions' => AppStore::get('evolutions'),
        'dashboardCounts' => $dashboardCounts,
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

Route::middleware('auth.user')->group(function () {

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

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_utilisateur', true);
})->name('utilisateurs.destroy');

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

});