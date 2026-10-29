<section class="panel" id="panel-fa-achat">
    <div class="content-head fa-head">
        <div>
            <h1>Factures Achats</h1>
            <p class="fa-sub">Saisie des factures fournisseurs</p>
        </div>
        <button type="button" class="fa-nouveau" id="faAdd">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
            Nouveau
        </button>
    </div>

    <div class="fa-filters">
        <div class="fa-filter-side">
            <label>De<input type="text" id="faFrom" inputmode="numeric" maxlength="10" placeholder="JJ/MM/AAAA" autocomplete="off"></label>
            <label>À<input type="text" id="faTo" inputmode="numeric" maxlength="10" placeholder="JJ/MM/AAAA" autocomplete="off"></label>
            <label>Mois
                <select id="faMonth" data-keep-native="1">
                    <option value="">Tous</option>
                    <option value="01">Janvier</option>
                    <option value="02">Février</option>
                    <option value="03">Mars</option>
                    <option value="04">Avril</option>
                    <option value="05">Mai</option>
                    <option value="06">Juin</option>
                    <option value="07">Juillet</option>
                    <option value="08">Août</option>
                    <option value="09">Septembre</option>
                    <option value="10">Octobre</option>
                    <option value="11">Novembre</option>
                    <option value="12">Décembre</option>
                </select>
            </label>
            <label>Nom fournisseur
                <select id="faName" class="fa-name" data-keep-native="1">
                    <option value="">Tous</option>
                </select>
            </label>
        </div>
        <div class="fa-stats">
            <div class="fa-stat achats"><span>Total Achats</span><strong id="faSumTtc">0,00</strong></div>
        </div>
    </div>

    <div class="fa-card">
        <div class="fa-list-bar">
            <h3>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                Liste des factures
            </h3>
            <button type="button" id="faRefresh" title="Actualiser">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.2-5.8M21 4v5h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        <div class="fa-scroll">
            <table class="fa-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Réf</th>
                        <th>Fournisseur</th>
                        <th>Ice</th>
                        <th>Total HT</th>
                        <th>TVA</th>
                        <th>Total TTC</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="faBody">
                    <tr><td colspan="8">Aucune facture enregistrée</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fa-window" id="faModal" hidden>
        <div class="fa-sheet" role="dialog" aria-modal="true" aria-labelledby="faModalTitle">
            <div class="fa-sheet-head">
                <h3 id="faModalTitle">Nouvelle facture achat</h3>
                <button type="button" class="fa-x" id="faCloseX" title="Fermer">×</button>
            </div>
            <form id="faForm" class="fa-form" autocomplete="off">
                <div class="fa-error" id="faError" hidden></div>
                <div class="fa-grid">
                    <div class="fa-field">
                        <label for="faDate">Date</label>
                        <input id="faDate" type="date" required>
                    </div>
                    <div class="fa-field fa-field-ref">
                        <label for="faRef">Réf</label>
                        <input id="faRef" readonly>
                    </div>
                    <div class="fa-field fa-supplier">
                        <label for="faSupplier">Fournisseur</label>
                        <input id="faSupplier" required maxlength="255" autocomplete="off" placeholder="Liste ou saisie">
                        <div class="fa-supplier-menu" id="faSupplierMenu" hidden></div>
                    </div>
                    <div class="fa-field">
                        <label for="faIce">Ice</label>
                        <input id="faIce" inputmode="numeric" maxlength="15" pattern="\d{15}" required placeholder="15 chiffres">
                    </div>
                </div>

                <div class="fa-lines-head">
                    <p>Lignes facture</p>
                    <div>
                        <button type="button" class="fa-add-line" id="faAddLine">+ Ligne</button>
                    </div>
                </div>
                <div class="fa-lines-wrap">
                    <table class="fa-lines">
                        <colgroup>
                            <col class="c-ref">
                            <col class="c-desc">
                            <col class="c-qty">
                            <col class="c-price">
                            <col class="c-remise">
                            <col class="c-sub">
                            <col class="c-x">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Réf</th>
                                <th>Désignation</th>
                                <th>Qté</th>
                                <th>Prix/U</th>
                                <th>Remise</th>
                                <th>Sous-Total TTC</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="faLines"></tbody>
                    </table>
                </div>
                <div class="fa-totals">
                    <div class="ht"><small>Montant HT</small><strong id="faHt">0.00</strong></div>
                    <div class="tva"><small>TVA (20%)</small><strong id="faTva">0.00</strong></div>
                    <div class="ttc"><small>Montant TTC</small><strong id="faTtc">0.00</strong></div>
                </div>
                <div class="fa-actions">
                    <button type="button" class="fa-btn ghost" id="faClose">Fermer</button>
                    <button type="submit" class="fa-btn primary" id="faSave">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fa-window" id="faView" hidden>
        <div class="fa-sheet fa-view" role="dialog" aria-modal="true">
            <div class="fa-sheet-head">
                <div>
                    <p class="fa-kicker">Facture Achat</p>
                    <h3 id="faViewTitle">—</h3>
                </div>
                <button type="button" class="fa-x" id="faViewClose" title="Fermer">×</button>
            </div>
            <div class="fa-view-body" id="faViewBody"></div>
            <div class="fa-actions">
                <button type="button" class="fa-btn ghost" id="faViewPrint">Imprimer</button>
                <button type="button" class="fa-btn primary" id="faViewPdf">PDF</button>
            </div>
        </div>
    </div>
