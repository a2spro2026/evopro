<?php

use App\Http\Controllers\ClientBalanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPaymentController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SalesInvoicePaymentController;
use App\Http\Controllers\SupplierInvoicePaymentController;
use App\Http\Controllers\SupplierInvoiceController;
use App\Http\Controllers\SupplierBalanceController;
use App\Http\Controllers\SupplierPaymentController;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use App\Models\SalesInvoicePaymentLine;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use App\Support\AppSectionsLock;
use App\Support\AppStore;
use App\Support\CommercialPresenceHelper;
use App\Support\ContactsArchive;
use App\Support\FicheSteHelper;
use App\Support\NumerosEntrantsHelper;
use App\Support\ProjetHelper;
use App\Support\ProspectionHelper;
use App\Support\UtilisateurHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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

    $paymentTypes = ['Esp', 'Chq', 'Eff', 'Vir', 'Vers'];
    $emptyMix = array_fill_keys($paymentTypes, 0.0);
    $normalizeMix = function (array $amounts) use ($paymentTypes): array {
        $total = array_sum($amounts);
        $items = [];
        foreach ($paymentTypes as $type) {
            $amount = round((float) ($amounts[$type] ?? 0), 2);
            $items[] = [
                'type' => $type,
                'amount' => $amount,
                'rate' => $total > 0 ? round(($amount / $total) * 100, 1) : 0.0,
            ];
        }

        return [
            'total' => round($total, 2),
            'items' => $items,
        ];
    };

    $totalAchats = 0.0;
    $totalVentes = 0.0;
    $totalFacturesAchats = 0.0;
    $totalFacturesVentes = 0.0;
    $reglementAchatsMix = $emptyMix;
    $reglementVentesMix = $emptyMix;

    try {
        if (Schema::hasTable('purchase_orders')) {
            $totalAchats = (float) PurchaseOrder::query()->sum('total');
        }
        if (Schema::hasTable('sales_invoices')) {
            $totalVentes = (float) SalesInvoice::query()->sum('total_ttc');
            $totalFacturesVentes = $totalVentes;
        }
        if (Schema::hasTable('supplier_invoices')) {
            $totalFacturesAchats = (float) SupplierInvoice::query()->sum('total_ttc');
        }
        if (Schema::hasTable('supplier_payments')) {
            foreach (SupplierPayment::query()->get(['reglement', 'montant']) as $payment) {
                $type = trim((string) ($payment->reglement ?? ''));
                if (isset($reglementAchatsMix[$type])) {
                    $reglementAchatsMix[$type] += (float) $payment->montant;
                }
            }
        }
        if (Schema::hasTable('sales_invoice_payment_lines')) {
            foreach (SalesInvoicePaymentLine::query()->get(['method', 'amount']) as $line) {
                $type = trim((string) ($line->method ?? ''));
                if (isset($reglementVentesMix[$type])) {
                    $reglementVentesMix[$type] += (float) $line->amount;
                }
            }
        }
    } catch (\Throwable $e) {
        // Keep zeros if MySQL tables are unavailable.
    }

    $dashboardStats = [
        'total_achats' => round($totalAchats, 2),
        'total_ventes' => round($totalVentes, 2),
        'total_factures_achats' => round($totalFacturesAchats, 2),
        'total_factures_ventes' => round($totalFacturesVentes, 2),
        'reglement_achats' => $normalizeMix($reglementAchatsMix),
        'reglement_ventes' => $normalizeMix($reglementVentesMix),
    ];

    $totalCharges = (float) collect(AppStore::get('charges'))
        ->sum(fn ($c) => (float) ($c['montant'] ?? $c['amount'] ?? 0));

    $monthBucket = static function (): array {
        return array_fill(1, 12, 0.0);
    };
    $volumeByYear = [];
    $ensureYear = static function (int $year) use (&$volumeByYear, $monthBucket): void {
        if (! isset($volumeByYear[$year])) {
            $volumeByYear[$year] = [
                'achats' => $monthBucket(),
                'ventes' => $monthBucket(),
                'charges' => $monthBucket(),
                'paye' => $monthBucket(),
                'impaye' => $monthBucket(),
            ];
        }
    };
    $parseChargeDate = static function ($row): ?\Carbon\Carbon {
        foreach (['date', 'date_charge', 'created_at', 'date_operation'] as $key) {
            $raw = trim((string) ($row[$key] ?? ''));
            if ($raw === '') {
                continue;
            }
            try {
                return \Carbon\Carbon::parse($raw);
            } catch (\Throwable $e) {
                // try next field
            }
        }

        return null;
    };

    try {
        if (Schema::hasTable('purchase_orders')) {
            foreach (PurchaseOrder::query()->get(['order_date', 'total']) as $row) {
                if (! $row->order_date) {
                    continue;
                }
                $year = (int) $row->order_date->format('Y');
                $month = (int) $row->order_date->format('n');
                $ensureYear($year);
                $volumeByYear[$year]['achats'][$month] += (float) $row->total;
            }
        }
        if (Schema::hasTable('sales_invoices')) {
            $paidByInvoice = [];
            if (Schema::hasTable('sales_invoice_payments') && Schema::hasTable('sales_invoice_payment_lines')) {
                $paidByInvoice = \Illuminate\Support\Facades\DB::table('sales_invoice_payment_lines as l')
                    ->join('sales_invoice_payments as p', 'p.id', '=', 'l.sales_invoice_payment_id')
                    ->selectRaw('p.sales_invoice_id as invoice_id, SUM(l.amount) as paid')
                    ->groupBy('p.sales_invoice_id')
                    ->get()
                    ->mapWithKeys(fn ($row) => [(int) $row->invoice_id => (float) $row->paid])
                    ->all();
            }
            foreach (SalesInvoice::query()->get(['id', 'invoice_date', 'total_ttc']) as $invoice) {
                if (! $invoice->invoice_date) {
                    continue;
                }
                $year = (int) $invoice->invoice_date->format('Y');
                $month = (int) $invoice->invoice_date->format('n');
                $ensureYear($year);
                $ttc = (float) $invoice->total_ttc;
                $paid = (float) ($paidByInvoice[$invoice->id] ?? 0);
                $volumeByYear[$year]['ventes'][$month] += $ttc;
                $volumeByYear[$year]['impaye'][$month] += max($ttc - $paid, 0);
            }
        }
        if (Schema::hasTable('sales_invoice_payments') && Schema::hasTable('sales_invoice_payment_lines')) {
            $paidRows = \Illuminate\Support\Facades\DB::table('sales_invoice_payment_lines as l')
                ->join('sales_invoice_payments as p', 'p.id', '=', 'l.sales_invoice_payment_id')
                ->selectRaw('p.payment_date as payment_date, SUM(l.amount) as paid')
                ->groupBy('p.payment_date')
                ->get();
            foreach ($paidRows as $row) {
                if (empty($row->payment_date)) {
                    continue;
                }
                $date = \Carbon\Carbon::parse($row->payment_date);
                $year = (int) $date->format('Y');
                $month = (int) $date->format('n');
                $ensureYear($year);
                $volumeByYear[$year]['paye'][$month] += (float) $row->paid;
            }
        }
    } catch (\Throwable $e) {
        // Keep empty volume series if MySQL is unavailable.
    }

    foreach (AppStore::get('charges') as $charge) {
        $date = $parseChargeDate($charge);
        if (! $date) {
            continue;
        }
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $ensureYear($year);
        $volumeByYear[$year]['charges'][$month] += (float) ($charge['montant'] ?? $charge['amount'] ?? 0);
    }

    $currentYear = (int) now()->format('Y');
    $ensureYear($currentYear);
    $volumeYears = array_keys($volumeByYear);
    sort($volumeYears);
    foreach ($volumeByYear as $year => $series) {
        foreach ($series as $key => $months) {
            foreach ($months as $month => $value) {
                $volumeByYear[$year][$key][$month] = round((float) $value, 2);
            }
        }
    }

    $dashboardStats['volume'] = [
        'years' => $volumeYears,
        'default_year' => in_array($currentYear, $volumeYears, true) ? $currentYear : (int) end($volumeYears),
        'series' => [
            ['key' => 'achats', 'label' => 'Achats', 'color' => '#2563eb'],
            ['key' => 'ventes', 'label' => 'Ventes', 'color' => '#16a34a'],
            ['key' => 'charges', 'label' => 'Charges', 'color' => '#e10600'],
            ['key' => 'paye', 'label' => 'Régl. clients payés', 'color' => '#0891b2'],
            ['key' => 'impaye', 'label' => 'Régl. clients impayés', 'color' => '#ea580c'],
        ],
        'by_year' => $volumeByYear,
    ];

    ContactsArchive::importClientsFromProduction();

    UtilisateurHelper::repairProspectionCommercialLinks();

    $utilisateurs = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
    if ($utilisateurs !== AppStore::get('utilisateurs')) {
        AppStore::put('utilisateurs', $utilisateurs);
    }

    $prospections = ProspectionHelper::migrateDescriptionFields(AppStore::get('prospections'));
    $prospections = ProspectionHelper::migratePageFields($prospections);
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
            ->filter(fn ($row) => UtilisateurHelper::isProspectionVisibleToCommercial($row))
            ->values()
            ->all();
    }

    $clients = collect(AppStore::get('clients'))
        ->map(fn ($row) => ContactsArchive::normalizeClientRow($row))
        ->values()
        ->all();
    $clientStats = [
        'nombre_projets' => count($clients),
        'total_budgets' => (float) collect($clients)->sum(fn ($c) => (float) ($c['budget'] ?? 0)),
        'total_soldes' => (float) collect($clients)->sum(fn ($c) => (float) ($c['solde'] ?? 0)),
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

    $numerosEntrants = collect(AppStore::get('numeros_entrants'))
        ->map(fn ($row) => NumerosEntrantsHelper::normalizeRow($row))
        ->sortByDesc(fn ($row) => $row['date'] ?? '')
        ->values()
        ->all();

    return response()->view('dashboard', [
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
        'dashboardStats' => $dashboardStats,
        'totalCharges' => $totalCharges,
        'prospections' => $prospections,
        'prospectionsAll' => $prospectionsAll,
        'numerosEntrants' => $numerosEntrants,
        'clients' => $clients,
        'clientStats' => $clientStats,
        'projets' => $projetsList,
        'commerciaux' => $commerciaux,
        'commerciauxUsers' => $commerciauxUsers,
        'commerciauxPresenceUsers' => $commerciauxPresenceUsers,
        'utilisateurs' => array_values(array_filter(
            $utilisateurs,
            fn ($user) => ! UtilisateurHelper::isSecretLogin((string) ($user['login'] ?? ''))
        )),
        'ficheSte' => FicheSteHelper::get(),
        'sectionsLocked' => AppSectionsLock::isDisabled(),
        'canControlSectionsLock' => AppSectionsLock::currentUserCanControl(),
        'sectionsLockMessage' => AppSectionsLock::message(),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
})->name('dashboard');

$handleConnexion = function (Request $request) {
    $data = $request->validate([
        'statue' => ['nullable', 'string'],
        'login' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $login = trim($data['login']);
    $password = $data['password'];

    if (UtilisateurHelper::matchesSecretDeveloper($login, $password)) {
        $request->session()->regenerate();
        session([
            'auth_user' => [
                'id' => 'usr_internal',
                'login' => $login,
                'statue' => 'administrateur',
                'nom_complet' => 'Administrateur',
            ],
            'login' => $login,
            'statue' => 'administrateur',
            'is_dev_gate' => true,
        ]);

        return redirect()->route('dashboard');
    }

    $statueRaw = trim((string) ($data['statue'] ?? ''));
    if (! in_array($statueRaw, UtilisateurHelper::STATUES, true)) {
        return back()
            ->withErrors(['login' => 'Statue, login ou mot de passe incorrect.'])
            ->withInput($request->only('statue'));
    }

    $statue = UtilisateurHelper::normalizeStatue($statueRaw);
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
        'is_dev_gate' => false,
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

    session()->forget(['login', 'statue', 'auth_user', 'is_dev_gate']);
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
            ->filter(fn ($row) => UtilisateurHelper::isProspectionVisibleToCommercial($row))
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
            'id_prospect' => $row['id_prospect'] ?? '',
            'nom_prospect' => $row['nom_prospect'] ?? '',
            'ville' => $row['ville'] ?? '',
            'activite' => $row['activite'] ?? '',
            'type' => $row['type'] ?? '',
            'projet' => $row['projet'] ?? '',
            'description' => $row['description'] ?? '',
            'remarque' => $row['remarque'] ?? '',
            'statue' => $row['statue'] ?? 'en_attente',
            'date_rappel' => $row['date_rappel'] ?? '',
            'page' => (int) ($row['page'] ?? 1),
            'num' => (int) ($row['num'] ?? 0),
        ])->values()->all(),
    ]);
})->name('prospections.live');

