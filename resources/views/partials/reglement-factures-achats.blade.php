<section class="panel" id="panel-fa-regl-achat">
    <div class="content-head fa-head">
        <div>
            <h1>Régl Fact/Achats</h1>
            <p class="fa-sub">Règlement des factures fournisseurs</p>
        </div>
    </div>

    <div class="fa-filters">
        <div class="fa-filter-side">
            <label>N° Facture<input type="search" id="fraInvoiceNo" autocomplete="off"></label>
            <label>Nom fournisseur
                <select id="fraSupplierName" class="fa-name" data-keep-native="1">
                    <option value="">Tous</option>
                </select>
            </label>
            <label>Montant<input type="search" id="fraAmount" inputmode="decimal" autocomplete="off"></label>
            <label>Réf Règlement<input type="search" id="fraPayRef" autocomplete="off"></label>
        </div>
        <div class="fa-stats">
            <div class="fa-stat regl"><span>Total Règlements</span><strong id="fraSum">0,00</strong></div>
        </div>
    </div>

    <div class="fa-card fr-card">
        <div class="fa-list-bar">
            <h3>Liste des règlements</h3>
            <button type="button" id="fraRefresh" title="Actualiser">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.2-5.8M21 4v5h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        <div class="fa-scroll">
            <table class="fa-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>N° Règlement</th>
                        <th>N° Facture</th>
                        <th>Fournisseur</th>
                        <th>Mode</th>
                        <th>Référence</th>
                        <th>Montant</th>
                        <th>Solde Facture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="fraBody">
                    <tr><td colspan="9">Aucun règlement enregistré</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fa-window" id="fraModal" hidden>
        <div class="fa-sheet" role="dialog" aria-modal="true" aria-labelledby="fraModalTitle">
            <div class="fa-sheet-head">
                <h3 id="fraModalTitle">Règlement facture achat</h3>
                <button type="button" class="fa-x" id="fraCloseX" title="Fermer">×</button>
            </div>
            <form id="fraForm" class="fa-form" autocomplete="off">
                <div class="fa-error" id="fraError" hidden></div>
                <div class="fra-head">
                    <div class="fa-field">
                        <label for="fraDate">Date</label>
                        <input id="fraDate" type="date" required>
                    </div>
                    <div class="fa-field">
                        <label for="fraRef">N° Rég</label>
                        <input id="fraRef" readonly>
                    </div>
                    <div class="fa-field">
                        <label for="fraSupplier">Nom fournisseur</label>
                        <input id="fraSupplier" readonly>
                    </div>
                    <div class="fa-field">
                        <label for="fraInvoice">Mnt Facture</label>
                        <input id="fraInvoice" readonly>
                    </div>
                </div>
                <div class="fa-lines-wrap">
                    <table class="fa-lines fra-lines">
                        <colgroup>
                            <col class="c-amt">
                            <col class="c-type">
                            <col class="c-ref">
                            <col class="c-bnq">
                            <col class="c-tire">
                            <col class="c-solde">
                            <col class="c-x">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Mnt Rég</th>
                                <th>Type</th>
                                <th>Réf</th>
                                <th>Bnq</th>
                                <th>Nom Tiré</th>
                                <th>Solde</th>
                                <th><button type="button" class="fa-add-line" id="fraAdd" title="Ajouter une ligne">+</button></th>
                            </tr>
                        </thead>
                        <tbody id="fraLines"></tbody>
                    </table>
                </div>
                <div class="fa-actions">
                    <button type="button" class="fa-btn ghost" id="fraClose">Fermer</button>
                    <button type="submit" class="fa-btn primary" id="fraSave">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fa-window" id="fraView" hidden>
        <div class="fa-sheet fa-view" role="dialog" aria-modal="true">
            <div class="fa-sheet-head">
                <div>
                    <p class="fa-kicker">Règlement facture achat</p>
                    <h3 id="fraViewTitle">—</h3>
                </div>
                <button type="button" class="fa-x" id="fraViewClose" title="Fermer">×</button>
            </div>
            <div class="fa-view-body" id="fraViewBody"></div>
            <div class="fa-actions">
                <button type="button" class="fa-btn ghost" id="fraViewOk">Fermer</button>
            </div>
        </div>
    </div>
