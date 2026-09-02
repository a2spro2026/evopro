<?php

use App\Support\AppStore;
use App\Support\CommercialPresenceHelper;
use App\Support\ContactsArchive;
use App\Support\FicheSteHelper;
use App\Support\ProjetHelper;
use App\Support\ProspectionHelper;
use App\Support\UtilisateurHelper;
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

    $authUser = session('auth_user', []);
    $authUserNom = (string) ($authUser['nom_complet'] ?? '');

    $nameParts = preg_split('/\s+/', trim($authUserNom)) ?: [];
    $welcomeName = count($nameParts) >= 2
        ? implode(' ', array_reverse($nameParts))
        : ($authUserNom !== '' ? $authUserNom : 'Utilisateur');

    $projets = collect(AppStore::get('projets'));
    $resolveStatut = function (array $projet): string {
        $statut = $projet['statut'] ?? '';
        if (in_array($statut, ['actif', 'attente', 'annule', 'execute'], true)) {
            return $statut;
        }

        return (float) ($projet['montant_paye'] ?? 0) > 0 ? 'actif' : 'attente';
    };

    $dashboardCounts = [
        'confirme' => $projets->filter(fn ($p) => $resolveStatut($p) === 'actif')->count(),
        'attente' => $projets->filter(fn ($p) => $resolveStatut($p) === 'attente')->count(),
        'annule' => $projets->filter(fn ($p) => $resolveStatut($p) === 'annule')->count(),
    ];

    $totalCharges = (float) collect(AppStore::get('charges'))
        ->sum(fn ($c) => (float) ($c['montant'] ?? $c['amount'] ?? 0));

    ContactsArchive::importClientsFromProduction();

    UtilisateurHelper::repairProspectionCommercialLinks();

    $utilisateurs = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
    if ($utilisateurs !== AppStore::get('utilisateurs')) {
        AppStore::put('utilisateurs', $utilisateurs);
    }

    $prospections = AppStore::get('prospections');
    $prospectionsAll = $prospections;
    $authStatue = UtilisateurHelper::normalizeStatue((string) ($authUser['statue'] ?? ''));
    $canManageProspectionCommercial = UtilisateurHelper::canManageProspectionCommercial($authStatue);
    $isAdministrateur = UtilisateurHelper::isAdministrateur($authStatue);
    $isAssistante = UtilisateurHelper::isAssistante($authStatue);
    $isCommercialRole = UtilisateurHelper::isCommercialRole($authStatue);
    $defaultPanel = UtilisateurHelper::defaultPanel($authStatue);

    if ($authStatue === 'commercial') {
        $prospections = collect($prospections)
            ->filter(fn ($row) => UtilisateurHelper::rowBelongsToCommercial($row, $authUser))
            ->values()
            ->all();
    }

    $clients = AppStore::get('clients');
    $clientStats = [
        'nombre_projets' => count($clients),
        'total_budgets' => (float) collect($clients)->sum(fn ($c) => (float) ($c['budget'] ?? 0)),
    ];
    $commerciaux = collect($utilisateurs)
        ->filter(fn ($u) => UtilisateurHelper::isCommercial($u))
        ->pluck('nom_complet')
        ->merge(collect($prospectionsAll)->pluck('commercial'))
        ->map(fn ($n) => trim((string) $n))
        ->filter()
        ->unique(fn ($n) => mb_strtolower($n))
        ->sort(fn ($a, $b) => strcasecmp($a, $b))
        ->values()
        ->all();

    $commerciauxUsers = collect($utilisateurs)
        ->filter(fn ($u) => UtilisateurHelper::isCommercial($u))
        ->pluck('nom_complet')
        ->map(fn ($n) => trim((string) $n))
        ->filter()
        ->unique(fn ($n) => mb_strtolower($n))
        ->sort(fn ($a, $b) => strcasecmp($a, $b))
        ->values()
        ->all();

    $commerciauxPresenceUsers = collect($utilisateurs)
        ->filter(fn ($u) => UtilisateurHelper::isCommercial($u))
        ->values()
        ->all();

    $projetsList = collect(AppStore::get('projets'))
        ->map(fn ($row) => ProjetHelper::normalizeRow($row))
        ->sortByDesc(fn ($row) => $row['date'] ?? '')
        ->values()
        ->all();

    return view('dashboard', [
        'authUserNom' => $authUserNom,
        'authUserStatue' => (string) ($authUser['statue'] ?? ''),
        'authStatue' => $authStatue,
        'canManageProspectionCommercial' => $canManageProspectionCommercial,
        'isAdministrateur' => $isAdministrateur,
        'isAssistante' => $isAssistante,
        'isCommercialRole' => $isCommercialRole,
        'canViewCommercialPresence' => $isAdministrateur || $isAssistante,
        'defaultPanel' => $defaultPanel,
        'welcomeName' => $welcomeName,
        'dashboardCounts' => $dashboardCounts,
        'totalCharges' => $totalCharges,
        'prospections' => $prospections,
        'prospectionsAll' => $prospectionsAll,
        'clients' => $clients,
        'clientStats' => $clientStats,
        'projets' => $projetsList,
        'commerciaux' => $commerciaux,
        'commerciauxUsers' => $commerciauxUsers,
        'commerciauxPresenceUsers' => $commerciauxPresenceUsers,
        'utilisateurs' => $utilisateurs,
        'ficheSte' => FicheSteHelper::get(),
    ]);
})->name('dashboard');

