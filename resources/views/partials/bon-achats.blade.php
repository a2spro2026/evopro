<section class="panel" id="panel-f-bon">
    <div class="content-head ba-head">
        <div>
            <h1>Bon Achats</h1>
            <p class="ba-sub">Bons d’achat fournisseurs</p>
        </div>
        <button type="button" class="ba-add" id="baAdd" title="Ajouter" aria-label="Ajouter">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
        </button>
    </div>

    <div class="ba-summary">
        <div class="ba-card achats"><span>Total Achats</span><strong id="baSumAchats">0,00</strong></div>
        <div class="ba-card paye"><span>Total Payé</span><strong id="baSumPaye">0,00</strong></div>
        <div class="ba-card solde"><span>Solde</span><strong id="baSumSolde">0,00</strong></div>
    </div>

    <div class="ba-filters">
        <label>Mois
            <select id="baMonth" data-keep-native="1">
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
        <label>Fournisseur
            <select id="baSupplierFilter" data-keep-native="1">
                <option value="">Tous</option>
            </select>
        </label>
        <button type="button" class="ba-refresh" id="baRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Liste des bons d’achat</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>N° B-A</th>
                        <th>Fournisseur</th>
                        <th>N° Frns</th>
                        <th>Qté totale</th>
                        <th>Total</th>
                        <th>Paiement</th>
                        <th>Échéance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="baBody">
                    <tr><td colspan="9">Aucun bon d’achat enregistré</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ba-window" id="baModal" hidden>
        <div class="ba-sheet" role="dialog" aria-modal="true" aria-labelledby="baModalTitle">
            <div class="ba-sheet-head">
                <h3 id="baModalTitle">Nouveau bon d’achat</h3>
                <button type="button" class="ba-x" id="baCloseX" title="Fermer" aria-label="Fermer">×</button>
            </div>
            <form id="baForm" class="ba-form" autocomplete="off">
                <div class="ba-error" id="baError" hidden></div>
                <div class="ba-grid">
                    <div class="ba-field">
                        <label for="baDate">Date</label>
                        <input id="baDate" type="date" required>
                    </div>
                    <div class="ba-field">
                        <label for="baFrns">ID Frns</label>
                        <input id="baFrns" maxlength="64" placeholder="CF-0001" list="baFrnsList">
                        <datalist id="baFrnsList"></datalist>
                    </div>
                    <div class="ba-field ba-supplier">
                        <label for="baSupplier">Nom Fournisseur</label>
                        <input id="baSupplier" required maxlength="255" placeholder="Nom fournisseur" list="baSupplierList">
                        <datalist id="baSupplierList"></datalist>
                    </div>
                    <div class="ba-field">
                        <label for="baRef">N° BA</label>
                        <input id="baRef" required maxlength="64" placeholder="BA-0001">
                    </div>
                    <div class="ba-field ba-field-reg">
                        <label for="baPaiement">Type Rég</label>
                        <select id="baPaiement">
                            <option value="">—</option>
                            <option>Esp</option>
                            <option>Chq</option>
                            <option>Eff</option>
                            <option>Vir</option>
                            <option>Vers</option>
                        </select>
                    </div>
                    <div class="ba-field ba-field-ech">
                        <label for="baEcheance">Échéance</label>
                        <select id="baEcheance">
                            <option value="">—</option>
                            <option>45 Jrs</option>
                            <option>60 Jrs</option>
                            <option>90 Jrs</option>
                            <option>120 Jrs</option>
                        </select>
                    </div>
                </div>

                <div class="ba-lines-head">
                    <p>Détail BA</p>
                    <button type="button" class="ba-add-line" id="baAddLine" title="Ajouter une ligne" aria-label="Ajouter une ligne">+</button>
                </div>
                <div class="ba-lines-wrap">
                    <table class="ba-lines">
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
                        <tbody id="baLines"></tbody>
                    </table>
                    <datalist id="baBrandList"></datalist>
                    <datalist id="baFamilyList"></datalist>
                </div>
                <div class="ba-totals">
                    <div><small>Qté totale</small><strong id="baQtyTotal">0</strong></div>
                    <div><small>Total</small><strong id="baAmountTotal">0,00</strong></div>
                </div>
                <div class="ba-actions">
                    <button type="button" class="ba-btn ghost" id="baClose">Fermer</button>
                    <button type="submit" class="ba-btn primary" id="baSave">Valider</button>
                </div>
            </form>
        </div>
    </div>
