<section class="panel" id="panel-m-charge">
    <div class="content-head ch-head">
        <div>
            <h1>Charge</h1>
            <p class="ch-sub">Suivi des charges et dépenses</p>
        </div>
    </div>

    <div class="ch-toolbar">
        <div class="ch-filters">
            <label>Mois
                <select id="chMonth" data-keep-native="1">
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
            <label class="ch-filter-cat">Catégorie
                <select id="chCatFilter" data-keep-native="1">
                    <option value="">Tous</option>
                </select>
            </label>
            <button type="button" class="ch-add" id="chAdd">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
                Ajouter
            </button>
            <button type="button" class="ch-refresh" id="chRefresh" title="Actualiser" aria-label="Actualiser">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
            </button>
        </div>
        <div class="ch-summary">
            <div class="ch-card total"><span>Total Charge</span><strong id="chSumTotal">0,00</strong></div>
        </div>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Liste des charges</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th class="ch-slim">Imprimer</th>
                        <th class="ch-slim">PDF</th>
                        <th>Date</th>
                        <th>Réf</th>
                        <th>Catégorie</th>
                        <th>Désignation</th>
                        <th>Bénéficiaire</th>
                        <th>Montant</th>
                        <th>Type Rég</th>
                        <th>N°</th>
                        <th>Bnq</th>
                        <th>Tiré</th>
                        <th>Date Encaiss</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="chBody">
                    <tr><td colspan="14">Aucune charge enregistrée</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ch-cat-pop" id="chCatPop" hidden>
        <div class="ch-cat-card" role="dialog" aria-modal="true" aria-labelledby="chCatPopTitle">
            <h3 id="chCatPopTitle">Nouvelle catégorie</h3>
            <label>Nom catégorie
                <input id="chCatName" type="text" maxlength="150" placeholder="Ex. Loyer, Carburant…" autocomplete="off">
            </label>
            <div class="ch-cat-actions">
                <button type="button" class="ch-btn ghost" id="chCatCancel">Annuler</button>
                <button type="button" class="ch-btn primary" id="chCatSave">Valider</button>
            </div>
        </div>
    </div>

    <div class="ch-window" id="chModal" hidden>
        <div class="ch-sheet" role="dialog" aria-modal="true" aria-labelledby="chModalTitle">
            <div class="ch-sheet-head">
                <h3 id="chModalTitle">Nouvelle charge</h3>
                <button type="button" class="ch-x" id="chCloseX" title="Fermer" aria-label="Fermer">×</button>
            </div>
            <form id="chForm" class="ch-form" autocomplete="off">
                <div class="ch-error" id="chError" hidden></div>
                <div class="ch-grid">
                    <div class="ch-field">
                        <label for="chDate">Date</label>
                        <input id="chDate" type="text" inputmode="numeric" maxlength="10" placeholder="JJ/MM/AAAA" autocomplete="off" required>
                    </div>
                    <div class="ch-field">
                        <label for="chRef">Réf</label>
                        <input id="chRef" readonly>
                    </div>
                    <div class="ch-field ch-field-cat">
                        <label for="chCategory">Catégorie</label>
                        <div class="ch-cat-row">
                            <select id="chCategory" data-keep-native="1">
                                <option value="">—</option>
                            </select>
                            <button type="button" class="ch-cat-plus" id="chCatPlusForm" title="Ajouter une catégorie" aria-label="Ajouter une catégorie">+</button>
                        </div>
                    </div>
                    <div class="ch-field ch-span-2">
                        <label for="chDesignation">Désignation</label>
                        <input id="chDesignation" maxlength="255" placeholder="Libellé de la charge">
                    </div>
                    <div class="ch-field">
                        <label for="chBeneficiaire">Bénéficiaire</label>
                        <input id="chBeneficiaire" maxlength="255">
                    </div>
                    <div class="ch-field">
                        <label for="chMontant">Montant</label>
                        <input id="chMontant" inputmode="decimal" required placeholder="0.00">
                    </div>
                    <div class="ch-field">
                        <label for="chType">Type Rég</label>
                        <select id="chType" data-keep-native="1">
                            <option value="">—</option>
                            <option>Esp</option>
                            <option>Chq</option>
                            <option>Eff</option>
                            <option>Vir</option>
                            <option>Vers</option>
                        </select>
                    </div>
                    <div class="ch-field">
                        <label for="chNumero">N°</label>
                        <input id="chNumero" maxlength="50">
                    </div>
                    <div class="ch-field">
                        <label for="chBanque">Bnq</label>
                        <select id="chBanque" data-keep-native="1">
                            <option value="">—</option>
                            <option>Attijariwafa</option>
                            <option>BMCE</option>
                            <option>Banque Populaire</option>
                            <option>CIH</option>
                            <option>SGMB</option>
                            <option>Crédit Agricole</option>
                            <option>CDM</option>
                            <option>Al Barid Bank</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div class="ch-field">
                        <label for="chTire">Tiré</label>
                        <input id="chTire" maxlength="150">
                    </div>
                    <div class="ch-field">
                        <label for="chEncaiss">Date Encaiss</label>
                        <input id="chEncaiss" type="text" inputmode="numeric" maxlength="10" placeholder="JJ/MM/AAAA" autocomplete="off">
                    </div>
                </div>
                <div class="ch-actions">
                    <button type="button" class="ch-btn ghost" id="chClose">Annuler</button>
                    <button type="submit" class="ch-btn primary" id="chSave">Valider</button>
                </div>
            </form>
        </div>
    </div>