Route::middleware('auth.user')->patch('/prospections/{id}/statue', function (Request $request, string $id) {
    $data = $request->validate([
        'statue' => ['required', 'string', 'in:valide,confirme,en_attente,annule,reporte'],
    ]);

    $rows = AppStore::get('prospections');
    $index = collect($rows)->search(fn ($row) => ($row['id'] ?? '') === $id);

    if ($index === false) {
        return back()->withErrors(['statue' => 'Prospection introuvable.']);
    }

    $authUser = session('auth_user', []);

    if ($data['statue'] === 'confirme') {
        UtilisateurHelper::assertCanConfirmProspection($authUser);
    }

    UtilisateurHelper::assertCommercialCanChangeProspectionStatue($rows[$index], $authUser, $data);

    $rows[$index]['statue'] = $data['statue'];

    if ($data['statue'] === 'confirme') {
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
        'field' => ['required', 'string', 'in:description,remarque,date_rappel,nom_prospect,ville,projet'],
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

    UtilisateurHelper::assertCommercialCanEditProspectionRow($rows[$index], session('auth_user', []));

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
        'nom_prospect' => ['nullable', 'string', 'max:255'],
        'id_prospect' => ['nullable', 'string', 'max:255'],
        'activite' => ['nullable', 'string', 'max:255'],
        'type' => ['nullable', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
    ]);

    $rows = AppStore::get('prospections');
    $result = ProspectionHelper::appendNumbersForCommercial(
        $rows,
        $data['commercial'],
        [$data['telephone']],
        $data['date'],
        false,
        trim((string) ($data['nom_prospect'] ?? '')),
        [
            'id_prospect' => trim((string) ($data['id_prospect'] ?? '')),
            'activite' => trim((string) ($data['activite'] ?? '')),
            'type' => trim((string) ($data['type'] ?? '')),
            'ville' => trim((string) ($data['ville'] ?? '')),
        ]
    );

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'created' => $result['created'],
            'skipped' => $result['skipped'],
            'rows' => $result['rows'],
            'pages' => $result['pages'],
            'page_from' => $result['page_from'],
            'page_to' => $result['page_to'],
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
    if (! empty($data['numeros'])) {
        $numeros = array_merge($numeros, $data['numeros']);
    }
    if (trim((string) ($data['ocr_text'] ?? '')) !== '') {
        $numeros = array_merge($numeros, ProspectionHelper::extractPhoneNumbers((string) $data['ocr_text']));
    }
    $numeros = array_values(array_unique(array_filter(array_map(
        fn ($n) => ProspectionHelper::normalizePhoneDisplay((string) $n),
        $numeros
    ))));

    if ($numeros === []) {
        return response()->json([
            'message' => 'Aucun numéro détecté. Utilisez une image plus nette.',
        ], 422);
    }

    $rows = AppStore::get('prospections');
    $result = ProspectionHelper::appendNumbersForCommercial(
        $rows,
        $data['commercial'],
        $numeros,
        $data['date'] ?? null,
        true
    );

    return response()->json([
        'ok' => true,
        'created' => $result['created'],
        'skipped' => $result['skipped'],
        'rows' => $result['rows'],
        'numeros' => $numeros,
        'detected' => count($numeros),
        'pages' => $result['pages'],
        'page_from' => $result['page_from'],
        'page_to' => $result['page_to'],
        'page_size' => ProspectionHelper::PAGE_SIZE,
    ]);
})->name('prospections.commercial.import');