</section>
<style>
    #panel-f-bon .ba-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-f-bon .ba-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .ba-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 220px)); gap: 0.75rem; margin-bottom: 1rem; }
    .ba-card { border-radius: 14px; padding: 0.85rem 1rem; color: #fff; box-shadow: 0 10px 24px rgba(0,0,0,0.2); }
    .ba-card span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.9; }
    .ba-card strong { display: block; margin-top: 0.25rem; font-size: 1.3rem; font-variant-numeric: tabular-nums; }
    .ba-card.achats { background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .ba-card.paye { background: linear-gradient(135deg, #16a34a, #14532d); }
    .ba-card.solde { background: linear-gradient(135deg, #dc2626, #7f1d1d); }
    .ba-filters {
        display: grid; grid-template-columns: 180px minmax(220px, 1fr) 38px; gap: 0.75rem;
        align-items: end; margin-bottom: 1rem;
    }
    .ba-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .ba-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    .ba-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    .ba-refresh svg { width: 16px; height: 16px; }
    [data-theme="light"] .ba-filters label { color: #5c1220; }
    [data-theme="light"] .ba-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    .ba-add {
        display: inline-grid; place-items: center;
        width: 42px; height: 42px; border: none; border-radius: 12px; cursor: pointer;
        background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff;
        box-shadow: 0 8px 18px rgba(143, 36, 54, 0.28);
    }
    .ba-add svg { width: 20px; height: 20px; }
    .ba-add:hover { filter: brightness(1.06); }
    .ba-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden .ba-window { left: 0; }
    .ba-window[hidden] { display: none !important; }
    .ba-sheet {
        width: min(1380px, calc(100vw - 2.5rem)); margin: auto; border-radius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #4a1824; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    .ba-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .ba-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .ba-x {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        background: transparent; color: rgba(255,255,255,0.82); font-size: 1.35rem; line-height: 1;
    }
    .ba-x:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .ba-form { padding: 1rem 1.15rem 0.4rem; }
    .ba-error { margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-radius: 10px; background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #7f1d1d; font-size: 0.82rem; }
    .ba-grid {
        display: grid;
        grid-template-columns: 128px 96px minmax(150px, 1fr) 118px 96px 96px;
        gap: 0.5rem;
        align-items: end;
    }
    .ba-supplier { min-width: 0; max-width: 220px; }
    .ba-field { display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }
    .ba-field label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em; color: #94a3b8; }
    .ba-field input, .ba-field select {
        box-sizing: border-box;
        width: 100%; height: 38px; min-height: 38px; max-height: 38px;
        border-radius: 10px; border: 1px solid #334155;
        background-color: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem;
        padding: 0 0.7rem; line-height: 36px; margin: 0;
    }
    .ba-field select {
        -webkit-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2394a3b8' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 12px 8px;
        padding-right: 1.8rem;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .ba-field select:focus,
    .ba-field input:focus {
        background-color: #fff; color: #000;
        outline: 2px solid #8f2436; outline-offset: 1px;
    }
    .ba-field select option { background: #fff; color: #000; }
    .ba-field input::placeholder,
    .ba-lines input::placeholder { color: #64748b; }
    .ba-lines-head {
        display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
        margin: 1rem 0 0.55rem;
    }
    .ba-lines-head p {
        margin: 0; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #e7c4c8;
        text-decoration: underline; text-underline-offset: 3px;
    }
    .ba-add-line {
        width: 32px; height: 32px; border: none; border-radius: 8px; cursor: pointer;
        background: #1e293b; color: #fff; font-size: 1.2rem; line-height: 1; font-weight: 700;
    }
    .ba-add-line:hover { background: #8f2436; }
    .ba-lines-wrap {
        max-height: min(52vh, 520px); overflow: auto; border: 1px solid #334155; border-radius: 10px;
    }
    .ba-lines { width: 100%; border-collapse: collapse; table-layout: fixed; min-width: 1180px; }
    .ba-lines col.c-ref { width: 7%; }
    .ba-lines col.c-barcode { width: 9%; }
    .ba-lines col.c-equiv { width: 7%; }
    .ba-lines col.c-desc { width: 22%; }
    .ba-lines col.c-brand { width: 10%; }
    .ba-lines col.c-family { width: 10%; }
    .ba-lines col.c-qty { width: 6%; }
    .ba-lines col.c-price { width: 8%; }
    .ba-lines col.c-remise { width: 7%; }
    .ba-lines col.c-sub { width: 9%; }
    .ba-lines col.c-x { width: 40px; }
    .ba-lines th {
        position: sticky; top: 0; background: #5c1220; color: #fff;
        font-size: 0.62rem; letter-spacing: 0.06em; text-transform: uppercase;
        padding: 0.5rem; text-align: center;
    }
    .ba-lines th:nth-child(4) { text-align: left; padding-left: 0.65rem; }
    .ba-lines td { padding: 0.3rem; border-top: 1px solid #1e293b; }
    .ba-lines input {
        box-sizing: border-box;
        width: 100%; height: 34px; border-radius: 8px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.78rem; padding: 0 0.45rem; text-align: center;
    }
    .ba-lines input[data-f="desc"] { text-align: left; padding-left: 0.65rem; }
    .ba-lines select {
        box-sizing: border-box;
        width: 100%; height: 34px; border-radius: 8px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.78rem; padding: 0 0.35rem; text-align: center;
    }
    .ba-lines select option { background: #fff; color: #000; }
    .ba-lines select[data-f="desc"] { text-align: left; }
    .ba-remise { position: relative; }
    .ba-remise input { padding-right: 1.1rem; }
    .ba-remise span {
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
        font-size: 0.68rem; color: #94a3b8; pointer-events: none;
    }
    .ba-lines .ba-line-x {
        display: inline-grid; place-items: center; width: 30px; height: 30px; padding: 0;
        border: 1px solid rgba(220, 38, 38, 0.35); border-radius: 9px;
        background: #fef2f2; color: #dc2626; cursor: pointer;
    }
    .ba-lines .ba-line-x svg { width: 15px; height: 15px; }
    .ba-lines .ba-line-x:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
    .ba-totals {
        display: grid; grid-template-columns: repeat(2, minmax(0, 180px)); gap: 0.55rem;
        justify-content: end; margin-top: 0.8rem;
    }
    .ba-totals div {
        border-radius: 12px; padding: 0.55rem 0.7rem; text-align: center;
        background: #fff; border: 1px solid #334155; color: #000;
    }
    .ba-totals small { display: block; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; }
    .ba-totals strong { display: block; margin-top: 0.15rem; font-size: 0.95rem; }
    .ba-actions { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.95rem 0 1rem; }
    .ba-btn { height: 36px; padding: 0 1rem; border-radius: 999px; border: none; font-family: inherit; font-weight: 700; font-size: 0.78rem; cursor: pointer; }
    .ba-btn.ghost { background: #1e293b; color: #e2e8f0; border: 1px solid #475569; }
    .ba-btn.primary { background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff; }
    .ba-icon {
        display: inline-grid; place-items: center; width: 28px; height: 28px; padding: 0;
        border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer;
    }
    .ba-icon svg { width: 15px; height: 15px; }
    .ba-icon.see:hover, .ba-icon.edit:hover { color: #e7c4c8; background: rgba(143,36,54,0.16); }
    .ba-icon.del:hover { color: #f87171; background: rgba(248,113,113,0.12); }
    .ba-form.is-view .ba-add-line,
    .ba-form.is-view .ba-line-x,
    .ba-form.is-view #baSave { display: none; }
    .ba-form.is-view input,
    .ba-form.is-view select { opacity: 0.85; }
    [data-theme="light"] .ba-sheet { background: #fff; border-color: #e7c9cf; color: #1a1214; }
    [data-theme="light"] .ba-field label,
    [data-theme="light"] .ba-totals small,
    [data-theme="light"] .ba-lines th { color: #5c1220; background: #f3d5db; }
    [data-theme="light"] .ba-field input,
    [data-theme="light"] .ba-field select,
    [data-theme="light"] .ba-lines input,
    [data-theme="light"] .ba-lines select { background: #fff; color: #000; border-color: #e4c5cb; }
    [data-theme="light"] .ba-field select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%237a5a62' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 12px 8px;
    }
    [data-theme="light"] .ba-lines-wrap { border-color: #e4c5cb; }
    [data-theme="light"] .ba-lines th { background: #f3d5db; color: #5c1220; }
    [data-theme="light"] .ba-lines td { border-top-color: #f0dde1; }
    [data-theme="light"] .ba-totals div { background: #fff; border-color: #e7c9cf; }
    [data-theme="light"] .ba-add-line { background: #f3d5db; color: #5c1220; }
    [data-theme="light"] .ba-btn.ghost { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .ba-icon { color: #64748b; }
    [data-theme="light"] .ba-lines-head p { color: #8f2436; }
    [data-theme="light"] .ba-remise span { color: #7a5a62; }
    @media (max-width: 900px) {
        .ba-window { left: 0; }
        .ba-grid { grid-template-columns: 1fr 1fr; }
        .ba-field.ba-supplier { grid-column: 1 / -1; max-width: none; }
    }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-f-bon');
    const modal = document.getElementById('baModal');
    const form = document.getElementById('baForm');
    const linesBody = document.getElementById('baLines');
    if (!panel || !modal || !form || !linesBody) return;
    const body = document.getElementById('baBody');
    const errorBox = document.getElementById('baError');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    let rows = [];
    let suppliers = [];
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
        return `BA-${String(seq).padStart(4, '0')}`;
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
    function knownProduct(key) {
        return catalog.find((item) => productKey(item) === key) || null;
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
        document.getElementById('baBrandList').innerHTML = brands.map((value) => `<option value="${esc(value)}"></option>`).join('');
        document.getElementById('baFamilyList').innerHTML = families.map((value) => `<option value="${esc(value)}"></option>`).join('');
    }
    function brandField(value = '') {
        return `<input data-f="brand" maxlength="120" list="baBrandList" value="${esc(value || '')}" placeholder="Marque" autocomplete="off">`;
    }
    function familyField(value = '') {
        return `<input data-f="family" maxlength="120" list="baFamilyList" value="${esc(value || '')}" placeholder="Famille" autocomplete="off">`;
    }
    function identityCells(item, pickedKey) {
        const known = pickedKey ? knownProduct(pickedKey) : null;
        if (catalog.length && (known || !item.description && !item.ref && !item.equiv)) {
            const selected = known ? pickedKey : '';
            const choices = (field, labelOf) => catalog.map((product) => {
                const key = productKey(product);
                const label = labelOf(product) || '—';
                return `<option value="${esc(key)}"${key === selected ? ' selected' : ''}>${esc(label)}</option>`;
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
            <td><input data-f="qty" inputmode="decimal" value="${esc(item.qty != null ? item.qty : (item.quantity != null ? item.quantity : 1))}" placeholder="1"></td>
            <td><input data-f="price" inputmode="decimal" value="${esc((item.price != null || item.unit_price != null) ? moneyDot(item.price ?? item.unit_price) : '')}" placeholder="0.00"></td>
            <td><div class="ba-remise"><input data-f="remise" inputmode="decimal" value="${esc(item.remise != null ? item.remise : 0)}"><span>%</span></div></td>
            <td><input data-f="sub" readonly tabindex="-1" value="0.00"></td>
            <td><button type="button" class="ba-line-x" data-remove title="Retirer" aria-label="Retirer">${trash}</button></td>`;
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
            const identity = lineIdentity(other);
            return identity && productKey(identity) === key;
        });
    }
    function refreshLine(row) {
        const price = parseNum(row.querySelector('[data-f="price"]').value);
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
        document.getElementById('baQtyTotal').textContent = Number.isInteger(qtyTotal) ? String(qtyTotal) : String(qtyTotal).replace('.', ',');
        document.getElementById('baAmountTotal').textContent = money(amount);
        return { qtyTotal, amount };
    }
    function fillSuppliers() {
        fetch('/fournisseurs', { headers: { 'Accept': 'application/json' } })
            .then((response) => response.ok ? response.json() : { data: [] })
            .then((payload) => {
                suppliers = payload.data || [];
                document.getElementById('baSupplierList').innerHTML = suppliers.map((item) => `<option value="${esc(item.name)}"></option>`).join('');
                document.getElementById('baFrnsList').innerHTML = suppliers.map((item) => `<option value="${esc(item.code)}"></option>`).join('');
            })
            .catch(() => { suppliers = []; });
    }
    function syncSupplierFromId() {
        const code = document.getElementById('baFrns').value.trim().toLowerCase();
        const found = suppliers.find((item) => String(item.code || '').trim().toLowerCase() === code);
        if (found) document.getElementById('baSupplier').value = found.name || '';
    }
    function syncIdFromSupplier() {
        const name = document.getElementById('baSupplier').value.trim().toLowerCase();
        const found = suppliers.find((item) => String(item.name || '').trim().toLowerCase() === name);
        if (found) document.getElementById('baFrns').value = found.code || '';
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
    function fillSupplierFilter() {
        const select = document.getElementById('baSupplierFilter');
        const current = select.value;
        const values = [...new Set(rows.map((row) => String(row.supplier || '').trim()).filter(Boolean))]
            .sort((a, b) => a.localeCompare(b, 'fr', { sensitivity: 'base' }));
        select.innerHTML = `<option value="">Tous</option>${values.map((value) => `<option value="${esc(value)}">${esc(value)}</option>`).join('')}`;
        select.value = values.includes(current) ? current : '';
    }
    function filteredRows() {
        const month = document.getElementById('baMonth').value;
        const supplier = document.getElementById('baSupplierFilter').value.trim().toLowerCase();
        return rows.filter((row) => {
            if (month && String(row.month || '') !== month) return false;
            if (supplier && String(row.supplier || '').trim().toLowerCase() !== supplier) return false;
            return true;
        });
    }
    function updateSummary(list) {
        const achats = list.reduce((sum, row) => sum + (Number(row.total) || 0), 0);
        const paye = list.reduce((sum, row) => sum + (Number(row.paid) || 0), 0);
        document.getElementById('baSumAchats').textContent = money(achats);
        document.getElementById('baSumPaye').textContent = money(paye);
        document.getElementById('baSumSolde').textContent = money(achats - paye);
    }
    function render() {
        const list = filteredRows();
        updateSummary(list);
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="9">${rows.length ? 'Aucun bon d’achat pour cette recherche.' : 'Aucun bon d’achat enregistré'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(formatDate(row.date))}</td>
            <td>${esc(row.ref)}</td>
            <td>${esc(row.supplier)}</td>
            <td>${esc(row.frns) || '—'}</td>
            <td>${esc(row.qty)}</td>
            <td>${esc(money(row.total))}</td>
            <td>${esc(row.paiement)}</td>
            <td>${esc(row.echeance) || '—'}</td>
            <td>
                <button type="button" class="ba-icon see" data-act="voir" data-id="${row.id}" title="Voir" aria-label="Voir">${eye}</button>
                <button type="button" class="ba-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier" aria-label="Modifier">${pencil}</button>
                <button type="button" class="ba-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer" aria-label="Supprimer">${trash}</button>
            </td>
        </tr>`).join('');
    }
    function applyMode() {
        const locked = mode === 'view';
        form.classList.toggle('is-view', locked);
        document.getElementById('baSave').hidden = locked;
        document.getElementById('baAddLine').hidden = locked;
        document.getElementById('baClose').textContent = locked ? 'Fermer' : 'Annuler';
        form.querySelectorAll('input, select').forEach((field) => { field.disabled = locked; });
        const title = mode === 'view' ? 'Bon d’achat' : (mode === 'edit' ? 'Modifier le bon d’achat' : 'Nouveau bon d’achat');
        const ref = document.getElementById('baRef').value;
        document.getElementById('baModalTitle').textContent = mode === 'create' ? title : `${title} ${ref}`;
    }
    async function openModal(row, nextMode) {
        mode = nextMode || 'create';
        await ensureCatalog();
        editingId = row?.id || null;
        form.reset();
        document.getElementById('baDate').value = row?.date || todayInput();
        document.getElementById('baRef').value = row?.ref || nextRef();
        document.getElementById('baFrns').value = row?.frns || '';
        document.getElementById('baSupplier').value = row?.supplier || '';
        document.getElementById('baPaiement').value = row?.paiement || '';
        if (!row) document.getElementById('baPaiement').selectedIndex = 0;
        document.getElementById('baEcheance').value = row?.echeance || '';
        linesBody.innerHTML = '';
        const items = row?.items?.length ? row.items : [{}];
        items.forEach((item) => linesBody.appendChild(lineRow(item)));
        recalc();
        applyMode();
        showError('');
        fillSuppliers();
        modal.hidden = false;
        if (mode !== 'view') document.getElementById('baSupplier').focus();
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
    document.getElementById('baAdd').addEventListener('click', () => openModal(null, 'create'));
    document.getElementById('baClose').addEventListener('click', closeModal);
    document.getElementById('baCloseX').addEventListener('click', () => {
        if (entryOpen()) { blockLeave(); return; }
        closeModal();
    });
    document.getElementById('baAddLine').addEventListener('click', () => {
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
    document.getElementById('baFrns').addEventListener('change', syncSupplierFromId);
    document.getElementById('baFrns').addEventListener('blur', syncSupplierFromId);
    document.getElementById('baSupplier').addEventListener('change', syncIdFromSupplier);
    document.getElementById('baSupplier').addEventListener('blur', syncIdFromSupplier);
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
        const key = select.value;
        if (key && sameProductOnOtherLine(row, key)) {
            row.querySelectorAll('select[data-f]').forEach((field) => { field.value = ''; });
            showError('Ce produit est déjà sur ce bon.');
            return;
        }
        row.querySelectorAll('select[data-f]').forEach((field) => { field.value = key; });
        const product = key ? knownProduct(key) : null;
        if (product) {
            const brandInput = row.querySelector('[data-f="brand"]');
            const familyInput = row.querySelector('[data-f="family"]');
            if (brandInput) brandInput.value = product.brand || brandInput.value;
            if (familyInput) familyInput.value = product.family || familyInput.value;
        }
        fillBrandFamilyLists();
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
        const response = await fetch('/bons-achats', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json();
        if (!response.ok) throw new Error(payload.message || 'Chargement impossible.');
        rows = payload.data || [];
        const match = String(payload.meta?.next_ref || '').match(/(\d+)$/);
        seq = match ? Number(match[1]) : rows.length + 1;
        fillSupplierFilter();
        fillBrandFamilyLists();
        render();
    }
    document.getElementById('baMonth').addEventListener('change', render);
    document.getElementById('baSupplierFilter').addEventListener('change', render);
    document.getElementById('baRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
    });
    document.addEventListener('evopro-regl-achats-changed', () => { load().catch(() => {}); });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (mode === 'view') return;
        showError('');
        if (linesBody.querySelectorAll('tr').length > 50) {
            showError('Un bon comprend au maximum 50 articles.');
            return;
        }
        const date = document.getElementById('baDate').value;
        const ref = document.getElementById('baRef').value.trim();
        const supplier = document.getElementById('baSupplier').value.trim();
        const frns = document.getElementById('baFrns').value.trim();
        const paiement = document.getElementById('baPaiement').value;
        const echeance = document.getElementById('baEcheance').value;
        const items = collectLines();
        if (!date || !ref || !supplier) {
            showError('Remplissez Date, Nom Fournisseur et N° BA.');
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
        const save = document.getElementById('baSave');
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/bons-achats/${editingId}` : '/bons-achats', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    order_date: date,
                    reference: ref,
                    supplier_code: frns,
                    supplier_name: supplier,
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
            document.dispatchEvent(new CustomEvent('evopro-ba-changed'));
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
        if (button.dataset.act === 'modifier') {
            const id = row.id;
            try { await load(); } catch (error) { showError(error.message); }
            const fresh = rows.find((item) => String(item.id) === String(id)) || row;
            openModal(fresh, 'edit');
        }
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer le bon ${row.ref} ?`)) return;
            const response = await fetch(`/bons-achats/${row.id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            if (!response.ok) { showError('Impossible de supprimer ce bon.'); return; }
            await load();
            document.dispatchEvent(new CustomEvent('evopro-ba-changed'));
        }
    });
    document.querySelector('[data-panel="f-bon"]')?.addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
})();
</script>
