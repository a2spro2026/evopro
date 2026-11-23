<section class="panel" id="panel-m-treso">
    <div class="content-head tr-head">
        <div>
            <h1>Rapport Trésorerie</h1>
            <p class="tr-sub">Flux BA / BL, espèces et facturation</p>
        </div>
    </div>

    <div class="tr-toolbar">
        <div class="tr-filters">
            <label>Mois
                <select id="trMonth" data-keep-native="1">
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
            <label class="tr-filter-type">Type
                <select id="trType" data-keep-native="1">
                    <option value="">Tous</option>
                    <option value="ba">BA / Débit</option>
                    <option value="bl">BL / Crédit</option>
                    <option value="caisse">ESP / Caisse</option>
                    <option value="charge">Charge</option>
                    <option value="fa">Débit facturé</option>
                    <option value="fv">Crédit facturé</option>
                    <option value="esp_facture">Esp facturé</option>
                </select>
            </label>
            <button type="button" class="tr-refresh" id="trRefresh" title="Actualiser" aria-label="Actualiser">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
            </button>
            <button type="button" class="tr-btn print" id="trPrintAll" title="Imprimer">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5h20v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                Imprimer
            </button>
            <button type="button" class="tr-btn pdf" id="trPdfAll" title="PDF">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M14 2v6h6" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                PDF
            </button>
        </div>
        <div class="tr-summary">
            <div class="tr-card debit"><span>BA / Débit</span><strong id="trSumDebit">0,00</strong></div>
            <div class="tr-card credit"><span>BL / Crédit</span><strong id="trSumCredit">0,00</strong></div>
            <div class="tr-card caisse"><span>ESP / Caisse</span><strong id="trSumCaisse">0,00</strong></div>
            <div class="tr-card solde"><span>Solde</span><strong id="trSumSolde">0,00</strong></div>
            <div class="tr-card charge"><span>Total Charge</span><strong id="trSumCharge">0,00</strong></div>
            <div class="tr-card fa-debit"><span>Débit facturé</span><strong id="trSumDebitFact">0,00</strong></div>
            <div class="tr-card fa-credit"><span>Crédit facturé</span><strong id="trSumCreditFact">0,00</strong></div>
        </div>
    </div>

    <div class="procar-table-card">
        <div class="procar-table-bar"><h3>Mouvements trésorerie</h3></div>
        <div class="procar-table-scroll">
            <table class="procar-table tr-table">
                <colgroup>
                    <col class="tr-col-date">
                    <col class="tr-col-op">
                    <col class="tr-col-amt">
                    <col class="tr-col-amt">
                    <col class="tr-col-amt">
                    <col class="tr-col-amt">
                    <col class="tr-col-amt">
                    <col class="tr-col-amt">
                    <col class="tr-col-amt">
                </colgroup>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Opération</th>
                        <th>Débit</th>
                        <th>Crédit</th>
                        <th>Caisse</th>
                        <th>Charge</th>
                        <th>Débit facturé</th>
                        <th>Crédit facturé</th>
                        <th>Esp facturé</th>
                    </tr>
                </thead>
                <tbody id="trBody">
                    <tr><td colspan="9">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    #panel-m-treso .tr-sub { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.82rem; text-transform: none; }
    #panel-m-treso .tr-toolbar {
        display: flex; align-items: end; justify-content: space-between; gap: 0.75rem;
        margin-bottom: 1rem; flex-wrap: wrap;
    }
    #panel-m-treso .tr-filters { display: flex; flex-wrap: wrap; align-items: end; gap: 0.5rem; margin: 0; }
    #panel-m-treso .tr-filters label {
        display: flex; flex-direction: column; gap: 0.35rem; width: 140px;
        font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #d6c4b4;
    }
    #panel-m-treso .tr-filters label.tr-filter-type { width: 168px; }
    #panel-m-treso .tr-filters select,
    #panel-m-treso .tr-refresh,
    #panel-m-treso .tr-btn {
        height: 34px; min-height: 34px; max-height: 34px; box-sizing: border-box;
        border-radius: 8px; border: 1px solid #334155; font-family: inherit;
    }
    #panel-m-treso .tr-filters select {
        width: 100%; background: #fff; color: #000; color-scheme: light;
        font-size: 0.8rem; padding: 0 0.55rem;
    }
    #panel-m-treso .tr-refresh {
        width: 34px; padding: 0;
        display: grid; place-items: center; cursor: pointer; background: #111827; color: #f8fafc;
    }
    #panel-m-treso .tr-refresh svg { width: 15px; height: 15px; }
    #panel-m-treso .tr-btn {
        padding: 0 0.85rem;
        display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; cursor: pointer;
        font-size: 0.76rem; font-weight: 700; background: #111827; color: #f8fafc;
    }
    #panel-m-treso .tr-btn svg { width: 14px; height: 14px; flex: 0 0 14px; }
    #panel-m-treso .tr-btn.pdf { background: linear-gradient(180deg, #ea580c, #9a3412); border-color: #9a3412; }
    #panel-m-treso .tr-summary {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.5rem;
        margin: 0 0 0 auto;
        flex: 1 1 720px;
        max-width: 100%;
    }
    #panel-m-treso .tr-card {
        border-radius: 12px; padding: 0.55rem 0.7rem; color: #fff;
        width: 100%; min-width: 0; min-height: 70px; box-sizing: border-box;
        box-shadow: 0 4px 14px rgba(15,23,42,0.14);
        display: flex; flex-direction: column; justify-content: center;
    }
    #panel-m-treso .tr-card span {
        display: block; font-size: 0.62rem; font-weight: 700; letter-spacing: 0.06em;
        text-transform: uppercase; opacity: 0.92; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    #panel-m-treso .tr-card strong {
        display: block; margin-top: 0.2rem; font-size: 1.12rem; font-variant-numeric: tabular-nums;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    @media (max-width: 1280px) {
        #panel-m-treso .tr-summary { grid-template-columns: repeat(4, minmax(0, 1fr)); flex-basis: 100%; }
    }
    @media (max-width: 720px) {
        #panel-m-treso .tr-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    #panel-m-treso .tr-card.debit { background: linear-gradient(160deg, #facc15, #ca8a04) !important; color: #1a1204 !important; }
    #panel-m-treso .tr-card.credit { background: linear-gradient(160deg, #3b82f6, #1d4ed8) !important; color: #fff !important; }
    #panel-m-treso .tr-card.caisse { background: linear-gradient(160deg, #22c55e, #15803d) !important; color: #fff !important; }
    #panel-m-treso .tr-card.solde { background: linear-gradient(160deg, #ef4444, #b91c1c) !important; color: #fff !important; }
    #panel-m-treso .tr-card.charge { background: linear-gradient(160deg, #0b1f3a, #06101f) !important; color: #fff !important; }
    #panel-m-treso .tr-card.fa-debit { background: linear-gradient(160deg, #8b5cf6, #6d28d9) !important; color: #fff !important; }
    #panel-m-treso .tr-card.fa-credit { background: linear-gradient(160deg, #9b1b2e, #6b0f1a) !important; color: #fff !important; }
    #panel-m-treso .tr-table {
        table-layout: fixed;
        width: 100%;
        min-width: 1100px;
    }
    #panel-m-treso .tr-table .tr-col-date { width: 110px; }
    #panel-m-treso .tr-table .tr-col-op { width: auto; }
    #panel-m-treso .tr-table .tr-col-amt { width: 118px; }
    #panel-m-treso .tr-table th:nth-child(n+3),
    #panel-m-treso .tr-table td:nth-child(n+3) {
        width: 118px;
        text-align: center;
        white-space: nowrap;
    }
    #panel-m-treso .procar-table td.num { font-variant-numeric: tabular-nums; font-weight: 700; text-align: center; }
    #panel-m-treso .procar-table td.debit { color: #dc2626; }
    #panel-m-treso .procar-table td.credit { color: #2563eb; }
    #panel-m-treso .procar-table td.caisse { color: #d97706; }
    #panel-m-treso .procar-table td.charge { color: #0b1f3a; }
    #panel-m-treso .procar-table td.fa-debit { color: #c47a3a; }
    #panel-m-treso .procar-table td.fa-credit { color: #15803d; }
    #panel-m-treso .procar-table td.esp-fact { color: #ea580c; }
    [data-theme="light"] #panel-m-treso .tr-filters label { color: #0b1f3a; }
    [data-theme="light"] #panel-m-treso .tr-refresh,
    [data-theme="light"] #panel-m-treso .tr-btn.print { background: #fff; color: #0b1f3a; border-color: #e0d0c0; }
