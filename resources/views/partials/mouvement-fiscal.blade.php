<section class="panel" id="panel-fa-fisc">
    <div class="content-head mf-head">
        <div>
            <h1>Mouvement Fiscal</h1>
            <p class="mf-sub">Produits achetés, vendus et stock restant</p>
        </div>
    </div>

    <div class="mf-toolbar">
        <div class="mf-filters">
            <label>Réf
                <input type="search" id="mfRef" autocomplete="off">
            </label>
            <label>Désignation
                <input type="search" id="mfDesignation" autocomplete="off">
            </label>
            <button type="button" class="mf-refresh" id="mfRefresh" title="Actualiser" aria-label="Actualiser">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
            </button>
        </div>

        <div class="mf-stats">
            <div class="mf-stat"><span>Produits</span><strong id="mfCount">0</strong></div>
            <div class="mf-stat buy"><span>Achat</span><strong id="mfAchat">0</strong></div>
            <div class="mf-stat sell"><span>Vente</span><strong id="mfVente">0</strong></div>
            <div class="mf-stat stock"><span>Stock restant</span><strong id="mfStock">0</strong></div>
        </div>
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
    #panel-fa-fisc .mf-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .mf-toolbar { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1rem; }
    .mf-filters { display: flex; align-items: center; gap: 0.75rem; flex: 0 1 auto; min-width: 0; }
    .mf-filters label {
        display: flex; align-items: center; gap: 0.4rem; font-size: 0.62rem; font-weight: 800;
        letter-spacing: 0.08em; color: #e7d5d8; white-space: nowrap;
    }
    .mf-filters input {
        height: 38px; width: 140px; border-radius: 8px; border: 1px solid rgba(143, 36, 54, 0.45);
        background: #0a0a0c; color: #f4f5f6; font-family: inherit; font-size: 0.78rem; padding: 0 0.55rem;
    }
    .mf-filters input#mfDesignation { width: 220px; }
    .mf-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid rgba(143, 36, 54, 0.45); border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; flex: 0 0 auto;
        background: #1a1216; color: #f4f5f6;
    }
    .mf-refresh svg { width: 16px; height: 16px; }
    .mf-stats { display: flex; gap: 0.55rem; margin: 0 0 0 auto; flex: 0 0 auto; }
    .mf-stat { border-radius: 14px; padding: 0.7rem 1rem; min-width: 168px; color: #fff; background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .mf-stat span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; opacity: 0.85; }
    .mf-stat strong { display: block; margin-top: 0.25rem; font-size: 1.45rem; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .mf-stat.buy { background: linear-gradient(135deg, #e10600, #9b0400); }
    .mf-stat.sell { background: linear-gradient(135deg, #16a34a, #14532d); }
    .mf-stat.stock { color: #1a1204; background: linear-gradient(135deg, #facc15, #eab308); }
    .mf-card { border-radius: 16px; overflow: hidden; border: 1px solid rgba(143, 36, 54, 0.45); background: #0a0a0c; }
    .mf-bar { padding: 0.8rem 1.1rem; color: #fff; background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436); }
    .mf-bar h3 { margin: 0; font-size: 0.82rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .mf-scroll { overflow: auto; min-height: 220px; }
    .mf-table { width: 100%; border-collapse: collapse; min-width: 720px; color: #e2e8f0; }
    .mf-table th {
        padding: 0.75rem 0.6rem; font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase;
        color: #e7d5d8; background: #1a1014; text-align: center;
    }
    .mf-table td { padding: 0.7rem 0.6rem; text-align: center; border-top: 1px solid rgba(110, 18, 36, 0.35); }
    .mf-table td.desc { text-align: center; }
    .mf-table .buy { color: #f0c9cf; font-weight: 700; }
    .mf-table .sell { color: #e8b4bc; font-weight: 700; }
    .mf-table .left { color: #f4f5f6; font-weight: 700; }
    .mf-table .neg { color: #f87171; }
    @media (max-width: 1100px) {
        .mf-toolbar { flex-wrap: wrap; }
        .mf-stats { margin-left: 0; }
    }
    [data-theme="light"] .mf-filters label { color: #5c1220; }
    [data-theme="light"] .mf-filters input { background: #fff; color: #1a1214; border-color: #e4c5cb; }
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
    [data-theme="light"] .mf-table th { color: #5c1220; background: #f7eef0; }
    [data-theme="light"] .mf-table td { border-top-color: rgba(143, 36, 54, 0.14); }
    [data-theme="light"] .mf-table tbody tr:hover td { background: rgba(225, 6, 0, 0.06); }
    [data-theme="light"] .mf-table .buy { color: #8f2436; }
    [data-theme="light"] .mf-table .sell { color: #6e1224; }
    [data-theme="light"] .mf-table .left { color: #121214; }
</style>
<script>
(function () {
    const body = document.getElementById('mfBody');
    const refInput = document.getElementById('mfRef');
    const desInput = document.getElementById('mfDesignation');
    if (!body || !refInput || !desInput) return;
    let allRows = [];

    function qty(value) {
        const n = Number(value) || 0;
        return n.toLocaleString('fr-FR', { maximumFractionDigits: 3 });
    }
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function filteredRows() {
        const ref = refInput.value.trim().toLowerCase();
        const des = desInput.value.trim().toLowerCase();
        return allRows.filter((row) => {
            const okRef = !ref || String(row.reference || '').toLowerCase().includes(ref);
            const okDes = !des || String(row.designation || '').toLowerCase().includes(des);
            return okRef && okDes;
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
        render(filteredRows());
    }
    refInput.addEventListener('input', () => render(filteredRows()));
    desInput.addEventListener('input', () => render(filteredRows()));
    document.getElementById('mfRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="5">${esc(error.message)}</td></tr>`; });
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="5">${esc(error.message)}</td></tr>`; });
})();
</script>
