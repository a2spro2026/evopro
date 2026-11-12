<section class="panel" id="panel-f-fiche">
    <div class="content-head">
        <h1>Fiche Fournisseur</h1>
    </div>

    <form class="ff-card is-locked" id="ficheFournisseurForm" autocomplete="off">
        <div class="ff-error" id="ffError" hidden></div>
        <div class="ff-grid">
            <div>
                <label for="ffDate">Date</label>
                <input id="ffDate" readonly>
            </div>
            <div>
                <label for="ffCode">ID</label>
                <input id="ffCode" readonly>
            </div>
            <div>
                <label for="ffName">Nom Fournisseur</label>
                <input id="ffName" required maxlength="255" placeholder="Raison sociale">
            </div>
            <div>
                <label for="ffPhone">N° Téléphone</label>
                <input id="ffPhone" type="tel" maxlength="50" placeholder="06 XX XX XX XX">
            </div>
            <div>
                <label for="ffCity">Ville</label>
                <input id="ffCity" maxlength="255" placeholder="Ville">
            </div>
            <div class="ff-narrow">
                <label for="ffReglement">Règlement</label>
                <select id="ffReglement">
                    <option value="">—</option>
                    <option>Esp</option>
                    <option>Chq</option>
                    <option>Eff</option>
                    <option>Vir</option>
                    <option>Vers</option>
                </select>
            </div>
            <div class="ff-narrow">
                <label for="ffEcheance">Échéance</label>
                <select id="ffEcheance">
                    <option value="">—</option>
                    <option>45 Jrs</option>
                    <option>60 Jrs</option>
                    <option>90 Jrs</option>
                    <option>120 Jrs</option>
                </select>
            </div>
            <div class="ff-solde">
                <label for="ffSolde">Solde Initial</label>
                <input id="ffSolde" inputmode="decimal" placeholder="0.00">
            </div>
            <div class="ff-inline-actions">
                <button type="submit" class="ff-btn primary" id="ffSave" title="Enregistrer" aria-label="Enregistrer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.2 12.2 2.6 2.6 5-5.2"/></svg>
                </button>
                <button type="button" class="ff-btn muted" id="ffReset" title="Nouveau" aria-label="Nouveau">
                    <svg class="ff-ico-new" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>
                    <svg class="ff-ico-cancel" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                <button type="button" class="ff-btn secondary" id="ffRefresh" title="Actualiser" aria-label="Actualiser">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
                </button>
            </div>
            <div class="ff-total" id="ffTotal">
                <div>
                    <small>Total Soldes Fournisseurs</small>
                    <strong id="ffTotalValue">0.00</strong>
                </div>
            </div>
        </div>
    </form>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Liste des fournisseurs</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Fournisseur</th>
                        <th>Contact</th>
                        <th>Ville</th>
                        <th>Règlement</th>
                        <th>Échéance</th>
                        <th>Solde Initial</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ffBody">
                    <tr><td colspan="8">Aucune donnée pour le moment.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ff-modal" id="ffModal" hidden>
        <div class="ff-modal-card" role="dialog" aria-modal="true" aria-labelledby="ffModalTitle">
            <div class="ff-modal-head">
                <div>
                    <small style="letter-spacing:.08em;text-transform:uppercase;font-size:.65rem;opacity:.8">Fiche Fournisseur</small>
                    <h3 id="ffModalTitle" style="margin:0">—</h3>
                </div>
                <button type="button" class="ff-btn muted" id="ffModalClose">Fermer</button>
            </div>
            <div class="ff-modal-body" id="ffModalBody"></div>
        </div>
    </div>