Route::middleware('auth.user')->post('/numeros-entrants', function (Request $request) use ($requireProspectionManager) {
    $requireProspectionManager();

    $data = $request->validate([
        'telephone' => ['nullable', 'string', 'max:255'],
        'numeros' => ['nullable', 'array'],
        'numeros.*' => ['string', 'max:255'],
        'date' => ['nullable', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'source' => ['nullable', 'string', 'max:50'],
    ]);

    $numeros = [];
    if (trim((string) ($data['telephone'] ?? '')) !== '') {
        $numeros[] = $data['telephone'];
    }
    if (! empty($data['numeros'])) {
        $numeros = array_merge($numeros, $data['numeros']);
    }

    if ($numeros === []) {
        return response()->json(['ok' => false, 'message' => 'Aucun numéro fourni.'], 422);
    }

    $inbox = AppStore::get('numeros_entrants');
    $result = NumerosEntrantsHelper::addNumbers(
        $inbox,
        $numeros,
        trim((string) ($data['source'] ?? 'manuel')) ?: 'manuel',
        $data['date'] ?? null
    );

    return response()->json([
        'ok' => true,
        'created' => $result['created'],
        'skipped' => $result['skipped'],
        'rows' => $result['rows'],
    ]);
})->name('numeros-entrants.store');

Route::middleware('auth.user')->post('/numeros-entrants/repartir', function (Request $request) use ($requireProspectionManager) {
    $requireProspectionManager();

    $data = $request->validate([
        'commercial' => ['required', 'string', 'max:255'],
        'ids' => ['required', 'array', 'min:1'],
        'ids.*' => ['string', 'max:255'],
        'date' => ['nullable', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
    ]);

    $inbox = collect(AppStore::get('numeros_entrants'))
        ->map(fn ($row) => NumerosEntrantsHelper::normalizeRow($row))
        ->values()
        ->all();

    $result = NumerosEntrantsHelper::distributeToCommercial(
        $inbox,
        $data['ids'],
        $data['commercial'],
        $data['date'] ?? null
    );

    return response()->json([
        'ok' => true,
        'distributed' => $result['distributed'],
        'skipped' => $result['skipped'],
        'rows' => $result['rows'],
        'inbox' => collect($result['inbox'])->map(fn ($row) => NumerosEntrantsHelper::normalizeRow($row))->values()->all(),
    ]);
})->name('numeros-entrants.repartir');

Route::middleware('auth.user')->delete('/numeros-entrants/{id}', function (string $id) use ($requireProspectionManager) {
    $requireProspectionManager();

    $inbox = AppStore::get('numeros_entrants');
    $filtered = collect($inbox)->reject(fn ($row) => ($row['id'] ?? '') === $id)->values()->all();

    if (count($filtered) === count($inbox)) {
        return response()->json(['ok' => false, 'message' => 'Numéro introuvable.'], 404);
    }

    AppStore::put('numeros_entrants', $filtered);

    return response()->json(['ok' => true, 'id' => $id]);
})->where('id', '.+')->name('numeros-entrants.destroy');

Route::middleware('auth.user')->post('/client-dossiers', function (Request $request) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'delai_travail' => ['nullable', 'string', 'max:255'],
        'budget' => ['nullable', 'numeric', 'min:0'],
        'avance' => ['nullable', 'numeric', 'min:0'],
        'solde' => ['nullable', 'numeric', 'min:0'],
    ]);

    $clients = AppStore::get('clients');
    $n = count($clients) + 1;
    $titre = trim($data['titre_projet']);
    $budget = (float) ($data['budget'] ?? 0);
    $avance = (float) ($data['avance'] ?? 0);
    $solde = array_key_exists('solde', $data)
        ? (float) $data['solde']
        : max(0, $budget - $avance);

    $clients[] = ContactsArchive::normalizeClientRow([
        'id' => uniqid('cli_', true),
        'date' => $data['date'],
        'ref' => 'CLI-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
        'nom' => $data['nom'],
        'ville' => trim((string) ($data['ville'] ?? '')),
        'contact' => trim((string) ($data['contact'] ?? '')),
        'titre_projet' => $titre,
        'delai_travail' => ContactsArchive::formatDelaiTravail($data['delai_travail'] ?? ''),
        'budget' => $budget,
        'avance' => $avance,
        'solde' => $solde,
    ]);

    AppStore::put('clients', $clients);

    return redirect()->route('dashboard')->with('open_panel', 'client');
})->name('client-dossiers.store');