$handleConnexion = function (Request $request) {
    $data = $request->validate([
        'statue' => ['required', 'string', 'in:administrateur,assistante,commercial'],
        'login' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $statue = UtilisateurHelper::normalizeStatue($data['statue']);
    $login = trim($data['login']);
    $password = $data['password'];
    $utilisateurs = AppStore::get('utilisateurs');

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
        return UtilisateurHelper::normalizeStatue($u['statue'] ?? '') === $statue
            && mb_strtolower(trim((string) ($u['login'] ?? ''))) === mb_strtolower($login)
            && (string) ($u['password'] ?? '') === (string) $password;
    });

    if (! $user) {
        return back()
            ->withErrors(['login' => 'Statue, login ou mot de passe incorrect.'])
            ->withInput($request->only('statue', 'login'));
    }

    if ($user['suspendu'] ?? false) {
        return back()
            ->withErrors(['login' => 'Ce compte est suspendu.'])
            ->withInput($request->only('statue', 'login'));
    }

    $userStatue = UtilisateurHelper::normalizeStatue($user['statue'] ?? $statue);
    $defaultPanel = UtilisateurHelper::defaultPanel($userStatue);

    $request->session()->regenerate();
    session([
        'auth_user' => [
            'id' => $user['id'] ?? '',
            'login' => $user['login'] ?? $login,
            'statue' => $userStatue,
            'nom_complet' => $user['nom_complet'] ?? '',
        ],
        'login' => $user['login'] ?? $login,
        'statue' => $userStatue,
    ]);

    if ($userStatue === 'commercial') {
        CommercialPresenceHelper::markOnline(session('auth_user', []));
    }

    $redirect = redirect()->route('dashboard');
    if ($defaultPanel === 'prospection') {
        return $redirect->with('open_panel', 'prospection')->with('open_prospection', 'liste');
    }

    return $redirect;
};

Route::post('/connexion', $handleConnexion)->name('connexion');
Route::post('/', $handleConnexion);

