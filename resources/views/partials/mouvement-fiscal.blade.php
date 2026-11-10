<section class="panel" id="panel-fa-fisc">
    <div class="content-head mf-head">
        <div>
            <h1>Mouvement Fiscal</h1>
            <p class="mf-sub">Produits achetés, vendus et stock restant</p>
        </div>
    </div>

    <div class="mf-toolbar">
        <div class="mf-stats">
            <div class="mf-stat"><span>Produits</span><strong id="mfCount">0</strong></div>
            <div class="mf-stat buy"><span>Achat</span><strong id="mfAchat">0</strong></div>
            <div class="mf-stat sell"><span>Vente</span><strong id="mfVente">0</strong></div>
            <div class="mf-stat stock"><span>Stock restant</span><strong id="mfStock">0</strong></div>
        </div>
    </div>

    <div class="mf-filters">
        <label>Mois
            <select id="mfMonth" data-keep-native="1">
                <option value="">Tous</option>
                <option value="1">Janvier</option>
                <option value="2">Février</option>
                <option value="3">Mars</option>
                <option value="4">Avril</option>
                <option value="5">Mai</option>
                <option value="6">Juin</option>
                <option value="7">Juillet</option>
                <option value="8">Août</option>
                <option value="9">Septembre</option>
                <option value="10">Octobre</option>
                <option value="11">Novembre</option>
                <option value="12">Décembre</option>
            </select>
        </label>
        <label>Réf<select id="mfRef" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Désignation<select id="mfDesc" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Equiv<select id="mfEquiv" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Marque<select id="mfBrand" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Famille<select id="mfFamily" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Achat<select id="mfBuy" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Vente<select id="mfSell" data-keep-native="1"><option value="">Tous</option></select></label>
        <button type="button" class="mf-refresh" id="mfRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>

    <div class="mf-card">
        <div class="mf-bar"><h3>Mouvement fiscal</h3></div>
        <div class="mf-scroll">
            <table class="mf-table">
                <thead>
                    <tr>
                        <th>Réf</th>
                        <th>Désignation</th>
                        <th>Achat</th>
                        <th>Vente</th>
                        <th>Stock restant</th>
                    </tr>
                </thead>
                <tbody id="mfBody">
                    <tr><td colspan="5">Aucune donnée pour le moment.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    #panel-fa-fisc .mf-sub { text-transform: none; color: var(--muted); margin-top: 0.35rem; }
    .mf-toolbar { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1rem; flex: 0 0 auto; }
    #panel-fa-fisc .mf-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex: 0 0 auto; }
    .mf-filters {
        display: grid;
        grid-template-columns: repeat(8, minmax(0, 1fr)) 38px;
        gap: 0.75rem;
        align-items: end;
        margin-bottom: 1rem;
    }
    .mf-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .mf-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.45rem;
    }
    .mf-filters option { background: #fff; color: #000; }
    .mf-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer;
        background: #111827; color: #f8fafc;
    }
    .mf-refresh svg { width: 16px; height: 16px; }
    .mf-stats { display: flex; gap: 0.55rem; margin: 0 0 0 auto; flex: 0 0 auto; }
    .mf-stat { border-radius: 14px; padding: 0.7rem 1rem; min-width: 168px; color: #fff; background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .mf-stat span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; opacity: 0.85; }
    .mf-stat strong { display: block; margin-top: 0.25rem; font-size: 1.45rem; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .mf-stat.buy { background: linear-gradient(135deg, #e10600, #9b0400); }
    .mf-stat.sell { background: linear-gradient(135deg, #16a34a, #14532d); }
    .mf-stat.stock { color: #1a1204; background: linear-gradient(135deg, #facc15, #eab308); }
    .mf-card { border-radius: 16px; overflow: hidden; border: 1px solid rgba(143, 36, 54, 0.45); background: #0a0a0c; flex: 1; min-height: 0; display: flex; flex-direction: column; }
    .mf-bar { padding: 0.8rem 1.1rem; color: #fff; background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436); flex: 0 0 auto; }
    .mf-bar h3 { margin: 0; font-size: 0.82rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .mf-scroll { flex: 1; min-height: 0; overflow: auto; }
    .mf-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 720px; color: #e2e8f0; }
    .mf-table th {
        position: sticky; top: 0; z-index: 2;
        padding: 0.75rem 0.6rem; font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase;
        color: #fff; background: #5c1220; text-align: center;
        box-shadow: inset 0 -1px 0 #8f2436;
    }
    .mf-table td { padding: 0.7rem 0.6rem; text-align: center; border-top: 1px solid rgba(110, 18, 36, 0.35); color: #000; background: #fff; }
    .mf-table td.desc { text-align: center; }
    .mf-table .buy { color: #000; font-weight: 700; }
    .mf-table .sell { color: #000; font-weight: 700; }
    .mf-table .left { color: #000; font-weight: 700; }
    .mf-table .neg { color: #f87171; }
    @media (max-width: 1200px) {
        .mf-toolbar { flex-wrap: wrap; }
        .mf-stats { margin-left: 0; }
        .mf-filters { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .mf-refresh { grid-column: 4; justify-self: end; }
    }
    [data-theme="light"] .mf-filters label { color: #5c1220; }
    [data-theme="light"] .mf-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .mf-stat { color: #fff; background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    [data-theme="light"] .mf-stat.buy { color: #fff; background: linear-gradient(135deg, #e10600, #9b0400); }
    [data-theme="light"] .mf-stat.sell { color: #fff; background: linear-gradient(135deg, #16a34a, #14532d); }
    [data-theme="light"] .mf-stat.stock { color: #1a1204; background: linear-gradient(135deg, #facc15, #eab308); }
    [data-theme="light"] .mf-card {
        background: #fff;
        border-color: rgba(143, 36, 54, 0.22);
        box-shadow: 0 8px 28px rgba(15, 31, 53, 0.06);
    }
    [data-theme="light"] .mf-bar {
        color: #5c1220;
        background: linear-gradient(90deg, #fff, #f8e8eb 58%, #f3d5db);
    }
    [data-theme="light"] .mf-table { color: #1a1214; }
    [data-theme="light"] .mf-table th { color: #5c1220; background: #f3d5db; box-shadow: inset 0 -1px 0 #e7c9cf; }
    body:has(#panel-fa-fisc.active) .main { height: 100vh; max-height: 100vh; min-height: 0; overflow: hidden; }
    body:has(#panel-fa-fisc.active) .content { display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
    #panel-fa-fisc.panel.active { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; }
    [data-theme="light"] .mf-table td { border-top-color: rgba(143, 36, 54, 0.14); }
    [data-theme="light"] .mf-table tbody tr:hover td { background: rgba(225, 6, 0, 0.06); }
    [data-theme="light"] .mf-table .buy { color: #000; }
    [data-theme="light"] .mf-table .sell { color: #000; }
    [data-theme="light"] .mf-table .left { color: #000; }
</style>
<script>
(function () {
    const body = document.getElementById('mfBody');
    if (!body) return;
    let allRows = [];

    function qty(value) {
        const n = Number(value) || 0;
        return n.toLocaleString('fr-FR', { maximumFractionDigits: 3 });
    }
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function numKey(value) {
        return String(Math.round((Number(value) || 0) * 1000) / 1000);
    }
    function same(value, query) {
        if (!query) return true;
        return String(value ?? '').trim().toLowerCase() === query;
    }
    const lists = [
        ['mfRef', 'reference', false],
        ['mfDesc', 'designation', false],
        ['mfEquiv', 'equiv', false],
        ['mfBrand', 'brand', false],
        ['mfFamily', 'family', false],
        ['mfBuy', 'achat', true],
        ['mfSell', 'vente', true],
    ];
    function fillLists() {
        lists.forEach(([id, key, numeric]) => {
            const select = document.getElementById(id);
            const current = select.value;
            const values = [...new Set(allRows.map((row) => numeric ? numKey(row[key]) : String(row[key] ?? '').trim()).filter((value) => value !== ''))];
            values.sort((a, b) => numeric ? Number(a) - Number(b) : a.localeCompare(b, 'fr', { sensitivity: 'base' }));
            select.innerHTML = `<option value="">Tous</option>${values.map((value) => `<option value="${esc(value)}">${esc(numeric ? qty(value) : value)}</option>`).join('')}`;
            select.value = values.includes(current) ? current : '';
        });
    }
    function filteredRows() {
        const month = document.getElementById('mfMonth').value;
        const ref = document.getElementById('mfRef').value.trim().toLowerCase();
        const desc = document.getElementById('mfDesc').value.trim().toLowerCase();
        const equiv = document.getElementById('mfEquiv').value.trim().toLowerCase();
        const brand = document.getElementById('mfBrand').value.trim().toLowerCase();
        const family = document.getElementById('mfFamily').value.trim().toLowerCase();
        const achat = document.getElementById('mfBuy').value;
        const vente = document.getElementById('mfSell').value;
        return allRows.filter((row) => {
            if (month && !(row.months || []).map(String).includes(month)) return false;
            if (achat !== '' && numKey(row.achat) !== achat) return false;
            if (vente !== '' && numKey(row.vente) !== vente) return false;
            return same(row.reference, ref) && same(row.designation, desc) && same(row.equiv, equiv) && same(row.brand, brand) && same(row.family, family);
        });
    }
    function render(rows) {
        const sum = (key) => rows.reduce((total, row) => total + (Number(row[key]) || 0), 0);
        document.getElementById('mfCount').textContent = qty(rows.length);
        document.getElementById('mfAchat').textContent = qty(sum('achat'));
        document.getElementById('mfVente').textContent = qty(sum('vente'));
        document.getElementById('mfStock').textContent = qty(sum('stock'));
        if (!rows.length) {
            const message = allRows.length ? 'Aucun produit pour cette recherche.' : 'Aucune donnée pour le moment.';
            body.innerHTML = `<tr><td colspan="5">${message}</td></tr>`;
            return;
        }
        body.innerHTML = rows.map((row) => `<tr>
            <td>${esc(row.reference) || '—'}</td>
            <td class="desc">${esc(row.designation) || '—'}</td>
            <td class="buy">${qty(row.achat)}</td>
            <td class="sell">${qty(row.vente)}</td>
            <td class="${Number(row.stock) < 0 ? 'neg' : 'left'}">${qty(row.stock)}</td>
        </tr>`).join('');
    }
    async function load() {
        const response = await fetch('/mouvement-fiscal', { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Chargement impossible.');
        const payload = await response.json();
        allRows = payload.data || [];
        fillLists();
        render(filteredRows());
    }
    ['mfMonth', 'mfRef', 'mfDesc', 'mfEquiv', 'mfBrand', 'mfFamily', 'mfBuy', 'mfSell'].forEach((id) => {
        document.getElementById(id).addEventListener('change', () => render(filteredRows()));
    });
    document.getElementById('mfRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="5">${esc(error.message)}</td></tr>`; });
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="5">${esc(error.message)}</td></tr>`; });
})();
</script>
