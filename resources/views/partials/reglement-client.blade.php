<section class="panel" id="panel-c-regl">
    <div class="content-head rc-head">
        <div>
            <h1>Règlement Client</h1>
            <p class="rc-sub">Règlement des bons de livraison clients</p>
        </div>
        <button type="button" class="rc-add" id="rcAdd">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
            Ajouter
        </button>
    </div>

    <div class="rc-filters">
        <label>Mois
            <select id="rcMonth" data-keep-native="1">
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
        <label>N° Rég<select id="rcRefFilter" data-keep-native="1"><option value="">Tous</option></select></label>
        <label>Mnt<select id="rcAmountFilter" data-keep-native="1"><option value="">Tous</option></select></label>
        <button type="button" class="rc-refresh" id="rcRefresh" title="Actualiser" aria-label="Actualiser">
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
                        <th>client</th>
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
                <tbody id="rcBody">
                    <tr><td colspan="11">Aucun règlement enregistré</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rc-window" id="rcModal" hidden>
        <div class="rc-sheet" role="dialog" aria-modal="true" aria-labelledby="rcModalTitle">
            <div class="rc-sheet-head">
                <h3 id="rcModalTitle">Nouveau Règlement Client</h3>
                <button type="button" class="rc-x" id="rcCloseX" title="Fermer" aria-label="Fermer">×</button>
            </div>
            <form id="rcForm" class="rc-form" autocomplete="off">
                <div class="rc-error" id="rcError" hidden></div>
                <div class="rc-grid">
                    <div class="rc-field">
                        <label for="rcDate">Date</label>
                        <input id="rcDate" type="date" required>
                    </div>
                    <div class="rc-field">
                        <label for="rcRef">Réf Régl</label>
                        <input id="rcRef" readonly>
                    </div>
                    <div class="rc-field rc-span-2">
                        <label for="rcClient">Nom Client</label>
                        <select id="rcClient" data-keep-native="1" required>
                            <option value="">— Sélectionner —</option>
                        </select>
                    </div>
                    <div class="rc-field">
                        <label for="rcType">Type Rég</label>
                        <select id="rcType" data-keep-native="1">
                            <option value="">—</option>
                            <option>Esp</option>
                            <option>Chq</option>
                            <option>Eff</option>
                            <option>Vir</option>
                            <option>Vers</option>
                        </select>
                    </div>
                    <div class="rc-field">
                        <label for="rcNumero">N° régl</label>
                        <input id="rcNumero" maxlength="50">
                    </div>
                    <div class="rc-field">
                        <label for="rcBanque">Banq</label>
                        <select id="rcBanque" data-keep-native="1">
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
                    <div class="rc-field">
                        <label for="rcTire">Nom de Tiré</label>
                        <input id="rcTire" maxlength="150">
                    </div>
                    <div class="rc-field">
                        <label for="rcMontant">Montant Régl</label>
                        <input id="rcMontant" inputmode="decimal" required placeholder="0.00">
                    </div>
                    <div class="rc-field">
                        <label for="rcDecaiss">Date Décaiss</label>
                        <input id="rcDecaiss" type="date">
                    </div>
                    <div class="rc-field rc-span-2">
                        <label for="rcRemarque">Remarque</label>
                        <input id="rcRemarque" maxlength="1000">
                    </div>
                </div>

                <div class="rc-form-cards">
                    <div class="rc-mini"><span>Total TTC</span><strong id="rcTotalTtc">0,00</strong></div>
                    <div class="rc-mini solde"><span>Solde TTC</span><strong id="rcSoldeTtc">0,00</strong></div>
                </div>

                <div class="rc-orders-head"><p>Commande à Payer</p></div>
                <div class="rc-orders-wrap">
                    <table class="rc-orders">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="rcSelectAll" title="Tout sélectionner"></th>
                                <th>N° BA</th>
                                <th>Date</th>
                                <th>Montant Bon</th>
                                <th>Montant Payé</th>
                                <th>Solde</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="rcOrders">
                            <tr><td colspan="7">Sélectionnez un client</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="rc-actions">
                    <button type="button" class="rc-btn ghost" id="rcClose">Annuler</button>
                    <button type="submit" class="rc-btn primary" id="rcSave">Valider</button>
                </div>
            </form>
        </div>
    </div>
