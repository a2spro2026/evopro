<section class="panel" id="panel-s-mvt">
    <div class="content-head sm-head">
        <div>
            <h1>Mouvement Stock</h1>
            <p class="sm-sub">Achat = bon et facture d’achat. Vente = bon de livraison et facture de vente.</p>
        </div>
    </div>

    <div class="sm-toolbar">
        <div class="sm-stats">
            <div class="sm-stat"><span>Produits</span><strong id="smCount">0</strong></div>
            <div class="sm-stat buy"><span>Achat</span><strong id="smSumAchat">0</strong></div>
            <div class="sm-stat sell"><span>Vente</span><strong id="smSumVente">0</strong></div>
            <div class="sm-stat stock"><span>Stock actuel</span><strong id="smSumStock">0</strong></div>
        </div>
    </div>

    <div class="sm-filters">
        <label>Année
            <select id="smYear" data-keep-native="1"><option value="">Tous</option></select>
        </label>
        <label>Mois
            <select id="smMonth" data-keep-native="1">
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
        <label>Réf<select id="smRef" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Désignation<select id="smDesc" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Equiv<select id="smEquiv" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Marque<select id="smBrand" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Famille<select id="smFamily" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Achat<select id="smAchat" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Vente<select id="smVente" data-keep-native="1"><option value="">Tous</option></select></label>
        <button type="button" class="sm-refresh" id="smRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>

    <div class="sm-card">
        <div class="sm-bar"><h3>Liste des mouvements</h3></div>
        <div class="sm-scroll">
            <table class="sm-table">
                <colgroup>
                    <col class="sm-c-ref">
                    <col class="sm-c-desc">
                    <col class="sm-c-init">
                    @for ($i = 0; $i < 24; $i++)
                        <col class="sm-c-m">
                    @endfor
                    <col class="sm-c-stock">
                </colgroup>
                <thead>
                    <tr>
                        <th class="sm-sticky-col sm-col-ref" rowspan="2">Réf</th>
                        <th class="sm-sticky-col sm-col-desc" rowspan="2">Désignation</th>
                        <th class="sm-sticky-col sm-col-init" rowspan="2">Stock Initial</th>
                        <th colspan="2">Jan</th>
                        <th colspan="2">Fév</th>
                        <th colspan="2">Mar</th>
                        <th colspan="2">Avr</th>
                        <th colspan="2">Mai</th>
                        <th colspan="2">Juin</th>
                        <th colspan="2">Juil</th>
                        <th colspan="2">Août</th>
                        <th colspan="2">Sep</th>
                        <th colspan="2">Oct</th>
                        <th colspan="2">Nov</th>
                        <th colspan="2">Déc</th>
                        <th class="sm-col-stock" rowspan="2">Stock Actuel</th>
                    </tr>
                    <tr>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                        <th class="sm-h-achat">Achat</th><th class="sm-h-vente">Vente</th>
                    </tr>
                </thead>
                <tbody id="smBody">
                    <tr><td class="sm-empty" colspan="28">Aucun mouvement</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    #panel-s-mvt .sm-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-s-mvt .sm-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex: 0 0 auto; }
    .sm-toolbar { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 0.85rem; flex: 0 0 auto; }
    .sm-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 168px)); gap: 0.55rem; flex: 0 0 auto; }
    .sm-stat { border-radius: 14px; padding: 0.7rem 1rem; color: #fff; background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .sm-stat span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; opacity: 0.85; }
    .sm-stat strong { display: block; margin-top: 0.25rem; font-size: 1.35rem; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .sm-stat.buy { background: linear-gradient(135deg, #e10600, #9b0400); }
    .sm-stat.sell { background: linear-gradient(135deg, #16a34a, #14532d); }
    .sm-stat.stock { color: #1a1204; background: linear-gradient(135deg, #facc15, #eab308); }
    .sm-filters {
        display: grid;
        grid-template-columns: 110px 140px repeat(5, minmax(0, 1fr)) 100px 100px 38px;
        gap: 0.7rem;
        align-items: end;
        margin-bottom: 0.85rem;
        flex: 0 0 auto;
    }
    .sm-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .sm-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.45rem;
    }
    .sm-filters option { background: #fff; color: #000; }
    .sm-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    .sm-refresh svg { width: 16px; height: 16px; }
    .sm-card {
        border-radius: 16px; overflow: hidden; border: 1px solid rgba(143, 36, 54, 0.45);
        background: #0a0a0c; flex: 1; min-height: 0; display: flex; flex-direction: column;
    }
    .sm-bar {
        padding: 0.8rem 1.1rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436); flex: 0 0 auto;
    }
    .sm-bar h3 { margin: 0; font-size: 0.82rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .sm-scroll { flex: 1; min-height: 0; overflow: auto; scrollbar-gutter: stable; }
    .sm-table {
        --sm-ref: 118px;
        --sm-desc: 240px;
        --sm-init: 88px;
        --sm-m: 58px;
        --sm-stock: 104px;
        width: calc(var(--sm-ref) + var(--sm-desc) + var(--sm-init) + (24 * var(--sm-m)) + var(--sm-stock));
        min-width: calc(var(--sm-ref) + var(--sm-desc) + var(--sm-init) + (24 * var(--sm-m)) + var(--sm-stock));
        border-collapse: separate; border-spacing: 0;
        color: #e2e8f0; table-layout: fixed;
    }
    .sm-table col.sm-c-ref { width: var(--sm-ref); }
    .sm-table col.sm-c-desc { width: var(--sm-desc); }
    .sm-table col.sm-c-init { width: var(--sm-init); }
    .sm-table col.sm-c-m { width: var(--sm-m); }
    .sm-table col.sm-c-stock { width: var(--sm-stock); }
    .sm-table th,
    .sm-table td {
        box-sizing: border-box;
        border-right: 1px solid rgba(143, 36, 54, 0.22);
        border-bottom: 1px solid rgba(143, 36, 54, 0.22);
    }
    .sm-table th {
        position: sticky; z-index: 3;
        padding: 0.45rem 0.2rem; font-size: 0.62rem; letter-spacing: 0.04em; text-transform: uppercase;
        color: #fff; background: #5c1220; text-align: center; white-space: nowrap;
        vertical-align: middle; overflow: hidden;
    }
    .sm-table thead tr:first-child th { top: 0; z-index: 4; height: 2.15rem; }
    .sm-table thead tr:nth-child(2) th { top: 2.15rem; z-index: 3; background: #7a1a2c; height: 1.95rem; }
    .sm-table thead tr:first-child th[rowspan] { z-index: 5; vertical-align: middle; }
    .sm-table thead tr:first-child th[colspan] {
        border-left: 1px solid rgba(255,255,255,0.18);
        border-right: 2px solid rgba(255,255,255,0.28);
    }
    .sm-table .sm-h-achat { color: #fecaca; border-left: 1px solid rgba(255,255,255,0.16); }
    .sm-table .sm-h-vente { color: #bbf7d0; border-right: 2px solid rgba(255,255,255,0.28); }
    .sm-table .sm-col-stock { color: #fde68a; font-weight: 800; }
    .sm-table td {
        padding: 0.55rem 0.2rem; text-align: center; white-space: nowrap;
        color: #000; background: #fff;
        font-variant-numeric: tabular-nums; font-size: 0.78rem; overflow: hidden;
    }
    .sm-table td.sm-empty {
        text-align: left; padding: 1.4rem 1rem; color: #64748b; font-weight: 600;
        position: sticky; left: 0; background: #fff; border-right: 0;
    }
    .sm-table .sm-achat { color: #b91c1c; font-weight: 700; border-left: 1px solid rgba(143, 36, 54, 0.28); }
    .sm-table .sm-vente { color: #15803d; font-weight: 700; border-right: 2px solid rgba(143, 36, 54, 0.35); }
    .sm-table .sm-stock {
        color: #92400e; font-weight: 800; background: #fffbeb;
        position: sticky; right: 0; z-index: 2;
        border-left: 2px solid rgba(143, 36, 54, 0.35);
        border-right: 0;
    }
    .sm-table thead .sm-col-stock {
        position: sticky; right: 0; z-index: 6; background: #5c1220;
        border-left: 2px solid rgba(255,255,255,0.28); border-right: 0;
    }
    .sm-table .sm-sticky-col { position: sticky; z-index: 6; background: #5c1220; }
    .sm-table thead .sm-col-ref { left: 0; }
    .sm-table thead .sm-col-desc { left: var(--sm-ref); }
    .sm-table thead .sm-col-init {
        left: calc(var(--sm-ref) + var(--sm-desc));
        box-shadow: 4px 0 10px rgba(0,0,0,0.12);
        border-right: 2px solid rgba(255,255,255,0.28);
    }
    .sm-table tbody td:nth-child(1) {
        position: sticky; left: 0; z-index: 2; background: #fff; font-weight: 700;
    }
    .sm-table tbody td:nth-child(2) {
        position: sticky; left: var(--sm-ref); z-index: 2; background: #fff; text-align: left; padding-left: 0.55rem;
    }
    .sm-table tbody td:nth-child(3) {
        position: sticky; left: calc(var(--sm-ref) + var(--sm-desc)); z-index: 2; background: #fff;
        box-shadow: 4px 0 10px rgba(0,0,0,0.06); font-weight: 700;
        border-right: 2px solid rgba(143, 36, 54, 0.35);
    }
    .sm-table tbody tr:hover td { background: rgba(225, 6, 0, 0.05); }
    .sm-table tbody tr:hover td:nth-child(1),
    .sm-table tbody tr:hover td:nth-child(2),
    .sm-table tbody tr:hover td:nth-child(3),
    .sm-table tbody tr:hover td.sm-stock { background: #fff5f5; }
    .sm-table tbody tr:hover td.sm-stock { background: #fef3c7; }
    @media (max-width: 1400px) {
        .sm-filters { grid-template-columns: repeat(5, minmax(0, 1fr)) 38px; }
        .sm-refresh { grid-column: 6; justify-self: end; }
        .sm-stats { margin-left: 0; }
    }
    @media (max-width: 900px) {
        .sm-filters { grid-template-columns: repeat(2, minmax(0, 1fr)) 38px; }
        .sm-refresh { grid-column: 3; justify-self: end; }
    }
    [data-theme="light"] .sm-filters label { color: #5c1220; }
    [data-theme="light"] .sm-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .sm-card {
        background: #fff; border-color: rgba(143, 36, 54, 0.22);
        box-shadow: 0 8px 28px rgba(15, 31, 53, 0.06);
    }
    [data-theme="light"] .sm-bar {
        color: #5c1220; background: linear-gradient(90deg, #fff, #f8e8eb 58%, #f3d5db);
    }
    [data-theme="light"] .sm-table th {
        color: #5c1220; background: #f3d5db; box-shadow: inset 0 -1px 0 #e7c9cf;
    }
    [data-theme="light"] .sm-table thead tr:nth-child(2) th { background: #ead0d6; }
    [data-theme="light"] .sm-table .sm-sticky-col { background: #f3d5db; }
    [data-theme="light"] .sm-table .sm-h-achat { color: #b91c1c; }
    [data-theme="light"] .sm-table .sm-h-vente { color: #15803d; }
    [data-theme="light"] .sm-table .sm-col-stock { color: #92400e; background: #f3d5db; }
    [data-theme="light"] .sm-table th,
    [data-theme="light"] .sm-table td { border-color: #e4c5cb; }
    [data-theme="light"] .sm-table thead tr:first-child th[colspan],
    [data-theme="light"] .sm-table .sm-h-vente,
    [data-theme="light"] .sm-table .sm-vente,
    [data-theme="light"] .sm-table thead .sm-col-init,
    [data-theme="light"] .sm-table tbody td:nth-child(3) { border-right-color: #c98a96; }
    [data-theme="light"] .sm-table .sm-stock,
    [data-theme="light"] .sm-table thead .sm-col-stock { border-left-color: #c98a96; }
    body:has(#panel-s-mvt.active) .main { height: 100vh; max-height: 100vh; min-height: 0; overflow: hidden; }
    body:has(#panel-s-mvt.active) .content { display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
    #panel-s-mvt.panel.active { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; }
</style>
<script>
(function () {
    const body = document.getElementById('smBody');
    const yearSelect = document.getElementById('smYear');
    if (!body || !yearSelect) return;
    let rows = [];
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function qty(value) {
        const n = Number(value) || 0;
        return n.toLocaleString('fr-FR', { maximumFractionDigits: 3 });
    }
    function dash(value) {
        const text = String(value ?? '').trim();
        return text ? esc(text) : '—';
    }
    function numKey(value) {
        return String(Math.round((Number(value) || 0) * 1000) / 1000);
    }
    function same(value, query) {
        if (!query) return true;
        return String(value ?? '').trim().toLowerCase() === query;
    }
    const lists = [
        ['smRef', 'reference', false],
        ['smDesc', 'designation', false],
        ['smEquiv', 'equiv', false],
        ['smBrand', 'brand', false],
        ['smFamily', 'family', false],
        ['smAchat', 'achat', true],
        ['smVente', 'vente', true],
    ];
    function fillLists() {
        lists.forEach(([id, key, numeric]) => {
            const select = document.getElementById(id);
            const current = select.value;
            const values = [...new Set(rows.map((row) => numeric ? numKey(row[key]) : String(row[key] ?? '').trim()).filter((value) => value !== ''))];
            values.sort((a, b) => numeric ? Number(a) - Number(b) : a.localeCompare(b, 'fr', { sensitivity: 'base' }));
            select.innerHTML = `<option value="">Tous</option>${values.map((value) => `<option value="${esc(value)}">${esc(numeric ? qty(value) : value)}</option>`).join('')}`;
            select.value = values.includes(current) ? current : '';
        });
    }
    function activeMonths(row) {
        const year = yearSelect.value;
        const source = year ? (row.years?.[year] || {}) : (row.months || {});
        const found = [];
        for (let month = 1; month <= 12; month++) {
            const bag = source[month] || source[String(month)] || {};
            if ((Number(bag.achat) || 0) > 0 || (Number(bag.vente) || 0) > 0) found.push(String(month));
        }
        return found;
    }
    function filtered() {
        const month = document.getElementById('smMonth').value;
        const ref = document.getElementById('smRef').value.trim().toLowerCase();
        const desc = document.getElementById('smDesc').value.trim().toLowerCase();
        const equiv = document.getElementById('smEquiv').value.trim().toLowerCase();
        const brand = document.getElementById('smBrand').value.trim().toLowerCase();
        const family = document.getElementById('smFamily').value.trim().toLowerCase();
        const achat = document.getElementById('smAchat').value;
        const vente = document.getElementById('smVente').value;
        return rows.filter((row) => {
            if (month && !activeMonths(row).includes(month)) return false;
            if (achat !== '' && numKey(row.achat) !== achat) return false;
            if (vente !== '' && numKey(row.vente) !== vente) return false;
            return same(row.reference, ref) && same(row.designation, desc) && same(row.equiv, equiv) && same(row.brand, brand) && same(row.family, family);
        });
    }
    function monthBag(row, month) {
        const year = yearSelect.value;
        const source = year ? (row.years?.[year] || {}) : (row.months || {});
        return source[month] || source[String(month)] || { achat: 0, vente: 0 };
    }
    function paintSummary(list) {
        const achat = list.reduce((s, r) => s + (Number(r.achat) || 0), 0);
        const vente = list.reduce((s, r) => s + (Number(r.vente) || 0), 0);
        const stock = list.reduce((s, r) => s + (Number(r.stock) || 0), 0);
        document.getElementById('smCount').textContent = qty(list.length);
        document.getElementById('smSumAchat').textContent = qty(achat);
        document.getElementById('smSumVente').textContent = qty(vente);
        document.getElementById('smSumStock').textContent = qty(stock);
    }
    function render() {
        const list = filtered();
        paintSummary(list);
        if (!list.length) {
            body.innerHTML = `<tr><td class="sm-empty" colspan="28">${rows.length ? 'Aucun mouvement pour cette recherche.' : 'Aucun mouvement'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => {
            const cells = [];
            for (let month = 1; month <= 12; month++) {
                const bag = monthBag(row, month);
                cells.push(`<td class="sm-achat">${esc(qty(bag.achat))}</td><td class="sm-vente">${esc(qty(bag.vente))}</td>`);
            }
            return `<tr>
                <td>${dash(row.reference)}</td>
                <td>${esc(row.designation)}</td>
                <td>${esc(qty(row.stock_initial))}</td>
                ${cells.join('')}
                <td class="sm-stock">${esc(qty(row.stock))}</td>
            </tr>`;
        }).join('');
    }
    async function load() {
        const response = await fetch('/mouvement-stock', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json();
        if (!response.ok) throw new Error(payload.message || 'Chargement impossible.');
        rows = payload.data || [];
        const current = yearSelect.value;
        const years = payload.meta?.years || [];
        yearSelect.innerHTML = '<option value="">Tous</option>' + years.map((year) => `<option value="${esc(year)}">${esc(year)}</option>`).join('');
        yearSelect.value = years.map(String).includes(current) ? current : '';
        fillLists();
        render();
    }
    yearSelect.addEventListener('change', render);
    ['smMonth', 'smRef', 'smDesc', 'smEquiv', 'smBrand', 'smFamily', 'smAchat', 'smVente'].forEach((id) => {
        document.getElementById(id).addEventListener('change', render);
    });
    document.getElementById('smRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td class="sm-empty" colspan="28">${esc(error.message)}</td></tr>`; });
    });
    document.querySelector('[data-panel="s-mvt"]')?.addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td class="sm-empty" colspan="28">${esc(error.message)}</td></tr>`; });
    });
    document.addEventListener('evopro-ba-changed', () => { load().catch(() => {}); });
    load().catch((error) => { body.innerHTML = `<tr><td class="sm-empty" colspan="28">${esc(error.message)}</td></tr>`; });
})();
</script>
