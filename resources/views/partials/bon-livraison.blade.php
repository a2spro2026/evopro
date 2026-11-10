<section class="panel" id="panel-c-bl">
    <div class="content-head bl-head">
        <div>
            <h1>Bon Livraison</h1>
            <p class="bl-sub">Bons de livraison clients</p>
        </div>
        <button type="button" class="bl-add" id="blAdd" title="Ajouter" aria-label="Ajouter">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
        </button>
    </div>

    <div class="bl-summary">
        <div class="bl-card ventes"><span>Total Ventes</span><strong id="blSumVentes">0,00</strong></div>
        <div class="bl-card paye"><span>Total Payé</span><strong id="blSumPaye">0,00</strong></div>
        <div class="bl-card solde"><span>Solde</span><strong id="blSumSolde">0,00</strong></div>
    </div>

    <div class="bl-filters">
        <label>Mois
            <select id="blMonth" data-keep-native="1">
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
        <label>Client
            <select id="blClientFilter" data-keep-native="1">
                <option value="">Tous</option>
            </select>
        </label>
        <button type="button" class="bl-refresh" id="blRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Liste des bons de livraison</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>N° BL</th>
                        <th>Client</th>
                        <th>ID Clt</th>
                        <th>Qté totale</th>
                        <th>Total</th>
                        <th>Paiement</th>
                        <th>Échéance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="blBody">
                    <tr><td colspan="9">Aucun bon de livraison enregistré</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bl-window" id="blModal" hidden>
        <div class="bl-sheet" role="dialog" aria-modal="true" aria-labelledby="blModalTitle">
            <div class="bl-sheet-head">
                <h3 id="blModalTitle">Nouveau bon de livraison</h3>
                <button type="button" class="bl-x" id="blCloseX" title="Fermer" aria-label="Fermer">×</button>
            </div>
            <form id="blForm" class="bl-form" autocomplete="off">
                <div class="bl-error" id="blError" hidden></div>
                <div class="bl-grid">
                    <div class="bl-field">
                        <label for="blDate">Date</label>
                        <input id="blDate" type="date" required>
                    </div>
                    <div class="bl-field">
                        <label for="blClt">ID Clt</label>
                        <input id="blClt" maxlength="64" placeholder="CC-0001" list="blCltList">
                        <datalist id="blCltList"></datalist>
                    </div>
                    <div class="bl-field bl-span-2">
                        <label for="blClient">Nom Client</label>
                        <input id="blClient" required maxlength="255" placeholder="Nom client" list="blClientList">
                        <datalist id="blClientList"></datalist>
                    </div>
                    <div class="bl-field">
                        <label for="blRef">N° BL</label>
                        <input id="blRef" required maxlength="64" placeholder="BL-0001">
                    </div>
                    <div class="bl-field bl-field-reg">
                        <label for="blPaiement">Type Rég</label>
                        <select id="blPaiement">
                            <option value="">—</option>
                            <option>Esp</option>
                            <option>Chq</option>
                            <option>Eff</option>
                            <option>Vir</option>
                            <option>Vers</option>
                        </select>
                    </div>
                    <div class="bl-field bl-field-ech">
                        <label for="blEcheance">Échéance</label>
                        <select id="blEcheance">
                            <option value="">—</option>
                            <option>45 Jrs</option>
                            <option>60 Jrs</option>
                            <option>90 Jrs</option>
                            <option>120 Jrs</option>
                        </select>
                    </div>
                </div>

                <div class="bl-lines-head">
                    <p>Détail</p>
                    <button type="button" class="bl-add-line" id="blAddLine" title="Ajouter une ligne" aria-label="Ajouter une ligne">+</button>
                </div>
                <div class="bl-lines-wrap">
                    <table class="bl-lines">
                        <colgroup>
                            <col class="c-ref">
                            <col class="c-barcode">
                            <col class="c-equiv">
                            <col class="c-desc">
                            <col class="c-brand">
                            <col class="c-family">
                            <col class="c-qty">
                            <col class="c-price">
                            <col class="c-remise">
                            <col class="c-sub">
                            <col class="c-x">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Réf</th>
                                <th>Code Barre</th>
                                <th>Equiv</th>
                                <th>Désignation</th>
                                <th>Marque</th>
                                <th>Famille</th>
                                <th>Qté</th>
                                <th>Prix/U</th>
                                <th>Remise</th>
                                <th>Sous-total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="blLines"></tbody>
                    </table>
                    <datalist id="blBrandList"></datalist>
                    <datalist id="blFamilyList"></datalist>
                </div>
                <div class="bl-totals">
                    <div><small>Qté totale</small><strong id="blQtyTotal">0</strong></div>
                    <div><small>Total</small><strong id="blAmountTotal">0,00</strong></div>
                </div>
                <div class="bl-actions">
                    <button type="button" class="bl-btn ghost" id="blClose">Fermer</button>
                    <button type="submit" class="bl-btn primary" id="blSave">Valider</button>
                </div>
            </form>
        </div>
    </div>