</section>
<style>
    #panel-m-charge .ch-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-m-charge .ch-toolbar {
        display: flex; align-items: end; justify-content: space-between; gap: 0.75rem;
        margin-bottom: 1rem; flex-wrap: wrap;
    }
    #panel-m-charge .ch-filters { display: flex; flex-wrap: wrap; align-items: end; gap: 0.55rem; margin: 0; }
    #panel-m-charge .ch-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0; width: 140px;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #d6c4b4;
    }
    #panel-m-charge .ch-filter-cat { width: 220px; }
    #panel-m-charge .ch-filters select,
    #panel-m-charge .ch-filters input,
    #panel-m-charge .ch-form select,
    #panel-m-charge .ch-form input {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    #panel-m-charge .ch-form select:disabled,
    #panel-m-charge .ch-form input:disabled {
        background: #e5e7eb; color: #9ca3af; border-color: #d1d5db; cursor: not-allowed;
    }
    #panel-m-charge .ch-cat-row { display: flex; align-items: center; gap: 0.35rem; }
    #panel-m-charge .ch-cat-row select { flex: 1 1 auto; min-width: 0; }
    #panel-m-charge .ch-cat-plus {
        width: 38px; height: 38px; flex: 0 0 38px; border: none; border-radius: 10px; cursor: pointer;
        background: linear-gradient(180deg, #c47a3a, #0b1f3a); color: #fff; font-size: 1.25rem; font-weight: 700; line-height: 1;
    }
    #panel-m-charge .ch-add {
        display: inline-flex; align-items: center; gap: 0.35rem; height: 38px; padding: 0 0.85rem;
        border: none; border-radius: 10px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 0.8rem;
        background: linear-gradient(180deg, #c47a3a, #0b1f3a); color: #fff;
    }
    #panel-m-charge .ch-add svg { width: 15px; height: 15px; }
    #panel-m-charge .ch-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    #panel-m-charge .ch-refresh svg { width: 16px; height: 16px; }
    #panel-m-charge .ch-summary { display: flex; margin: 0 0 0 auto; }
    #panel-m-charge .ch-card {
        border-radius: 10px; padding: 0.45rem 0.9rem; color: #fff; min-width: 140px;
        background: linear-gradient(160deg, #ef4444, #c47a3a); box-shadow: 0 3px 12px rgba(15,23,42,0.12);
    }
    #panel-m-charge .ch-card span { display: block; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; opacity: 0.9; }
    #panel-m-charge .ch-card strong { display: block; margin-top: 0.12rem; font-size: 1.08rem; font-variant-numeric: tabular-nums; }
    #panel-m-charge .procar-table th.ch-slim,
    #panel-m-charge .procar-table td.ch-slim { width: 4.2rem; text-align: center; }
    #panel-m-charge .ch-icon {
        display: inline-grid; place-items: center; width: 28px; height: 28px; padding: 0;
        border: none; border-radius: 8px; background: transparent; color: #64748b; cursor: pointer;
    }
    #panel-m-charge .ch-icon svg { width: 15px; height: 15px; }
    #panel-m-charge .ch-icon.print:hover { color: #1e293b; }
    #panel-m-charge .ch-icon.pdf:hover { color: #ea580c; }
    #panel-m-charge .ch-icon.edit:hover { color: #2563eb; }
    #panel-m-charge .ch-icon.del:hover { color: #dc2626; }
    #panel-m-charge .ch-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden #panel-m-charge .ch-window { left: 0; }
    #panel-m-charge .ch-window[hidden] { display: none !important; }
    #panel-m-charge .ch-sheet {
        width: min(860px, calc(100vw - 2.5rem)); margin: auto; border-radius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #1e3a5f; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    #panel-m-charge .ch-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #0b1f3a 58%, #c47a3a);
    }
    #panel-m-charge .ch-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    #panel-m-charge .ch-x {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        background: transparent; color: rgba(255,255,255,0.82); font-size: 1.35rem; line-height: 1;
    }
    #panel-m-charge .ch-form { padding: 1rem 1.15rem 1rem; }
    #panel-m-charge .ch-error {
        margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-radius: 10px;
        background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #1e3a5f; font-size: 0.82rem;
    }
    #panel-m-charge .ch-grid {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.65rem; align-items: end;
    }
    #panel-m-charge .ch-field { display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }
    #panel-m-charge .ch-field label {
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #d6c4b4;
    }
    #panel-m-charge .ch-span-2 { grid-column: span 2; }
    #panel-m-charge .ch-actions { display: flex; justify-content: flex-end; gap: 0.55rem; margin-top: 1rem; }
    #panel-m-charge .ch-btn {
        height: 38px; padding: 0 1rem; border-radius: 10px; border: 1px solid #334155; cursor: pointer;
        font-family: inherit; font-weight: 700; font-size: 0.82rem;
    }
    #panel-m-charge .ch-btn.primary { background: linear-gradient(180deg, #c47a3a, #0b1f3a); color: #fff; border: none; }
    #panel-m-charge .ch-btn.ghost { background: #111827; color: #f8fafc; }
    #panel-m-charge .ch-cat-pop {
        position: fixed; inset: 0; z-index: 50; display: grid; place-items: center;
        background: rgba(0,0,0,0.4); padding: 1rem;
    }
    #panel-m-charge .ch-cat-pop[hidden] { display: none !important; }
    #panel-m-charge .ch-cat-card {
        width: min(360px, 100%); border-radius: 14px; padding: 1rem 1.1rem;
        background: #0c0c0e; border: 1px solid #1e3a5f; color: #f4f5f6;
        box-shadow: 0 18px 40px rgba(0,0,0,0.4);
    }
    #panel-m-charge .ch-cat-card h3 { margin: 0 0 0.75rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }
    #panel-m-charge .ch-cat-card label {
        display: flex; flex-direction: column; gap: 0.35rem;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #d6c4b4;
    }
    #panel-m-charge .ch-cat-card input {
        height: 38px; border-radius: 10px; border: 1px solid #334155; background: #fff; color: #000;
        font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    #panel-m-charge .ch-cat-actions { display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.9rem; }
    [data-theme="light"] #panel-m-charge .ch-filters label,
    [data-theme="light"] #panel-m-charge .ch-field label { color: #0b1f3a; }
    [data-theme="light"] #panel-m-charge .ch-refresh,
    [data-theme="light"] #panel-m-charge .ch-btn.ghost { background: #fff; color: #0b1f3a; border-color: #e0d0c0; }
    [data-theme="light"] #panel-m-charge .ch-sheet,
    [data-theme="light"] #panel-m-charge .ch-cat-card { background: #fff; color: #1a1214; border-color: #e0d0c0; }
    [data-theme="light"] #panel-m-charge .ch-sheet-head { color: #fff; }
    @media (max-width: 900px) {
        #panel-m-charge .ch-grid { grid-template-columns: 1fr 1fr; }
        #panel-m-charge .ch-window { left: 0; }
    }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-m-charge');
    if (!panel) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const body = panel.querySelector('#chBody');
    const monthFilter = panel.querySelector('#chMonth');
    const catFilter = panel.querySelector('#chCatFilter');
    const form = panel.querySelector('#chForm');
    const modal = panel.querySelector('#chModal');
    const catPop = panel.querySelector('#chCatPop');
    const catNameInput = panel.querySelector('#chCatName');
    const errorBox = panel.querySelector('#chError');
    const fields = {
        date: panel.querySelector('#chDate'),
        ref: panel.querySelector('#chRef'),
        category: panel.querySelector('#chCategory'),
        designation: panel.querySelector('#chDesignation'),
        beneficiaire: panel.querySelector('#chBeneficiaire'),
        montant: panel.querySelector('#chMontant'),
        type: panel.querySelector('#chType'),
        numero: panel.querySelector('#chNumero'),
        banque: panel.querySelector('#chBanque'),
        tire: panel.querySelector('#chTire'),
        encaiss: panel.querySelector('#chEncaiss'),
    };
    if (!body || !form || !modal) return;

    let rows = [];
    let categories = [];
    let editingId = null;
    let nextRef = 'CH001';
    const printer = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5h20v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z"/></svg>';
    const file = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>';
    const trash = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>';

    function money(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }
    function todayFr() {
        const d = new Date();
        return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
    }
    function isoToFr(value) {
        const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
        return match ? match[3] + '/' + match[2] + '/' + match[1] : '';
    }
    function frToIso(value) {
        const match = String(value || '').match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        return match ? match[3] + '-' + match[2] + '-' + match[1] : '';
    }
    function maskDate(input) {
        const digits = String(input.value || '').replace(/\D/g, '').slice(0, 8);
        input.value = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 8)].filter(Boolean).join('/');
    }
    function syncCashFields() {
        const cash = fields.type.value === 'Esp';
        [fields.numero, fields.banque, fields.tire].forEach(function (field) {
            field.disabled = cash;
            if (cash) field.value = '';
        });
    }
    function showError(message) {
        if (!message) {
            errorBox.hidden = true;
            errorBox.textContent = '';
            return;
        }
        errorBox.hidden = false;
        errorBox.textContent = message;
    }
    function fillCategories(selectedId) {
        const currentFilter = catFilter.value;
        const currentForm = fields.category.value;
        const opts = categories.map(function (item) {
            return '<option value="' + esc(item.id) + '">' + esc(item.name) + '</option>';
        }).join('');
        catFilter.innerHTML = '<option value="">Tous</option>' + opts;
        fields.category.innerHTML = '<option value="">—</option>' + opts;
        if ([].some.call(catFilter.options, function (opt) { return opt.value === currentFilter; })) {
            catFilter.value = currentFilter;
        }
        const prefer = selectedId != null && selectedId !== '' ? String(selectedId) : currentForm;
        if ([].some.call(fields.category.options, function (opt) { return opt.value === prefer; })) {
            fields.category.value = prefer;
        }
    }
    function filteredRows() {
        const month = monthFilter.value;
        const cat = catFilter.value;
        return rows.filter(function (row) {
            if (month && String(row.month || '') !== month) return false;
            if (cat && String(row.charge_category_id || '') !== cat) return false;
            return true;
        });
    }
    function paintSummary(list) {
        const total = list.reduce(function (sum, row) { return sum + (Number(row.montant) || 0); }, 0);
        panel.querySelector('#chSumTotal').textContent = money(total);
    }
    function render() {
        const list = filteredRows();
        paintSummary(list);
        if (!list.length) {
            body.innerHTML = '<tr><td colspan="14">' + (rows.length ? 'Aucune charge pour cette recherche.' : 'Aucune charge enregistrée') + '</td></tr>';
            return;
        }
        body.innerHTML = list.map(function (row) {
            return '<tr>'
                + '<td class="ch-slim"><button type="button" class="ch-icon print" data-act="print" data-id="' + row.id + '" title="Imprimer">' + printer + '</button></td>'
                + '<td class="ch-slim"><button type="button" class="ch-icon pdf" data-act="pdf" data-id="' + row.id + '" title="PDF">' + file + '</button></td>'
                + '<td>' + esc(row.charge_date_fr || '—') + '</td>'
                + '<td>' + esc(row.reference) + '</td>'
                + '<td>' + esc(row.categorie || '—') + '</td>'
                + '<td>' + esc(row.designation || '—') + '</td>'
                + '<td>' + esc(row.beneficiaire || '—') + '</td>'
                + '<td>' + esc(money(row.montant)) + '</td>'
                + '<td>' + esc(row.reglement || '—') + '</td>'
                + '<td>' + esc(row.numero || '—') + '</td>'
                + '<td>' + esc(row.banque || '—') + '</td>'
                + '<td>' + esc(row.nom_tire || '—') + '</td>'
                + '<td>' + esc(row.date_encaissement_fr || '—') + '</td>'
                + '<td>'
                + '<button type="button" class="ch-icon edit" data-act="edit" data-id="' + row.id + '" title="Modifier">' + pencil + '</button>'
                + '<button type="button" class="ch-icon del" data-act="delete" data-id="' + row.id + '" title="Supprimer">' + trash + '</button>'
                + '</td>'
                + '</tr>';
        }).join('');
    }
    function openModal(row) {
        editingId = row ? row.id : null;
        showError('');
        panel.querySelector('#chModalTitle').textContent = row ? 'Modifier charge' : 'Nouvelle charge';
        fields.date.value = row ? (row.charge_date_fr || isoToFr(row.charge_date) || todayFr()) : todayFr();
        fields.ref.value = row ? row.reference : nextRef;
        fillCategories(row ? row.charge_category_id : '');
        fields.designation.value = row ? (row.designation || '') : '';
        fields.beneficiaire.value = row ? (row.beneficiaire || '') : '';
        fields.montant.value = row ? String(row.montant ?? '') : '';
        fields.type.value = row ? (row.reglement || '') : '';
        fields.numero.value = row ? (row.numero || '') : '';
        fields.banque.value = row ? (row.banque || '') : '';
        fields.tire.value = row ? (row.nom_tire || '') : '';
        fields.encaiss.value = row ? (row.date_encaissement_fr || isoToFr(row.date_encaissement) || '') : '';
        syncCashFields();
        modal.hidden = false;
    }
    function closeModal() {
        modal.hidden = true;
        editingId = null;
        showError('');
    }
    function openCatPop() {
        catNameInput.value = '';
        catPop.hidden = false;
        setTimeout(function () { catNameInput.focus(); }, 30);
    }
    function closeCatPop() {
        catPop.hidden = true;
        catNameInput.value = '';
    }
    function printRow(row) {
        const win = window.open('', '_blank', 'width=900,height=700');
        if (!win) return;
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + esc(row.reference) + '</title>'
            + '<style>body{font-family:Arial,sans-serif;padding:28px;color:#1e293b}h1{margin:0 0 .4rem;font-size:1.2rem}'
            + 'table{width:100%;border-collapse:collapse;margin-top:1rem}td{padding:.45rem .5rem;border:1px solid #cbd5e1}'
            + 'td:first-child{width:180px;background:#f8fafc;font-weight:700}</style></head><body>'
            + '<h1>Charge ' + esc(row.reference) + '</h1>'
            + '<table>'
            + '<tr><td>Date</td><td>' + esc(row.charge_date_fr || '—') + '</td></tr>'
            + '<tr><td>Catégorie</td><td>' + esc(row.categorie || '—') + '</td></tr>'
            + '<tr><td>Désignation</td><td>' + esc(row.designation || '—') + '</td></tr>'
            + '<tr><td>Bénéficiaire</td><td>' + esc(row.beneficiaire || '—') + '</td></tr>'
            + '<tr><td>Montant</td><td>' + esc(money(row.montant)) + '</td></tr>'
            + '<tr><td>Type Rég</td><td>' + esc(row.reglement || '—') + '</td></tr>'
            + '<tr><td>N°</td><td>' + esc(row.numero || '—') + '</td></tr>'
            + '<tr><td>Banque</td><td>' + esc(row.banque || '—') + '</td></tr>'
            + '<tr><td>Tiré</td><td>' + esc(row.nom_tire || '—') + '</td></tr>'
            + '<tr><td>Date Encaiss</td><td>' + esc(row.date_encaissement_fr || '—') + '</td></tr>'
            + '</table></body></html>');
        win.document.close();
        win.focus();
        setTimeout(function () { win.print(); }, 250);
    }
    async function load() {
        const response = await fetch('/charges', {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error('Chargement impossible');
        const payload = await response.json();
        rows = Array.isArray(payload.data) ? payload.data : [];
        categories = (payload.meta && payload.meta.categories) ? payload.meta.categories : [];
        nextRef = (payload.meta && payload.meta.next_ref) ? payload.meta.next_ref : 'CH001';
        fillCategories();
        render();
    }
    async function saveCategory() {
        const name = String(catNameInput.value || '').trim();
        if (!name) {
            catNameInput.focus();
            return;
        }
        const response = await fetch('/charge-categories', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ name: name }),
        });
        const payload = await response.json().catch(function () { return {}; });
        if (!response.ok) throw new Error(payload.message || 'Création catégorie impossible');
        const exists = categories.some(function (item) { return String(item.id) === String(payload.id); });
        if (!exists) categories.push({ id: payload.id, name: payload.name });
        categories.sort(function (a, b) { return String(a.name).localeCompare(String(b.name), 'fr', { sensitivity: 'base' }); });
        fillCategories(payload.id);
        fields.category.value = String(payload.id);
        closeCatPop();
        render();
    }

    panel.querySelector('#chAdd').addEventListener('click', function () { openModal(null); });
    panel.querySelector('#chClose').addEventListener('click', closeModal);
    panel.querySelector('#chCloseX').addEventListener('click', closeModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });
    panel.querySelector('#chCatPlusForm').addEventListener('click', function () { openCatPop(); });
    panel.querySelector('#chCatCancel').addEventListener('click', closeCatPop);
    panel.querySelector('#chCatSave').addEventListener('click', function () {
        saveCategory().catch(function (err) { alert(err.message || 'Erreur'); });
    });
    catNameInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            saveCategory().catch(function (err) { alert(err.message || 'Erreur'); });
        }
    });
    catPop.addEventListener('click', function (event) {
        if (event.target === catPop) closeCatPop();
    });
    monthFilter.addEventListener('change', render);
    catFilter.addEventListener('change', render);
    panel.querySelector('#chRefresh').addEventListener('click', function () {
        load().catch(function () {
            body.innerHTML = '<tr><td colspan="14">Erreur de chargement</td></tr>';
        });
    });
    fields.type.addEventListener('change', syncCashFields);
    [fields.date, fields.encaiss].forEach(function (input) {
        input.addEventListener('input', function () { maskDate(input); });
        input.addEventListener('blur', function () { maskDate(input); });
    });
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        showError('');
        const chargeDate = frToIso(fields.date.value);
        const encaissDate = frToIso(fields.encaiss.value);
        if (!chargeDate) {
            showError('Date invalide. Format attendu : JJ/MM/AAAA');
            fields.date.focus();
            return;
        }
        if (fields.encaiss.value.trim() && !encaissDate) {
            showError('Date Encaiss invalide. Format attendu : JJ/MM/AAAA');
            fields.encaiss.focus();
            return;
        }
        const payload = {
            reference: fields.ref.value,
            charge_date: chargeDate,
            charge_category_id: fields.category.value || null,
            designation: fields.designation.value,
            beneficiaire: fields.beneficiaire.value,
            reglement: fields.type.value,
            numero: fields.numero.value,
            banque: fields.banque.value,
            nom_tire: fields.tire.value,
            montant: String(fields.montant.value || '').replace(',', '.'),
            date_encaissement: encaissDate || null,
        };
        try {
            const response = await fetch(editingId ? '/charges/' + editingId : '/charges', {
                method: editingId ? 'PUT' : 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(function () { return {}; });
            if (!response.ok) {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Enregistrement impossible');
                throw new Error(msg);
            }
            closeModal();
            await load();
        } catch (err) {
            showError(err.message || 'Erreur');
        }
    });
    body.addEventListener('click', async function (event) {
        const btn = event.target.closest('[data-act]');
        if (!btn) return;
        const id = Number(btn.getAttribute('data-id'));
        const row = rows.find(function (item) { return Number(item.id) === id; });
        if (!row) return;
        const act = btn.getAttribute('data-act');
        if (act === 'print' || act === 'pdf') {
            printRow(row);
            return;
        }
        if (act === 'edit') {
            openModal(row);
            return;
        }
        if (act === 'delete') {
            if (!confirm('Supprimer la charge ' + row.reference + ' ?')) return;
            const response = await fetch('/charges/' + id, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                alert('Suppression impossible');
                return;
            }
            await load();
        }
    });
    document.querySelector('[data-panel="m-charge"]')?.addEventListener('click', function () {
        load().catch(function () {});
    });
    load().catch(function () {
        body.innerHTML = '<tr><td colspan="14">Erreur de chargement</td></tr>';
    });
})();
</script>