Route::post('/deconnexion', function () {
    $authUser = session('auth_user', []);
    if (UtilisateurHelper::isCommercialRole($authUser['statue'] ?? '')) {
        CommercialPresenceHelper::markOffline($authUser);
    }

    session()->forget(['login', 'statue', 'auth_user']);
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware('auth.user')->post('/presence/heartbeat', function () {
    $authUser = session('auth_user', []);
    if (! UtilisateurHelper::isCommercialRole($authUser['statue'] ?? '')) {
        abort(403);
    }

    CommercialPresenceHelper::heartbeat($authUser);

    return response()->json(['ok' => true]);
})->name('presence.heartbeat');

Route::middleware('auth.user')->post('/presence/offline', function () {
    $authUser = session('auth_user', []);
    if (UtilisateurHelper::isCommercialRole($authUser['statue'] ?? '')) {
        CommercialPresenceHelper::markOffline($authUser);
    }

    return response()->json(['ok' => true]);
})->name('presence.offline');

Route::middleware('auth.user')->get('/presence/live', function () {
    $authStatue = UtilisateurHelper::normalizeStatue(session('auth_user.statue'));
    if (! in_array($authStatue, ['administrateur', 'assistante'], true)) {
        abort(403);
    }

    return response()->json([
        'ok' => true,
        'commercials' => CommercialPresenceHelper::statusesForManager(),
    ]);
})->name('presence.live');

Route::middleware('auth.user')->get('/prospections/live', function () {
    $authUser = session('auth_user', []);
    $authStatue = UtilisateurHelper::normalizeStatue($authUser['statue'] ?? '');
    $rows = AppStore::get('prospections');

    if ($authStatue === 'commercial') {
        $rows = collect($rows)
            ->filter(fn ($row) => UtilisateurHelper::rowBelongsToCommercial($row, $authUser))
            ->values()
            ->all();
    }

    return response()->json([
        'ok' => true,
        'rows' => collect($rows)->map(fn ($row) => [
            'id' => $row['id'] ?? '',
            'date' => $row['date'] ?? '',
            'commercial' => $row['commercial'] ?? '',
            'telephone' => $row['telephone'] ?? '',
            'nom_prospect' => $row['nom_prospect'] ?? '',
            'ville' => $row['ville'] ?? '',
            'projet' => $row['projet'] ?? '',
            'remarque' => $row['remarque'] ?? '',
            'statue' => $row['statue'] ?? 'en_attente',
            'date_rappel' => $row['date_rappel'] ?? '',
        ])->values()->all(),
    ]);
})->name('prospections.live');

Route::middleware('auth.user')->patch('/prospections/{id}/statue', function (Request $request, string $id) {
    $data = $request->validate([
        'statue' => ['required', 'string', 'in:valide,en_attente,annule,reporte'],
    ]);

    $rows = AppStore::get('prospections');
    $index = collect($rows)->search(fn ($row) => ($row['id'] ?? '') === $id);

    if ($index === false) {
        return back()->withErrors(['statue' => 'Prospection introuvable.']);
    }

    UtilisateurHelper::assertCanAccessProspectionRow($rows[$index], session('auth_user', []));

    $rows[$index]['statue'] = $data['statue'];

    if ($data['statue'] === 'valide') {
        ContactsArchive::transferProspectionToClient($rows[$index]);
    }

    AppStore::put('prospections', $rows);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'id' => $id,
            'statue' => $rows[$index]['statue'],
        ]);
    }

    return redirect()->route('dashboard')->with('open_panel', 'prospection');
})->where('id', '.+')->name('prospections.statue');

Route::middleware('auth.user')->patch('/prospections/{id}/inline', function (Request $request, string $id) {
    $data = $request->validate([
        'field' => ['required', 'string', 'in:remarque,date_rappel,nom_prospect,ville,projet'],
        'value' => ['nullable', 'string', 'max:2000'],
    ]);

    $field = $data['field'];
    $value = trim((string) ($data['value'] ?? ''));

    if ($field === 'date_rappel' && $value !== '' && ! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
        return response()->json(['ok' => false, 'message' => 'Date invalide'], 422);
    }

    if (in_array($field, ['nom_prospect', 'ville', 'projet'], true) && mb_strlen($value) > 255) {
        return response()->json(['ok' => false, 'message' => 'Texte trop long'], 422);
    }

    $rows = AppStore::get('prospections');
    $index = collect($rows)->search(fn ($row) => ($row['id'] ?? '') === $id);

    if ($index === false) {
        return response()->json(['ok' => false, 'message' => 'Introuvable'], 404);
    }

    UtilisateurHelper::assertCanAccessProspectionRow($rows[$index], session('auth_user', []));

    $rows[$index][$field] = $value;
    AppStore::put('prospections', $rows);

    return response()->json([
        'ok' => true,
        'id' => $id,
        'field' => $field,
        'value' => $rows[$index][$field],
    ]);
})->where('id', '.+')->name('prospections.inline');

$requireProspectionManager = function () {
    if (! UtilisateurHelper::canManageProspectionCommercial(session('auth_user.statue'))) {
        abort(403, 'Accès réservé à l’administrateur ou l’assistante.');
    }
};

Route::middleware('auth.user')->post('/prospections/commercial/numeros', function (Request $request) use ($requireProspectionManager) {
    $requireProspectionManager();

    $data = $request->validate([
        'commercial' => ['required', 'string', 'max:255'],
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'telephone' => ['required', 'string', 'max:255'],
    ]);

    $rows = AppStore::get('prospections');
    $result = ProspectionHelper::appendNumbersForCommercial(
        $rows,
        $data['commercial'],
        [$data['telephone']],
        $data['date']
    );

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'created' => $result['created'],
            'skipped' => $result['skipped'],
            'rows' => $result['rows'],
        ]);
    }

    return redirect()->route('dashboard')
        ->with('open_panel', 'prospection')
        ->with('open_prospection', 'commercial');
})->name('prospections.commercial.store');

