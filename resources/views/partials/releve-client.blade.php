<section class="panel" id="panel-c-rel">
    <div class="content-head relc-head">
        <div>
            <h1>Relevé Compte Client</h1>
            <p class="relc-sub">Bons de livraison et état des règlements associés</p>
        </div>
    </div>

    <div class="relc-toolbar">
        <div class="relc-filters">
            <label class="relc-filter-name">Client
                <input id="relcClientSearch" type="search" list="relcClientList" placeholder="Rechercher…" autocomplete="off">
                <datalist id="relcClientList"></datalist>
            </label>
            <button type="button" class="relc-refresh" id="relcRefresh" title="Actualiser" aria-label="Actualiser">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
            </button>
            <button type="button" class="relc-btn print" id="relcPrint" title="Imprimer">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5h20v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                Imprimer
            </button>
            <button type="button" class="relc-btn pdf" id="relcPdf" title="PDF">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M14 2v6h6M8 13h8M8 17h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                PDF
            </button>
        </div>
        <div class="relc-kpis">
            <div class="relc-card debit"><span>Total Débit</span><strong id="relcSumDebit">0,00</strong></div>
            <div class="relc-card credit"><span>Total Crédit</span><strong id="relcSumCredit">0,00</strong></div>
            <div class="relc-card solde"><span>Solde</span><strong id="relcSumSolde">0,00</strong></div>
        </div>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Relevé compte</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table relc-table">
                <thead>
                    <tr>
                        <th>Opération</th>
                        <th>Date</th>
                        <th>Nom Client</th>
                        <th>N° Bn</th>
                        <th>Qte</th>
                        <th>Débit</th>
                        <th>Crédit</th>
                        <th>Solde</th>
                        <th class="relc-slim">Type Rég</th>
                        <th class="relc-slim">N° Rég</th>
                        <th>Nom Tiré</th>
                        <th class="relc-slim">Date Encaiss</th>
                        <th class="relc-slim">Payé</th>
                        <th class="relc-slim">Déva</th>
                        <th class="relc-slim">Imp</th>
                        <th class="relc-slim">Repo</th>
                    </tr>
                </thead>
                <tbody id="relcBody">
                    <tr><td colspan="16">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    #panel-c-rel .relc-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-c-rel .relc-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    #panel-c-rel .relc-toolbar {
        display: flex; align-items: end; justify-content: space-between; gap: 0.75rem;
        margin-bottom: 1rem; flex-wrap: wrap;
    }
    #panel-c-rel .relc-kpis { display: flex; flex-wrap: wrap; gap: 0.55rem; margin: 0 0 0 auto; }
    #panel-c-rel .relc-btn {
        height: 38px; padding: 0 0.85rem; border-radius: 10px; border: 1px solid #334155;
        display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer;
        font-family: inherit; font-size: 0.76rem; font-weight: 700; background: #111827; color: #f8fafc;
    }
    #panel-c-rel .relc-btn svg { width: 15px; height: 15px; }
    #panel-c-rel .relc-btn.pdf { background: linear-gradient(180deg, #ea580c, #9a3412); border-color: #9a3412; }
    #panel-c-rel .relc-btn.print { background: linear-gradient(180deg, #334155, #0f172a); }
    #panel-c-rel .relc-card { border-radius: 10px; padding: 0.45rem 0.85rem; color: #fff; box-shadow: 0 3px 12px rgba(15,23,42,0.12); min-width: 110px; }
    #panel-c-rel .relc-card span { display: block; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; opacity: 0.9; }
    #panel-c-rel .relc-card strong { display: block; margin-top: 0.12rem; font-size: 1.05rem; font-variant-numeric: tabular-nums; }
    #panel-c-rel .relc-card.debit { background: linear-gradient(160deg, #3b82f6, #1d4ed8); }
    #panel-c-rel .relc-card.credit { background: linear-gradient(160deg, #22c55e, #15803d); }
    #panel-c-rel .relc-card.solde { background: linear-gradient(160deg, #ef4444, #c47a3a); }
    #panel-c-rel .relc-filters {
        display: flex; flex-wrap: wrap; align-items: end; gap: 0.5rem; margin: 0;
    }
    #panel-c-rel .relc-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; min-width: 0; width: 220px;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #d6c4b4;
    }
    #panel-c-rel .relc-filters input {
        width: 100%; height: 38px; box-sizing: border-box; border-radius: 10px; border: 1px solid #334155;
        background: #fff; color: #000; color-scheme: light; font-family: inherit; font-size: 0.84rem; padding: 0 0.55rem;
    }
    #panel-c-rel .relc-refresh {
        width: 38px; height: 38px; padding: 0; border: 1px solid #334155; border-radius: 10px;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    #panel-c-rel .relc-refresh svg { width: 16px; height: 16px; }
    #panel-c-rel .procar-table-card { min-height: 280px; }
    #panel-c-rel .procar-table-scroll { min-height: 220px; }
    #panel-c-rel .procar-table td.num { font-variant-numeric: tabular-nums; font-weight: 700; }
    #panel-c-rel .procar-table td.debit { color: #1d4ed8; }
    #panel-c-rel .procar-table td.credit { color: #15803d; }
    #panel-c-rel .procar-table td.solde { color: #dc2626; }
    #panel-c-rel .procar-table td.op { font-weight: 800; }
    #panel-c-rel .procar-table td.op-vente { color: #2563eb; }
    #panel-c-rel .procar-table td.op-reg { color: #ca8a04; }
    #panel-c-rel .procar-table td.flag { font-weight: 800; }
    #panel-c-rel .procar-table td.flag-paye { color: #16a34a; }
    #panel-c-rel .procar-table td.flag-deva { color: #7c3aed; }
    #panel-c-rel .procar-table td.flag-imp { color: #dc2626; }
    #panel-c-rel .procar-table td.flag-repo { color: #ca8a04; }
    #panel-c-rel .relc-table th.relc-slim,
    #panel-c-rel .relc-table td.relc-slim {
        width: 4.2rem; min-width: 4.2rem; max-width: 4.2rem;
        padding-left: 0.25rem; padding-right: 0.25rem;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-align: center; font-size: 0.72rem;
    }
    [data-theme="light"] #panel-c-rel .relc-filters label { color: #0b1f3a; }
    [data-theme="light"] #panel-c-rel .relc-refresh,
    [data-theme="light"] #panel-c-rel .relc-btn.print { background: #fff; color: #0b1f3a; border-color: #e0d0c0; }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-c-rel');
    if (!panel) return;
    const body = panel.querySelector('#relcBody');
    const clientSearch = panel.querySelector('#relcClientSearch');
    const clientList = panel.querySelector('#relcClientList');
    const refreshBtn = panel.querySelector('#relcRefresh');
    const printBtn = panel.querySelector('#relcPrint');
    const pdfBtn = panel.querySelector('#relcPdf');
    if (!body || !clientSearch || !clientList || !refreshBtn || !printBtn || !pdfBtn) return;

    let rows = [];
    let clients = [];
    let meta = { total_debit: 0, total_credit: 0, solde: 0 };

    function money(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }
    function qty(value) {
        if (value === null || value === undefined || value === '') return '—';
        return (Number(value) || 0).toLocaleString('fr-FR', { maximumFractionDigits: 3 });
    }
    function flag(on, cls) {
        return on
            ? '<td class="relc-slim flag ' + cls + '">✓</td>'
            : '<td class="relc-slim">—</td>';
    }
    function opCell(operation) {
        const op = String(operation || '');
        const cls = op === 'Rég' ? 'op op-reg' : (op === 'Vente' ? 'op op-vente' : 'op');
        return '<td class="' + cls + '">' + esc(op) + '</td>';
    }
    function amountCell(value, cls) {
        const num = Number(value) || 0;
        return num
            ? '<td class="num ' + cls + '">' + esc(money(num)) + '</td>'
            : '<td class="num ' + cls + '">—</td>';
    }
    function fillClients() {
        clientList.innerHTML = clients.map(function (item) {
            return '<option value="' + esc(item.name) + '"></option>';
        }).join('');
    }
    function resolveClientId() {
        const q = String(clientSearch.value || '').trim().toLowerCase();
        if (!q) return { id: '', ok: true };
        const exact = clients.find(function (item) {
            return String(item.name || '').trim().toLowerCase() === q;
        });
        if (exact) return { id: String(exact.id), ok: true };
        const partial = clients.find(function (item) {
            return String(item.name || '').toLowerCase().includes(q);
        });
        return partial ? { id: String(partial.id), ok: true } : { id: '', ok: false };
    }
    function paintSummary() {
        panel.querySelector('#relcSumDebit').textContent = money(meta.total_debit);
        panel.querySelector('#relcSumCredit').textContent = money(meta.total_credit);
        panel.querySelector('#relcSumSolde').textContent = money(meta.solde);
    }
    function selectedLabel() {
        const typed = String(clientSearch.value || '').trim();
        return typed || 'Tous les clients';
    }
    function render() {
        paintSummary();
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="16">Aucun bon de livraison</td></tr>';
            return;
        }
        body.innerHTML = rows.map(function (row) {
            return '<tr>'
                + opCell(row.operation)
                + '<td>' + esc(row.date_fr || '—') + '</td>'
                + '<td>' + esc(row.nom || '—') + '</td>'
                + '<td>' + esc(row.bon || '—') + '</td>'
                + '<td>' + esc(qty(row.qte)) + '</td>'
                + amountCell(row.debit, 'debit')
                + amountCell(row.credit, 'credit')
                + '<td class="num solde">' + esc(money(row.solde)) + '</td>'
                + '<td class="relc-slim">' + esc(row.type_reg || '—') + '</td>'
                + '<td class="relc-slim">' + esc(row.num_reg || '—') + '</td>'
                + '<td>' + esc(row.nom_tire || '—') + '</td>'
                + '<td class="relc-slim">' + esc(row.date_encaiss_fr || '—') + '</td>'
                + flag(row.paye, 'flag-paye')
                + flag(row.devalide, 'flag-deva')
                + flag(row.impaye, 'flag-imp')
                + flag(row.reporte, 'flag-repo')
                + '</tr>';
        }).join('');
    }
    function printStatement() {
        const win = window.open('', '_blank', 'width=1100,height=800');
        if (!win) return;
        const lines = rows.map(function (row) {
            return '<tr>'
                + '<td>' + esc(row.operation) + '</td>'
                + '<td>' + esc(row.date_fr || '—') + '</td>'
                + '<td>' + esc(row.nom || '—') + '</td>'
                + '<td>' + esc(row.bon || '—') + '</td>'
                + '<td>' + esc(qty(row.qte)) + '</td>'
                + '<td>' + (row.debit ? esc(money(row.debit)) : '—') + '</td>'
                + '<td>' + (row.credit ? esc(money(row.credit)) : '—') + '</td>'
                + '<td>' + esc(money(row.solde)) + '</td>'
                + '<td>' + esc(row.type_reg || '—') + '</td>'
                + '<td>' + esc(row.num_reg || '—') + '</td>'
                + '<td>' + esc(row.nom_tire || '—') + '</td>'
                + '<td>' + esc(row.date_encaiss_fr || '—') + '</td>'
                + '<td>' + (row.paye ? 'Oui' : '—') + '</td>'
                + '<td>' + (row.devalide ? 'Oui' : '—') + '</td>'
                + '<td>' + (row.impaye ? 'Oui' : '—') + '</td>'
                + '<td>' + (row.reporte ? 'Oui' : '—') + '</td>'
                + '</tr>';
        }).join('');
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Releve compte client</title>'
            + '<style>body{font-family:Arial,sans-serif;padding:24px;color:#1e293b}h1{margin:0 0 .3rem;font-size:1.2rem}'
            + 'p{margin:0 0 1rem;color:#64748b}.kpi{display:flex;gap:1rem;margin-bottom:1rem}'
            + '.kpi div{border:1px solid #e2e8f0;border-radius:8px;padding:.5rem .8rem;min-width:140px}'
            + '.kpi span{display:block;font-size:.7rem;text-transform:uppercase;color:#64748b}.kpi strong{font-size:1rem}'
            + 'table{width:100%;border-collapse:collapse;font-size:.78rem}th,td{border:1px solid #cbd5e1;padding:.35rem .4rem;text-align:left}'
            + 'th{background:#0b1f3a;color:#fff}</style></head><body>'
            + '<h1>Releve Compte Client</h1><p>' + esc(selectedLabel()) + '</p>'
            + '<div class="kpi"><div><span>Total Debit</span><strong>' + esc(money(meta.total_debit)) + '</strong></div>'
            + '<div><span>Total Credit</span><strong>' + esc(money(meta.total_credit)) + '</strong></div>'
            + '<div><span>Solde</span><strong>' + esc(money(meta.solde)) + '</strong></div></div>'
            + '<table><thead><tr><th>Operation</th><th>Date</th><th>Nom Client</th><th>N° Bn</th><th>Qte</th>'
            + '<th>Debit</th><th>Credit</th><th>Solde</th><th>Type Reg</th><th>N° Reg</th><th>Nom Tire</th>'
            + '<th>Date Encaiss</th><th>Paye</th><th>Deva</th><th>Imp</th><th>Repo</th></tr></thead>'
            + '<tbody>' + (lines || '<tr><td colspan="16">Aucune donnee</td></tr>') + '</tbody></table>'
            + '</body></html>');
        win.document.close();
        win.focus();
        setTimeout(function () { win.print(); }, 300);
    }
    async function load() {
        if (!clients.length) {
            const boot = await fetch('/releve-clients', {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!boot.ok) throw new Error('Chargement impossible');
            const bootPayload = await boot.json();
            clients = (bootPayload.meta && bootPayload.meta.clients) ? bootPayload.meta.clients : [];
            fillClients();
            if (!String(clientSearch.value || '').trim()) {
                rows = Array.isArray(bootPayload.data) ? bootPayload.data : [];
                meta = {
                    total_debit: (bootPayload.meta && bootPayload.meta.total_debit) || 0,
                    total_credit: (bootPayload.meta && bootPayload.meta.total_credit) || 0,
                    solde: (bootPayload.meta && bootPayload.meta.solde) || 0,
                };
                render();
                return;
            }
        }

        const resolved = resolveClientId();
        if (!resolved.ok) {
            rows = [];
            meta = { total_debit: 0, total_credit: 0, solde: 0 };
            render();
            body.innerHTML = '<tr><td colspan="16">Aucun client trouvé pour cette recherche.</td></tr>';
            return;
        }
        const clientId = resolved.id;
        const url = clientId
            ? '/releve-clients?client_id=' + encodeURIComponent(clientId)
            : '/releve-clients';
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error('Chargement impossible');
        const payload = await response.json();
        clients = (payload.meta && payload.meta.clients) ? payload.meta.clients : clients;
        rows = Array.isArray(payload.data) ? payload.data : [];
        meta = {
            total_debit: (payload.meta && payload.meta.total_debit) || 0,
            total_credit: (payload.meta && payload.meta.total_credit) || 0,
            solde: (payload.meta && payload.meta.solde) || 0,
        };
        fillClients();
        if (clientId) {
            const match = clients.find(function (item) { return String(item.id) === String(clientId); });
            if (match && match.name) clientSearch.value = match.name;
        }
        render();
    }

    function runLoad() {
        load().catch(function () {
            body.innerHTML = '<tr><td colspan="16">Erreur de chargement</td></tr>';
        });
    }
    refreshBtn.addEventListener('click', runLoad);
    clientSearch.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            runLoad();
        }
    });
    printBtn.addEventListener('click', printStatement);
    pdfBtn.addEventListener('click', printStatement);
    document.addEventListener('evopro-bl-changed', function () { load().catch(function () {}); });
    document.addEventListener('evopro-regl-clients-changed', function () { load().catch(function () {}); });
    document.querySelector('[data-panel="c-rel"]')?.addEventListener('click', function () {
        load().catch(function () {});
    });
    load().catch(function () {
        body.innerHTML = '<tr><td colspan="16">Erreur de chargement</td></tr>';
    });
})();
</script>
