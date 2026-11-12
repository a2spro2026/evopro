<section class="panel" id="panel-f-regl">
    <div class="content-head ra-head">
        <div>
            <h1>Règlement Achats</h1>
            <p class="ra-sub">Règlement des bons d’achat fournisseurs</p>
        </div>
        <button type="button" class="ra-add" id="raAdd">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
            Ajouter
        </button>
    </div>

    <div class="ra-filters">
        <label>Mois
            <select id="raMonth" data-keep-native="1">
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
        <label>N° Rég<select id="raRefFilter" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Mnt<select id="raAmountFilter" data-keep-native="1"><option value="">Tous</option></select></label>
        <button type="button" class="ra-refresh" id="raRefresh" title="Actualiser" aria-label="Actualiser">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Liste des règlements</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th>Réf</th>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th>Type</th>
                        <th>Nom de Tiré</th>
                        <th>N°</th>
                        <th>Bnq</th>
                        <th>Date Décaiss</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="raBody">
                    <tr><td colspan="11">Aucun règlement enregistré</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ra-window" id="raModal" hidden>
        <div class="ra-sheet" role="dialog" aria-modal="true" aria-labelledby="raModalTitle">
            <div class="ra-sheet-head">
                <h3 id="raModalTitle">Nouveau règlement achats</h3>
                <button type="button" class="ra-x" id="raCloseX" title="Fermer" aria-label="Fermer">×</button>
            </div>
            <form id="raForm" class="ra-form" autocomplete="off">
                <div class="ra-error" id="raError" hidden></div>
                <div class="ra-grid">
                    <div class="ra-field">
                        <label for="raDate">Date</label>
                        <input id="raDate" type="date" required>
                    </div>
                    <div class="ra-field">
                        <label for="raRef">Réf Régl</label>
                        <input id="raRef" readonly>
                    </div>
                    <div class="ra-field ra-span-2">
                        <label for="raSupplier">Nom Fournisseur</label>
                        <select id="raSupplier" data-keep-native="1" required>
                            <option value="">— Sélectionner —</option>
                        </select>
                    </div>
                    <div class="ra-field">
                        <label for="raType">Type Rég</label>
                        <select id="raType" data-keep-native="1">
                            <option value="">—</option>
                            <option>Esp</option>
                            <option>Chq</option>
                            <option>Eff</option>
                            <option>Vir</option>
                            <option>Vers</option>
                        </select>
                    </div>
                    <div class="ra-field">
                        <label for="raNumero">N° régl</label>
                        <input id="raNumero" maxlength="50">
                    </div>
                    <div class="ra-field">
                        <label for="raBanque">Banq</label>
                        <select id="raBanque" data-keep-native="1">
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
                    <div class="ra-field">
                        <label for="raTire">Nom de Tiré</label>
                        <input id="raTire" maxlength="150">
                    </div>
                    <div class="ra-field">
                        <label for="raMontant">Montant Régl</label>
                        <input id="raMontant" inputmode="decimal" required placeholder="0.00">
                    </div>
                    <div class="ra-field">
                        <label for="raDecaiss">Date Décaiss</label>
                        <input id="raDecaiss" type="date">
                    </div>
                    <div class="ra-field ra-span-2">
                        <label for="raRemarque">Remarque</label>
                        <input id="raRemarque" maxlength="1000">
                    </div>
                </div>

                <div class="ra-form-cards">
                    <div class="ra-mini"><span>Total TTC</span><strong id="raTotalTtc">0,00</strong></div>
                    <div class="ra-mini solde"><span>Solde TTC</span><strong id="raSoldeTtc">0,00</strong></div>
                </div>

                <div class="ra-orders-head"><p>Commande à Payer</p></div>
                <div class="ra-orders-wrap">
                    <table class="ra-orders">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="raSelectAll" title="Tout sélectionner"></th>
                                <th>N° BA</th>
                                <th>Date</th>
                                <th>Montant Bon</th>
                                <th>Montant Payé</th>
                                <th>Solde</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="raOrders">
                            <tr><td colspan="7">Sélectionnez un fournisseur</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="ra-actions">
                    <button type="button" class="ra-btn ghost" id="raClose">Annuler</button>
                    <button type="submit" class="ra-btn primary" id="raSave">Valider</button>
                </div>
            </form>
        </div>
    </div>