Route::middleware('auth.user')->post('/prospections/commercial/import', function (Request $request) use ($requireProspectionManager) {
    $requireProspectionManager();

    $data = $request->validate([
        'commercial' => ['required', 'string', 'max:255'],
        'date' => ['nullable', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'ocr_text' => ['nullable', 'string'],
        'numeros' => ['nullable', 'array'],
        'numeros.*' => ['string', 'max:255'],
    ]);

    $numeros = [];
    if (trim((string) ($data['ocr_text'] ?? '')) !== '') {
        $numeros = ProspectionHelper::extractPhoneNumbers((string) $data['ocr_text']);
    }
    if ($numeros === [] && ! empty($data['numeros'])) {
        $numeros = $data['numeros'];
    }

    if ($numeros === []) {
        return response()->json([
            'message' => 'Aucun numéro détecté. Utilisez une image plus nette.',
        ], 422);
    }

    $rows = AppStore::get('prospections');
    $result = ProspectionHelper::appendNumbersForCommercial(
        $rows,
        $data['commercial'],
        $data['numeros'],
        $data['date'] ?? null
    );

    return response()->json([
        'ok' => true,
        'created' => $result['created'],
        'skipped' => $result['skipped'],
        'rows' => $result['rows'],
        'numeros' => $numeros,
    ]);
})->name('prospections.commercial.import');

Route::middleware('auth.user')->post('/clients', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'delai_travail' => ['nullable', 'string', 'max:255'],
        'budget' => ['nullable', 'numeric', 'min:0'],
    ]);

    $clients = AppStore::get('clients');
    $n = count($clients) + 1;
    $titre = trim($data['titre_projet']);

    $clients[] = ContactsArchive::normalizeClientRow([
        'id' => uniqid('cli_', true),
        'date' => $data['date'],
        'ref' => 'CLI-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
        'nom' => $data['nom'],
        'ville' => trim((string) ($data['ville'] ?? '')),
        'contact' => trim((string) ($data['contact'] ?? '')),
        'titre_projet' => $titre,
        'delai_travail' => ContactsArchive::formatDelaiTravail($data['delai_travail'] ?? ''),
        'budget' => (float) ($data['budget'] ?? 0),
    ]);

    AppStore::put('clients', $clients);

    return redirect()->route('dashboard')->with('open_panel', 'client');
})->name('clients.store');

Route::middleware('auth.user')->put('/clients/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'delai_travail' => ['nullable', 'string', 'max:255'],
        'budget' => ['nullable', 'numeric', 'min:0'],
    ]);

    $clients = AppStore::get('clients');
    $index = collect($clients)->search(fn ($client) => ($client['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()->route('dashboard')->with('open_panel', 'client');
    }

    $titre = trim($data['titre_projet']);
    $clients[$index]['date'] = $data['date'];
    $clients[$index]['nom'] = trim($data['nom']);
    $clients[$index]['ville'] = trim((string) ($data['ville'] ?? ''));
    $clients[$index]['contact'] = trim((string) ($data['contact'] ?? ''));
    $clients[$index]['titre_projet'] = $titre;
    $clients[$index]['activite'] = $titre;
    $clients[$index]['delai_travail'] = ContactsArchive::formatDelaiTravail($data['delai_travail'] ?? '');
    $clients[$index]['budget'] = (float) ($data['budget'] ?? 0);

    AppStore::put('clients', $clients);

    return redirect()->route('dashboard')->with('open_panel', 'client');
})->where('id', '.+')->name('clients.update');