</section>
<style>
    #panel-c-regl .rc-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-c-regl .rc-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .rc-add {
        display: inline-flex; align-items: center; gap: 0.4rem; height: 40px; padding: 0 1rem;
        border: none; border-rcdius: 10px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 0.85rem;
        background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff;
    }
    .rc-add svg { width: 16px; height: 16px; }
    .rc-filters {
        display: grid; grid-template-columns: 160px 180px 160px 38px; gap: 0.75rem;
        align-items: end; margin-bottom: 1rem;
    }
    .rc-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #e7d5d8;
    }
    .rc-filters select {
        width: 100%; height: 38px; box-sizing: border-box; border-rcdius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    .rc-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-rcdius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    .rc-refresh svg { width: 16px; height: 16px; }
    .rc-icon {
        display: inline-grid; place-items: center; width: 28px; height: 28px; padding: 0;
        border: none; border-rcdius: 8px; background: transparent; color: #64748b; cursor: pointer;
    }
    .rc-icon svg { width: 15px; height: 15px; }
    .rc-icon.del:hover { color: #dc2626; }
    .rc-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden .rc-window { left: 0; }
    .rc-window[hidden] { display: none !important; }
    .rc-sheet {
        width: min(1180px, calc(100vw - 2.5rem)); margin: auto; border-rcdius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #4a1824; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    .rc-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .rc-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .rc-x {
        width: 34px; height: 34px; border: none; border-rcdius: 8px; cursor: pointer;
        background: transparent; color: rgba(255,255,255,0.82); font-size: 1.35rem; line-height: 1;
    }
    .rc-form { padding: 1rem 1.15rem 0.4rem; }
    .rc-error { margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-rcdius: 10px; background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #7f1d1d; font-size: 0.82rem; }
    .rc-grid {
        display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 0.65rem; align-items: end;
    }
    .rc-span-2 { grid-column: span 2; }
    .rc-field { display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }
    .rc-field label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em; color: #94a3b8; }
    .rc-field input, .rc-field select {
        box-sizing: border-box; width: 100%; height: 38px; border-rcdius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.7rem;
    }
    .rc-form-cards { display: grid; grid-template-columns: repeat(2, minmax(0, 180px)); gap: 0.65rem; margin: 0.9rem 0 0.7rem; justify-content: end; }
    .rc-mini { border-rcdius: 12px; padding: 0.55rem 0.7rem; text-align: center; background: #1e293b; border: 1px solid #334155; }
    .rc-mini span { display: block; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; }
    .rc-mini strong { display: block; margin-top: 0.15rem; font-size: 0.95rem; color: #fff; }
    .rc-mini.solde strong { color: #f87171; }
    .rc-orders-head p {
        margin: 0 0 0.55rem; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase;
        color: #e7c4c8; text-decoration: underline; text-underline-offset: 3px;
    }
    .rc-orders-wrap { max-height: 280px; overflow: auto; border: 1px solid #334155; border-rcdius: 10px; }
    .rc-orders { width: 100%; border-collapse: collapse; min-width: 860px; }
    .rc-orders th {
        position: sticky; top: 0; background: #5c1220; color: #fff;
        font-size: 0.62rem; letter-spacing: 0.06em; text-transform: uppercase; padding: 0.5rem; text-align: center;
    }
    .rc-orders td { padding: 0.45rem; border-top: 1px solid #1e293b; text-align: center; color: #000; background: #fff; font-size: 0.78rem; }
    .rc-actions { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.95rem 0 1rem; }
    .rc-btn { height: 36px; padding: 0 1rem; border-rcdius: 999px; border: none; font-family: inherit; font-weight: 700; font-size: 0.78rem; cursor: pointer; }
    .rc-btn.ghost { background: #1e293b; color: #e2e8f0; border: 1px solid #475569; }
    .rc-btn.primary { background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff; }
    .rc-act {
        border: 1px solid #cbd5e1; border-rcdius: 999px; background: #f8fafc; color: #334155;
        font-size: 0.68rem; font-weight: 700; padding: 0.18rem 0.45rem; cursor: pointer; margin: 0 0.1rem;
    }
    .rc-act.is-on { background: #5c1220; color: #fff; border-color: #5c1220; }
    .rc-act.is-on[data-a="Payé"] { background: #15803d; border-color: #15803d; }
    .rc-act.is-on[data-a="Report"] { background: #d97706; border-color: #d97706; }
    .rc-act.is-on[data-a="Imp"] { background: #dc2626; border-color: #dc2626; }
    .rc-act.is-on[data-a="Dévalidé"] { background: #7c3aed; border-color: #7c3aed; }
    [data-theme="light"] .rc-filters label { color: #5c1220; }
    [data-theme="light"] .rc-refresh { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .rc-sheet { background: #fff; border-color: #e7c9cf; color: #1a1214; }
    [data-theme="light"] .rc-field label { color: #5c1220; }
    [data-theme="light"] .rc-mini { background: #fff; border-color: #e7c9cf; }
    [data-theme="light"] .rc-mini strong { color: #1a1214; }
    [data-theme="light"] .rc-mini.solde strong { color: #dc2626; }
    [data-theme="light"] .rc-btn.ghost { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .rc-orders-head p { color: #8f2436; }
    @media (max-width: 1100px) {
        .rc-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rc-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rc-window { left: 0; }
    }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-c-regl');
    const modal = document.getElementById('rcModal');
    const form = document.getElementById('rcForm');
    const body = document.getElementById('rcBody');
    const ordersBody = document.getElementById('rcOrders');
    if (!panel || !modal || !form || !body || !ordersBody) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const errorBox = document.getElementById('rcError');
    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    const actions = ['Inst', 'Payé', 'Report', 'Imp', 'Dévalidé'];
    let rows = [];
    let meta = { next_ref: 'RC-0001', date: '', clients: [] };
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
        const refSelect = document.getElementById('rcRefFilter');
        const amountSelect = document.getElementById('rcAmountFilter');
        const currentRef = refSelect.value;
        const currentAmount = amountSelect.value;
        refSelect.innerHTML = `<option value="">Tous</option>${refs.map((value) => `<option value="${esc(value)}">${esc(value)}</option>`).join('')}`;
        amountSelect.innerHTML = `<option value="">Tous</option>${amounts.map((value) => `<option value="${esc(value)}">${esc(money(value))}</option>`).join('')}`;
        refSelect.value = refs.includes(currentRef) ? currentRef : '';
        amountSelect.value = amounts.includes(currentAmount) ? currentAmount : '';
    }
    function filteredRows() {
        const month = document.getElementById('rcMonth').value;
        const ref = document.getElementById('rcRefFilter').value;
        const amount = document.getElementById('rcAmountFilter').value;
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
            <td>${esc(row.client)}</td>
            <td>${esc(row.reglement) || '—'}</td>
            <td>${esc(row.nom_tire) || '—'}</td>
            <td>${esc(row.numero) || '—'}</td>
            <td>${esc(row.banque) || '—'}</td>
            <td>${esc(row.date_decaissement_fr || formatDate(row.date_decaissement))}</td>
            <td>${esc(money(row.montant))}</td>
            <td>${esc(row.statut)}</td>
            <td>
                <button type="button" class="rc-icon" data-act="voir" data-id="${row.id}" title="Voir">${eye}</button>
                <button type="button" class="rc-icon" data-act="modifier" data-id="${row.id}" title="Modifier">${pencil}</button>
                <button type="button" class="rc-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer">${trash}</button>
            </td>
        </tr>`).join('');
    }
    function fillClients(selectedId) {
        const select = document.getElementById('rcClient');
        select.innerHTML = `<option value="">— Sélectionner —</option>${(meta.clients || []).map((item) =>
            `<option value="${esc(item.id)}"${String(item.id) === String(selectedId || '') ? ' selected' : ''}>${esc(item.name)}</option>`
        ).join('')}`;
    }
    function syncCashFields() {
        const cash = document.getElementById('rcType').value === 'Esp';
        ['rcNumero', 'rcBanque', 'rcTire'].forEach((id) => {
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
        const amount = parseNum(document.getElementById('rcMontant').value);
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
        document.getElementById('rcTotalTtc').textContent = money(total);
        document.getElementById('rcSoldeTtc').textContent = money(solde);
        if (!orders.length) {
            ordersBody.innerHTML = '<tr><td colspan="7">Aucun bon de livraison pour ce client</td></tr>';
            return;
        }
        ordersBody.innerHTML = orders.map((order) => {
            const preview = map[order.id] || {};
            const checked = selected[order.id] ? ' checked' : '';
            const action = orderActions[order.id] || order.payment_action || 'Inst';
            const buttons = actions.map((name) =>
                `<button type="button" class="rc-act${action === name ? ' is-on' : ''}" data-order="${order.id}" data-a="${esc(name)}">${esc(name)}</button>`
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
    async function loadOrders(clientId) {
        if (!clientId) {
            orders = [];
            selected = {};
            orderActions = {};
            renderOrders();
            return;
        }
        const response = await fetch(`/reglements-clients/orders?client_id=${encodeURIComponent(clientId)}`, { headers: { 'Accept': 'application/json' } });
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
        document.getElementById('rcDate').value = row?.payment_date || meta.date || todayInput();
        document.getElementById('rcRef').value = row?.reference || meta.next_ref || 'RC-0001';
        fillClients(row?.client_id || '');
        document.getElementById('rcType').value = row?.reglement || '';
        document.getElementById('rcNumero').value = row?.numero || '';
        document.getElementById('rcBanque').value = row?.banque || '';
        document.getElementById('rcTire').value = row?.nom_tire || '';
        document.getElementById('rcMontant').value = row ? moneyDot(row.montant) : '';
        document.getElementById('rcDecaiss').value = row?.date_decaissement || document.getElementById('rcDate').value;
        document.getElementById('rcRemarque').value = row?.remarque || '';
        document.getElementById('rcModalTitle').textContent = mode === 'view'
            ? `Règlement ${row?.reference || ''}`
            : (mode === 'edit' ? `Modifier ${row?.reference || ''}` : 'Nouveau Règlement Client');
        document.getElementById('rcSave').hidden = mode === 'view';
        document.getElementById('rcClose').textContent = mode === 'view' ? 'Fermer' : 'Annuler';
        syncCashFields();
        form.querySelectorAll('input, select, button').forEach((field) => {
            if (field.id === 'rcClose' || field.id === 'rcCloseX') return;
            if (field.id === 'rcRef') { field.disabled = true; return; }
            field.disabled = mode === 'view';
        });
        modal.hidden = false;
        if (row?.client_id) {
            loadOrders(row.client_id).then(() => {
                if (row.allocations?.length) {
                    row.allocations.forEach((allocation) => {
                        selected[allocation.delivery_order_id] = true;
                        orderActions[allocation.delivery_order_id] = allocation.action || 'Inst';
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
        const response = await fetch('/reglements-clients', { headers: { 'Accept': 'application/json' } });
        const payload = await response.json();
        if (!response.ok) throw new Error(payload.message || 'Chargement impossible.');
        rows = payload.data || [];
        meta = payload.meta || meta;
        fillLists();
        render();
    }
    document.getElementById('rcAdd').addEventListener('click', () => openModal(null, 'create'));
    document.getElementById('rcClose').addEventListener('click', closeModal);
    document.getElementById('rcCloseX').addEventListener('click', () => {
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
    document.getElementById('rcType').addEventListener('change', syncCashFields);
    document.getElementById('rcMontant').addEventListener('input', renderOrders);
    document.getElementById('rcClient').addEventListener('change', () => {
        loadOrders(document.getElementById('rcClient').value).catch((error) => showError(error.message));
    });
    document.getElementById('rcSelectAll').addEventListener('change', (event) => {
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
    ['rcMonth', 'rcRefFilter', 'rcAmountFilter'].forEach((id) => {
        document.getElementById(id).addEventListener('change', render);
    });
    document.getElementById('rcRefresh').addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="11">${esc(error.message)}</td></tr>`; });
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (mode === 'view') return;
        showError('');
        const clientId = document.getElementById('rcClient').value;
        const montant = parseNum(document.getElementById('rcMontant').value);
        const ids = selectedIds();
        if (!clientId) { showError('Sélectionnez un client.'); return; }
        if (montant <= 0) { showError('Saisissez un montant de règlement.'); return; }
        if (!ids.length) { showError('Cochez au moins un bon de livraison.'); return; }
        const map = previewMap();
        const allocations = ids.map((id) => ({
            delivery_order_id: id,
            amount: map[id]?.allocation || 0,
            action: orderActions[id] || 'Inst',
        }));
        const save = document.getElementById('rcSave');
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/reglements-clients/${editingId}` : '/reglements-clients', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    payment_date: document.getElementById('rcDate').value,
                    client_id: Number(clientId),
                    reglement: document.getElementById('rcType').value,
                    numero: document.getElementById('rcNumero').value,
                    banque: document.getElementById('rcBanque').value,
                    nom_tire: document.getElementById('rcTire').value,
                    montant,
                    date_decaissement: document.getElementById('rcDecaiss').value || null,
                    remarque: document.getElementById('rcRemarque').value,
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
            document.dispatchEvent(new CustomEvent('evopro-regl-clients-changed'));
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
        if (button.dataset.act === 'modifier') {
            const id = row.id;
            try { await load(); } catch (error) { showError(error.message); }
            const fresh = rows.find((item) => String(item.id) === String(id)) || row;
            openModal(fresh, 'edit');
        }
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer le règlement ${row.reference} ?`)) return;
            const response = await fetch(`/reglements-clients/${row.id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            if (!response.ok) { showError('Impossible de supprimer ce règlement.'); return; }
            await load();
            document.dispatchEvent(new CustomEvent('evopro-regl-clients-changed'));
            document.dispatchEvent(new CustomEvent('evopro-bl-changed'));
        }
    });
    document.querySelector('[data-panel="f-regl"]')?.addEventListener('click', () => {
        load().catch((error) => { body.innerHTML = `<tr><td colspan="11">${esc(error.message)}</td></tr>`; });
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="11">${esc(error.message)}</td></tr>`; });
})();
</script>