</style>
<script>
(function () {
    const panel = document.getElementById('panel-m-treso');
    if (!panel) return;
    const body = panel.querySelector('#trBody');
    const monthFilter = panel.querySelector('#trMonth');
    const typeFilter = panel.querySelector('#trType');
    const refreshBtn = panel.querySelector('#trRefresh');
    const printAllBtn = panel.querySelector('#trPrintAll');
    const pdfAllBtn = panel.querySelector('#trPdfAll');
    if (!body || !monthFilter || !typeFilter || !refreshBtn) return;

    let rows = [];
    let meta = {
        debit: 0, credit: 0, caisse: 0, charge: 0, solde: 0,
        debit_facture: 0, credit_facture: 0, esp_facture: 0,
    };

    function money(value) {
        return (Number(value) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }
    function amountCell(value, cls) {
        const num = Number(value) || 0;
        return num
            ? '<td class="num ' + cls + '">' + esc(money(num)) + '</td>'
            : '<td class="num ' + cls + '">—</td>';
    }
    function paintSummary() {
        panel.querySelector('#trSumDebit').textContent = money(meta.debit);
        panel.querySelector('#trSumCredit').textContent = money(meta.credit);
        panel.querySelector('#trSumCaisse').textContent = money(meta.caisse);
        panel.querySelector('#trSumSolde').textContent = money(meta.solde);
        panel.querySelector('#trSumCharge').textContent = money(meta.charge);
        panel.querySelector('#trSumDebitFact').textContent = money(meta.debit_facture);
        panel.querySelector('#trSumCreditFact').textContent = money(meta.credit_facture);
    }
    function render() {
        paintSummary();
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="9">Aucun mouvement pour cette période.</td></tr>';
            return;
        }
        body.innerHTML = rows.map(function (row) {
            return '<tr>'
                + '<td>' + esc(row.date_fr || '—') + '</td>'
                + '<td>' + esc(row.operation || '—') + '</td>'
                + amountCell(row.debit, 'debit')
                + amountCell(row.credit, 'credit')
                + amountCell(row.caisse, 'caisse')
                + amountCell(row.charge, 'charge')
                + amountCell(row.debit_facture, 'fa-debit')
                + amountCell(row.credit_facture, 'fa-credit')
                + amountCell(row.esp_facture, 'esp-fact')
                + '</tr>';
        }).join('');
    }
    function printReport(list, title) {
        const win = window.open('', '_blank', 'width=1100,height=800');
        if (!win) return;
        const lines = (list || []).map(function (row) {
            return '<tr>'
                + '<td>' + esc(row.date_fr || '—') + '</td>'
                + '<td>' + esc(row.operation || '—') + '</td>'
                + '<td>' + (row.debit ? esc(money(row.debit)) : '—') + '</td>'
                + '<td>' + (row.credit ? esc(money(row.credit)) : '—') + '</td>'
                + '<td>' + (row.caisse ? esc(money(row.caisse)) : '—') + '</td>'
                + '<td>' + (row.charge ? esc(money(row.charge)) : '—') + '</td>'
                + '<td>' + (row.debit_facture ? esc(money(row.debit_facture)) : '—') + '</td>'
                + '<td>' + (row.credit_facture ? esc(money(row.credit_facture)) : '—') + '</td>'
                + '<td>' + (row.esp_facture ? esc(money(row.esp_facture)) : '—') + '</td>'
                + '</tr>';
        }).join('');
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + esc(title) + '</title>'
            + '<style>body{font-family:Arial,sans-serif;padding:24px;color:#1e293b}h1{margin:0 0 .3rem;font-size:1.2rem}'
            + 'p{margin:0 0 1rem;color:#64748b}.kpi{display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:1rem}'
            + '.kpi div{border:1px solid #e2e8f0;border-radius:8px;padding:.4rem .7rem;min-width:110px}'
            + '.kpi span{display:block;font-size:.65rem;text-transform:uppercase;color:#64748b}.kpi strong{font-size:.95rem}'
            + 'table{width:100%;border-collapse:collapse;font-size:.78rem}th,td{border:1px solid #cbd5e1;padding:.35rem .4rem;text-align:left}'
            + 'th{background:#0b1f3a;color:#fff}</style></head><body>'
            + '<h1>' + esc(title) + '</h1>'
            + '<div class="kpi">'
            + '<div><span>BA / Debit</span><strong>' + esc(money(meta.debit)) + '</strong></div>'
            + '<div><span>BL / Credit</span><strong>' + esc(money(meta.credit)) + '</strong></div>'
            + '<div><span>ESP / Caisse</span><strong>' + esc(money(meta.caisse)) + '</strong></div>'
            + '<div><span>Solde</span><strong>' + esc(money(meta.solde)) + '</strong></div>'
            + '<div><span>Total Charge</span><strong>' + esc(money(meta.charge)) + '</strong></div>'
            + '<div><span>Debit facture</span><strong>' + esc(money(meta.debit_facture)) + '</strong></div>'
            + '<div><span>Credit facture</span><strong>' + esc(money(meta.credit_facture)) + '</strong></div>'
            + '</div>'
            + '<table><thead><tr><th>Date</th><th>Operation</th><th>Debit</th><th>Credit</th><th>Caisse</th><th>Charge</th>'
            + '<th>Debit facture</th><th>Credit facture</th><th>Esp facture</th></tr></thead>'
            + '<tbody>' + (lines || '<tr><td colspan="9">Aucune donnee</td></tr>') + '</tbody></table>'
            + '</body></html>');
        win.document.close();
        win.focus();
        setTimeout(function () { win.print(); }, 300);
    }
    async function load() {
        const params = new URLSearchParams();
        if (monthFilter.value) params.set('month', monthFilter.value);
        if (typeFilter.value) params.set('type', typeFilter.value);
        const query = params.toString();
        const url = query ? '/rapport-tresorerie?' + query : '/rapport-tresorerie';
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error('Chargement impossible');
        const payload = await response.json();
        rows = Array.isArray(payload.data) ? payload.data : [];
        meta = Object.assign({
            debit: 0, credit: 0, caisse: 0, charge: 0, solde: 0,
            debit_facture: 0, credit_facture: 0, esp_facture: 0,
        }, payload.meta || {});
        render();
    }

    function runLoad() {
        load().catch(function () {
            body.innerHTML = '<tr><td colspan="9">Erreur de chargement</td></tr>';
        });
    }
    monthFilter.addEventListener('change', runLoad);
    typeFilter.addEventListener('change', runLoad);
    refreshBtn.addEventListener('click', runLoad);
    printAllBtn.addEventListener('click', function () { printReport(rows, 'Rapport Tresorerie'); });
    pdfAllBtn.addEventListener('click', function () { printReport(rows, 'Rapport Tresorerie'); });
    document.querySelector('[data-panel="m-treso"]')?.addEventListener('click', function () {
        load().catch(function () {});
    });
    load().catch(function () {
        body.innerHTML = '<tr><td colspan="9">Erreur de chargement</td></tr>';
    });
})();
</script>