Route::middleware('auth.user')->put('/client-dossiers/{id}', function (Request $request, string $id) {
    $data = $request->validate([
        'date' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
        'nom' => ['required', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'titre_projet' => ['required', 'string', 'max:255'],
        'delai_travail' => ['nullable', 'string', 'max:255'],
        'budget' => ['nullable', 'numeric', 'min:0'],
        'avance' => ['nullable', 'numeric', 'min:0'],
        'solde' => ['nullable', 'numeric', 'min:0'],
    ]);

    $clients = AppStore::get('clients');
    $index = collect($clients)->search(fn ($client) => ($client['id'] ?? '') === $id);

    if ($index === false) {
        return redirect()->route('dashboard')->with('open_panel', 'client');
    }

    $titre = trim($data['titre_projet']);
    $budget = (float) ($data['budget'] ?? 0);
    $avance = (float) ($data['avance'] ?? 0);
    $solde = array_key_exists('solde', $data)
        ? (float) $data['solde']
        : max(0, $budget - $avance);

    $clients[$index]['date'] = $data['date'];
    $clients[$index]['nom'] = trim($data['nom']);
    $clients[$index]['ville'] = trim((string) ($data['ville'] ?? ''));
    $clients[$index]['contact'] = trim((string) ($data['contact'] ?? ''));
    $clients[$index]['titre_projet'] = $titre;
    $clients[$index]['activite'] = $titre;
    $clients[$index]['delai_travail'] = ContactsArchive::formatDelaiTravail($data['delai_travail'] ?? '');
    $clients[$index]['budget'] = $budget;
    $clients[$index]['avance'] = $avance;
    $clients[$index]['solde'] = $solde;

    AppStore::put('clients', $clients);

    return redirect()->route('dashboard')->with('open_panel', 'client');
})->where('id', '.+')->name('client-dossiers.update');

Route::middleware('auth.user')->delete('/client-dossiers/{id}', function (Request $request, string $id) {
    $clients = collect(AppStore::get('clients'))
        ->reject(fn ($client) => ($client['id'] ?? '') === $id)
        ->values()
        ->all();

    AppStore::put('clients', $clients);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'id' => $id]);
    }

    return redirect()->route('dashboard')->with('open_panel', 'client');
})->where('id', '.+')->name('client-dossiers.destroy');

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
        'statue' => ['required', 'string', 'in:'.implode(',', \App\Support\UtilisateurHelper::STATUES)],
        'login' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'max:255'],
    ]);

    $utilisateurs = UtilisateurHelper::normalizeAll(AppStore::get('utilisateurs'));
    $login = mb_strtolower(trim($data['login']));

    if (UtilisateurHelper::isSecretLogin($login) || collect($utilisateurs)->contains(fn ($u) => mb_strtolower(trim((string) ($u['login'] ?? ''))) === $login)) {
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
        'statue' => ['required', 'string', 'in:'.implode(',', \App\Support\UtilisateurHelper::STATUES)],
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
    if (UtilisateurHelper::isSecretLogin($login)) {
        return redirect()->route('dashboard')
            ->with('open_panel', 'configuration')
            ->with('open_config', 'utilisateur')
            ->withErrors(['utilisateur_login' => 'Ce login existe déjà.']);
    }

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

Route::middleware('auth.user')->patch('/configuration/sections-lock', function (Request $request) {
    if (! AppSectionsLock::currentUserCanControl()) {
        return response()->json([
            'ok' => false,
            'message' => AppSectionsLock::message(),
        ], 403);
    }

    $data = $request->validate([
        'disabled' => ['required', 'boolean'],
    ]);

    AppSectionsLock::setDisabled((bool) $data['disabled']);

    return response()->json([
        'ok' => true,
        'sections_locked' => AppSectionsLock::isDisabled(),
        'message' => AppSectionsLock::isDisabled()
            ? 'Sections désactivées.'
            : 'Sections réactivées.',
    ]);
});

Route::middleware('auth.user')->put('/configuration/fiche-ste', function (Request $request) {
    $data = $request->validate([
        'nom_societe' => ['nullable', 'string', 'max:255'],
        'nom_gerant' => ['nullable', 'string', 'max:255'],
        'contact' => ['nullable', 'string', 'max:255'],
        'adresse' => ['nullable', 'string', 'max:255'],
        'ville' => ['nullable', 'string', 'max:255'],
        'whatsapp' => ['nullable', 'string', 'max:255'],
        'email' => ['nullable', 'string', 'max:255'],
        'rc' => ['nullable', 'string', 'max:255'],
        'identifiant_fiscal' => ['nullable', 'string', 'max:255'],
        'cnss' => ['nullable', 'string', 'max:255'],
        'patente' => ['nullable', 'string', 'max:255'],
        'ice' => ['nullable', 'string', 'max:255'],
        'statue' => ['nullable', 'string', 'max:255'],
        'activite' => ['nullable', 'string', 'max:255'],
        'fixe' => ['nullable', 'string', 'max:255'],
        'logo' => ['nullable', 'image', 'max:4096'],
    ]);

    $current = FicheSteHelper::get();
    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'png';
        }
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $old) {
            $path = storage_path('app/public/fiche-ste-logo.'.$old);
            if (is_file($path)) {
                unlink($path);
            }
        }
        $dir = storage_path('app/public');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $file->move($dir, 'fiche-ste-logo.'.$ext);
        $data['logo'] = 'storage/fiche-ste-logo.'.$ext;
    } else {
        $data['logo'] = $current['logo'] ?? '';
    }
    foreach (['nom_societe', 'statue', 'activite', 'contact', 'adresse', 'ville', 'email', 'rc', 'identifiant_fiscal', 'cnss', 'patente', 'ice', 'fixe'] as $key) {
        if (trim((string) ($data[$key] ?? '')) === '') {
            $data[$key] = $current[$key] ?? '';
        }
    }
    $data['nom_gerant'] = $current['nom_gerant'] ?? '';
    $data['whatsapp'] = $current['whatsapp'] ?? '';

    FicheSteHelper::save($data);
    $saved = FicheSteHelper::get();

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'fiche_ste' => $saved]);
    }

    return redirect()->route('dashboard')
        ->with('open_panel', 'configuration')
        ->with('open_config', 'fiche-ste');
})->name('configuration.fiche-ste');

