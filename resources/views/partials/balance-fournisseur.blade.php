<section class="panel" id="panel-f-bal">
    <div class="content-head bf-head">
        <div>
            <h1>Balance Fournisseur</h1>
            <p class="bf-sub">Totaux bons d’achat, règlements, soldes et reliquats</p>
        </div>
    </div>

    <div class="bf-summary">
        <div class="bf-card achats"><span>Total Achats</span><strong id="bfSumAchats">0,00</strong></div>
        <div class="bf-card paye"><span>Montant Payé</span><strong id="bfSumPaye">0,00</strong></div>
        <div class="bf-card solde"><span>Solde</span><strong id="bfSumSolde">0,00</strong></div>
        <div class="bf-card reliquat"><span>Reliquat</span><strong id="bfSumReliquat">0,00</strong></div>
    </div>

    <div class="bf-filters">
        <label>ID
            <select id="bfCodeFilter" data-keep-native="1"><option value="">Tous</option></select>
        </label>
        <label>Fournisseur
            <select id="bfNameFilter" data-keep-native="1"><option value="">Tous</option></select>
        </label>
        <button type="button" class="bf-refresh" id="bfRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Balance fournisseurs</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th>ID Fournisseur</th>
                        <th>Fournisseur</th>
                        <th>Total Achats</th>
                        <th>Montant Payé</th>
                        <th>Solde</th>
                        <th>Reliquat</th>
                    </tr>
                </thead>
                <tbody id="bfBody">
                    <tr><td colspan="6">Aucune donnée pour le moment.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    #panel-f-bal .bf-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-f-bal .bf-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .bf-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 220px)); gap: 0.75rem; margin-bottom: 1rem; }
    .bf-card { border-radius: 14px; padding: 0.85rem 1rem; color: #fff; box-shadow: 0 10px 24px rgba(0,0,0,0.2); }
    .bf-card span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.9; }
    .bf-card strong { display: block; margin-top: 0.25rem; font-size: 1.25rem; font-variant-numeric: tabular-nums; }
    .bf-card.achats { background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .bf-card.paye { background: linear-gradient(135deg, #16a34a, #14532d); }
    .bf-card.solde { background: linear-gradient(135deg, #dc2626, #7f1d1d); }
    .bf-card.reliquat { background: linear-gradient(135deg, #ea580c, #9a3412); }
    .bf-filters {
        display: grid; grid-template-columns: 160px minmax(220px, 1fr) 38px; gap: 0.75rem;
        align-items: end; margin-bottom: 1rem;
    }
    .bf-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .bf-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    .bf-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    .bf-refresh svg { width: 16px; height: 16px; }
    #panel-f-bal .procar-table td.num { font-variant-numeric: tabular-nums; font-weight: 700; }
    #panel-f-bal .procar-table td.solde { color: #dc2626; }
    #panel-f-bal .procar-table td.reliquat { color: #ea580c; }
    #panel-f-bal .procar-table td.paye { color: #15803d; }
    [data-theme="light"] .bf-filters label { color: #5c1220; }
    [data-theme="light"] .bf-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    @media (max-width: 1100px) {
        .bf-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .bf-filters { grid-template-columns: 1fr 1fr 38px; }
    }
</style>
<script>
(function () {
    const body = document.getElementById('bfBody');
    const codeFilter = document.getElementById('bfCodeFilter');
    const nameFilter = document.getElementById('bfNameFilter');
    const refreshBtn = document.getElementById('bfRefresh');
    if (!body || !codeFilter || !nameFilter || !refreshBtn) return;

    let rows = [];

    function money(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function fillSelect(select, values, allLabel) {
        const current = select.value;
        select.innerHTML = `<option value="">${allLabel}</option>` + values.map((v) => `<option value="${esc(v)}">${esc(v)}</option>`).join('');
        if ([...select.options].some((opt) => opt.value === current)) select.value = current;
    }
    function filteredRows() {
        return rows.filter((row) => {
            if (codeFilter.value && row.code !== codeFilter.value) return false;
            if (nameFilter.value && row.name !== nameFilter.value) return false;
            return true;
        });
    }
    function paintSummary(list) {
        const achats = list.reduce((s, r) => s + (Number(r.total_achats) || 0), 0);
        const paye = list.reduce((s, r) => s + (Number(r.montant_paye) || 0), 0);
        const solde = list.reduce((s, r) => s + (Number(r.solde) || 0), 0);
        const reliquat = list.reduce((s, r) => s + (Number(r.reliquat) || 0), 0);
        document.getElementById('bfSumAchats').textContent = money(achats);
        document.getElementById('bfSumPaye').textContent = money(paye);
        document.getElementById('bfSumSolde').textContent = money(solde);
        document.getElementById('bfSumReliquat').textContent = money(reliquat);
    }
    function render() {
        const list = filteredRows();
        paintSummary(list);
        if (!list.length) {
            body.innerHTML = '<tr><td colspan="6">Aucun fournisseur pour cette sélection</td></tr>';
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(row.code)}</td>
            <td>${esc(row.name) || '—'}</td>
            <td class="num">${esc(money(row.total_achats))}</td>
            <td class="num paye">${esc(money(row.montant_paye))}</td>
            <td class="num solde">${esc(money(row.solde))}</td>
            <td class="num reliquat">${esc(money(row.reliquat))}</td>
        </tr>`).join('');
    }
    async function load() {
        const response = await fetch('/balance-fournisseurs', {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!response.ok) throw new Error('Chargement impossible');
        const payload = await response.json();
        rows = Array.isArray(payload.data) ? payload.data : [];
        fillSelect(codeFilter, [...new Set(rows.map((r) => r.code).filter(Boolean))].sort(), 'Tous');
        fillSelect(nameFilter, [...new Set(rows.map((r) => r.name).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'fr')), 'Tous');
        render();
    }

    codeFilter.addEventListener('change', render);
    nameFilter.addEventListener('change', render);
    refreshBtn.addEventListener('click', () => load().catch(() => {
        body.innerHTML = '<tr><td colspan="6">Erreur de chargement</td></tr>';
    }));
    document.addEventListener('evopro-ba-changed', () => load().catch(() => {}));
    document.addEventListener('evopro-regl-achats-changed', () => load().catch(() => {}));
    load().catch(() => {
        body.innerHTML = '<tr><td colspan="6">Erreur de chargement</td></tr>';
    });
})();
</script>
