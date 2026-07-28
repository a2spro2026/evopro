<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $clients = session('clients', []);
    $projets = session('projets', []);

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

    $paiements = session('paiements', []);

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
        'utilisateurs' => session('utilisateurs', []),
        'evolutions' => session('evolutions', []),
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
    $request->validate([
        'statue' => ['required', 'string'],
        'login' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    return redirect()
        ->route('dashboard')
        ->with('login', $request->input('login'))
        ->with('statue', $request->input('statue'));
};

Route::post('/connexion', $handleConnexion)->name('connexion');
Route::post('/', $handleConnexion);

Route::post('/deconnexion', function () {
    session()->forget(['login', 'statue']);
    session()->regenerate();

    return redirect('/');
})->name('logout');

Route::post('/clients', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string'],
        'ref' => ['required', 'string'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'contact' => ['required', 'string', 'max:255'],
        'activite' => ['required', 'string', 'max:255'],
    ]);

    $clients = session('clients', []);
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

    session(['clients' => $clients]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_client', true);
})->name('clients.store');

Route::put('/clients/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['required', 'string', 'max:255'],
        'contact' => ['required', 'string', 'max:255'],
        'activite' => ['required', 'string', 'max:255'],
    ]);

    $clients = session('clients', []);
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
        $projets = collect(session('projets', []))
            ->map(function ($projet) use ($ancienNom, $nouveauNom) {
                if (($projet['client'] ?? '') === $ancienNom) {
                    $projet['client'] = $nouveauNom;
                }

                return $projet;
            })
            ->all();

        $paiements = collect(session('paiements', []))
            ->map(function ($paiement) use ($ancienNom, $nouveauNom) {
                if (($paiement['client'] ?? '') === $ancienNom) {
                    $paiement['client'] = $nouveauNom;
                }

                return $paiement;
            })
            ->all();

        session(['projets' => $projets, 'paiements' => $paiements]);
    }

    session(['clients' => $clients]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_client', true);
})->name('clients.update');

Route::delete('/clients/{id}', function (string $id) {
    $clients = collect(session('clients', []))
        ->reject(fn ($client) => ($client['id'] ?? '') === $id)
        ->values()
        ->all();

    session(['clients' => $clients]);

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

    $utilisateurs = session('utilisateurs', []);
    $utilisateurs[] = [
        'id' => uniqid('usr_', true),
        'date' => $data['date'],
        'nom_complet' => $data['nom_complet'],
        'statue' => $data['statue'],
        'login' => $data['login'],
        'password' => $data['password'],
    ];

    session(['utilisateurs' => $utilisateurs]);

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

    $utilisateurs = session('utilisateurs', []);
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

    session(['utilisateurs' => $utilisateurs]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_utilisateur', true);
})->name('utilisateurs.update');

Route::delete('/utilisateurs/{id}', function (string $id) {
    $utilisateurs = collect(session('utilisateurs', []))
        ->reject(fn ($u) => ($u['id'] ?? '') === $id)
        ->values()
        ->all();

    session(['utilisateurs' => $utilisateurs]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_utilisateur', true);
})->name('utilisateurs.destroy');

Route::post('/projets', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string'],
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

    $projets = session('projets', []);
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

    session(['projets' => $projets]);

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

    $evolutions = session('evolutions', []);
    $evolutions[] = [
        'id' => uniqid('evo_', true),
        'date' => $data['date'],
        'titre_projet' => $data['titre_projet'],
        'description' => $data['description'],
        'pull' => $data['pull'],
    ];

    session(['evolutions' => $evolutions]);

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

    $evolutions = session('evolutions', []);
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

    session(['evolutions' => $evolutions]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.update');

Route::patch('/evolutions/{id}/pull', function (Request $request, string $id) {
    $data = $request->validate([
        'pull' => ['required', 'string', 'in:oui,non'],
    ]);

    $evolutions = session('evolutions', []);
    $index = collect($evolutions)->search(fn ($e) => ($e['id'] ?? '') === $id);

    if ($index !== false) {
        $evolutions[$index]['pull'] = $data['pull'];
        session(['evolutions' => $evolutions]);
    }

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.pull');

Route::delete('/evolutions/{id}', function (string $id) {
    $evolutions = collect(session('evolutions', []))
        ->reject(fn ($e) => ($e['id'] ?? '') === $id)
        ->values()
        ->all();

    session(['evolutions' => $evolutions]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_evolution', true);
})->name('evolutions.destroy');

Route::put('/projets/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string'],
        'nom' => ['required', 'string', 'max:255'],
        'designation' => ['required', 'string', 'max:255'],
        'client' => ['required', 'string', 'max:255'],
        'delai' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'numeric', 'min:0'],
        'statut' => ['required', 'string', 'in:actif,attente,annule,execute'],
    ]);

    $projets = session('projets', []);
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

    session(['projets' => $projets]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_projet', true);
})->name('projets.update');

Route::patch('/projets/{id}/statut', function (Request $request, string $id) {
    $data = $request->validate([
        'statut' => ['required', 'string', 'in:actif,attente,annule,execute'],
    ]);

    $projets = session('projets', []);
    $index = collect($projets)->search(fn ($projet) => ($projet['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()
            ->route('dashboard')
            ->with('open_fiche_projet', true);
    }

    $projets[$index]['statut'] = $data['statut'];
    session(['projets' => $projets]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_projet', true);
})->name('projets.statut');

Route::delete('/projets/{id}', function (string $id) {
    $projets = collect(session('projets', []))
        ->reject(fn ($projet) => ($projet['id'] ?? '') === $id)
        ->values()
        ->all();

    session(['projets' => $projets]);

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

    $projets = session('projets', []);
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

    $paiements = session('paiements', []);
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

    session(['projets' => $projets, 'paiements' => $paiements]);

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

    $paiements = session('paiements', []);
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

    $projets = session('projets', []);
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
        session(['projets' => $projets]);
    }

    $paiements[$index]['date'] = $data['date'];
    $paiements[$index]['increment_paye'] = $nouvelIncrement;
    $paiements[$index]['type_reg'] = $data['type_reg'];
    $paiements[$index]['bnq'] = $data['bnq'];
    $paiements[$index]['tresorerie'] = $data['tresorerie'];

    session(['paiements' => $paiements]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_paiement', true);
})->name('paiements.update');

Route::delete('/paiements/{id}', function (string $id) {
    $paiements = session('paiements', []);
    $paiement = collect($paiements)->firstWhere('id', $id);

    if ($paiement) {
        $projets = session('projets', []);
        $projetIndex = collect($projets)->search(fn ($p) => ($p['id'] ?? '') === ($paiement['projet_id'] ?? ''));

        if ($projetIndex !== false) {
            $increment = (float) ($paiement['increment_paye'] ?? 0);
            $budget = (float) ($projets[$projetIndex]['budget'] ?? 0);
            $nouveauPaye = max(0, (float) ($projets[$projetIndex]['montant_paye'] ?? 0) - $increment);
            $projets[$projetIndex]['montant_paye'] = $nouveauPaye;
            $projets[$projetIndex]['solde'] = $budget - $nouveauPaye;
            session(['projets' => $projets]);
        }
    }

    $paiements = collect($paiements)
        ->reject(fn ($p) => ($p['id'] ?? '') === $id)
        ->values()
        ->all();

    session(['paiements' => $paiements]);

    return redirect()
        ->route('dashboard')
        ->with('open_fiche_paiement', true);
})->name('paiements.destroy');