</section>
<style>
    #panel-fa-regl-achat .fa-sub { text-transform: none; }
    #panel-fa-regl-achat .fa-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    #fraModal .fa-sheet { width: min(1280px, 100%); margin-top: 0; }
    #fraView .fa-sheet { width: min(720px, 100%); }
    .fra-head { display: grid; grid-template-columns: 150px 120px minmax(0, 1.4fr) 150px; gap: 0.75rem; }
    .fra-lines .c-amt { width: 110px; }
    .fra-lines .c-type { width: 92px; }
    .fra-lines .c-ref { width: 120px; }
    .fra-lines .c-bnq { width: 120px; }
    .fra-lines .c-tire { width: 160px; }
    .fra-lines .c-solde { width: 110px; }
    .fra-lines .c-x { width: 34px; }
    .fra-lines select {
        width: 100%; height: 32px; box-sizing: border-box; border-radius: 6px; border: 1px solid #475569;
        background: #161214; color: #fff; text-align: center; font-size: 0.75rem; font-family: inherit; border-color: #5c2430;
    }
    .fra-lines input:disabled, .fra-lines select:disabled {
        background: #100e10; color: #8a7076; border-color: #3d2430; cursor: not-allowed;
    }
    #fraAdd { font-size: 1.05rem; line-height: 1; }
    #panel-fa-regl-achat .fa-filters input { width: 150px; }
    #panel-fa-regl-achat .fa-filters select.fa-name { width: 240px; }
    .fa-stat.regl,
    [data-theme="light"] .fa-stat.regl { background: linear-gradient(135deg, #f97316, #c2410c); color: #fff; }
    #fraModal.fa-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden #fraModal.fa-window { left: 0; }
    #fraModal.fa-window[hidden] { display: none !important; }
    #fraModal .fa-sheet { margin: auto; }
    @media (max-width: 900px) {
        #fraModal.fa-window { left: 0; }
    }
</style>
<script>
(function () {
    const modal = document.getElementById('fraModal');
    const view = document.getElementById('fraView');
    const form = document.getElementById('fraForm');
    const body = document.getElementById('fraBody');
    const linesBody = document.getElementById('fraLines');
    if (!modal || !view || !form || !body || !linesBody) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const errorBox = document.getElementById('fraError');
    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    let meta = { next_ref: 'RA-0001', date: '', invoices: [] };
    let rows = [];
    let invoice = null;
    let editingId = null;

    function moneyDot(value) {
        const n = Number(String(value ?? '').replace(/\s/g, '').replace(',', '.')) || 0;
        return n.toFixed(2);
    }
    function moneyFr(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function parseNum(value) {
        const n = Number(String(value ?? '').replace(/\s/g, '').replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    }
    function showError(message) {
        errorBox.hidden = !message;
        errorBox.textContent = message || '';
    }
    function lineRow(line) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input data-f="amount" inputmode="decimal" placeholder="0.00" required></td>
            <td><select data-f="type" data-keep-native="1" required>
                <option value="" disabled selected>Type</option>
                <option value="Esp">Esp</option>
                <option value="Chq">Chq</option>
                <option value="Eff">Eff</option>
                <option value="Vir">Vir</option>
                <option value="Vers">Vers</option>
            </select></td>
            <td><input data-f="ref" maxlength="64"></td>
            <td><input data-f="bnq" maxlength="64"></td>
            <td><input data-f="tire" maxlength="255"></td>
            <td><input data-f="solde" readonly tabindex="-1" value="0.00"></td>
            <td><button type="button" class="fa-line-x" data-remove title="Retirer" aria-label="Retirer">${trash}</button></td>`;
        if (line) {
            tr.querySelector('[data-f="amount"]').value = moneyDot(line.amount);
            tr.querySelector('[data-f="type"]').value = line.method || '';
            tr.querySelector('[data-f="ref"]').value = line.line_reference || '';
            tr.querySelector('[data-f="bnq"]').value = line.bank || '';
            tr.querySelector('[data-f="tire"]').value = line.nom_tire || '';
            applyType(tr);
        }
        return tr;
    }
    function applyType(row) {
        const cash = row.querySelector('[data-f="type"]').value === 'Esp';
        ['ref', 'bnq', 'tire'].forEach((name) => {
            const field = row.querySelector(`[data-f="${name}"]`);
            field.disabled = cash;
            if (cash) field.value = '';
        });
    }
    function paidBeforeForm() {
        let paid = Number(invoice?.paid) || 0;
        if (editingId) {
            paid -= rows.filter((item) => String(item.id) === String(editingId)).reduce((sum, item) => sum + Number(item.amount), 0);
        }
        return Math.max(0, Math.round(paid * 100) / 100);
    }
    function recalc() {
        const total = Number(invoice?.total_ttc) || 0;
        let used = paidBeforeForm();
        linesBody.querySelectorAll('tr').forEach((row) => {
            used += parseNum(row.querySelector('[data-f="amount"]').value);
            row.querySelector('[data-f="solde"]').value = moneyDot(total - used);
        });
    }
    function fillSuppliers() {
        const select = document.getElementById('fraSupplierName');
        const current = select.value;
        const seen = new Set();
        const names = [];
        rows.forEach((row) => {
            const name = String(row.fournisseur || '').trim();
            const key = name.toLowerCase();
            if (!name || name === '—' || seen.has(key)) return;
            seen.add(key);
            names.push(name);
        });
        names.sort((a, b) => a.localeCompare(b, 'fr'));
        select.innerHTML = '<option value="">Tous</option>' + names.map((name) => `<option value="${esc(name)}">${esc(name)}</option>`).join('');
        select.value = names.some((name) => name.toLowerCase() === current.toLowerCase()) ? current : '';
    }
    function amountMatches(amount, query) {
        const q = String(query || '').replace(/\s/g, '').replace(',', '.');
        if (!q) return true;
        return String(amount).includes(q) || moneyDot(amount).includes(q);
    }
    function filteredRows() {
        const invoiceNo = document.getElementById('fraInvoiceNo').value.trim().toLowerCase();
        const client = document.getElementById('fraSupplierName').value.trim().toLowerCase();
        const amount = document.getElementById('fraAmount').value;
        const payRef = document.getElementById('fraPayRef').value.trim().toLowerCase();
        return rows.filter((row) => {
            if (invoiceNo && !String(row.invoice_reference || '').toLowerCase().includes(invoiceNo)) return false;
            if (client && String(row.fournisseur || '').trim().toLowerCase() !== client) return false;
            if (!amountMatches(row.amount, amount)) return false;
            if (payRef && !String(row.reference || '').toLowerCase().includes(payRef)) return false;
            return true;
        });
    }
    function render(list) {
        const total = list.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
        document.getElementById('fraSum').textContent = moneyFr(total);
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="9">${rows.length ? 'Aucun règlement pour cette recherche.' : 'Aucun règlement enregistré'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(row.payment_date)}</td>
            <td class="fa-ref">${esc(row.reference)}</td>
            <td>${esc(row.invoice_reference)}</td>
            <td>${esc(row.fournisseur)}</td>
            <td>${esc(row.method)}</td>
            <td>${esc(row.line_reference) || '—'}</td>
            <td>${esc(moneyFr(row.amount))}</td>
            <td class="fa-ttc">${esc(moneyFr(row.balance))}</td>
            <td><div class="fa-row-actions">
                <button type="button" class="fa-icon see" data-act="voir" data-id="${row.id}" title="Voir">${eye}</button>
                <button type="button" class="fa-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier">${pencil}</button>
                <button type="button" class="fa-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer">${trash}</button>
            </div></td>
        </tr>`).join('');
    }
    async function load() {
        const response = await fetch('/reglements-factures-achats', { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Chargement impossible.');
        const payload = await response.json();
        meta = payload.meta || meta;
        rows = payload.data || [];
        fillSuppliers();
        render(filteredRows());
    }
    function showSheet() {
        modal.hidden = false;
    }
    function paymentGroup(id) {
        return rows.filter((item) => String(item.id) === String(id)).sort((a, b) => Number(a.line_id) - Number(b.line_id));
    }
    function fillHead(found, reference, date) {
        document.getElementById('fraDate').value = date || '';
        document.getElementById('fraRef').value = reference || '';
        document.getElementById('fraSupplier').value = found.fournisseur && found.fournisseur !== '—' ? found.fournisseur : '';
        document.getElementById('fraInvoice').value = moneyDot(found.total_ttc);
    }
    function openFor(found) {
        editingId = null;
        invoice = found;
        document.getElementById('fraModalTitle').textContent = 'Règlement facture achat';
        fillHead(found, meta.next_ref || 'RA-0001', meta.date || '');
        linesBody.innerHTML = '';
        linesBody.appendChild(lineRow());
        showError(Number(found.balance) <= 0 ? 'Cette facture est déjà soldée.' : '');
        recalc();
        view.hidden = true;
        modal.hidden = false;
        showSheet();
    }
    function openEdit(group) {
        const head = group[0];
        editingId = head.id;
        invoice = (meta.invoices || []).find((item) => String(item.id) === String(head.invoice_id)) || {
            id: head.invoice_id,
            fournisseur: head.fournisseur,
            total_ttc: head.total_ttc,
            paid: 0,
            balance: head.total_ttc,
        };
        document.getElementById('fraModalTitle').textContent = `Modifier ${head.reference}`;
        fillHead(invoice, head.reference, head.payment_date_raw || '');
        linesBody.innerHTML = '';
        group.forEach((line) => linesBody.appendChild(lineRow(line)));
        showError('');
        recalc();
        view.hidden = true;
        modal.hidden = false;
        showSheet();
    }
    function openView(group) {
        const head = group[0];
        document.getElementById('fraViewTitle').textContent = head.reference || '—';
        const fields = [
            ['N° Rég', head.reference],
            ['Date', head.payment_date],
            ['N° Facture', head.invoice_reference],
            ['Fournisseur', head.fournisseur],
            ['Mnt Facture', moneyFr(head.total_ttc)],
        ];
        const lines = group.map((line) => `<div class="fa-view-line"><strong>${esc(moneyFr(line.amount))} — ${esc(line.method)}</strong><div>Réf ${esc(line.line_reference || '—')} · Bnq ${esc(line.bank || '—')} · ${esc(line.nom_tire || '—')}</div></div>`).join('');
        document.getElementById('fraViewBody').innerHTML = fields.map(([label, value]) => `<div class="fa-view-row"><span>${esc(label)}</span><strong>${esc(value || '—')}</strong></div>`).join('') + lines;
        modal.hidden = true;
        view.hidden = false;
    }
    function closeModal() { modal.hidden = true; invoice = null; editingId = null; }
    function closeView() { view.hidden = true; }
    document.getElementById('fraClose').addEventListener('click', closeModal);
    document.getElementById('fraCloseX').addEventListener('click', closeModal);
    document.getElementById('fraRefresh').addEventListener('click', () => load().catch((error) => showError(error.message)));
    ['fraInvoiceNo', 'fraAmount', 'fraPayRef'].forEach((id) => {
        document.getElementById(id).addEventListener('input', () => render(filteredRows()));
    });
    document.getElementById('fraSupplierName').addEventListener('change', () => render(filteredRows()));
    document.getElementById('fraViewClose').addEventListener('click', closeView);
    document.getElementById('fraViewOk').addEventListener('click', closeView);
    view.addEventListener('click', (event) => { if (event.target === view) closeView(); });
    document.querySelector('[data-panel="fa-regl-achat"]')?.addEventListener('click', () => { closeModal(); closeView(); });
    document.getElementById('fraAdd').addEventListener('click', () => { linesBody.appendChild(lineRow()); recalc(); });
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
    linesBody.addEventListener('input', recalc);
    linesBody.addEventListener('change', (event) => {
        const row = event.target.closest('tr');
        if (row && event.target.matches('[data-f="type"]')) applyType(row);
        recalc();
    });
    linesBody.addEventListener('focusout', (event) => {
        const amount = event.target.closest('[data-f="amount"]');
        if (!amount || !String(amount.value).trim()) return;
        amount.value = moneyDot(amount.value);
        recalc();
    });
    linesBody.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove]');
        if (!button || linesBody.querySelectorAll('tr').length < 2) return;
        button.closest('tr').remove();
        recalc();
    });
    body.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-act]');
        if (!button) return;
        const group = paymentGroup(button.dataset.id);
        const row = group[0];
        if (!row) return;
        if (button.dataset.act === 'voir') openView(group);
        if (button.dataset.act === 'modifier') {
            const id = row.id;
            try { await load(); } catch (error) { showError(error.message); }
            const fresh = paymentGroup(String(id));
            if (!fresh.length) { showError('Règlement introuvable.'); return; }
            openEdit(fresh);
        }
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer le règlement ${row.reference} ?`)) return;
            const response = await fetch(`/reglements-factures-achats/${row.id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            if (!response.ok) { showError('Impossible de supprimer ce règlement.'); return; }
            await load();
        }
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError('');
        if (!invoice) return;
        const save = document.getElementById('fraSave');
        const lines = [...linesBody.querySelectorAll('tr')].map((row) => {
            const method = row.querySelector('[data-f="type"]').value;
            const cash = method === 'Esp';
            return {
                amount: parseNum(row.querySelector('[data-f="amount"]').value),
                method,
                reference: cash ? '' : row.querySelector('[data-f="ref"]').value.trim(),
                bank: cash ? '' : row.querySelector('[data-f="bnq"]').value.trim(),
                nom_tire: cash ? '' : row.querySelector('[data-f="tire"]').value.trim(),
            };
        });
        if (!lines.length || lines.some((line) => line.amount <= 0 || !line.method)) {
            showError('Chaque ligne doit avoir un montant et un type.');
            return;
        }
        const incoming = lines.reduce((sum, line) => sum + line.amount, 0);
        const room = Math.round(((Number(invoice.total_ttc) || 0) - paidBeforeForm()) * 100) / 100;
        if (incoming - room > 0.009) {
            showError('Le montant réglé dépasse le solde de la facture.');
            return;
        }
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/reglements-factures-achats/${editingId}` : '/reglements-factures-achats', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    supplier_invoice_id: invoice.id,
                    payment_date: document.getElementById('fraDate').value,
                    lines,
                }),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const first = data.errors ? Object.values(data.errors)[0]?.[0] : data.message;
                throw new Error(first || 'Enregistrement impossible.');
            }
            closeModal();
            await load();
        } catch (error) {
            showError(error.message);
        } finally {
            save.disabled = false;
        }
    });
    document.addEventListener('evopro-pay-supplier-invoice', async (event) => {
        const source = event.detail || {};
        try { await load(); } catch (error) { showError(error.message); }
        const found = (meta.invoices || []).find((item) => String(item.id) === String(source.id)) || {
            id: source.id,
            reference: source.reference,
            fournisseur: source.fournisseur,
            total_ttc: source.total_ttc,
            paid: 0,
            balance: source.total_ttc,
        };
        openFor(found);
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="9">${esc(error.message)}</td></tr>`; });
})();
</script>
