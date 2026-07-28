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

        if (in_array($statut, ['actif', 'attente', 'annule'], true)) {
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

    $projetsCollection = collect($projets);

    $dashboardCounts = [
        'actif' => $projetsCollection->where('statut', 'actif')->count(),
        'attente' => $projetsCollection->where('statut', 'attente')->count(),
        'annule' => $projetsCollection->where('statut', 'annule')->count(),
    ];

    $chartProjets = $projetsCollection
        ->map(function ($projet) {
            $parts = explode('/', $projet['date'] ?? '');

            if (count($parts) < 3) {
                return null;
            }

            return [
                'mois' => $parts[1].'/'.$parts[2],
                'annee' => $parts[2],
                'statut' => $projet['statut'],
            ];
        })
        ->filter()
        ->values()
        ->all();

    return view('dashboard', [
        'clients' => $clients,
        'projets' => $projets,
        'paiements' => session('paiements', []),
        'dashboardCounts' => $dashboardCounts,
        'totalRevenu' => $projetsCollection->sum(fn ($p) => (float) ($p['montant_paye'] ?? 0)),
        'totalSolde' => $projetsCollection->sum(fn ($p) => (float) ($p['solde'] ?? 0)),
        'chartProjets' => $chartProjets,
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
        'statut' => ['required', 'string', 'in:actif,attente,annule'],
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

Route::put('/projets/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'nom' => ['required', 'string', 'max:255'],
        'designation' => ['required', 'string', 'max:255'],
        'client' => ['required', 'string', 'max:255'],
        'delai' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'numeric', 'min:0'],
        'statut' => ['required', 'string', 'in:actif,attente,annule'],
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
        'statut' => ['required', 'string', 'in:actif,attente,annule'],
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
        'date' => ['required', 'string'],
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