Route::middleware('auth.user')->delete('/clients/{id}', function (Request $request, string $id) {
    $clients = collect(AppStore::get('clients'))
        ->reject(fn ($client) => ($client['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('clients', $clients);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'id' => $id]);
    }

    return redirect()->route('dashboard')->with('open_panel', 'client');
})->where('id', '.+')->name('clients.destroy');

Route::middleware('auth.user')->post('/projets', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'commercial' => ['required', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'nom_client' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'budget' => ['nullable', 'numeric', 'min:0'],
        'avance' => ['nullable', 'numeric', 'min:0'],
        'mode' => ['required', 'string', 'in:Vir,Esp,Chq,Vers'],
        'part_commercial' => ['required', 'integer', 'in:10,15,20,30,50'],
        'prospection_id' => ['nullable', 'string', 'max:255'],
    ]);

    $projets = AppStore::get('projets');
    $budget = (float) ($data['budget'] ?? 0);
    $avance = (float) ($data['avance'] ?? 0);

    $projets[] = ProjetHelper::normalizeRow([
        'id' => uniqid('prj_', true),
        'date' => $data['date'],
        'ref' => ProjetHelper::nextRef($projets),
        'commercial' => trim($data['commercial']),
        'titre_projet' => trim($data['titre_projet']),
        'nom_client' => trim($data['nom_client']),
        'ville' => trim((string) ($data['ville'] ?? '')),
        'contact' => trim((string) ($data['contact'] ?? '')),
        'budget' => $budget,
        'avance' => $avance,
        'mode' => $data['mode'],
        'part_commercial' => $data['part_commercial'],
        'prospection_id' => $data['prospection_id'] ?? null,
    ]);

    AppStore::put('projets', $projets);

    return redirect()->route('dashboard')->with('open_panel', 'projet');
})->name('projets.store');

Route::middleware('auth.user')->put('/projets/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'commercial' => ['required', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'nom_client' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'budget' => ['nullable', 'numeric', 'min:0'],
        'avance' => ['nullable', 'numeric', 'min:0'],
        'mode' => ['required', 'string', 'in:Vir,Esp,Chq,Vers'],
        'part_commercial' => ['required', 'integer', 'in:10,15,20,30,50'],
        'prospection_id' => ['nullable', 'string', 'max:255'],
    ]);

    $projets = AppStore::get('projets');
    $index = collect($projets)->search(fn ($projet) => ($projet['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()->route('dashboard')->with('open_panel', 'projet');
    }

    $existing = $projets[$index];
    $budget = (float) ($data['budget'] ?? 0);
    $avance = (float) ($data['avance'] ?? 0);

    $projets[$index] = ProjetHelper::normalizeRow([
        ...$existing,
        'date' => $data['date'],
        'ref' => $existing['ref'] ?? ProjetHelper::nextRef($projets),
        'commercial' => trim($data['commercial']),
        'titre_projet' => trim($data['titre_projet']),
        'nom_client' => trim($data['nom_client']),
        'ville' => trim((string) ($data['ville'] ?? '')),
        'contact' => trim((string) ($data['contact'] ?? '')),
        'budget' => $budget,
        'avance' => $avance,
        'mode' => $data['mode'],
        'part_commercial' => $data['part_commercial'],
        'prospection_id' => $data['prospection_id'] ?? ($existing['prospection_id'] ?? null),
        'statut' => $existing['statut'] ?? null,
    ]);

    AppStore::put('projets', $projets);

    return redirect()->route('dashboard')->with('open_panel', 'projet');
})->where('id', '.+')->name('projets.update');

Route::middleware('auth.user')->delete('/projets/{id}', function (Request $request, string $id) {
    $projets = collect(AppStore::get('projets'))
        ->reject(fn ($projet) => ($projet['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('projets', $projets);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'id' => $id]);
    }

    return redirect()->route('dashboard')->with('open_panel', 'projet');
})->where('id', '.+')->name('projets.destroy');

Route::middleware('auth.user')->post('/utilisateurs', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom_complet' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'statue' => ['required', 'string', 'in:administrateur,assistante,commercial'],
        'login' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'max:255'],
    ]);

    $utilisateurs = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
    $login = mb_strtolower(trim($data['login']));

    if (collect($utilisateurs)->contains(fn ($u) => mb_strtolower(trim((string) ($u['login'] ?? ''))) === $login)) {
        return redirect()->route('dashboard')
            ->with('open_panel', 'configuration')
            ->with('open_config', 'utilisateur')
            ->withErrors(['utilisateur_login' => 'Ce login existe déjà.']);
    }

    $utilisateurs[] = UtilisateurHelper::normalizeRow([
        'id' => uniqid('usr_', true),
        'ref' => 'USR-'.str_pad((string) (count($utilisateurs) + 1), 4, '0', STR_PAD_LEFT),
        'date' => $data['date'],
        'nom_complet' => trim($data['nom_complet']),
        'ville' => trim((string) ($data['ville'] ?? '')),
        'statue' => $data['statue'],
        'login' => trim($data['login']),
        'password' => $data['password'],
        'suspendu' => false,
    ], count($utilisateurs));

    AppStore::put('utilisateurs', $utilisateurs);

    return redirect()->route('dashboard')
        ->with('open_panel', 'configuration')
        ->with('open_config', 'utilisateur');
})->name('utilisateurs.store');