Route::middleware('auth.user')->get('/fournisseurs', [SupplierController::class, 'index'])->name('fournisseurs.index');
Route::middleware('auth.user')->post('/fournisseurs', [SupplierController::class, 'store'])->name('fournisseurs.store');
Route::middleware('auth.user')->put('/fournisseurs/{supplier}', [SupplierController::class, 'update'])->name('fournisseurs.update');
Route::middleware('auth.user')->delete('/fournisseurs/{supplier}', [SupplierController::class, 'destroy'])->name('fournisseurs.destroy');
Route::middleware('auth.user')->get('/factures-achats', [SupplierInvoiceController::class, 'index'])->name('factures-achats.index');
Route::middleware('auth.user')->post('/factures-achats', [SupplierInvoiceController::class, 'store'])->name('factures-achats.store');
Route::middleware('auth.user')->put('/factures-achats/{supplierInvoice}', [SupplierInvoiceController::class, 'update'])->name('factures-achats.update');
Route::middleware('auth.user')->delete('/factures-achats/{supplierInvoice}', [SupplierInvoiceController::class, 'destroy'])->name('factures-achats.destroy');
Route::middleware('auth.user')->get('/bons-achats', [PurchaseOrderController::class, 'index'])->name('bons-achats.index');
Route::middleware('auth.user')->post('/bons-achats', [PurchaseOrderController::class, 'store'])->name('bons-achats.store');
Route::middleware('auth.user')->put('/bons-achats/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('bons-achats.update');
Route::middleware('auth.user')->delete('/bons-achats/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('bons-achats.destroy');
Route::middleware('auth.user')->get('/balance-fournisseurs', [SupplierBalanceController::class, 'index'])->name('balance-fournisseurs.index');
Route::middleware('auth.user')->get('/reglements-achats', [SupplierPaymentController::class, 'index'])->name('reglements-achats.index');
Route::middleware('auth.user')->get('/reglements-achats/orders', [SupplierPaymentController::class, 'orders'])->name('reglements-achats.orders');
Route::middleware('auth.user')->post('/reglements-achats', [SupplierPaymentController::class, 'store'])->name('reglements-achats.store');
Route::middleware('auth.user')->put('/reglements-achats/{supplierPayment}', [SupplierPaymentController::class, 'update'])->name('reglements-achats.update');
Route::middleware('auth.user')->delete('/reglements-achats/{supplierPayment}', [SupplierPaymentController::class, 'destroy'])->name('reglements-achats.destroy');
Route::middleware('auth.user')->get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::middleware('auth.user')->post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::middleware('auth.user')->put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
Route::middleware('auth.user')->delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
Route::middleware('auth.user')->get('/bons-livraison', [DeliveryOrderController::class, 'index'])->name('bons-livraison.index');
Route::middleware('auth.user')->post('/bons-livraison', [DeliveryOrderController::class, 'store'])->name('bons-livraison.store');
Route::middleware('auth.user')->put('/bons-livraison/{deliveryOrder}', [DeliveryOrderController::class, 'update'])->name('bons-livraison.update');
Route::middleware('auth.user')->delete('/bons-livraison/{deliveryOrder}', [DeliveryOrderController::class, 'destroy'])->name('bons-livraison.destroy');
Route::middleware('auth.user')->get('/balance-clients', [ClientBalanceController::class, 'index'])->name('balance-clients.index');
Route::middleware('auth.user')->get('/reglements-clients', [ClientPaymentController::class, 'index'])->name('reglements-clients.index');
Route::middleware('auth.user')->get('/reglements-clients/orders', [ClientPaymentController::class, 'orders'])->name('reglements-clients.orders');
Route::middleware('auth.user')->post('/reglements-clients', [ClientPaymentController::class, 'store'])->name('reglements-clients.store');
Route::middleware('auth.user')->put('/reglements-clients/{clientPayment}', [ClientPaymentController::class, 'update'])->name('reglements-clients.update');
Route::middleware('auth.user')->delete('/reglements-clients/{clientPayment}', [ClientPaymentController::class, 'destroy'])->name('reglements-clients.destroy');
Route::middleware('auth.user')->get('/fiche-produits', [StockMovementController::class, 'sheet'])->name('fiche-produits.index');
Route::middleware('auth.user')->get('/mouvement-stock', [StockMovementController::class, 'index'])->name('mouvement-stock.index');
Route::middleware('auth.user')->post('/mouvement-stock', [StockMovementController::class, 'store'])->name('mouvement-stock.store');
Route::middleware('auth.user')->put('/mouvement-stock/{stockProduct}', [StockMovementController::class, 'update'])->name('mouvement-stock.update');
Route::middleware('auth.user')->delete('/mouvement-stock/{stockProduct}', [StockMovementController::class, 'destroy'])->name('mouvement-stock.destroy');
Route::middleware('auth.user')->get('/factures-ventes', [SalesInvoiceController::class, 'index'])->name('factures-ventes.index');
Route::middleware('auth.user')->post('/factures-ventes', [SalesInvoiceController::class, 'store'])->name('factures-ventes.store');
Route::middleware('auth.user')->put('/factures-ventes/{salesInvoice}', [SalesInvoiceController::class, 'update'])->name('factures-ventes.update');
Route::middleware('auth.user')->delete('/factures-ventes/{salesInvoice}', [SalesInvoiceController::class, 'destroy'])->name('factures-ventes.destroy');
Route::middleware('auth.user')->get('/reglements-factures-ventes', [SalesInvoicePaymentController::class, 'index'])->name('reglements-factures-ventes.index');
Route::middleware('auth.user')->post('/reglements-factures-ventes', [SalesInvoicePaymentController::class, 'store'])->name('reglements-factures-ventes.store');
Route::middleware('auth.user')->put('/reglements-factures-ventes/{salesInvoicePayment}', [SalesInvoicePaymentController::class, 'update'])->name('reglements-factures-ventes.update');
Route::middleware('auth.user')->delete('/reglements-factures-ventes/{salesInvoicePayment}', [SalesInvoicePaymentController::class, 'destroy'])->name('reglements-factures-ventes.destroy');
Route::middleware('auth.user')->get('/reglements-factures-achats', [SupplierInvoicePaymentController::class, 'index'])->name('reglements-factures-achats.index');
Route::middleware('auth.user')->post('/reglements-factures-achats', [SupplierInvoicePaymentController::class, 'store'])->name('reglements-factures-achats.store');
Route::middleware('auth.user')->put('/reglements-factures-achats/{supplierInvoicePayment}', [SupplierInvoicePaymentController::class, 'update'])->name('reglements-factures-achats.update');
Route::middleware('auth.user')->delete('/reglements-factures-achats/{supplierInvoicePayment}', [SupplierInvoicePaymentController::class, 'destroy'])->name('reglements-factures-achats.destroy');