</section>
<script>
(function () {
    const form = document.getElementById('ficheFournisseurForm');
    if (!form) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const fields = {
        date: document.getElementById('ffDate'),
        code: document.getElementById('ffCode'),
        name: document.getElementById('ffName'),
        phone: document.getElementById('ffPhone'),
        city: document.getElementById('ffCity'),
        reglement: document.getElementById('ffReglement'),
        echeance: document.getElementById('ffEcheance'),
        solde: document.getElementById('ffSolde'),
    };
    const errorBox = document.getElementById('ffError');
    const body = document.getElementById('ffBody');
    const saveBtn = document.getElementById('ffSave');
    const totalBox = document.getElementById('ffTotal');
    const totalValue = document.getElementById('ffTotalValue');
    const modal = document.getElementById('ffModal');
    const modalBody = document.getElementById('ffModalBody');
    const modalTitle = document.getElementById('ffModalTitle');
    let rows = [];
    let editingId = null;
    let meta = { next_id: 'CF-0001', date: '', total_soldes: '0.00' };

    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const printer = '<svg viewBox="0 0 24 24"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5h20v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const file = '<svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M14 3v5h5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

    function money(value) {
        const n = Number(String(value ?? '').replace(',', '.')) || 0;
        return n.toFixed(2);
    }

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }

    function showError(message) {
        errorBox.hidden = !message;
        errorBox.textContent = message || '';
    }

    function paintSolde() {
        const due = Number(money(fields.solde.value)) !== 0;
        fields.solde.classList.toggle('is-solde', due);
    }

    function setFormLocked(locked) {
        form.classList.toggle('is-locked', locked);
        ['name', 'phone', 'city', 'solde'].forEach((key) => {
            fields[key].readOnly = locked;
        });
        fields.reglement.disabled = locked;
        fields.echeance.disabled = locked;
        saveBtn.disabled = locked;
    }

    function syncResetIcon(isEdit) {
        const resetBtn = document.getElementById('ffReset');
        resetBtn.title = isEdit ? 'Annuler' : 'Nouveau';
        resetBtn.setAttribute('aria-label', resetBtn.title);
        resetBtn.querySelector('.ff-ico-new').hidden = isEdit;
        resetBtn.querySelector('.ff-ico-cancel').hidden = !isEdit;
    }

    function fillForm(row, unlock = false) {
        editingId = row ? row.id : null;
        fields.date.value = row?.created_at || meta.date || '';
        fields.code.value = row?.code || meta.next_id || '';
        fields.name.value = row?.name || '';
        fields.phone.value = row?.phone || '';
        fields.city.value = row?.city || '';
        fields.reglement.value = row?.reglement || '';
        fields.echeance.value = row?.echeance || '';
        fields.solde.value = row ? money(row.initial_balance) : '';
        saveBtn.title = editingId ? 'Mettre à jour' : 'Enregistrer';
        saveBtn.setAttribute('aria-label', saveBtn.title);
        const active = unlock || Boolean(editingId);
        syncResetIcon(active);
        setFormLocked(!active);
        paintSolde();
        showError('');
        if (active) fields.name.focus();
    }

    function render() {
        const total = Number(meta.total_soldes || 0);
        totalValue.textContent = money(total);
        totalBox.classList.toggle('is-due', total !== 0);
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="8">Aucun fournisseur enregistré</td></tr>';
            return;
        }
        body.innerHTML = rows.map((row) => {
            const due = Number(money(row.initial_balance)) !== 0;
            return `<tr>
                <td>${esc(row.code)}</td>
                <td>${esc(row.name) || '—'}</td>
                <td>${esc(row.contact) || '—'}</td>
                <td>${esc(row.city) || '—'}</td>
                <td>${esc(row.reglement) || '—'}</td>
                <td>${esc(row.echeance) || '—'}</td>
                <td style="${due ? 'color:#dc2626;font-weight:700' : ''}">${money(row.initial_balance)}</td>
                <td>
                    <div class="ff-actions">
                        <button type="button" class="ff-icon see" data-act="voir" data-id="${row.id}" title="Voir" aria-label="Voir">${eye}</button>
                        <button type="button" class="ff-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier" aria-label="Modifier">${pencil}</button>
                        <button type="button" class="ff-icon print" data-act="imprimer" data-id="${row.id}" title="Imprimer" aria-label="Imprimer">${printer}</button>
                        <button type="button" class="ff-icon pdf" data-act="pdf" data-id="${row.id}" title="PDF" aria-label="PDF">${file}</button>
                        <button type="button" class="ff-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer" aria-label="Supprimer">${trash}</button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    async function load() {
        const response = await fetch('/fournisseurs', { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Chargement impossible.');
        const payload = await response.json();
        rows = payload.data || [];
        meta = payload.meta || meta;
        if (!editingId) fillForm(null, false);
        render();
    }

    function payload() {
        return {
            name: fields.name.value.trim(),
            phone: fields.phone.value.trim(),
            city: fields.city.value.trim(),
            reglement: fields.reglement.value,
            echeance: fields.echeance.value,
            initial_balance: fields.solde.value === '' ? 0 : Number(String(fields.solde.value).replace(',', '.')),
        };
    }

    function printRow(row) {
        const win = window.open('', '_blank', 'width=800,height=600');
        if (!win) return;
        const lines = [
            ['ID', row.code], ['Nom Fournisseur', row.name], ['Contact', row.contact],
            ['Ville', row.city], ['Règlement', row.reglement], ['Échéance', row.echeance],
            ['Solde Initial', money(row.initial_balance)], ['Date création', row.created_at],
        ].map(([k, v]) => `<tr><th>${k}</th><td>${v || '—'}</td></tr>`).join('');
        win.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>Fiche ${row.code}</title>
            <style>body{font-family:Arial,sans-serif;padding:32px;color:#1e293b}h1{color:#1e3a5f}table{width:100%;border-collapse:collapse}th,td{border:1px solid #e2e8f0;padding:10px;text-align:left}th{width:180px;background:#f8fafc}</style>
            </head><body><h1>Fiche Fournisseur</h1><table>${lines}</table></body></html>`);
        win.document.close();
        win.focus();
        setTimeout(() => win.print(), 300);
    }

    function openView(row) {
        modalTitle.textContent = row.code;
        const pairs = [
            ['Nom Fournisseur', row.name], ['Contact', row.contact],
            ['Ville', row.city], ['Règlement', row.reglement], ['Échéance', row.echeance],
            ['Solde Initial', money(row.initial_balance)], ['Date', row.created_at],
        ];
        modalBody.innerHTML = pairs.map(([k, v]) => `<div class="ff-modal-row"><span>${esc(k)}</span><strong>${esc(v) || '—'}</strong></div>`).join('');
        modal.hidden = false;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError('');
        saveBtn.disabled = true;
        try {
            const response = await fetch(editingId ? `/fournisseurs/${editingId}` : '/fournisseurs', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(payload()),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const first = data.errors ? Object.values(data.errors)[0]?.[0] : data.message;
                throw new Error(first || 'Enregistrement impossible.');
            }
            editingId = null;
            await load();
            fillForm(null, false);
        } catch (error) {
            showError(error.message);
            saveBtn.title = editingId ? 'Mettre à jour' : 'Enregistrer';
            saveBtn.setAttribute('aria-label', saveBtn.title);
            setFormLocked(false);
        } finally {
            if (!form.classList.contains('is-locked')) saveBtn.disabled = false;
        }
    });

    document.getElementById('ffReset').addEventListener('click', () => {
        if (!form.classList.contains('is-locked') || editingId) {
            editingId = null;
            fillForm(null, false);
            return;
        }
        fillForm(null, true);
    });
    document.getElementById('ffRefresh').addEventListener('click', () => load().catch((error) => showError(error.message)));
    fields.solde.addEventListener('input', paintSolde);
    document.getElementById('ffModalClose').addEventListener('click', () => { modal.hidden = true; });
    modal.addEventListener('click', (event) => { if (event.target === modal) modal.hidden = true; });

    body.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-act]');
        if (!button) return;
        const row = rows.find((item) => String(item.id) === button.dataset.id);
        if (!row) return;
        if (button.dataset.act === 'voir') openView(row);
        if (button.dataset.act === 'modifier') {
            const id = row.id;
            try { await load(); } catch (error) { showError(error.message); }
            const fresh = rows.find((item) => String(item.id) === String(id)) || row;
            fillForm(fresh, true);
        }
        if (button.dataset.act === 'imprimer' || button.dataset.act === 'pdf') printRow(row);
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer le fournisseur « ${row.name} » ?`)) return;
            const response = await fetch(`/fournisseurs/${row.id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            if (!response.ok) {
                showError('Suppression impossible.');
                return;
            }
            if (editingId === row.id) editingId = null;
            await load();
            fillForm(null, false);
        }
    });

    setFormLocked(true);
    load().catch((error) => showError(error.message));
})();
</script>
