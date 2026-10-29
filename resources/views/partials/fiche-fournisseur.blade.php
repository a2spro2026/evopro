<section class="panel" id="panel-f-fiche">
    <div class="content-head">
        <h1>Fiche Fournisseur</h1>
    </div>

    <form class="ff-card" id="ficheFournisseurForm" autocomplete="off">
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
                <label for="ffEmail">E-mail</label>
                <input id="ffEmail" type="email" maxlength="255" placeholder="contact@...">
            </div>
            <div>
                <label for="ffAddress">Adresse</label>
                <input id="ffAddress" maxlength="500" placeholder="Adresse">
            </div>
            <div>
                <label for="ffCity">Ville</label>
                <input id="ffCity" maxlength="255" placeholder="Ville">
            </div>
            <div>
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
            <div>
                <label for="ffEcheance">Échéance</label>
                <select id="ffEcheance">
                    <option value="">—</option>
                    <option>45 Jrs</option>
                    <option>60 Jrs</option>
                    <option>90 Jrs</option>
                    <option>120 Jrs</option>
                </select>
            </div>
            <div>
                <label for="ffSolde">Solde Initial</label>
                <input id="ffSolde" inputmode="decimal" placeholder="0.00">
            </div>
        </div>
        <div class="ff-bar">
            <button type="submit" class="ff-btn primary" id="ffSave">Enregistrer</button>
            <button type="button" class="ff-btn muted" id="ffReset">Nouveau</button>
            <button type="button" class="ff-btn secondary" id="ffRefresh">Actualiser</button>
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
                        <th>Adresse</th>
                        <th>Ville</th>
                        <th>Règlement</th>
                        <th>Échéance</th>
                        <th>Solde Initial</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ffBody">
                    <tr><td colspan="9">Aucune donnée pour le moment.</td></tr>
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
        email: document.getElementById('ffEmail'),
        address: document.getElementById('ffAddress'),
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

    function fillForm(row) {
        editingId = row ? row.id : null;
        fields.date.value = row?.created_at || meta.date || '';
        fields.code.value = row?.code || meta.next_id || '';
        fields.name.value = row?.name || '';
        fields.phone.value = row?.phone || '';
        fields.email.value = row?.email || '';
        fields.address.value = row?.address || '';
        fields.city.value = row?.city || '';
        fields.reglement.value = row?.reglement || '';
        fields.echeance.value = row?.echeance || '';
        fields.solde.value = row ? money(row.initial_balance) : '';
        saveBtn.textContent = editingId ? 'Mettre à jour' : 'Enregistrer';
        document.getElementById('ffReset').textContent = editingId ? 'Annuler' : 'Nouveau';
        paintSolde();
        showError('');
    }

    function render() {
        const total = Number(meta.total_soldes || 0);
        totalValue.textContent = money(total);
        totalBox.classList.toggle('is-due', total !== 0);
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="9">Aucun fournisseur enregistré</td></tr>';
            return;
        }
        body.innerHTML = rows.map((row) => {
            const due = Number(money(row.initial_balance)) !== 0;
            return `<tr>
                <td>${esc(row.code)}</td>
                <td>${esc(row.name) || '—'}</td>
                <td>${esc(row.contact) || '—'}</td>
                <td>${esc(row.address) || '—'}</td>
                <td>${esc(row.city) || '—'}</td>
                <td>${esc(row.reglement) || '—'}</td>
                <td>${esc(row.echeance) || '—'}</td>
                <td style="${due ? 'color:#dc2626;font-weight:700' : ''}">${money(row.initial_balance)}</td>
                <td>
                    <button type="button" class="ff-icon" data-act="voir" data-id="${row.id}" title="Voir">Voir</button>
                    <button type="button" class="ff-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier">Modifier</button>
                    <button type="button" class="ff-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer">Supprimer</button>
                    <button type="button" class="ff-icon print" data-act="imprimer" data-id="${row.id}" title="Imprimer">Imprimer</button>
                    <button type="button" class="ff-icon pdf" data-act="pdf" data-id="${row.id}" title="PDF">PDF</button>
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
        if (!editingId) fillForm(null);
        render();
    }

    function payload() {
        return {
            name: fields.name.value.trim(),
            phone: fields.phone.value.trim(),
            email: fields.email.value.trim(),
            address: fields.address.value.trim(),
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
            ['ID', row.code], ['Nom Fournisseur', row.name], ['Contact', row.contact], ['E-mail', row.email],
            ['Adresse', row.address], ['Ville', row.city], ['Règlement', row.reglement], ['Échéance', row.echeance],
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
            ['Nom Fournisseur', row.name], ['Contact', row.contact], ['E-mail', row.email], ['Adresse', row.address],
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
        saveBtn.textContent = 'Enregistrement...';
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
            fillForm(null);
        } catch (error) {
            showError(error.message);
            saveBtn.textContent = editingId ? 'Mettre à jour' : 'Enregistrer';
        } finally {
            saveBtn.disabled = false;
        }
    });

    document.getElementById('ffReset').addEventListener('click', () => { editingId = null; fillForm(null); });
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
        if (button.dataset.act === 'modifier') fillForm(row);
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
        }
    });

    load().catch((error) => showError(error.message));
})();
</script>