</section>
<style>
    #panel-fa-achat .fa-sub { text-transform: none; }
    #panel-fa-achat .fa-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .fa-nouveau {
        display: inline-flex; align-items: center; gap: 0.4rem;
        height: 40px; padding: 0 1rem; border: none; border-radius: 10px; cursor: pointer;
        background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff;
        font-weight: 700; font-size: 0.85rem; font-family: inherit;
    }
    .fa-nouveau svg, .fa-list-bar svg { width: 16px; height: 16px; }
    .fa-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 220px)); gap: 0.75rem; margin-bottom: 1rem; }
    .fa-stat { border-radius: 14px; padding: 0.95rem 1rem; color: #fff; box-shadow: 0 10px 24px rgba(0,0,0,0.25); }
    .fa-stat span { display: block; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.85; }
    .fa-stat strong { display: block; margin-top: 0.25rem; font-size: 1.35rem; font-variant-numeric: tabular-nums; }
    .fa-stat.slate { background: linear-gradient(135deg, #2a2a2e, #0a0a0c); }
    .fa-stat.blue { background: linear-gradient(135deg, #6e1224, #3a0a12); }
    .fa-stat.green { background: linear-gradient(135deg, #8f2436, #4a1018); }
    .fa-card { border-radius: 16px; overflow: hidden; border: 1px solid rgba(143, 36, 54, 0.45); background: #0a0a0c; }
    .fa-list-bar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.8rem 1.1rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .fa-list-bar h3 { margin: 0; display: flex; align-items: center; gap: 0.45rem; font-size: 0.82rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .fa-list-bar button, .fa-x {
        width: 32px; height: 32px; border: none; border-radius: 8px; background: transparent; color: rgba(255,255,255,0.75); cursor: pointer;
    }
    .fa-list-bar button svg { width: 16px; height: 16px; }
    .fa-list-bar button:hover, .fa-x:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .fa-scroll { overflow: auto; max-height: calc(100dvh - 22rem); min-height: 220px; }
    .fa-table { width: 100%; border-collapse: collapse; min-width: 980px; color: #e2e8f0; }
    .fa-table th {
        padding: 0.75rem 0.55rem; font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase;
        color: #e7d5d8; background: #1a1014; text-align: center; white-space: nowrap;
    }
    .fa-table td { padding: 0.7rem 0.55rem; text-align: center; border-top: 1px solid rgba(110, 18, 36, 0.35); font-size: 0.86rem; }
    .fa-table tbody tr:hover td { background: rgba(110, 18, 36, 0.32); }
    .fa-ref { color: #f0c9cf; font-weight: 700; font-family: ui-monospace, monospace; font-size: 0.78rem; }
    .fa-ttc { color: #e8b4bc; font-weight: 700; }
    .fa-depot { display: inline-flex; padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
    .fa-depot.a { background: rgba(37,99,235,0.25); color: #93c5fd; }
    .fa-depot.b { background: rgba(139,92,246,0.25); color: #c4b5fd; }
    .fa-row-actions { display: flex; justify-content: center; gap: 0.15rem; }
    .fa-icon {
        width: 28px; height: 28px; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer;
    }
    .fa-icon svg { width: 15px; height: 15px; }
    .fa-icon:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .fa-icon.see:hover { color: #f0c9cf; }
    .fa-icon.edit:hover { color: #fbbf24; }
    .fa-icon.print:hover { color: #e2e8f0; }
    .fa-icon.pdf:hover { color: #fb923c; }
    .fa-icon.del:hover { color: #f87171; }
    .fa-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: flex-start; justify-content: center; padding: 1.25rem;
        background: rgba(0,0,0,0.55); overflow: auto;
    }
    .shell.sidebar-hidden .fa-window, .fa-window.at-edge { left: 0; }
    .fa-window[hidden] { display: none !important; }
    #faModal.fa-window {
        position: static; inset: auto; z-index: auto; display: block;
        margin: 1rem 0 0; padding: 0; background: transparent; overflow: visible;
    }
    #faModal.fa-window[hidden] { display: none !important; }
    #faModal .fa-sheet { margin-top: 0; }
    .fa-sheet {
        width: min(920px, 100%); margin-top: 1.5rem; border-radius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #4a1824; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    #faModal .fa-sheet { width: min(1220px, 100%); margin-top: 0; }
    .fa-view { width: min(520px, 100%); }
    .fa-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .fa-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .fa-kicker { margin: 0; font-size: 0.62rem; letter-spacing: 0.12em; text-transform: uppercase; color: #e7c4c8; }
    .fa-form { padding: 1rem 1.15rem 0.4rem; }
    .fa-error { margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-radius: 10px; background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #7f1d1d; font-size: 0.82rem; }
    .fa-grid { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(86px, 0.48fr) minmax(0, 1.55fr) minmax(148px, 1.15fr); gap: 0.75rem 0.85rem; }
    .fa-span-2 { grid-column: span 2; }
    .fa-field label {
        display: flex; flex-direction: column; align-items: center; gap: 0.28rem;
        margin-bottom: 0.35rem; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.08em;
        text-transform: uppercase; color: #94a3b8;
    }
    .fa-field label::after { content: ''; width: 28px; height: 2px; border-radius: 2px; background: #e10600; }
    .fa-field input, .fa-field select {
        width: 100%; height: 40px; box-sizing: border-box; border-radius: 8px;
        border: 1px solid #475569; background: #1e293b; color: #f8fafc;
        text-align: center; font-size: 0.78rem; font-family: inherit; padding: 0 0.6rem;
    }
    .fa-field input[readonly] { background: #172033; color: #cbd5e1; }
    .fa-field input::placeholder { color: #64748b; }
    .fa-sheet .evopro-combo { width: 100%; }
    .fa-sheet .evopro-combo-trigger {
        height: 40px; min-height: 40px; box-sizing: border-box; justify-content: center; position: relative;
        border-radius: 8px; border: 1px solid #475569; background: #1e293b; color: #f8fafc;
        font-size: 0.78rem; font-weight: 500; padding: 0 1.6rem;
    }
    .fa-sheet .evopro-combo-trigger::after { position: absolute; right: 10px; }
    .fa-sheet .evopro-combo-menu { background: #0f172a; border-color: #475569; }
    .fa-sheet .evopro-combo-option { color: #f8fafc; text-align: center; }
    .fa-sheet .evopro-combo-option.is-placeholder { color: #94a3b8; }
    .fa-sheet .evopro-combo-option:hover, .fa-sheet .evopro-combo-option.is-active { background: rgba(37,99,235,0.35); color: #fff; }
    .fa-lines-head { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin: 1rem 0 0.45rem; }
    .fa-lines-head p { margin: 0; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #94a3b8; }
    .fa-lines-head > div { display: flex; align-items: center; gap: 0.65rem; }
    .fa-add-line { border: none; background: transparent; color: #e8b4bc; font-weight: 800; font-size: 0.78rem; cursor: pointer; font-family: inherit; }
    .fa-lines-wrap { max-height: 280px; overflow: auto; border: 1px solid #334155; border-radius: 10px; }
    .fa-lines { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .fa-lines .c-ref { width: 128px; }
    .fa-lines .c-qty { width: 62px; }
    .fa-lines .c-price { width: 78px; }
    .fa-lines .c-remise { width: 68px; }
    .fa-lines .c-sub { width: 108px; }
    .fa-lines .c-x { width: 34px; }
    .fa-lines th { position: sticky; top: 0; background: #1e293b; color: #94a3b8; font-size: 0.62rem; letter-spacing: 0.06em; text-transform: uppercase; padding: 0.5rem; text-align: center; }
    .fa-lines td { padding: 0.35rem; text-align: center; border-top: 1px solid #1e293b; color: #e2e8f0; font-size: 0.78rem; }
    .fa-lines input {
        width: 100%; height: 32px; box-sizing: border-box; border-radius: 6px; border: 1px solid #475569;
        background: #1e293b; color: #fff; text-align: center; font-size: 0.75rem; font-family: inherit;
    }
    .fa-lines input[readonly] { background: #172033; color: #f8fafc; }
    .fa-supplier { position: relative; }
    .fa-supplier-menu {
        position: absolute; z-index: 8; left: 0; right: 0; top: calc(100% + 4px);
        max-height: 180px; overflow: auto; border-radius: 8px;
        border: 1px solid #475569; background: #0f172a; box-shadow: 0 12px 28px rgba(0,0,0,0.35);
    }
    .fa-supplier-menu[hidden] { display: none; }
    .fa-supplier-menu button {
        display: block; width: 100%; border: 0; background: transparent; color: #f8fafc;
        text-align: center; padding: 0.5rem 0.6rem; cursor: pointer; font-family: inherit; font-size: 0.78rem;
    }
    .fa-supplier-menu button:hover { background: rgba(37,99,235,0.35); }
    .fa-remise { position: relative; }
    .fa-remise input { padding-right: 1.1rem; }
    .fa-remise span { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 0.68rem; color: #94a3b8; pointer-events: none; }
    .fa-line-x { border: none; background: transparent; color: #f87171; cursor: pointer; font-size: 1rem; }
    .fa-totals { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.55rem; margin-top: 0.8rem; }
    .fa-totals div { border-radius: 12px; padding: 0.55rem 0.4rem; text-align: center; background: #1e293b; border: 1px solid #334155; }
    .fa-totals small { display: block; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; }
    .fa-totals strong { display: block; margin-top: 0.15rem; font-size: 0.95rem; }
    .fa-totals .tva { background: rgba(92, 18, 32, 0.55); border-color: #8f2436; }
    .fa-totals .tva small, .fa-totals .tva strong { color: #f0c9cf; }
    .fa-totals .ttc { background: rgba(74, 14, 24, 0.85); border-color: #e10600; }
    .fa-totals .ttc small, .fa-totals .ttc strong { color: #fff; }
    .fa-actions { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.9rem 0 1rem; }
    .fa-view .fa-actions { padding: 0.9rem 1.15rem 1rem; border-top: 1px solid #1e293b; }
    .fa-btn { height: 36px; padding: 0 1rem; border-radius: 999px; border: none; font-family: inherit; font-weight: 700; font-size: 0.78rem; cursor: pointer; }
    .fa-btn.ghost { background: #1e293b; color: #e2e8f0; border: 1px solid #475569; }
    .fa-btn.primary { background: linear-gradient(180deg, #8f2436, #5c1220); color: #fff; }
    .fa-view-body { padding: 0.9rem 1.15rem; max-height: 50vh; overflow: auto; }
    .fa-view-row { display: flex; justify-content: space-between; gap: 1rem; padding: 0.4rem 0; border-bottom: 1px solid #1e293b; font-size: 0.84rem; }
    .fa-view-row span { color: #94a3b8; }
    .fa-view-line { margin-top: 0.45rem; padding: 0.45rem 0.6rem; border: 1px solid #1e293b; border-radius: 8px; font-size: 0.78rem; }
    @media (max-width: 900px) {
        .fa-window { left: 0; }
        .fa-grid { grid-template-columns: minmax(0, 1.15fr) minmax(86px, 0.48fr) minmax(0, 1.55fr) minmax(120px, 1.15fr); }
        .fa-span-2 { grid-column: span 2; }
        .fa-stats { grid-template-columns: 1fr; }
    }
</style>
<script>
(function () {
    const modal = document.getElementById('faModal');
    const form = document.getElementById('faForm');
    const view = document.getElementById('faView');
    if (!modal || !form || !view) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const body = document.getElementById('faBody');
    const linesBody = document.getElementById('faLines');
    const errorBox = document.getElementById('faError');
    const supplier = document.getElementById('faSupplier');
    const supplierMenu = document.getElementById('faSupplierMenu');
    const ice = document.getElementById('faIce');
    let meta = { next_ref: 'FA-0001', date: '', suppliers: [], count: 0, total_ht: 0, total_ttc: 0 };
    let rows = [];
    let editingId = null;
    let viewRow = null;

    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const printer = '<svg viewBox="0 0 24 24"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5h20v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const file = '<svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M14 3v5h5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

    function moneyDot(value) {
        const n = Number(String(value ?? '').replace(/\s/g, '').replace(',', '.')) || 0;
        return n.toFixed(2);
    }
    function moneyFr(value) {
        const n = Number(value) || 0;
        return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }
    function showError(message) {
        errorBox.hidden = !message;
        errorBox.textContent = message || '';
    }
    function parseNum(value) {
        const raw = String(value ?? '').replace(/\s/g, '').replace(',', '.');
        const n = Number(raw);
        return Number.isFinite(n) ? n : 0;
    }
    function lineAmount(row) {
        const qty = parseNum(row.querySelector('[data-f="qty"]').value);
        const price = parseNum(row.querySelector('[data-f="price"]').value);
        const remise = Math.min(100, Math.max(0, parseNum(row.querySelector('[data-f="remise"]').value)));
        return qty * price * (1 - remise / 100);
    }
    function lineRow(item) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input data-f="ref" maxlength="64" placeholder="Réf" value="${esc(item?.article_ref || '')}"></td>
            <td><input data-f="desc" required maxlength="255" placeholder="Désignation" value="${esc(item?.description || '')}"></td>
            <td><input data-f="qty" required inputmode="decimal" value="${esc(item?.quantity ?? 1)}"></td>
            <td><input data-f="price" required inputmode="decimal" placeholder="0.00" value="${item?.unit_price != null && item.unit_price !== '' ? esc(moneyDot(item.unit_price)) : ''}"></td>
            <td><div class="fa-remise"><input data-f="remise" inputmode="decimal" value="${esc(item?.remise ?? 0)}"><span>%</span></div></td>
            <td><input data-f="sub" readonly tabindex="-1" value="0.00"></td>
            <td><button type="button" class="fa-line-x" data-remove title="Retirer">×</button></td>`;
        return tr;
    }
    function recalc() {
        let ttc = 0;
        linesBody.querySelectorAll('tr').forEach((row) => {
            const sub = lineAmount(row);
            row.querySelector('[data-f="sub"]').value = moneyDot(sub);
            ttc += sub;
        });
        ttc = Math.round(ttc * 100) / 100;
        const ht = Math.round((ttc / 1.2) * 100) / 100;
        const tva = Math.round((ttc - ht) * 100) / 100;
        document.getElementById('faHt').textContent = moneyDot(ht);
        document.getElementById('faTva').textContent = moneyDot(tva);
        document.getElementById('faTtc').textContent = moneyDot(ttc);
        return ttc;
    }
    function resetForm(row) {
        editingId = row?.id || null;
        document.getElementById('faModalTitle').textContent = row ? `Modifier ${row.reference}` : 'Nouvelle facture achat';
        document.getElementById('faDate').value = row?.invoice_date_raw || meta.date || '';
        document.getElementById('faRef').value = row?.reference || meta.next_ref || 'FA-0001';
        supplier.value = row?.fournisseur && row.fournisseur !== '—' ? row.fournisseur : '';
        supplier.dataset.id = row?.supplier_id ? String(row.supplier_id) : '';
        supplierMenu.hidden = true;
        const selected = (meta.suppliers || []).find((item) => String(item.id) === String(supplier.dataset.id || ''));
        ice.value = String(row?.ice || selected?.ice || '').replace(/\D/g, '').slice(0, 15);
        linesBody.innerHTML = '';
        const items = row?.items?.length ? row.items : [{}];
        items.forEach((item) => linesBody.appendChild(lineRow(item)));
        showError('');
        recalc();
    }
    function isoDate(value) {
        const match = String(value || '').match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        return match ? `${match[3]}-${match[2]}-${match[1]}` : '';
    }
    function maskDate(input) {
        const digits = input.value.replace(/\D/g, '').slice(0, 8);
        input.value = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 8)].filter(Boolean).join('/');
    }
    function fillNames() {
        const select = document.getElementById('faName');
        const current = select.value;
        const seen = new Set();
        const names = [];
        const add = (value) => {
            const name = String(value || '').trim();
            const key = name.toLowerCase();
            if (!name || name === '—' || seen.has(key)) return;
            seen.add(key);
            names.push(name);
        };
        (meta.suppliers || []).forEach((item) => add(item.name));
        rows.forEach((row) => add(row.fournisseur));
        names.sort((a, b) => a.localeCompare(b, 'fr'));
        select.innerHTML = '<option value="">Tous</option>' + names.map((name) => `<option value="${esc(name)}">${esc(name)}</option>`).join('');
        select.value = names.some((name) => name.toLowerCase() === current.toLowerCase()) ? current : '';
    }
    function filteredRows() {
        const from = isoDate(document.getElementById('faFrom').value);
        const to = isoDate(document.getElementById('faTo').value);
        const month = document.getElementById('faMonth').value;
        const name = document.getElementById('faName').value.trim().toLowerCase();
        return rows.filter((row) => {
            const raw = String(row.invoice_date_raw || '');
            if (from && raw < from) return false;
            if (to && raw > to) return false;
            if (month && raw.slice(5, 7) !== month) return false;
            if (name && String(row.fournisseur || '').trim().toLowerCase() !== name) return false;
            return true;
        });
    }
    function render(list) {
        const total = list.reduce((sum, row) => sum + (Number(row.total_ttc) || 0), 0);
        document.getElementById('faSumTtc').textContent = moneyFr(total);
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="8">${rows.length ? 'Aucune facture pour cette recherche.' : 'Aucune facture enregistrée'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(row.invoice_date)}</td>
            <td class="fa-ref">${esc(row.reference)}</td>
            <td>${esc(row.fournisseur)}</td>
            <td>${esc(row.ice) || '—'}</td>
            <td>${esc(moneyFr(row.total_ht))}</td>
            <td>${esc(moneyFr(row.tva))}</td>
            <td class="fa-ttc">${esc(moneyFr(row.total_ttc))}</td>
            <td><div class="fa-row-actions">
                <button type="button" class="fa-icon see" data-act="voir" data-id="${row.id}" title="Voir">${eye}</button>
                <button type="button" class="fa-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier">${pencil}</button>
                <button type="button" class="fa-icon print" data-act="imprimer" data-id="${row.id}" title="Imprimer">${printer}</button>
                <button type="button" class="fa-icon pdf" data-act="pdf" data-id="${row.id}" title="PDF">${file}</button>
                <button type="button" class="fa-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer">${trash}</button>
            </div></td>
        </tr>`).join('');
    }
    function printRow(row) {
        const lines = (row.items || []).map((item) => `<tr><td>${esc(item.article_ref || '—')}</td><td>${esc(item.description || '—')}</td><td>${esc(item.quantity)}</td><td>${esc(moneyDot(item.unit_price))}</td><td>${esc(item.remise)}%</td><td><strong>${esc(moneyDot(item.total))}</strong></td></tr>`).join('') || '<tr><td colspan="6">—</td></tr>';
        const html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Facture ${esc(row.reference)}</title>
            <style>body{font-family:Arial,sans-serif;padding:32px;color:#1e293b}h1{font-size:22px}table{width:100%;border-collapse:collapse;margin-top:12px}th,td{border:1px solid #e2e8f0;padding:8px;font-size:12px;text-align:center}th{background:#f8fafc}</style></head><body>
            <h1>AUTO SIHAP — Facture Achat ${esc(row.reference)}</h1>
            <table><tr><th>Date</th><td>${esc(row.invoice_date)}</td><th>Fournisseur</th><td>${esc(row.fournisseur)}</td></tr>
            <tr><th>Réf</th><td>${esc(row.reference)}</td><th>Ice</th><td>${esc(row.ice || '—')}</td></tr>
            <tr><th>Total HT</th><td>${esc(moneyFr(row.total_ht))}</td><th>TVA</th><td>${esc(moneyFr(row.tva))}</td></tr>
            <tr><th>Total TTC</th><td colspan="3"><strong>${esc(moneyFr(row.total_ttc))}</strong></td></tr></table>
            <table><thead><tr><th>Réf</th><th>Désignation</th><th>Qté</th><th>Prix/U</th><th>Remise</th><th>Sous-Total TTC</th></tr></thead><tbody>${lines}</tbody></table>
            </body></html>`;
        const win = window.open('', '_blank', 'width=900,height=700');
        if (!win) return;
        win.document.write(html);
        win.document.close();
        win.focus();
        setTimeout(() => win.print(), 300);
    }
    function openView(row) {
        viewRow = row;
        document.getElementById('faViewTitle').textContent = row.reference || '—';
        const fields = [
            ['Réf', row.reference], ['Date', row.invoice_date], ['Fournisseur', row.fournisseur], ['Ice', row.ice],
            ['Total HT', moneyFr(row.total_ht)], ['TVA', moneyFr(row.tva)], ['Total TTC', moneyFr(row.total_ttc)],
        ];
        const lines = (row.items || []).map((item) => `<div class="fa-view-line"><strong>${esc([item.article_ref, item.description].filter(Boolean).join(' — ') || '—')}</strong><div>${esc(item.quantity)} × ${esc(moneyDot(item.unit_price))}${Number(item.remise) > 0 ? ` (−${esc(item.remise)}%)` : ''} = <strong>${esc(moneyDot(item.total))}</strong></div></div>`).join('');
        document.getElementById('faViewBody').innerHTML = fields.map(([label, value]) => `<div class="fa-view-row"><span>${esc(label)}</span><strong>${esc(value || '—')}</strong></div>`).join('') + lines;
        view.hidden = false;
    }
    async function load() {
        const response = await fetch('/factures-achats', { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Chargement impossible.');
        const payload = await response.json();
        meta = payload.meta || meta;
        rows = payload.data || [];
        fillNames();
        render(filteredRows());
    }
    function openModal(row) {
        resetForm(row || null);
        modal.hidden = false;
        const sheet = modal.querySelector('.fa-sheet');
        if (sheet) sheet.style.marginTop = '';
        modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function closeModal() { modal.hidden = true; editingId = null; }
    function supplierChoices(query) {
        const q = query.trim().toLowerCase();
        const list = meta.suppliers || [];
        if (!q) return list;
        return list.filter((item) => String(item.name || '').toLowerCase().includes(q));
    }
    function showSupplierMenu() {
        const items = supplierChoices(supplier.value);
        if (!items.length) { supplierMenu.hidden = true; supplierMenu.innerHTML = ''; return; }
        supplierMenu.innerHTML = items.map((item) => `<button type="button" data-id="${item.id}">${esc(item.name)}</button>`).join('');
        supplierMenu.hidden = false;
    }
    function pickSupplier(item) {
        supplier.value = item?.name || '';
        supplier.dataset.id = item?.id ? String(item.id) : '';
        ice.value = String(item?.ice || '').replace(/\D/g, '').slice(0, 15);
        supplierMenu.hidden = true;
    }
    supplier.addEventListener('focus', showSupplierMenu);
    supplier.addEventListener('input', () => {
        const typed = supplier.value.trim().toLowerCase();
        const exact = (meta.suppliers || []).find((item) => String(item.name || '').trim().toLowerCase() === typed);
        supplier.dataset.id = exact ? String(exact.id) : '';
        showSupplierMenu();
    });
    supplierMenu.addEventListener('click', (event) => {
        const button = event.target.closest('[data-id]');
        if (!button) return;
        const item = (meta.suppliers || []).find((entry) => String(entry.id) === button.dataset.id);
        if (item) pickSupplier(item);
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.fa-supplier')) supplierMenu.hidden = true;
    });
    ice.addEventListener('input', () => { ice.value = ice.value.replace(/\D/g, '').slice(0, 15); });
    document.getElementById('faAdd').addEventListener('click', () => openModal(null));
    document.getElementById('faClose').addEventListener('click', closeModal);
    document.getElementById('faCloseX').addEventListener('click', closeModal);
    document.getElementById('faRefresh').addEventListener('click', () => load().catch((error) => showError(error.message)));
    ['faFrom', 'faTo'].forEach((id) => {
        document.getElementById(id).addEventListener('input', (event) => {
            maskDate(event.target);
            render(filteredRows());
        });
    });
    ['faMonth', 'faName'].forEach((id) => {
        document.getElementById(id).addEventListener('input', () => render(filteredRows()));
        document.getElementById(id).addEventListener('change', () => render(filteredRows()));
    });
    document.getElementById('faAddLine').addEventListener('click', () => { linesBody.appendChild(lineRow()); recalc(); });
    document.getElementById('faViewClose').addEventListener('click', () => { view.hidden = true; });
    document.getElementById('faViewPrint').addEventListener('click', () => { if (viewRow) printRow(viewRow); });
    document.getElementById('faViewPdf').addEventListener('click', () => { if (viewRow) printRow(viewRow); });
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
    view.addEventListener('click', (event) => { if (event.target === view) view.hidden = true; });
    linesBody.addEventListener('input', recalc);
    linesBody.addEventListener('change', recalc);
    linesBody.addEventListener('focusout', (event) => {
        const price = event.target.closest('[data-f="price"]');
        if (!price || !String(price.value).trim()) return;
        price.value = moneyDot(price.value);
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
        const row = rows.find((item) => String(item.id) === button.dataset.id);
        if (!row) return;
        if (button.dataset.act === 'voir') openView(row);
        if (button.dataset.act === 'modifier') openModal(row);
        if (button.dataset.act === 'imprimer' || button.dataset.act === 'pdf') printRow(row);
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer la facture ${row.reference} ?`)) return;
            const response = await fetch(`/factures-achats/${row.id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            if (!response.ok) { showError('Impossible de supprimer cette facture.'); return; }
            await load();
        }
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError('');
        const save = document.getElementById('faSave');
        linesBody.querySelectorAll('[data-f="price"]').forEach((price) => {
            if (String(price.value).trim()) price.value = moneyDot(price.value);
        });
        const items = [...linesBody.querySelectorAll('tr')].map((row) => ({
            article_ref: row.querySelector('[data-f="ref"]').value.trim(),
            description: row.querySelector('[data-f="desc"]').value.trim(),
            quantity: Number(String(row.querySelector('[data-f="qty"]').value).replace(',', '.')),
            unit_price: Number(String(row.querySelector('[data-f="price"]').value).replace(',', '.')),
            remise: Number(String(row.querySelector('[data-f="remise"]').value).replace(',', '.')) || 0,
        }));
        const supplierName = supplier.value.trim();
        if (!supplierName) { showError('Saisissez un fournisseur.'); return; }
        const known = (meta.suppliers || []).find((item) => String(item.name || '').trim().toLowerCase() === supplierName.toLowerCase());
        const supplierId = supplier.dataset.id || (known ? String(known.id) : '');
        if (!/^\d{15}$/.test(ice.value)) { showError('L’ICE doit contenir 15 chiffres.'); return; }
        if (!items.length || items.some((item) => !item.description)) { showError('Chaque ligne doit avoir une désignation.'); return; }
        const totalTtc = recalc();
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/factures-achats/${editingId}` : '/factures-achats', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    supplier_id: supplierId || null,
                    supplier_name: supplierName,
                    ice: ice.value,
                    invoice_date: document.getElementById('faDate').value,
                    total_ttc: totalTtc,
                    items,
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
    load().catch((error) => { body.innerHTML = `<tr><td colspan="8">${esc(error.message)}</td></tr>`; });
})();
</script>
