<section class="panel" id="panel-s-prod">
    <div class="content-head">
        <div>
            <h1>Fiche Produit</h1>
            <p class="fp-sub">Produits importés des bons d’achat</p>
        </div>
    </div>
    <div class="fp-filters">
        <label>Mois
            <select id="fpMonth" data-keep-native="1">
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
        <label>Réf<select id="fpRef" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Désignation<select id="fpDesc" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Equiv<select id="fpEquiv" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Marque<select id="fpBrand" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Famille<select id="fpFamily" data-keep-native="1"><option value="">Tous</option></select></label>
        <button type="button" class="fp-refresh" id="fpRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>
    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Liste des produits</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Réf Prod</th>
                        <th>Code Barre</th>
                        <th>Désignation</th>
                        <th>Famille</th>
                        <th>Marque</th>
                        <th>Quantité</th>
                        <th>Stock</th>
                        <th>Prix d'achat</th>
                        <th>U</th>
                        <th>Prix/U</th>
                        <th>Statut</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody id="fpBody">
                    <tr><td colspan="13">Aucun produit importé</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    #panel-s-prod .fp-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-s-prod .fp-filters {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr)) 38px;
        gap: 0.75rem;
        align-items: end;
        margin-bottom: 1rem;
    }
    #panel-s-prod .fp-filters label {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 0;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #e7d5d8;
    }
    #panel-s-prod .fp-filters input,
    #panel-s-prod .fp-filters select {
        width: 100%;
        height: 38px;
        box-sizing: border-box;
        border-radius: 10px;
        border: 1px solid #334155;
        background: #111827;
        color: #f8fafc;
        font-family: inherit;
        font-size: 0.84rem;
        padding: 0 0.7rem;
    }
    #panel-s-prod .fp-refresh {
        width: 38px;
        height: 38px;
        padding: 0;
        border: 1px solid #334155;
        border-radius: 10px;
        display: grid;
        place-items: center;
        cursor: pointer;
        background: #111827;
        color: #f8fafc;
    }
    #panel-s-prod .fp-refresh svg { width: 16px; height: 16px; }
    #panel-s-prod .fp-actif { color: #4ade80; font-weight: 800; }
    #panel-s-prod .fp-rupture { color: #f87171; font-weight: 800; }
    [data-theme="light"] #panel-s-prod .fp-filters label { color: #5c1220; }
    [data-theme="light"] #panel-s-prod .fp-filters input,
    [data-theme="light"] #panel-s-prod .fp-filters select { background: #fff; color: #1a1214; border-color: #e4c5cb; }
    [data-theme="light"] #panel-s-prod .fp-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] #panel-s-prod .fp-actif { color: #15803d; }
    [data-theme="light"] #panel-s-prod .fp-rupture { color: #dc2626; }
    @media (max-width: 1100px) {
        #panel-s-prod .fp-filters { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        #panel-s-prod .fp-refresh { grid-column: 3; justify-self: end; }
    }
</style>
<script>
(function () {
    const body = document.getElementById('fpBody');
    if (!body) return;
    let rows = [];
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function qty(value) {
        const n = Number(value) || 0;
        return Number.isInteger(n) ? String(n) : String(n).replace('.', ',');
    }
    function money(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function dash(value) {
        const text = String(value ?? '').trim();
        return text ? esc(text) : '—';
    }
    const lists = [
        ['fpRef', 'reference'],
        ['fpDesc', 'designation'],
        ['fpEquiv', 'equiv'],
        ['fpBrand', 'brand'],
        ['fpFamily', 'family'],
    ];
    function same(value, query) {
        if (!query) return true;
        return String(value ?? '').trim().toLowerCase() === query;
    }
    function fillLists() {
        lists.forEach(([id, key]) => {
            const select = document.getElementById(id);
            const current = select.value;
            const values = [...new Set(rows.map((row) => String(row[key] ?? '').trim()).filter(Boolean))]
                .sort((a, b) => a.localeCompare(b, 'fr', { sensitivity: 'base' }));
            select.innerHTML = `<option value="">Tous</option>${values.map((value) => `<option value="${esc(value)}">${esc(value)}</option>`).join('')}`;
            select.value = values.includes(current) ? current : '';
        });
    }
    function filtered() {
        const month = document.getElementById('fpMonth').value;
        const ref = document.getElementById('fpRef').value.trim().toLowerCase();
        const desc = document.getElementById('fpDesc').value.trim().toLowerCase();
        const equiv = document.getElementById('fpEquiv').value.trim().toLowerCase();
        const brand = document.getElementById('fpBrand').value.trim().toLowerCase();
        const family = document.getElementById('fpFamily').value.trim().toLowerCase();
        return rows.filter((row) => {
            if (month && !(row.months || []).map(String).includes(month)) return false;
            return same(row.reference, ref)
                && same(row.designation, desc)
                && same(row.equiv, equiv)
                && same(row.brand, brand)
                && same(row.family, family);
        });
    }
    function render() {
        const list = filtered();
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="13">${rows.length ? 'Aucun produit pour cette recherche.' : 'Aucun produit importé'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(row.code)}</td>
            <td>${dash(row.reference)}</td>
            <td>${dash(row.barcode)}</td>
            <td>${esc(row.designation)}</td>
            <td>${dash(row.family)}</td>
            <td>${dash(row.brand)}</td>
            <td>${esc(qty(row.quantity))}</td>
            <td>${esc(qty(row.stock))}</td>
            <td>${esc(money(row.purchase_price))}</td>
            <td>${esc(row.unit || 'U')}</td>
            <td>${esc(money(row.unit_price))}</td>
            <td class="fp-actif">${esc(row.status)}</td>
            <td class="${row.state === 'Rupture' ? 'fp-rupture' : ''}">${esc(row.state)}</td>
        </tr>`).join('');
    }
    async function load() {
        const response = await fetch('/fiche-produits', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json();
        if (!response.ok) throw new Error(payload.message || 'Chargement impossible.');
        rows = payload.data || [];
        fillLists();
        render();
    }
    ['fpMonth', 'fpRef', 'fpDesc', 'fpEquiv', 'fpBrand', 'fpFamily'].forEach((id) => {
        const field = document.getElementById(id);
        field.addEventListener('input', render);
        field.addEventListener('change', render);
    });
    document.getElementById('fpRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="13">${esc(error.message)}</td></tr>`; });
    });
    document.querySelector('[data-panel="s-prod"]')?.addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="13">${esc(error.message)}</td></tr>`; });
    });
    document.addEventListener('evopro-ba-changed', () => { load().catch(() => {}); });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="13">${esc(error.message)}</td></tr>`; });
})();
</script>