</section>
<style>
    #panel-c-bl .bl-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-c-bl .bl-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .bl-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 220px)); gap: 0.75rem; margin-bottom: 1rem; }
    .bl-card { border-radius: 14px; padding: 0.85rem 1rem; color: #fff; box-shadow: 0 10px 24px rgba(0,0,0,0.2); }
    .bl-card span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.9; }
    .bl-card strong { display: block; margin-top: 0.25rem; font-size: 1.3rem; font-variant-numeric: tabular-nums; }
    .bl-card.ventes { background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .bl-card.paye { background: linear-gradient(135deg, #16a34a, #14532d); }
    .bl-card.solde { background: linear-gradient(135deg, #dc2626, #7f1d1d); }
    .bl-filters {
        display: grid; grid-template-columns: 180px minmax(220px, 1fr) 38px; gap: 0.75rem;
        align-items: end; margin-bottom: 1rem;
    }
    .bl-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .bl-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    .bl-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    .bl-refresh svg { width: 16px; height: 16px; }
    [data-theme="light"] .bl-filters label { color: #5c1220; }
    [data-theme="light"] .bl-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    .bl-add {
        display: inline-grid; place-items: center;
        width: 42px; height: 42px; border: none; border-radius: 12px; cursor: pointer;
        background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff;
        box-shadow: 0 8px 18px rgba(143, 36, 54, 0.28);
    }
    .bl-add svg { width: 20px; height: 20px; }
    .bl-add:hover { filter: brightness(1.06); }
    .bl-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden .bl-window { left: 0; }
    .bl-window[hidden] { display: none !important; }
    .bl-sheet {
        width: min(1380px, calc(100vw - 2.5rem)); margin: auto; border-radius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #4a1824; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    .bl-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .bl-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .bl-x {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        background: transparent; color: rgba(255,255,255,0.82); font-size: 1.35rem; line-height: 1;
    }
    .bl-x:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .bl-form { padding: 1rem 1.15rem 0.4rem; }
    .bl-error { margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-radius: 10px; background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #7f1d1d; font-size: 0.82rem; }
    .bl-grid {
        display: grid;
        grid-template-columns: 140px 110px minmax(220px, 1.6fr) 130px 110px 110px;
        gap: 0.65rem;
        align-items: end;
    }
    .bl-span-2 { grid-column: span 1; }
    .bl-field { display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }
    .bl-field label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em; color: #94a3b8; }
    .bl-field input, .bl-field select {
        box-sizing: border-box;
        width: 100%; height: 38px; min-height: 38px; max-height: 38px;
        border-radius: 10px; border: 1px solid #334155;
        background-color: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem;
        padding: 0 0.7rem; line-height: 36px; margin: 0;
    }
    .bl-field select {
        -webkit-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2394a3b8' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 12px 8px;
        padding-right: 1.8rem;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .bl-field select:focus,
    .bl-field input:focus {
        background-color: #fff; color: #000;
        outline: 2px solid #8f2436; outline-offset: 1px;
    }
    .bl-field select option { background: #fff; color: #000; }
    .bl-field input::placeholder,
    .bl-lines input::placeholder { color: #64748b; }
    .bl-lines-head {
        display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
        margin: 1rem 0 0.55rem;
    }
    .bl-lines-head p {
        margin: 0; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #e7c4c8;
        text-decoration: underline; text-underline-offset: 3px;
    }
    .bl-add-line {
        width: 32px; height: 32px; border: none; border-radius: 8px; cursor: pointer;
        background: #1e293b; color: #fff; font-size: 1.2rem; line-height: 1; font-weight: 700;
    }
    .bl-add-line:hover { background: #8f2436; }
    .bl-lines-wrap {
        max-height: 340px; overflow: auto; border: 1px solid #334155; border-radius: 10px;
    }
    .bl-lines { width: 100%; border-collapse: collapse; table-layout: fixed; min-width: 1180px; }
    .bl-lines col.c-ref { width: 7%; }
    .bl-lines col.c-barcode { width: 9%; }
    .bl-lines col.c-equiv { width: 7%; }
    .bl-lines col.c-desc { width: 22%; }
    .bl-lines col.c-brand { width: 10%; }
    .bl-lines col.c-family { width: 10%; }
    .bl-lines col.c-qty { width: 6%; }
    .bl-lines col.c-price { width: 8%; }
    .bl-lines col.c-remise { width: 7%; }
    .bl-lines col.c-sub { width: 9%; }
    .bl-lines col.c-x { width: 40px; }
    .bl-lines th {
        position: sticky; top: 0; background: #5c1220; color: #fff;
        font-size: 0.62rem; letter-spacing: 0.06em; text-transform: uppercase;
        padding: 0.5rem; text-align: center;
    }
    .bl-lines th:nth-child(4) { text-align: left; padding-left: 0.65rem; }
    .bl-lines td { padding: 0.3rem; border-top: 1px solid #1e293b; }
    .bl-lines input {
        box-sizing: border-box;
        width: 100%; height: 34px; border-radius: 8px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.78rem; padding: 0 0.45rem; text-align: center;
    }
    .bl-lines input[data-f="desc"] { text-align: left; padding-left: 0.65rem; }
    .bl-lines select {
        box-sizing: border-box;
        width: 100%; height: 34px; border-radius: 8px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.78rem; padding: 0 0.35rem; text-align: center;
    }
    .bl-lines select option { background: #fff; color: #000; }
    .bl-lines select[data-f="desc"] { text-align: left; }
    .bl-remise { position: relative; }
    .bl-remise input { padding-right: 1.1rem; }
    .bl-remise span {
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
        font-size: 0.68rem; color: #94a3b8; pointer-events: none;
    }
    .bl-lines .bl-line-x {
        width: 28px; height: 28px; border: none; border-radius: 8px; background: transparent;
        color: #f87171; cursor: pointer; font-size: 1rem;
    }
    .bl-totals {
        display: grid; grid-template-columns: repeat(2, minmax(0, 180px)); gap: 0.55rem;
        justify-content: end; margin-top: 0.8rem;
    }
    .bl-totals div {
        border-radius: 12px; padding: 0.55rem 0.7rem; text-align: center;
        background: #fff; border: 1px solid #334155; color: #000;
    }
    .bl-totals small { display: block; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; }
    .bl-totals strong { display: block; margin-top: 0.15rem; font-size: 0.95rem; }
    .bl-actions { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.95rem 0 1rem; }
    .bl-btn { height: 36px; padding: 0 1rem; border-radius: 999px; border: none; font-family: inherit; font-weight: 700; font-size: 0.78rem; cursor: pointer; }
    .bl-btn.ghost { background: #1e293b; color: #e2e8f0; border: 1px solid #475569; }
    .bl-btn.primary { background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff; }
    .bl-icon {
        display: inline-grid; place-items: center; width: 28px; height: 28px; padding: 0;
        border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer;
    }
    .bl-icon svg { width: 15px; height: 15px; }
    .bl-icon.see:hover, .bl-icon.edit:hover { color: #e7c4c8; background: rgba(143,36,54,0.16); }
    .bl-icon.del:hover { color: #f87171; background: rgba(248,113,113,0.12); }
    .bl-form.is-view .bl-add-line,
    .bl-form.is-view .bl-line-x,
    .bl-form.is-view #blSave { display: none; }
    .bl-form.is-view input,
    .bl-form.is-view select { opacity: 0.85; }
    [data-theme="light"] .bl-sheet { background: #fff; border-color: #e7c9cf; color: #1a1214; }
    [data-theme="light"] .bl-field label,
    [data-theme="light"] .bl-totals small,
    [data-theme="light"] .bl-lines th { color: #5c1220; background: #f3d5db; }
    [data-theme="light"] .bl-field input,
    [data-theme="light"] .bl-field select,
    [data-theme="light"] .bl-lines input,
    [data-theme="light"] .bl-lines select { background: #fff; color: #000; border-color: #e4c5cb; }
    [data-theme="light"] .bl-field select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%237a5a62' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 12px 8px;
    }
    [data-theme="light"] .bl-lines-wrap { border-color: #e4c5cb; }
    [data-theme="light"] .bl-lines th { background: #f3d5db; color: #5c1220; }
    [data-theme="light"] .bl-lines td { border-top-color: #f0dde1; }
    [data-theme="light"] .bl-totals div { background: #fff; border-color: #e7c9cf; }
    [data-theme="light"] .bl-add-line { background: #f3d5db; color: #5c1220; }
    [data-theme="light"] .bl-btn.ghost { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .bl-icon { color: #64748b; }
    [data-theme="light"] .bl-lines-head p { color: #8f2436; }
    [data-theme="light"] .bl-remise span { color: #7a5a62; }
    @media (max-width: 900px) {
        .bl-window { left: 0; }
        .bl-grid { grid-template-columns: 1fr 1fr; }
        .bl-field.bl-span-2 { grid-column: 1 / -1; }
    }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-c-bl');
    const modal = document.getElementById('blModal');
    const form = document.getElementById('blForm');
    const linesBody = document.getElementById('blLines');
    if (!panel || !modal || !form || !linesBody) return;
    const body = document.getElementById('blBody');
    const errorBox = document.getElementById('blError');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    let rows = [];
    let clients = [];
    let catalog = [];
    let seq = 1;
    let editingId = null;
    let mode = 'create';

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function parseNum(value) {
        const n = Number(String(value ?? '').replace(/\s/g, '').replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    }
    function money(value) {
        return parseNum(value).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function moneyDot(value) {
        return parseNum(value).toFixed(2);
    }
    function todayInput() {
        const d = new Date();
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }
    function formatDate(value) {
        if (!value) return '—';
        const parts = String(value).split('-');
        if (parts.length !== 3) return value;
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    function showError(message) {
        errorBox.hidden = !message;
        errorBox.textContent = message || '';
    }
    function nextRef() {
        return `BL-${String(seq).padStart(4, '0')}`;
    }
    function norm(value) {
        return String(value ?? '').trim().toLowerCase();
    }
    function productKey(item) {
        return [
            item.reference ?? item.ref,
            item.designation ?? item.description,
            item.equiv,
            item.brand,
            item.family,
        ].map(norm).join('\u0001');
    }
    function choiceValue(product) {
        return String(product.code || productKey(product));
    }
    function knownProduct(key) {
        if (!key || key === '__new__') return null;
        const direct = catalog.find((item) => choiceValue(item) === key)
            || catalog.find((item) => productKey(item) === key);
        if (direct) return direct;
        const needle = norm(key);
        const byLabel = catalog.filter((item) => [item.reference, item.designation, item.equiv, item.code]
            .some((value) => norm(value) === needle));
        return byLabel.length === 1 ? byLabel[0] : null;
    }
    function productFromSelect(select) {
        if (!select) return null;
        const byValue = knownProduct(select.value);
        if (byValue) return byValue;
        const label = select.selectedOptions?.[0]?.textContent?.trim() || '';
        return knownProduct(label);
    }
    function applyCatalogProduct(row, product) {
        if (!product) return;
        const code = choiceValue(product);
        row.querySelectorAll('select[data-f]').forEach((field) => {
            if ([...field.options].some((option) => option.value === code)) field.value = code;
        });
        const brandInput = row.querySelector('[data-f="brand"]');
        const familyInput = row.querySelector('[data-f="family"]');
        const barcodeInput = row.querySelector('[data-f="barcode"]');
        const priceInput = row.querySelector('[data-f="price"]');
        if (brandInput && !brandInput.matches('select')) brandInput.value = product.brand || '';
        if (familyInput && !familyInput.matches('select')) familyInput.value = product.family || '';
        if (barcodeInput) barcodeInput.value = product.barcode || '';
        if (priceInput) {
            priceInput.value = moneyDot(product.purchase_price ?? product.unit_price ?? 0);
            priceInput.readOnly = true;
        }
    }
    async function ensureCatalog() {
        const response = await fetch('/fiche-produits', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json().catch(() => ({}));
        catalog = response.ok ? (payload.data || []) : catalog;
        fillBrandFamilyLists();
    }
    function uniqueLabels(values) {
        return [...new Set(values.map((value) => String(value ?? '').trim()).filter(Boolean))]
            .sort((a, b) => a.localeCompare(b, 'fr', { sensitivity: 'base' }));
    }
    function fillBrandFamilyLists() {
        const fromCatalog = catalog || [];
        const fromOrders = (rows || []).flatMap((row) => row.items || []);
        const fromLines = [...linesBody.querySelectorAll('tr')].map((row) => ({
            brand: row.querySelector('[data-f="brand"]')?.value,
            family: row.querySelector('[data-f="family"]')?.value,
        }));
        const brands = uniqueLabels([
            ...fromCatalog.map((item) => item.brand),
            ...fromOrders.map((item) => item.brand),
            ...fromLines.map((item) => item.brand),
        ]);
        const families = uniqueLabels([
            ...fromCatalog.map((item) => item.family),
            ...fromOrders.map((item) => item.family),
            ...fromLines.map((item) => item.family),
        ]);
        document.getElementById('blBrandList').innerHTML = brands.map((value) => `<option value="${esc(value)}"></option>`).join('');
        document.getElementById('blFamilyList').innerHTML = families.map((value) => `<option value="${esc(value)}"></option>`).join('');
    }
    function brandField(value = '') {
        return `<input data-f="brand" maxlength="120" list="blBrandList" value="${esc(value || '')}" placeholder="Marque" autocomplete="off">`;
    }
    function familyField(value = '') {
        return `<input data-f="family" maxlength="120" list="blFamilyList" value="${esc(value || '')}" placeholder="Famille" autocomplete="off">`;
    }
    function identityCells(item, pickedKey) {
        const known = pickedKey ? knownProduct(pickedKey) : null;
        if (catalog.length && (known || !item.description && !item.ref && !item.equiv)) {
            const selected = known ? choiceValue(known) : '';
            const choices = (field, labelOf) => catalog.map((product) => {
                const key = choiceValue(product);
                const label = labelOf(product) || '—';
                const achat = product.purchase_price ?? product.unit_price ?? 0;
                return `<option value="${esc(key)}" data-achat="${esc(achat)}"${key === selected ? ' selected' : ''}>${esc(label)}</option>`;
            }).join('');
            const nouveau = mode === 'view' ? '' : '<option value="__new__">Nouveau</option>';
            const box = (field, labelOf) => `<select data-f="${field}" data-keep-native="1"><option value="">—</option>${nouveau}${choices(field, labelOf)}</select>`;
            return {
                mode: 'pick',
                ref: box('ref', (product) => product.reference),
                equiv: box('equiv', (product) => product.equiv),
                desc: box('desc', (product) => product.designation),
                brand: brandField(item.brand || known?.brand || ''),
                family: familyField(item.family || known?.family || ''),
            };
        }
        return {
            mode: 'new',
            ref: `<input data-f="ref" maxlength="64" value="${esc(item.ref || '')}" placeholder="Réf">`,
            equiv: `<input data-f="equiv" maxlength="64" value="${esc(item.equiv || '')}" placeholder="Equiv">`,
            desc: `<input data-f="desc" required maxlength="255" value="${esc(item.description || '')}" placeholder="Désignation">`,
            brand: brandField(item.brand || ''),
            family: familyField(item.family || ''),
        };
    }
    function lineRow(item = {}, forceNew = false) {
        const tr = document.createElement('tr');
        const key = productKey(item);
        const cells = forceNew ? {
            mode: 'new',
            ref: `<input data-f="ref" maxlength="64" value="${esc(item.ref || '')}" placeholder="Réf">`,
            equiv: `<input data-f="equiv" maxlength="64" value="${esc(item.equiv || '')}" placeholder="Equiv">`,
            desc: `<input data-f="desc" required maxlength="255" value="${esc(item.description || '')}" placeholder="Désignation">`,
            brand: brandField(item.brand || ''),
            family: familyField(item.family || ''),
        } : identityCells(item, knownProduct(key) ? key : '');
        tr.dataset.mode = cells.mode;
        tr.innerHTML = `
            <td>${cells.ref}</td>
            <td><input data-f="barcode" maxlength="64" value="${esc(item.barcode || '')}" placeholder="Code barre"></td>
            <td>${cells.equiv}</td>
            <td>${cells.desc}</td>
            <td>${cells.brand}</td>
            <td>${cells.family}</td>
            <td><input data-f="qty" inputmode="decimal" value="${esc(item.qty != null ? item.qty : 1)}" placeholder="1"></td>
            <td><input data-f="price" inputmode="decimal" value="${esc(item.price != null ? moneyDot(item.price) : '')}" placeholder="0.00"></td>
            <td><div class="bl-remise"><input data-f="remise" inputmode="decimal" value="${esc(item.remise != null ? item.remise : 0)}"><span>%</span></div></td>
            <td><input data-f="sub" readonly tabindex="-1" value="0.00"></td>
            <td><button type="button" class="bl-line-x" data-remove title="Retirer">×</button></td>`;
        refreshLine(tr);
        return tr;
    }
    function lineIdentity(row) {
        const brand = row.querySelector('[data-f="brand"]')?.value.trim() || '';
        const family = row.querySelector('[data-f="family"]')?.value.trim() || '';
        if (row.dataset.mode === 'pick') {
            const key = row.querySelector('[data-f="ref"]')?.value || '';
            const product = key && key !== '__new__' ? knownProduct(key) : null;
            if (!product) return null;
            return {
                ref: product.reference || '',
                equiv: product.equiv || '',
                description: product.designation || '',
                brand,
                family,
            };
        }
        return {
            ref: row.querySelector('[data-f="ref"]').value.trim(),
            equiv: row.querySelector('[data-f="equiv"]').value.trim(),
            description: row.querySelector('[data-f="desc"]').value.trim(),
            brand,
            family,
        };
    }
    function sameProductOnOtherLine(row, key) {
        return [...linesBody.querySelectorAll('tr')].some((other) => {
            if (other === row) return false;
            return (other.querySelector('[data-f="ref"]')?.value || '') === key;
        });
    }
    function importedPurchasePrice(row) {
        if (row.dataset.mode !== 'pick') return null;
        for (const field of ['ref', 'equiv', 'desc']) {
            const select = row.querySelector(`select[data-f="${field}"]`);
            const option = select?.selectedOptions?.[0];
            if (option && option.value && option.value !== '__new__' && option.dataset.achat != null && option.dataset.achat !== '') {
                return parseNum(option.dataset.achat);
            }
            const product = productFromSelect(select);
            if (product) return parseNum(product.purchase_price ?? product.unit_price ?? 0);
        }
        return null;
    }
    function refreshLine(row) {
        const imported = importedPurchasePrice(row);
        const priceInput = row.querySelector('[data-f="price"]');
        if (imported != null && priceInput) {
            priceInput.value = moneyDot(imported);
            priceInput.readOnly = true;
        }
        const price = parseNum(priceInput.value);
        const qty = parseNum(row.querySelector('[data-f="qty"]').value);
        const remise = Math.min(100, Math.max(0, parseNum(row.querySelector('[data-f="remise"]').value)));
        row.querySelector('[data-f="sub"]').value = moneyDot(qty * price * (1 - remise / 100));
    }
    function recalc() {
        let qtyTotal = 0;
        let amount = 0;
        linesBody.querySelectorAll('tr').forEach((row) => {
            refreshLine(row);
            qtyTotal += parseNum(row.querySelector('[data-f="qty"]').value);
            amount += parseNum(row.querySelector('[data-f="sub"]').value);
        });
        document.getElementById('blQtyTotal').textContent = Number.isInteger(qtyTotal) ? String(qtyTotal) : String(qtyTotal).replace('.', ',');
        document.getElementById('blAmountTotal').textContent = money(amount);
        return { qtyTotal, amount };
    }
    function fillSuppliers() {
        fetch('/clients', { headers: { 'Accept': 'application/json' } })
            .then((response) => response.ok ? response.json() : { data: [] })
            .then((payload) => {
                clients = payload.data || [];
                document.getElementById('blClientList').innerHTML = clients.map((item) => `<option value="${esc(item.name)}"></option>`).join('');
                document.getElementById('blCltList').innerHTML = clients.map((item) => `<option value="${esc(item.code)}"></option>`).join('');
            })
            .catch(() => { clients = []; });
    }
    function syncClientFromId() {
        const code = document.getElementById('blClt').value.trim().toLowerCase();
        const found = clients.find((item) => String(item.code || '').trim().toLowerCase() === code);
        if (found) document.getElementById('blClient').value = found.name || '';
    }
    function syncIdFromClient() {
        const name = document.getElementById('blClient').value.trim().toLowerCase();
        const found = clients.find((item) => String(item.name || '').trim().toLowerCase() === name);
        if (found) document.getElementById('blClt').value = found.code || '';
    }
    function collectLines() {
        return [...linesBody.querySelectorAll('tr')].map((row) => {
            const identity = lineIdentity(row);
            if (!identity) return null;
            return {
                ref: identity.ref,
                barcode: row.querySelector('[data-f="barcode"]').value.trim(),
                equiv: identity.equiv,
                description: identity.description,
                brand: identity.brand,
                family: identity.family,
                fresh: row.dataset.mode === 'new',
                qty: parseNum(row.querySelector('[data-f="qty"]').value),
                price: parseNum(row.querySelector('[data-f="price"]').value),
                remise: Math.min(100, Math.max(0, parseNum(row.querySelector('[data-f="remise"]').value))),
                sub: parseNum(row.querySelector('[data-f="sub"]').value),
            };
        }).filter((item) => item && (item.description || item.ref || item.barcode));
    }
    function fillClientFilter() {
        const select = document.getElementById('blClientFilter');
        const current = select.value;
        const values = [...new Set(rows.map((row) => String(row.client || '').trim()).filter(Boolean))]
            .sort((a, b) => a.localeCompare(b, 'fr', { sensitivity: 'base' }));
        select.innerHTML = `<option value="">Tous</option>${values.map((value) => `<option value="${esc(value)}">${esc(value)}</option>`).join('')}`;
        select.value = values.includes(current) ? current : '';
    }
    function filteredRows() {
        const month = document.getElementById('blMonth').value;
        const supplier = document.getElementById('blClientFilter').value.trim().toLowerCase();
        return rows.filter((row) => {
            if (month && String(row.month || '') !== month) return false;
            if (supplier && String(row.client || '').trim().toLowerCase() !== supplier) return false;
            return true;
        });
    }
    function updateSummary(list) {
        const achats = list.reduce((sum, row) => sum + (Number(row.total) || 0), 0);
        const paye = list.reduce((sum, row) => sum + (Number(row.paid) || 0), 0);
        document.getElementById('blSumVentes').textContent = money(achats);
        document.getElementById('blSumPaye').textContent = money(paye);
        document.getElementById('blSumSolde').textContent = money(achats - paye);
    }
    function render() {
        const list = filteredRows();
        updateSummary(list);
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="9">${rows.length ? 'Aucun bon de livraison pour cette recherche.' : 'Aucun bon de livraison enregistré'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(formatDate(row.date))}</td>
            <td>${esc(row.ref)}</td>
            <td>${esc(row.client)}</td>
            <td>${esc(row.clt) || '—'}</td>
            <td>${esc(row.qty)}</td>
            <td>${esc(money(row.total))}</td>
            <td>${esc(row.paiement)}</td>
            <td>${esc(row.echeance) || '—'}</td>
            <td>
                <button type="button" class="bl-icon see" data-act="voir" data-id="${row.id}" title="Voir" aria-label="Voir">${eye}</button>
                <button type="button" class="bl-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier" aria-label="Modifier">${pencil}</button>
                <button type="button" class="bl-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer" aria-label="Supprimer">${trash}</button>
            </td>
        </tr>`).join('');
    }
    function applyMode() {
        const locked = mode === 'view';
        form.classList.toggle('is-view', locked);
        document.getElementById('blSave').hidden = locked;
        document.getElementById('blAddLine').hidden = locked;
        document.getElementById('blClose').textContent = locked ? 'Fermer' : 'Annuler';
        form.querySelectorAll('input, select').forEach((field) => { field.disabled = locked; });
        const title = mode === 'view' ? 'Bon de livraison' : (mode === 'edit' ? 'Modifier le bon de livraison' : 'Nouveau bon de livraison');
        const ref = document.getElementById('blRef').value;
        document.getElementById('blModalTitle').textContent = mode === 'create' ? title : `${title} ${ref}`;
    }
    async function openModal(row, nextMode) {
        mode = nextMode || 'create';
        await ensureCatalog();
        editingId = row?.id || null;
        form.reset();
        document.getElementById('blDate').value = row?.date || todayInput();
        document.getElementById('blRef').value = row?.ref || nextRef();
        document.getElementById('blClt').value = row?.clt || '';
        document.getElementById('blClient').value = row?.client || '';
        document.getElementById('blPaiement').value = row?.paiement || '';
        if (!row) document.getElementById('blPaiement').selectedIndex = 0;
        document.getElementById('blEcheance').value = row?.echeance || '';
        linesBody.innerHTML = '';
        const items = row?.items?.length ? row.items : [{}];
        items.forEach((item) => linesBody.appendChild(lineRow(item)));
        recalc();
        applyMode();
        showError('');
        fillSuppliers();
        modal.hidden = false;
        if (mode !== 'view') document.getElementById('blClient').focus();
    }
    function closeModal() {
        modal.hidden = true;
        showError('');
    }
    function entryOpen() {
        return !modal.hidden && mode !== 'view';
    }
    function blockLeave() {
        window.alert('Vous devez valider ou annuler la saisie.');
    }
    document.getElementById('blAdd').addEventListener('click', () => openModal(null, 'create'));
    document.getElementById('blClose').addEventListener('click', closeModal);
    document.getElementById('blCloseX').addEventListener('click', () => {
        if (entryOpen()) { blockLeave(); return; }
        closeModal();
    });
    document.getElementById('blAddLine').addEventListener('click', () => {
        if (linesBody.querySelectorAll('tr').length >= 50) {
            showError('Un bon comprend au maximum 50 articles.');
            return;
        }
        linesBody.appendChild(lineRow());
        recalc();
    });
    modal.addEventListener('click', (event) => {
        if (event.target !== modal) return;
        if (entryOpen()) { blockLeave(); return; }
        closeModal();
    });
    document.addEventListener('click', (event) => {
        if (!entryOpen()) return;
        const leave = event.target.closest('[data-panel], [data-toggle-group], .btn-logout');
        if (!leave || modal.contains(leave)) return;
        event.preventDefault();
        event.stopPropagation();
        blockLeave();
    }, true);
    window.addEventListener('beforeunload', (event) => {
        if (!entryOpen()) return;
        event.preventDefault();
        event.returnValue = '';
    });
    document.getElementById('blClt').addEventListener('change', syncClientFromId);
    document.getElementById('blClt').addEventListener('blur', syncClientFromId);
    document.getElementById('blClient').addEventListener('change', syncIdFromClient);
    document.getElementById('blClient').addEventListener('blur', syncIdFromClient);
    linesBody.addEventListener('input', (event) => {
        recalc();
        if (event.target.matches('[data-f="brand"], [data-f="family"]')) fillBrandFamilyLists();
    });
    linesBody.addEventListener('change', (event) => {
        if (event.target.matches('[data-f="brand"], [data-f="family"]')) {
            fillBrandFamilyLists();
            return;
        }
        const select = event.target.closest('select[data-f]');
        if (!select || mode === 'view') return;
        const row = select.closest('tr');
        const keep = {
            barcode: row.querySelector('[data-f="barcode"]').value,
            brand: row.querySelector('[data-f="brand"]')?.value || '',
            family: row.querySelector('[data-f="family"]')?.value || '',
            qty: row.querySelector('[data-f="qty"]').value,
            price: row.querySelector('[data-f="price"]').value,
            remise: row.querySelector('[data-f="remise"]').value,
        };
        if (select.value === '__new__') {
            const fresh = lineRow({
                barcode: keep.barcode,
                brand: keep.brand,
                family: keep.family,
                qty: keep.qty,
                price: keep.price,
                remise: keep.remise,
            }, true);
            row.replaceWith(fresh);
            refreshLine(fresh);
            fillBrandFamilyLists();
            fresh.querySelector('[data-f="ref"]')?.focus();
            showError('');
            return;
        }
        const product = productFromSelect(select);
        const key = product ? choiceValue(product) : select.value;
        if (key && sameProductOnOtherLine(row, key)) {
            row.querySelectorAll('select[data-f]').forEach((field) => { field.value = ''; });
            showError('Ce produit est déjà sur ce bon.');
            return;
        }
        applyCatalogProduct(row, product);
        fillBrandFamilyLists();
        recalc();
        showError('');
    });
    linesBody.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove]');
        if (!button) return;
        if (linesBody.querySelectorAll('tr').length < 2) {
            const row = button.closest('tr');
            const replacement = lineRow();
            row.replaceWith(replacement);
            if (mode !== 'view') replacement.querySelector('input, select')?.focus();
            recalc();
            return;
        }
        button.closest('tr').remove();
        recalc();
    });
    async function load() {
        const response = await fetch('/bons-livraison', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json();
        if (!response.ok) throw new Error(payload.message || 'Chargement impossible.');
        rows = payload.data || [];
        const match = String(payload.meta?.next_ref || '').match(/(\d+)$/);
        seq = match ? Number(match[1]) : rows.length + 1;
        fillClientFilter();
        fillBrandFamilyLists();
        render();
    }
    document.getElementById('blMonth').addEventListener('change', render);
    document.getElementById('blClientFilter').addEventListener('change', render);
    document.getElementById('blRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
    });
    document.addEventListener('evopro-regl-clients-changed', () => { load().catch(() => {}); });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (mode === 'view') return;
        showError('');
        if (linesBody.querySelectorAll('tr').length > 50) {
            showError('Un bon comprend au maximum 50 articles.');
            return;
        }
        const date = document.getElementById('blDate').value;
        const ref = document.getElementById('blRef').value.trim();
        const clientName = document.getElementById('blClient').value.trim();
        const clt = document.getElementById('blClt').value.trim();
        const paiement = document.getElementById('blPaiement').value;
        const echeance = document.getElementById('blEcheance').value;
        const items = collectLines();
        if (!date || !ref || !clientName) {
            showError('Remplissez Date, Nom Client et N° BL.');
            return;
        }
        if (!items.length || items.some((item) => !item.description || item.qty <= 0)) {
            showError('Ajoutez au moins une ligne avec désignation et quantité.');
            return;
        }
        const keys = items.map((item) => productKey(item));
        if (keys.some((key, index) => keys.indexOf(key) !== index)) {
            showError('Ce produit est déjà saisi sur ce bon (réf, désignation, equiv, marque, famille).');
            return;
        }
        if (items.some((item) => item.fresh && knownProduct(productKey(item)))) {
            showError('Ce produit est déjà saisi. Choisissez-le dans la liste, sans le ressaisir.');
            return;
        }
        const save = document.getElementById('blSave');
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/bons-livraison/${editingId}` : '/bons-livraison', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    order_date: date,
                    reference: ref,
                    client_code: clt,
                    client_name: clientName,
                    payment_type: paiement,
                    due_term: echeance,
                    items: items.map((item) => ({
                        article_ref: item.ref,
                        barcode: item.barcode,
                        equiv: item.equiv,
                        description: item.description,
                        brand: item.brand,
                        family: item.family,
                        quantity: item.qty,
                        unit_price: item.price,
                        remise: item.remise,
                    })),
                }),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const first = data.errors ? Object.values(data.errors)[0]?.[0] : data.message;
                throw new Error(first || 'Enregistrement impossible.');
            }
            closeModal();
            await load();
            document.dispatchEvent(new CustomEvent('evopro-bl-changed'));
        } catch (error) {
            showError(error.message);
        } finally {
            save.disabled = false;
        }
    });
    body.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-act]');
        if (!button) return;
        const row = rows.find((item) => String(item.id) === button.dataset.id);
        if (!row) return;
        if (button.dataset.act === 'voir') openModal(row, 'view');
        if (button.dataset.act === 'modifier') openModal(row, 'edit');
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer le bon ${row.ref} ?`)) return;
            const response = await fetch(`/bons-livraison/${row.id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            if (!response.ok) { showError('Impossible de supprimer ce bon.'); return; }
            await load();
            document.dispatchEvent(new CustomEvent('evopro-bl-changed'));
        }
    });
    document.querySelector('[data-panel="f-bon"]')?.addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
})();
</script>