Route::middleware('auth.user')->put('/utilisateurs/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom_complet' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'statue' => ['required', 'string', 'in:administrateur,assistante,commercial'],
        'login' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'max:255'],
    ]);

    $utilisateurs = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
    $index = collect($utilisateurs)->search(fn ($user) => ($user['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()->route('dashboard')
            ->with('open_panel', 'configuration')
            ->with('open_config', 'utilisateur');
    }

    $login = mb_strtolower(trim($data['login']));
    $duplicate = collect($utilisateurs)->first(function ($user, $i) use ($login, $index) {
        return $i !== $index && mb_strtolower(trim((string) ($user['login'] ?? ''))) === $login;
    });

    if ($duplicate) {
        return redirect()->route('dashboard')
            ->with('open_panel', 'configuration')
            ->with('open_config', 'utilisateur')
            ->withErrors(['utilisateur_login' => 'Ce login existe déjà.']);
    }

    $previousUser = $utilisateurs[$index];

    $utilisateurs[$index] = UtilisateurHelper::normalizeRow(array_merge($utilisateurs[$index], [
        'date' => $data['date'],
        'nom_complet' => trim($data['nom_complet']),
        'ville' => trim((string) ($data['ville'] ?? '')),
        'statue' => $data['statue'],
        'login' => trim($data['login']),
        'password' => $data['password'],
    ]), $index);

    $utilisateurs[$index] = UtilisateurHelper::mergeCommercialAliases($previousUser, $utilisateurs[$index]);

    AppStore::put('utilisateurs', $utilisateurs);

    UtilisateurHelper::syncProspectionsAfterCommercialUpdate($previousUser, $utilisateurs[$index]);

    return redirect()->route('dashboard')
        ->with('open_panel', 'configuration')
        ->with('open_config', 'utilisateur');
})->where('id', '.+')->name('utilisateurs.update');

Route::middleware('auth.user')->patch('/utilisateurs/{id}/suspendre', function (Request $request, string $id) {
    $utilisateurs = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
    $index = collect($utilisateurs)->search(fn ($user) => ($user['id'] ?? '') === $id);

    if ($index === false) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'message' => 'Introuvable'], 404);
        }

        return redirect()->route('dashboard')
            ->with('open_panel', 'configuration')
            ->with('open_config', 'utilisateur');
    }

    $authId = (string) (session('auth_user.id') ?? '');
    if (($utilisateurs[$index]['id'] ?? '') === $authId) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'message' => 'Impossible de suspendre votre propre compte.'], 422);
        }

        return redirect()->route('dashboard')
            ->with('open_panel', 'configuration')
            ->with('open_config', 'utilisateur')
            ->withErrors(['utilisateur_suspend' => 'Impossible de suspendre votre propre compte.']);
    }

    $utilisateurs[$index]['suspendu'] = ! ($utilisateurs[$index]['suspendu'] ?? false);
    AppStore::put('utilisateurs', $utilisateurs);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'id' => $id,
            'suspendu' => $utilisateurs[$index]['suspendu'],
        ]);
    }

    return redirect()->route('dashboard')
        ->with('open_panel', 'configuration')
        ->with('open_config', 'utilisateur');
})->where('id', '.+')->name('utilisateurs.suspendre');

Route::middleware('auth.user')->put('/configuration/fiche-ste', function (Request $request) {
    $data = $request->validate([
        'nom_societe' => ['nullable', 'string', 'max:255'],
        'nom_gerant' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'whatsapp' => ['nullable', 'string', 'max:255'],
        'email' => ['nullable', 'string', 'max:255'],
    ]);

    FicheSteHelper::save($data);
    $saved = FicheSteHelper::get();

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'fiche_ste' => $saved]);
    }

    return redirect()->route('dashboard')
        ->with('open_panel', 'configuration')
        ->with('open_config', 'fiche-ste');
})->name('configuration.fiche-ste');