</section>
<style>
    #panel-f-regl .ra-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-f-regl .ra-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .ra-add {
        display: inline-flex; align-items: center; gap: 0.4rem; height: 40px; padding: 0 1rem;
        border: none; border-radius: 10px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 0.85rem;
        background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff;
    }
    .ra-add svg { width: 16px; height: 16px; }
    .ra-filters {
        display: grid; grid-template-columns: 160px 180px 160px 38px; gap: 0.75rem;
        align-items: end; margin-bottom: 1rem;
    }
    .ra-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .ra-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    .ra-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    .ra-refresh svg { width: 16px; height: 16px; }
    .ra-icon {
        display: inline-grid; place-items: center; width: 28px; height: 28px; padding: 0;
        border: none; border-radius: 8px; background: transparent; color: #64748b; cursor: pointer;
    }
    .ra-icon svg { width: 15px; height: 15px; }
    .ra-icon.del:hover { color: #dc2626; }
    .ra-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden .ra-window { left: 0; }
    .ra-window[hidden] { display: none !important; }
    .ra-sheet {
        width: min(1180px, calc(100vw - 2.5rem)); margin: auto; border-radius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #4a1824; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    .ra-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .ra-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .ra-x {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        background: transparent; color: rgba(255,255,255,0.82); font-size: 1.35rem; line-height: 1;
    }
    .ra-form { padding: 1rem 1.15rem 0.4rem; }
    .ra-error { margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-radius: 10px; background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #7f1d1d; font-size: 0.82rem; }
    .ra-grid {
        display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 0.65rem; align-items: end;
    }
    .ra-span-2 { grid-column: span 2; }
    .ra-field { display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }
    .ra-field label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em; color: #94a3b8; }
    .ra-field input, .ra-field select {
        box-sizing: border-box; width: 100%; height: 38px; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.7rem;
    }
    .ra-form-cards { display: grid; grid-template-columns: repeat(2, minmax(0, 180px)); gap: 0.65rem; margin: 0.9rem 0 0.7rem; justify-content: end; }
    .ra-mini { border-radius: 12px; padding: 0.55rem 0.7rem; text-align: center; background: #1e293b; border: 1px solid #334155; }
    .ra-mini span { display: block; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; }
    .ra-mini strong { display: block; margin-top: 0.15rem; font-size: 0.95rem; color: #fff; }
    .ra-mini.solde strong { color: #f87171; }
    .ra-orders-head p {
        margin: 0 0 0.55rem; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase;
        color: #e7c4c8; text-decoration: underline; text-underline-offset: 3px;
    }
    .ra-orders-wrap { max-height: 280px; overflow: auto; border: 1px solid #334155; border-radius: 10px; }
    .ra-orders { width: 100%; border-collapse: collapse; min-width: 860px; }
    .ra-orders th {
        position: sticky; top: 0; background: #5c1220; color: #fff;
        font-size: 0.62rem; letter-spacing: 0.06em; text-transform: uppercase; padding: 0.5rem; text-align: center;
    }
    .ra-orders td { padding: 0.45rem; border-top: 1px solid #1e293b; text-align: center; color: #000; background: #fff; font-size: 0.78rem; }
    .ra-actions { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.95rem 0 1rem; }
    .ra-btn { height: 36px; padding: 0 1rem; border-radius: 999px; border: none; font-family: inherit; font-weight: 700; font-size: 0.78rem; cursor: pointer; }
    .ra-btn.ghost { background: #1e293b; color: #e2e8f0; border: 1px solid #475569; }
    .ra-btn.primary { background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff; }
    .ra-act {
        border: 1px solid #cbd5e1; border-radius: 999px; background: #f8fafc; color: #334155;
        font-size: 0.68rem; font-weight: 700; padding: 0.18rem 0.45rem; cursor: pointer; margin: 0 0.1rem;
    }
    .ra-act.is-on { background: #5c1220; color: #fff; border-color: #5c1220; }
    .ra-act.is-on[data-a="Payé"] { background: #15803d; border-color: #15803d; }
    .ra-act.is-on[data-a="Report"] { background: #d97706; border-color: #d97706; }
    .ra-act.is-on[data-a="Imp"] { background: #dc2626; border-color: #dc2626; }
    .ra-act.is-on[data-a="Dévalidé"] { background: #7c3aed; border-color: #7c3aed; }
    [data-theme="light"] .ra-filters label { color: #5c1220; }
    [data-theme="light"] .ra-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .ra-sheet { background: #fff; border-color: #e7c9cf; color: #1a1214; }
    [data-theme="light"] .ra-field label { color: #5c1220; }
    [data-theme="light"] .ra-mini { background: #fff; border-color: #e7c9cf; }
    [data-theme="light"] .ra-mini strong { color: #1a1214; }
    [data-theme="light"] .ra-mini.solde strong { color: #dc2626; }
    [data-theme="light"] .ra-btn.ghost { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .ra-orders-head p { color: #8f2436; }
    @media (max-width: 1100px) {
        .ra-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ra-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ra-window { left: 0; }
    }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-f-regl');
    const modal = document.getElementById('raModal');
    const form = document.getElementById('raForm');
    const body = document.getElementById('raBody');
    const ordersBody = document.getElementById('raOrders');
    if (!panel || !modal || !form || !body || !ordersBody) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const errorBox = document.getElementById('raError');
    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    const actions = ['Inst', 'Payé', 'Report', 'Imp', 'Dévalidé'];
    let rows = [];
    let meta = { next_ref: 'RF-0001', date: '', suppliers: [] };
    let orders = [];
    let selected = {};
    let orderActions = {};
    let editingId = null;
    let mode = 'create';

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function money(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function moneyDot(value) {
        return (Number(value) || 0).toFixed(2);
    }
    function parseNum(value) {
        const n = Number(String(value ?? '').replace(/\s/g, '').replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    }
    function showError(message) {
        errorBox.hidden = !message;
        errorBox.textContent = message || '';
    }
    function formatDate(value) {
        if (!value) return '—';
        const parts = String(value).split('-');
        if (parts.length !== 3) return value;
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    function todayInput() {
        const d = new Date();
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }
    function fillLists() {
        const refs = [...new Set(rows.map((row) => String(row.reference || '').trim()).filter(Boolean))]
            .sort((a, b) => a.localeCompare(b, 'fr', { sensitivity: 'base' }));
        const amounts = [...new Set(rows.map((row) => moneyDot(row.montant)))]
            .sort((a, b) => Number(a) - Number(b));
        const refSelect = document.getElementById('raRefFilter');
        const amountSelect = document.getElementById('raAmountFilter');
        const currentRef = refSelect.value;
        const currentAmount = amountSelect.value;
        refSelect.innerHTML = `<option value="">Tous</option>${refs.map((value) => `<option value="${esc(value)}">${esc(value)}</option>`).join('')}`;
        amountSelect.innerHTML = `<option value="">Tous</option>${amounts.map((value) => `<option value="${esc(value)}">${esc(money(value))}</option>`).join('')}`;
        refSelect.value = refs.includes(currentRef) ? currentRef : '';
        amountSelect.value = amounts.includes(currentAmount) ? currentAmount : '';
    }
    function filteredRows() {
        const month = document.getElementById('raMonth').value;
        const ref = document.getElementById('raRefFilter').value;
        const amount = document.getElementById('raAmountFilter').value;
        return rows.filter((row) => {
            if (month && String(row.month || '') !== month) return false;
            if (ref && String(row.reference || '') !== ref) return false;
            if (amount && moneyDot(row.montant) !== amount) return false;
            return true;
        });
    }
    function render() {
        const list = filteredRows();
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="11">${rows.length ? 'Aucun règlement pour cette recherche.' : 'Aucun règlement enregistré'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(row.reference)}</td>
            <td>${esc(row.payment_date_fr || formatDate(row.payment_date))}</td>
            <td>${esc(row.fournisseur)}</td>
            <td>${esc(row.reglement) || '—'}</td>
            <td>${esc(row.nom_tire) || '—'}</td>
            <td>${esc(row.numero) || '—'}</td>
            <td>${esc(row.banque) || '—'}</td>
            <td>${esc(row.date_decaissement_fr || formatDate(row.date_decaissement))}</td>
            <td>${esc(money(row.montant))}</td>
            <td>${esc(row.statut)}</td>
            <td>
                <button type="button" class="ra-icon" data-act="voir" data-id="${row.id}" title="Voir">${eye}</button>
                <button type="button" class="ra-icon" data-act="modifier" data-id="${row.id}" title="Modifier">${pencil}</button>
                <button type="button" class="ra-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer">${trash}</button>
            </td>
        </tr>`).join('');
    }
    function fillSuppliers(selectedId) {
        const select = document.getElementById('raSupplier');
        select.innerHTML = `<option value="">— Sélectionner —</option>${(meta.suppliers || []).map((item) =>
            `<option value="${esc(item.id)}"${String(item.id) === String(selectedId || '') ? ' selected' : ''}>${esc(item.name)}</option>`
        ).join('')}`;
    }
    function syncCashFields() {
        const cash = document.getElementById('raType').value === 'Esp';
        ['raNumero', 'raBanque', 'raTire'].forEach((id) => {
            const field = document.getElementById(id);
            field.disabled = cash;
            if (cash) field.value = '';
        });
    }
    function selectedIds() {
        return Object.keys(selected).filter((id) => selected[id]).map(Number);
    }
    function previewMap() {
        const map = {};
        const amount = parseNum(document.getElementById('raMontant').value);
        let remaining = amount;
        const ids = selectedIds();
        orders.forEach((order) => {
            map[order.id] = {
                paye: Number(order.montant_paye) || 0,
                solde: Number(order.solde) || 0,
                allocation: 0,
            };
        });
        if (!ids.length || amount <= 0) return map;
        orders.filter((order) => ids.includes(order.id)).forEach((order) => {
            if (remaining <= 0) return;
            const due = Math.max((Number(order.montant_bon) || 0) - (Number(order.montant_paye) || 0), 0);
            const applied = Math.round(Math.min(due > 0 ? due : remaining, remaining) * 100) / 100;
            const paye = Math.round(((Number(order.montant_paye) || 0) + applied) * 100) / 100;
            map[order.id] = {
                paye,
                solde: Math.round(((Number(order.montant_bon) || 0) - paye) * 100) / 100,
                allocation: applied,
            };
            remaining = Math.round((remaining - applied) * 100) / 100;
        });
        return map;
    }
    function renderOrders() {
        const map = previewMap();
        const total = orders.reduce((sum, order) => sum + (Number(order.montant_bon) || 0), 0);
        const solde = orders.reduce((sum, order) => sum + (Number(map[order.id]?.solde ?? order.solde) || 0), 0);
        document.getElementById('raTotalTtc').textContent = money(total);
        document.getElementById('raSoldeTtc').textContent = money(solde);
        if (!orders.length) {
            ordersBody.innerHTML = '<tr><td colspan="7">Aucun bon d’achat pour ce fournisseur</td></tr>';
            return;
        }
        ordersBody.innerHTML = orders.map((order) => {
            const preview = map[order.id] || {};
            const checked = selected[order.id] ? ' checked' : '';
            const action = orderActions[order.id] || order.payment_action || 'Inst';
            const buttons = actions.map((name) =>
                `<button type="button" class="ra-act${action === name ? ' is-on' : ''}" data-order="${order.id}" data-a="${esc(name)}">${esc(name)}</button>`
            ).join('');
            return `<tr>
                <td><input type="checkbox" data-select="${order.id}"${checked}></td>
                <td>${esc(order.reference)}</td>
                <td>${esc(formatDate(order.order_date))}</td>
                <td>${esc(money(order.montant_bon))}</td>
                <td>${esc(money(preview.paye))}</td>
                <td>${esc(money(preview.solde))}</td>
                <td>${buttons}</td>
            </tr>`;
        }).join('');
    }
    async function loadOrders(supplierId) {
        if (!supplierId) {
            orders = [];
            selected = {};
            orderActions = {};
            renderOrders();
            return;
        }
        const response = await fetch(`/reglements-achats/orders?supplier_id=${encodeURIComponent(supplierId)}`, { headers: { 'Accept': 'application/json' } });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(payload.message || 'Chargement des bons impossible.');
        orders = payload.data || [];
        selected = {};
        orderActions = {};
        orders.forEach((order) => { orderActions[order.id] = order.payment_action || 'Inst'; });
        renderOrders();
    }
    function openModal(row, nextMode) {
        mode = nextMode || 'create';
        editingId = row?.id || null;
        showError('');
        form.reset();
        document.getElementById('raDate').value = row?.payment_date || meta.date || todayInput();
        document.getElementById('raRef').value = row?.reference || meta.next_ref || 'RF-0001';
        fillSuppliers(row?.supplier_id || '');
        document.getElementById('raType').value = row?.reglement || '';
        document.getElementById('raNumero').value = row?.numero || '';
        document.getElementById('raBanque').value = row?.banque || '';
        document.getElementById('raTire').value = row?.nom_tire || '';
        document.getElementById('raMontant').value = row ? moneyDot(row.montant) : '';
        document.getElementById('raDecaiss').value = row?.date_decaissement || document.getElementById('raDate').value;
        document.getElementById('raRemarque').value = row?.remarque || '';
        document.getElementById('raModalTitle').textContent = mode === 'view'
            ? `Règlement ${row?.reference || ''}`
            : (mode === 'edit' ? `Modifier ${row?.reference || ''}` : 'Nouveau règlement achats');
        document.getElementById('raSave').hidden = mode === 'view';
        document.getElementById('raClose').textContent = mode === 'view' ? 'Fermer' : 'Annuler';
        syncCashFields();
        form.querySelectorAll('input, select, button').forEach((field) => {
            if (field.id === 'raClose' || field.id === 'raCloseX') return;
            if (field.id === 'raRef') { field.disabled = true; return; }
            field.disabled = mode === 'view';
        });
        modal.hidden = false;
        if (row?.supplier_id) {
            loadOrders(row.supplier_id).then(() => {
                if (row.allocations?.length) {
                    row.allocations.forEach((allocation) => {
                        selected[allocation.purchase_order_id] = true;
                        orderActions[allocation.purchase_order_id] = allocation.action || 'Inst';
                    });
                    renderOrders();
                }
            }).catch((error) => showError(error.message));
        } else {
            orders = [];
            selected = {};
            orderActions = {};
            renderOrders();
        }
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
    async function load() {
        const response = await fetch('/reglements-achats', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json();
        if (!response.ok) throw new Error(payload.message || 'Chargement impossible.');
        rows = payload.data || [];
        meta = payload.meta || meta;
        fillLists();
        render();
    }
    document.getElementById('raAdd').addEventListener('click', () => openModal(null, 'create'));
    document.getElementById('raClose').addEventListener('click', closeModal);
    document.getElementById('raCloseX').addEventListener('click', () => {
        if (entryOpen()) { blockLeave(); return; }
        closeModal();
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
    document.getElementById('raType').addEventListener('change', syncCashFields);
    document.getElementById('raMontant').addEventListener('input', renderOrders);
    document.getElementById('raSupplier').addEventListener('change', () => {
        loadOrders(document.getElementById('raSupplier').value).catch((error) => showError(error.message));
    });
    document.getElementById('raSelectAll').addEventListener('change', (event) => {
        selected = {};
        if (event.target.checked) orders.forEach((order) => { selected[order.id] = true; });
        renderOrders();
    });
    ordersBody.addEventListener('change', (event) => {
        const box = event.target.closest('[data-select]');
        if (!box) return;
        selected[box.dataset.select] = box.checked;
        renderOrders();
    });
    ordersBody.addEventListener('click', (event) => {
        const button = event.target.closest('[data-a]');
        if (!button || mode === 'view') return;
        orderActions[button.dataset.order] = button.dataset.a;
        selected[button.dataset.order] = true;
        renderOrders();
    });
    ['raMonth', 'raRefFilter', 'raAmountFilter'].forEach((id) => {
        document.getElementById(id).addEventListener('change', render);
    });
    document.getElementById('raRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="11">${esc(error.message)}</td></tr>`; });
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (mode === 'view') return;
        showError('');
        const supplierId = document.getElementById('raSupplier').value;
        const montant = parseNum(document.getElementById('raMontant').value);
        const ids = selectedIds();
        if (!supplierId) { showError('Sélectionnez un fournisseur.'); return; }
        if (montant <= 0) { showError('Saisissez un montant de règlement.'); return; }
        if (!ids.length) { showError('Cochez au moins un bon d’achat.'); return; }
        const map = previewMap();
        const allocations = ids.map((id) => ({
            purchase_order_id: id,
            amount: map[id]?.allocation || 0,
            action: orderActions[id] || 'Inst',
        }));
        const save = document.getElementById('raSave');
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/reglements-achats/${editingId}` : '/reglements-achats', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    payment_date: document.getElementById('raDate').value,
                    supplier_id: Number(supplierId),
                    reglement: document.getElementById('raType').value,
                    numero: document.getElementById('raNumero').value,
                    banque: document.getElementById('raBanque').value,
                    nom_tire: document.getElementById('raTire').value,
                    montant,
                    date_decaissement: document.getElementById('raDecaiss').value || null,
                    remarque: document.getElementById('raRemarque').value,
                    allocations,
                }),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const first = data.errors ? Object.values(data.errors)[0]?.[0] : data.message;
                throw new Error(first || 'Enregistrement impossible.');
            }
            closeModal();
            await load();
            document.dispatchEvent(new CustomEvent('evopro-regl-achats-changed'));
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
            if (!window.confirm(`Supprimer le règlement ${row.reference} ?`)) return;
            const response = await fetch(`/reglements-achats/${row.id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            if (!response.ok) { showError('Impossible de supprimer ce règlement.'); return; }
            await load();
            document.dispatchEvent(new CustomEvent('evopro-regl-achats-changed'));
            document.dispatchEvent(new CustomEvent('evopro-ba-changed'));
        }
    });
    document.querySelector('[data-panel="f-regl"]')?.addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="11">${esc(error.message)}</td></tr>`; });
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="11">${esc(error.message)}</td></tr>`; });
})();
</script>
