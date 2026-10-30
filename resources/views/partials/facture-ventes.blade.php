<section class="panel" id="panel-fa-vente">
    <div class="content-head fa-head">
        <div>
            <h1>Facture Ventes</h1>
            <p class="fa-sub">Saisie des factures clients</p>
        </div>
        <button type="button" class="fa-nouveau" id="fvAdd">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
            Nouveau
        </button>
    </div>

    <div class="fa-filters">
        <div class="fa-filter-side">
            <label>De<input type="text" id="fvFrom" inputmode="numeric" maxlength="10" placeholder="JJ/MM/AAAA" autocomplete="off"></label>
            <label>À<input type="text" id="fvTo" inputmode="numeric" maxlength="10" placeholder="JJ/MM/AAAA" autocomplete="off"></label>
            <label>Mois
                <select id="fvMonth" data-keep-native="1">
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
            <label>Nom client
                <select id="fvName" class="fa-name" data-keep-native="1">
                    <option value="">Tous</option>
                </select>
            </label>
        </div>
        <div class="fa-stats">
            <div class="fa-stat ventes"><span>Ventes</span><strong id="fvSumTtc">0,00</strong></div>
        </div>
    </div>

    <div class="fa-card">
        <div class="fa-list-bar">
            <h3>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                Liste des factures
            </h3>
            <button type="button" id="fvRefresh" title="Actualiser">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.2-5.8M21 4v5h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        <div class="fa-scroll">
            <table class="fa-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Réf</th>
                        <th>ID Clt</th>
                        <th>Nom client</th>
                        <th>Ice</th>
                        <th>Total HT</th>
                        <th>TVA</th>
                        <th>Total TTC</th>
                        <th>Solde</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="fvBody">
                    <tr><td colspan="10">Aucune facture enregistrée</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fa-window" id="fvModal" hidden>
        <div class="fa-sheet" role="dialog" aria-modal="true" aria-labelledby="fvModalTitle">
            <div class="fa-sheet-head">
                <h3 id="fvModalTitle">Nouvelle facture vente</h3>
                <button type="button" class="fa-x" id="fvCloseX" title="Fermer">×</button>
            </div>
            <form id="fvForm" class="fa-form" autocomplete="off">
                <div class="fa-error" id="fvError" hidden></div>
                <div class="fa-grid">
                    <div class="fa-field">
                        <label for="fvDate">Date</label>
                        <input id="fvDate" type="date" required>
                    </div>
                    <div class="fa-field fa-field-ref">
                        <label for="fvRef">Réf</label>
                        <input id="fvRef" readonly>
                    </div>
                    <div class="fa-field fa-field-ref">
                        <label for="fvClientId">ID Clt</label>
                        <input id="fvClientId" readonly tabindex="-1">
                    </div>
                    <div class="fa-field fa-supplier">
                        <label for="fvSupplier">Nom client</label>
                        <input id="fvSupplier" required maxlength="255" autocomplete="off" placeholder="Liste ou saisie">
                        <div class="fa-supplier-menu" id="fvSupplierMenu" hidden></div>
                    </div>
                    <div class="fa-field">
                        <label for="fvIce">Ice</label>
                        <input id="fvIce" inputmode="numeric" maxlength="15" pattern="\d{15}" required placeholder="15 chiffres">
                    </div>
                </div>

                <div class="fa-lines-head">
                    <p>Lignes facture</p>
                    <div>
                        <button type="button" class="fa-add-line" id="fvAddLine">+ Ligne</button>
                    </div>
                </div>
                <div class="fa-lines-wrap">
                    <table class="fa-lines">
                        <colgroup>
                            <col class="c-ref">
                            <col class="c-achat">
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
                                <th>Prix achat</th>
                                <th>Désignation</th>
                                <th>Qté</th>
                                <th>Prix/U</th>
                                <th>Remise</th>
                                <th>Sous-Total TTC</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="fvLines"></tbody>
                    </table>
                </div>
                <div class="fa-totals">
                    <div class="ht"><small>Montant HT</small><strong id="fvHt">0.00</strong></div>
                    <div class="tva"><small>TVA (20%)</small><strong id="fvTva">0.00</strong></div>
                    <div class="ttc"><small>Montant TTC</small><strong id="fvTtc">0.00</strong></div>
                </div>
                <div class="fa-actions">
                    <button type="button" class="fa-btn ghost" id="fvClose">Fermer</button>
                    <button type="submit" class="fa-btn primary" id="fvSave">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fa-window" id="fvView" hidden>
        <div class="fa-sheet fa-view" role="dialog" aria-modal="true">
            <div class="fa-sheet-head">
                <div>
                    <p class="fa-kicker">Facture Vente</p>
                    <h3 id="fvViewTitle">—</h3>
                </div>
                <button type="button" class="fa-x" id="fvViewClose" title="Fermer">×</button>
            </div>
            <div class="fa-view-body" id="fvViewBody"></div>
            <div class="fa-actions">
                <button type="button" class="fa-btn ghost" id="fvViewPrint">Imprimer</button>
                <button type="button" class="fa-btn primary" id="fvViewPdf">PDF</button>
            </div>
        </div>
    </div>
</section>
<style>
    #panel-fa-vente .fa-sub { text-transform: none; }
    #panel-fa-vente .fa-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
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
    .fv-solde { font-weight: 800; font-variant-numeric: tabular-nums; }
    .fv-solde.due { color: #f87171; }
    .fv-solde.partial { color: #60a5fa; }
    .fv-solde.paid { color: #4ade80; }
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
    .fa-icon.pay:hover { color: #34d399; }
    .fa-icon.del:hover { color: #f87171; }
    .fa-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: flex-start; justify-content: center; padding: 1.25rem;
        background: rgba(0,0,0,0.55); overflow: auto;
    }
    .shell.sidebar-hidden .fa-window, .fa-window.at-edge { left: 0; }
    .fa-window[hidden] { display: none !important; }
    #fvModal.fa-window {
        position: fixed; top: 0; right: 0; bottom: 0; left: 272px; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        margin: 0; padding: 1.25rem; background: rgba(0,0,0,0.45); overflow: auto;
    }
    .shell.sidebar-hidden #fvModal.fa-window { left: 0; }
    #fvModal.fa-window[hidden] { display: none !important; }
    .fa-sheet {
        width: min(920px, 100%); margin-top: 1.5rem; border-radius: 16px; overflow: hidden;
        background: #0c0c0e; border: 1px solid #4a1824; color: #f4f5f6;
        box-shadow: 0 24px 60px rgba(0,0,0,0.45);
    }
    #fvModal .fa-sheet { width: min(1220px, 100%); margin: auto; }
    .fa-view { width: min(860px, 100%); margin: auto; }
    #fvView.fa-window { align-items: center; }
    .fa-view-body { padding: 0.9rem 1.15rem 1rem; max-height: min(68vh, 640px); overflow: auto; }
    .fa-view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 1.4rem; }
    .fa-view-row { display: flex; justify-content: space-between; gap: 1rem; padding: 0.42rem 0; border-bottom: 1px solid #1e293b; font-size: 0.84rem; }
    .fa-view-row span { color: #94a3b8; }
    .fa-view-row strong { text-align: right; }
    .fa-view-totals { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.5rem; margin-top: 0.85rem; }
    .fa-view-totals div { border: 1px solid #334155; border-radius: 10px; padding: 0.45rem 0.5rem; text-align: center; background: #1e293b; }
    .fa-view-totals small { display: block; color: #94a3b8; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.05em; }
    .fa-view-totals strong { display: block; margin-top: 0.15rem; font-size: 0.92rem; }
    .fa-view-table { width: 100%; border-collapse: collapse; margin-top: 0.85rem; }
    .fa-view-table th, .fa-view-table td { padding: 0.48rem 0.45rem; border-bottom: 1px solid #1e293b; font-size: 0.78rem; text-align: center; vertical-align: middle; }
    .fa-view-table th { color: #94a3b8; font-size: 0.66rem; letter-spacing: 0.05em; font-weight: 800; }
    .fa-view-table td.left { text-align: left; }
    .fa-sheet-head {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.9rem 1.15rem; color: #fff;
        background: linear-gradient(90deg, #0a0a0c, #5c1220 58%, #8f2436);
    }
    .fa-sheet-head h3 { margin: 0; font-size: 0.92rem; letter-spacing: 0.06em; text-transform: uppercase; }
    .fa-kicker { margin: 0; font-size: 0.62rem; letter-spacing: 0.12em; text-transform: uppercase; color: #e7c4c8; }
    .fa-form { padding: 1rem 1.15rem 0.4rem; }
    .fa-error { margin-bottom: 0.75rem; padding: 0.65rem 0.8rem; border-radius: 10px; background: rgba(127,29,29,0.35); color: #fecaca; border: 1px solid #7f1d1d; font-size: 0.82rem; }
    .fa-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(86px, 0.42fr) minmax(72px, 0.38fr) minmax(0, 1.35fr) minmax(130px, 1fr); gap: 0.75rem 0.85rem; }
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
    .fa-lines .c-achat { width: 92px; }
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
    #panel-fa-vente .fa-lines select {
        width: 100%; height: 32px; box-sizing: border-box; border-radius: 6px; border: 1px solid #475569;
        background: #1e293b; color: #fff; text-align: center; font-size: 0.75rem; font-family: inherit;
    }
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
    @media (max-width: 900px) {
        .fa-window, #fvModal.fa-window { left: 0; }
        .fa-grid { grid-template-columns: minmax(0, 1fr) minmax(86px, 0.42fr) minmax(72px, 0.38fr) minmax(0, 1.35fr) minmax(110px, 1fr); }
        .fa-span-2 { grid-column: span 2; }
        .fa-stats { grid-template-columns: 1fr; }
        .fa-view-grid, .fa-view-totals { grid-template-columns: 1fr 1fr; }
    }
    [data-theme="light"] .fa-stat { color: #3a1218; box-shadow: 0 8px 20px rgba(15, 31, 53, 0.06); }
    [data-theme="light"] .fa-stat.slate { background: linear-gradient(135deg, #ffffff, #f4f4f5); }
    [data-theme="light"] .fa-stat.blue { background: linear-gradient(135deg, #fff, #f8e8eb); }
    [data-theme="light"] .fa-stat.green { background: linear-gradient(135deg, #f8e8eb, #f3d5db); }
    [data-theme="light"] .fa-card {
        background: #fff;
        border-color: rgba(143, 36, 54, 0.22);
        box-shadow: 0 8px 28px rgba(15, 31, 53, 0.06);
    }
    [data-theme="light"] .fa-list-bar,
    [data-theme="light"] .fa-sheet-head {
        color: #5c1220;
        background: linear-gradient(90deg, #fff, #f8e8eb 58%, #f3d5db);
    }
    [data-theme="light"] .fa-kicker { color: #8f2436; }
    [data-theme="light"] .fa-list-bar button,
    [data-theme="light"] .fa-x { color: rgba(92, 18, 32, 0.72); }
    [data-theme="light"] .fa-list-bar button:hover,
    [data-theme="light"] .fa-x:hover { background: rgba(143, 36, 54, 0.1); color: #5c1220; }
    [data-theme="light"] .fa-table { color: #1a1214; }
    [data-theme="light"] .fa-table th { color: #5c1220; background: #f7eef0; }
    [data-theme="light"] .fa-table td { border-top-color: rgba(143, 36, 54, 0.14); }
    [data-theme="light"] .fa-table tbody tr:hover td { background: rgba(225, 6, 0, 0.06); }
    [data-theme="light"] .fv-solde.due { color: #dc2626; }
    [data-theme="light"] .fv-solde.partial { color: #1d4ed8; }
    [data-theme="light"] .fv-solde.paid { color: #15803d; }
    [data-theme="light"] .fa-ref { color: #8f2436; }
    [data-theme="light"] .fa-ttc { color: #6e1224; }
    [data-theme="light"] .fa-icon { color: #64748b; }
    [data-theme="light"] .fa-icon:hover { background: rgba(143, 36, 54, 0.08); color: #5c1220; }
    [data-theme="light"] .fa-sheet {
        background: #fff;
        border-color: #e7c9cf;
        color: #1a1214;
        box-shadow: 0 24px 60px rgba(15, 31, 53, 0.12);
    }
    [data-theme="light"] .fa-field label,
    [data-theme="light"] .fa-lines-head p,
    [data-theme="light"] .fa-totals small,
    [data-theme="light"] .fa-view-row span { color: #7a5a62; }
    [data-theme="light"] .fa-field input,
    [data-theme="light"] .fa-field select,
    [data-theme="light"] .fa-lines input,
    [data-theme="light"] .fa-lines select,
    [data-theme="light"] .fa-sheet .evopro-combo-trigger {
        background: #fff;
        color: #1a1214;
        border-color: #e4c5cb;
    }
    [data-theme="light"] .fa-field input[readonly],
    [data-theme="light"] .fa-lines input[readonly] { background: #f7f3f4; color: #3a1218; }
    [data-theme="light"] .fa-lines th { background: #f7eef0; color: #5c1220; }
    [data-theme="light"] .fa-lines td { color: #1a1214; border-top-color: #f0dde1; }
    [data-theme="light"] .fa-lines-wrap,
    [data-theme="light"] .fa-view-row,
    [data-theme="light"] .fa-view-table th,
    [data-theme="light"] .fa-view-table td,
    [data-theme="light"] .fa-view .fa-actions { border-color: #f0dde1; }
    [data-theme="light"] .fa-view-totals div { background: #fff; border-color: #e7c9cf; }
    [data-theme="light"] .fa-view-totals small,
    [data-theme="light"] .fa-view-table th { color: #8f2436; }
    [data-theme="light"] .fa-sheet .evopro-combo-menu,
    [data-theme="light"] .fa-supplier-menu { background: #fff; border-color: #e4c5cb; }
    [data-theme="light"] .fa-sheet .evopro-combo-option,
    [data-theme="light"] .fa-supplier-menu button { color: #1a1214; }
    [data-theme="light"] .fa-totals div { background: #fff; border-color: #e4c5cb; }
    [data-theme="light"] .fa-totals .tva { background: #f8e8eb; border-color: #e4c5cb; }
    [data-theme="light"] .fa-totals .tva small,
    [data-theme="light"] .fa-totals .tva strong { color: #5c1220; }
    [data-theme="light"] .fa-totals .ttc { background: #5c1220; border-color: #5c1220; }
    [data-theme="light"] .fa-btn.ghost { background: #fff; color: #5c1220; border-color: #e4c5cb; }
    [data-theme="light"] .fr-lines input:disabled,
    [data-theme="light"] .fr-lines select:disabled { background: #f3f4f6; color: #94a3b8; border-color: #e2e8f0; }
    .fa-filters { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1rem; }
    .fa-filter-side { display: flex; flex: 1; align-items: center; gap: 0.75rem; min-width: 0; }
    .fa-filter-side label:last-child { flex: 0 0 auto; }
    .fa-filters .fa-stats { margin: 0 0 0 auto; flex: 0 0 auto; }
    .fa-filters .fa-stat { padding: 0.4rem 0.85rem; min-width: 168px; text-align: center; }
    .fa-filters .fa-stat strong { margin-top: 0.05rem; font-size: 1.05rem; }
    .fa-filters label { display: flex; flex-direction: row; align-items: center; gap: 0.4rem; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.08em; color: #e7d5d8; white-space: nowrap; }
    .fa-filters input, .fa-filters select {
        height: 38px; width: 132px; border-radius: 8px; border: 1px solid rgba(143, 36, 54, 0.45);
        background: #0a0a0c; color: #f4f5f6; font-family: inherit; font-size: 0.78rem; padding: 0 0.55rem;
    }
    .fa-filters select.fa-name { width: 360px; }
    .fa-stats { grid-template-columns: minmax(0, 280px); }
    .fa-stat.achats { background: linear-gradient(135deg, #e10600, #9b0400); color: #fff; }
    .fa-stat.ventes { background: linear-gradient(135deg, #16a34a, #14532d); color: #fff; }
    [data-theme="light"] .fa-filters label { color: #5c1220; }
    [data-theme="light"] .fa-filters input,
    [data-theme="light"] .fa-filters select { background: #fff; color: #1a1214; border-color: #e4c5cb; }
    [data-theme="light"] .fa-stat.achats { background: linear-gradient(135deg, #e10600, #9b0400); color: #fff; }
    [data-theme="light"] .fa-stat.ventes { background: linear-gradient(135deg, #16a34a, #14532d); color: #fff; }
</style>
<script>
(function () {
    const modal = document.getElementById('fvModal');
    const form = document.getElementById('fvForm');
    const view = document.getElementById('fvView');
    if (!modal || !form || !view) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const body = document.getElementById('fvBody');
    const linesBody = document.getElementById('fvLines');
    const errorBox = document.getElementById('fvError');
    const supplier = document.getElementById('fvSupplier');
    const supplierMenu = document.getElementById('fvSupplierMenu');
    const ice = document.getElementById('fvIce');
    let meta = { next_ref: 'FV-0001', date: '', clients: [], stock: [], count: 0, total_ht: 0, total_ttc: 0 };
    let rows = [];
    let editingId = null;
    let viewRow = null;

    const eye = '<svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const pencil = '<svg viewBox="0 0 24 24"><path d="M4 20h4l10-10-4-4L4 16v4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const printer = '<svg viewBox="0 0 24 24"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5h20v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
    const file = '<svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M14 3v5h5" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
    const trash = '<svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M8 7l1 13h6l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    const payer = '<svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M3 10h18M7 15h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

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
        return Math.round(qty * price * (1 - remise / 100) * 100) / 100;
    }
    function refreshLine(row) {
        const option = row.querySelector('[data-f="desc"]')?.selectedOptions[0];
        const achatInput = row.querySelector('[data-f="achat"]');
        const priceInput = row.querySelector('[data-f="price"]');
        if (!option || !option.value) {
            achatInput.value = '';
            priceInput.value = '';
            return;
        }
        const achat = parseNum(option.dataset.achat);
        achatInput.value = moneyDot(achat);
        priceInput.value = moneyDot(achat);
    }
    function stockKey(item) {
        const ref = String(item?.article_ref || item?.reference || '').trim().toLowerCase();
        if (ref) return 'ref:' + ref;
        return 'desc:' + String(item?.description || item?.designation || '').trim().toLowerCase();
    }
    function stockChoices(extra) {
        const list = [...(meta.stock || [])];
        if (extra && (extra.article_ref || extra.description)) {
            const key = stockKey(extra);
            if (!list.some((item) => item.key === key)) {
                list.push({
                    key,
                    reference: extra.article_ref || '',
                    designation: extra.description || '',
                    purchase_price: extra.purchase_price || 0,
                });
            }
        }
        return list;
    }
    function stockOptions(list, selected, field) {
        const options = list.map((item) => {
            const label = field === 'ref'
                ? (item.reference || '—')
                : `${moneyDot(item.purchase_price || 0)}  ${item.designation || '—'}`;
            return `<option value="${esc(item.key)}" data-ref="${esc(item.reference)}" data-desc="${esc(item.designation)}" data-achat="${esc(item.purchase_price || 0)}"${item.key === selected ? ' selected' : ''}>${esc(label)}</option>`;
        }).join('');
        const placeholder = field === 'ref' ? 'Réf' : 'Désignation';
        return `<option value="">${placeholder}</option>${options}`;
    }
    function lineRow(item) {
        const tr = document.createElement('tr');
        const selected = item?.article_ref || item?.description ? stockKey(item) : '';
        const choices = stockChoices(item);
        tr.innerHTML = `<td><select data-f="ref" data-keep-native="1">${stockOptions(choices, selected, 'ref')}</select></td>
            <td><input data-f="achat" readonly tabindex="-1" value=""></td>
            <td><select data-f="desc" data-keep-native="1" required>${stockOptions(choices, selected, 'desc')}</select></td>
            <td><input data-f="qty" required inputmode="decimal" value="${esc(item?.quantity ?? 1)}"></td>
            <td><input data-f="price" required readonly tabindex="-1" inputmode="decimal" placeholder="0.00" value=""></td>
            <td><div class="fa-remise"><input data-f="remise" inputmode="decimal" value="${esc(item?.remise ?? 0)}"><span>%</span></div></td>
            <td><input data-f="sub" readonly tabindex="-1" value="0.00"></td>
            <td><button type="button" class="fa-line-x" data-remove title="Retirer">×</button></td>`;
        refreshLine(tr);
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
        document.getElementById('fvHt').textContent = moneyDot(ht);
        document.getElementById('fvTva').textContent = moneyDot(tva);
        document.getElementById('fvTtc').textContent = moneyDot(ttc);
        return ttc;
    }
    function resetForm(row) {
        editingId = row?.id || null;
        document.getElementById('fvModalTitle').textContent = row ? `Modifier ${row.reference}` : 'Nouvelle facture vente';
        document.getElementById('fvDate').value = row?.invoice_date_raw || meta.date || '';
        document.getElementById('fvRef').value = row?.reference || meta.next_ref || 'FV-0001';
        supplier.value = row?.client && row.client !== '—' ? row.client : '';
        supplier.dataset.id = row?.client_id ? String(row.client_id) : '';
        document.getElementById('fvClientId').value = clientCode(supplier.dataset.id);
        supplierMenu.hidden = true;
        const selected = (meta.clients || []).find((item) => String(item.id) === String(supplier.dataset.id || ''));
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
    function fillNames() {
        const select = document.getElementById('fvName');
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
        (meta.clients || []).forEach((item) => add(item.name));
        rows.forEach((row) => add(row.client));
        names.sort((a, b) => a.localeCompare(b, 'fr'));
        select.innerHTML = '<option value="">Tous</option>' + names.map((name) => `<option value="${esc(name)}">${esc(name)}</option>`).join('');
        select.value = names.some((name) => name.toLowerCase() === current.toLowerCase()) ? current : '';
    }
    function filteredRows() {
        const from = isoDate(document.getElementById('fvFrom').value);
        const to = isoDate(document.getElementById('fvTo').value);
        const month = document.getElementById('fvMonth').value;
        const name = document.getElementById('fvName').value.trim().toLowerCase();
        return rows.filter((row) => {
            const raw = String(row.invoice_date_raw || '');
            if (from && raw < from) return false;
            if (to && raw > to) return false;
            if (month && raw.slice(5, 7) !== month) return false;
            if (name && String(row.client || '').trim().toLowerCase() !== name) return false;
            return true;
        });
    }
    function clientCode(id) {
        const n = Number(id);
        if (!n) return '';
        return 'CLT' + String(n).padStart(4, '0');
    }
    function soldeClass(row) {
        const balance = Number(row.balance);
        const paid = Number(row.paid) || 0;
        if (balance <= 0.009) return 'paid';
        if (paid > 0.009) return 'partial';
        return 'due';
    }
    function render(list) {
        const total = list.reduce((sum, row) => sum + (Number(row.total_ttc) || 0), 0);
        document.getElementById('fvSumTtc').textContent = moneyFr(total);
        if (!list.length) {
            body.innerHTML = `<tr><td colspan="10">${rows.length ? 'Aucune facture pour cette recherche.' : 'Aucune facture enregistrée'}</td></tr>`;
            return;
        }
        body.innerHTML = list.map((row) => `<tr>
            <td>${esc(row.invoice_date)}</td>
            <td class="fa-ref">${esc(row.reference)}</td>
            <td>${esc(clientCode(row.client_id) || '—')}</td>
            <td>${esc(row.client)}</td>
            <td>${esc(row.ice) || '—'}</td>
            <td>${esc(moneyFr(row.total_ht))}</td>
            <td>${esc(moneyFr(row.tva))}</td>
            <td class="fa-ttc">${esc(moneyFr(row.total_ttc))}</td>
            <td class="fv-solde ${soldeClass(row)}">${esc(moneyFr(row.balance))}</td>
            <td><div class="fa-row-actions">
                <button type="button" class="fa-icon see" data-act="voir" data-id="${row.id}" title="Voir">${eye}</button>
                <button type="button" class="fa-icon edit" data-act="modifier" data-id="${row.id}" title="Modifier">${pencil}</button>
                <button type="button" class="fa-icon print" data-act="imprimer" data-id="${row.id}" title="Imprimer">${printer}</button>
                <button type="button" class="fa-icon pdf" data-act="pdf" data-id="${row.id}" title="PDF">${file}</button>
                <button type="button" class="fa-icon pay" data-act="payer" data-id="${row.id}" title="Payer">${payer}</button>
                <button type="button" class="fa-icon del" data-act="supprimer" data-id="${row.id}" title="Supprimer">${trash}</button>
            </div></td>
        </tr>`).join('');
    }
    function frenchInt(value) {
        const n = Math.floor(Math.abs(Number(value) || 0));
        const units = ['', 'UN', 'DEUX', 'TROIS', 'QUATRE', 'CINQ', 'SIX', 'SEPT', 'HUIT', 'NEUF', 'DIX', 'ONZE', 'DOUZE', 'TREIZE', 'QUATORZE', 'QUINZE', 'SEIZE'];
        const under100 = (x) => {
            if (x < 17) return units[x];
            if (x < 20) return 'DIX-' + units[x - 10];
            const tens = ['', '', 'VINGT', 'TRENTE', 'QUARANTE', 'CINQUANTE', 'SOIXANTE', 'SOIXANTE', 'QUATRE-VINGT', 'QUATRE-VINGT'];
            const t = Math.floor(x / 10);
            const r = x % 10;
            if (t === 7 || t === 9) {
                if (r === 0) return t === 7 ? 'SOIXANTE-DIX' : 'QUATRE-VINGT-DIX';
                if (r === 1 && t === 7) return 'SOIXANTE ET ONZE';
                return tens[t] + '-' + under100(10 + r);
            }
            if (r === 0) return t === 8 ? 'QUATRE-VINGTS' : tens[t];
            if (r === 1 && t !== 8) return tens[t] + ' ET UN';
            return tens[t] + '-' + units[r];
        };
        const under1000 = (x) => {
            if (x < 100) return under100(x);
            const c = Math.floor(x / 100);
            const r = x % 100;
            const cent = (c === 1 ? 'CENT' : units[c] + ' CENT') + (r === 0 && c > 1 ? 'S' : '');
            return r ? cent + ' ' + under100(r) : cent;
        };
        if (!n) return 'ZÉRO';
        const millions = Math.floor(n / 1000000);
        const thousands = Math.floor((n % 1000000) / 1000);
        const rest = n % 1000;
        const parts = [];
        if (millions) parts.push(millions === 1 ? 'UN MILLION' : under1000(millions) + ' MILLIONS');
        if (thousands) parts.push(thousands === 1 ? 'MILLE' : under1000(thousands) + ' MILLE');
        if (rest) parts.push(under1000(rest));
        return parts.join(' ');
    }
    function amountWords(value) {
        const centsTotal = Math.round((Number(value) || 0) * 100);
        const dirhams = Math.floor(centsTotal / 100);
        const cents = centsTotal % 100;
        let text = frenchInt(dirhams) + ' DH';
        if (cents) text += ' ' + String(cents).padStart(2, '0') + ' CTS';
        return text;
    }
    function printRow(row) {
        const company = window.ficheSteData || {};
        const companyTitle = String(company.nom_societe || 'STE AUTO-SIHAP').trim() || 'STE AUTO-SIHAP';
        const fallbackLogo = @json(asset('images/auto-sihap-marque.png'));
        const siteRoot = @json(rtrim(url('/'), '/'));
        const logoSrc = company.logo ? `${siteRoot}/${String(company.logo).replace(/^\/+/, '')}` : fallbackLogo;
        const brandLines = [company.statue, company.activite].map((value) => String(value || '').trim()).filter(Boolean).map((value) => `<p>${esc(value)}</p>`).join('');
        const show = (value) => esc(String(value || '').trim() || '—');
        const raw = (value) => esc(String(value || '').trim());
        const qtyText = (value) => {
            const n = Number(value) || 0;
            return Number.isInteger(n) ? String(n) : String(n).replace('.', ',');
        };
        const clientLines = [row.client, row.client_address, row.client_city, row.ice].map((value) => String(value || '').trim()).filter(Boolean);
        const lines = (row.items || []).map((item) => {
            const qty = Number(item.quantity) || 0;
            const remise = Number(item.remise) || 0;
            const purchase = Number(item.purchase_price) || 0;
            const saleTtc = Number(item.unit_price) || 0;
            const lineTtc = Number(item.total) || qty * saleTtc;
            const prixTtc = purchase > 0
                ? purchase
                : (remise > 0 && remise < 100 ? saleTtc / (1 - remise / 100) : saleTtc);
            return `<tr>
                <td class="ref">${esc(item.article_ref || '—')}</td>
                <td class="desc">${esc(item.description || '—')}</td>
                <td class="qty">${esc(qtyText(qty))}</td>
                <td class="num">${esc(moneyFr(prixTtc))}</td>
                <td class="num">${esc(moneyFr(remise))}</td>
                <td class="num">${esc(moneyFr(lineTtc / 1.2))}</td>
            </tr>`;
        }).join('') || '<tr><td colspan="6">—</td></tr>';
        const html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Facture ${esc(row.reference)}</title>
            <style>
                @page { size: A4; margin: 12mm 12mm 18mm; }
                * { box-sizing: border-box; }
                html, body { margin: 0; padding: 0; }
                body { font-family: Arial, Helvetica, sans-serif; color: #1a1a1a; padding: 16px 22px 0; min-height: 100vh; }
                .sheet { position: relative; z-index: 1; min-height: calc(100vh - 118px); padding-bottom: 12px; }
                .watermark { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; overflow: hidden; pointer-events: none; }
                .watermark span { font-size: 74px; font-weight: 800; letter-spacing: 0.04em; line-height: 1; white-space: nowrap; color: rgba(225, 6, 0, 0.22); transform: rotate(-28deg); text-transform: uppercase; }
                .banner { width: 100%; display: flex; align-items: center; justify-content: flex-start; gap: 16px; margin: 0 0 12px; padding: 0 0 8px; background: transparent; border-bottom: 2px solid #e10600; }
                .banner img { display: block; width: 148px; height: auto; flex: 0 0 auto; background: transparent; }
                .banner h1 { margin: 0; color: #e10600; font-size: 28px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; }
                .banner .brand p { margin: 2px 0 0; color: #5c1220; font-size: 12px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
                .client { display: inline-block; min-width: 260px; max-width: 420px; border: 1px solid #8d98a3; padding: 8px 12px; font-size: 12px; line-height: 1.4; text-transform: uppercase; }
                .meta, .lines, .totals { width: 100%; border-collapse: collapse; }
                .meta { margin-top: 12px; }
                .meta td { border: 1px solid #6d7580; padding: 5px 8px; font-size: 12px; }
                .meta b { margin-right: 8px; }
                .detail-title { margin: 14px 0 6px; color: #1a1a1a; font-size: 13px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; text-decoration: underline; text-underline-offset: 3px; }
                .lines { margin-top: 0; }
                .lines th { background: #d5dbe3; border: 1px solid #6d7580; padding: 5px 4px; font-size: 12px; text-align: center; }
                .lines td { border: 1px solid #c5ccd4; padding: 3px 5px; font-size: 11px; }
                .lines td.ref, .lines td.qty { text-align: center; }
                .lines td.desc { text-align: left; text-transform: uppercase; }
                .lines td.num, .totals td.num { text-align: right; font-variant-numeric: tabular-nums; }
                .totals { margin-top: 10px; }
                .totals td { border: 1px solid #6d7580; padding: 5px 8px; font-size: 12px; font-weight: 700; }
                .totals td.lbl { background: #f4f6f8; width: 18%; }
                .words { position: fixed; left: 22px; right: 22px; bottom: 62px; margin: 0; font-size: 12px; font-style: italic; background: #fff; }
                .words strong { display: block; margin-top: 6px; color: #1d4e9e; font-size: 15px; font-style: normal; letter-spacing: 0.03em; }
                footer { position: fixed; left: 0; right: 0; bottom: 0; padding: 8px 18px 8px; border-top: 2px solid #e10600; text-align: center; font-size: 9px; line-height: 1.45; color: #333; background: #fff; }
            </style></head><body>
            <div class="watermark"><span>${esc(companyTitle)}</span></div>
            <div class="sheet">
                <div class="banner">
                    <img src="${logoSrc}" alt="${esc(companyTitle)}">
                    <div class="brand">
                        <h1>${esc(companyTitle)}</h1>
                        ${brandLines}
                    </div>
                </div>
                <div class="client">${clientLines.map((line) => `<div>${esc(line)}</div>`).join('') || '<div>—</div>'}</div>
                <table class="meta"><tr>
                    <td><b>ID Client :</b> ${esc(clientCode(row.client_id))}</td>
                    <td><b>Facture N° :</b> ${esc(row.reference)}</td>
                    <td><b>Date :</b> ${esc(row.invoice_date)}</td>
                </tr></table>
                <div class="detail-title">Détail Facture</div>
                <table class="lines">
                    <thead><tr><th>Référence</th><th>Désignation</th><th>Qté</th><th>Prix U.</th><th>R. (%)</th><th>Total</th></tr></thead>
                    <tbody>${lines}</tbody>
                </table>
                <table class="totals"><tr>
                    <td class="lbl">Total H.T.</td><td class="num">${esc(moneyFr(row.total_ht))}</td>
                    <td class="lbl">T.V.A 20,00 %</td><td class="num">${esc(moneyFr(row.tva))}</td>
                    <td class="lbl">Total T.T.C</td><td class="num">${esc(moneyFr(row.total_ttc))}</td>
                </tr></table>
            </div>
            <div class="words">La présente facture s'élève à la somme de<strong>${esc(amountWords(row.total_ttc))}</strong></div>
            <footer>
                <div>${raw(company.adresse)}${company.adresse && company.ville ? ' — ' : ''}${raw(company.ville)}</div>
                <div>R.C. : ${show(company.rc)} &nbsp; I.F. : ${show(company.identifiant_fiscal)} &nbsp; Patente : ${show(company.patente)} &nbsp; C.N.S.S. : ${show(company.cnss)} &nbsp; I.C.E. : ${show(company.ice)}</div>
                <div>N° Télé : ${show(company.contact)} &nbsp; N° Fixe : ${show(company.fixe)} &nbsp; Email : ${show(company.email)}</div>
            </footer>
            </body></html>`;
        const win = window.open('', '_blank', 'width=900,height=700');
        if (!win) return;
        win.document.write(html);
        win.document.close();
        const logo = win.document.querySelector('img');
        const go = () => { win.focus(); win.print(); };
        if (logo && !logo.complete) {
            logo.onload = go;
            logo.onerror = go;
        } else {
            setTimeout(go, 200);
        }
    }
    function openView(row) {
        viewRow = row;
        document.getElementById('fvViewTitle').textContent = row.reference || '—';
        const info = [
            ['Réf', row.reference], ['Date', row.invoice_date], ['ID Clt', clientCode(row.client_id)],
            ['Nom client', row.client], ['Ice', row.ice],
        ];
        const lines = (row.items || []).map((item) => {
            const prix = Number(item.purchase_price) > 0 ? item.purchase_price : item.unit_price;
            return `<tr>
                <td>${esc(item.article_ref || '—')}</td>
                <td class="left">${esc(item.description || '—')}</td>
                <td>${esc(item.quantity)}</td>
                <td>${esc(moneyFr(prix))}</td>
                <td>${esc(moneyFr(item.remise || 0))} %</td>
                <td>${esc(moneyFr(item.total))}</td>
            </tr>`;
        }).join('') || '<tr><td colspan="6">—</td></tr>';
        document.getElementById('fvViewBody').innerHTML = `
            <div class="fa-view-grid">${info.map(([label, value]) => `<div class="fa-view-row"><span>${esc(label)}</span><strong>${esc(value || '—')}</strong></div>`).join('')}</div>
            <div class="fa-view-totals">
                <div><small>Total HT</small><strong>${esc(moneyFr(row.total_ht))}</strong></div>
                <div><small>TVA</small><strong>${esc(moneyFr(row.tva))}</strong></div>
                <div><small>Total TTC</small><strong>${esc(moneyFr(row.total_ttc))}</strong></div>
                <div><small>Solde</small><strong>${esc(moneyFr(row.balance))}</strong></div>
            </div>
            <table class="fa-view-table">
                <thead><tr><th>Réf</th><th>Désignation</th><th>Qté</th><th>Prix/U</th><th>Remise</th><th>Total</th></tr></thead>
                <tbody>${lines}</tbody>
            </table>`;
        view.hidden = false;
    }
    async function load() {
        const response = await fetch('/factures-ventes', { headers: { 'Accept': 'application/json' } });
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
    }
    function closeModal() { modal.hidden = true; editingId = null; }
    function supplierChoices(query) {
        const q = query.trim().toLowerCase();
        const list = meta.clients || [];
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
        document.getElementById('fvClientId').value = clientCode(supplier.dataset.id);
        ice.value = String(item?.ice || '').replace(/\D/g, '').slice(0, 15);
        supplierMenu.hidden = true;
    }
    supplier.addEventListener('focus', showSupplierMenu);
    supplier.addEventListener('input', () => {
        const typed = supplier.value.trim().toLowerCase();
        const exact = (meta.clients || []).find((item) => String(item.name || '').trim().toLowerCase() === typed);
        supplier.dataset.id = exact ? String(exact.id) : '';
        document.getElementById('fvClientId').value = clientCode(supplier.dataset.id);
        showSupplierMenu();
    });
    supplierMenu.addEventListener('click', (event) => {
        const button = event.target.closest('[data-id]');
        if (!button) return;
        const item = (meta.clients || []).find((entry) => String(entry.id) === button.dataset.id);
        if (item) pickSupplier(item);
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.fa-supplier')) supplierMenu.hidden = true;
    });
    ice.addEventListener('input', () => { ice.value = ice.value.replace(/\D/g, '').slice(0, 15); });
    async function openFresh(row) {
        try { await load(); } catch (error) { showError(error.message); }
        openModal(row || null);
    }
    document.getElementById('fvAdd').addEventListener('click', () => openFresh(null));
    document.getElementById('fvClose').addEventListener('click', closeModal);
    document.getElementById('fvCloseX').addEventListener('click', closeModal);
    document.getElementById('fvRefresh').addEventListener('click', () => load().catch((error) => showError(error.message)));
    ['fvFrom', 'fvTo'].forEach((id) => {
        document.getElementById(id).addEventListener('input', (event) => {
            const digits = event.target.value.replace(/\D/g, '').slice(0, 8);
            event.target.value = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 8)].filter(Boolean).join('/');
            render(filteredRows());
        });
    });
    ['fvMonth', 'fvName'].forEach((id) => {
        document.getElementById(id).addEventListener('input', () => render(filteredRows()));
        document.getElementById(id).addEventListener('change', () => render(filteredRows()));
    });
    document.getElementById('fvAddLine').addEventListener('click', () => { linesBody.appendChild(lineRow()); recalc(); });
    document.getElementById('fvViewClose').addEventListener('click', () => { view.hidden = true; });
    document.getElementById('fvViewPrint').addEventListener('click', () => { if (viewRow) printRow(viewRow); });
    document.getElementById('fvViewPdf').addEventListener('click', () => { if (viewRow) printRow(viewRow); });
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
    view.addEventListener('click', (event) => { if (event.target === view) view.hidden = true; });
    linesBody.addEventListener('input', (event) => {
        const row = event.target.closest('tr');
        if (row) refreshLine(row);
        recalc();
    });
    linesBody.addEventListener('change', (event) => {
        const row = event.target.closest('tr');
        if (row && event.target.matches('[data-f="ref"], [data-f="desc"]')) {
            const key = event.target.value;
            row.querySelector('[data-f="ref"]').value = key;
            row.querySelector('[data-f="desc"]').value = key;
        }
        if (row) refreshLine(row);
        recalc();
    });
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
        if (button.dataset.act === 'modifier') openFresh(row);
        if (button.dataset.act === 'imprimer' || button.dataset.act === 'pdf') printRow(row);
        if (button.dataset.act === 'payer') {
            document.querySelector('[data-panel="fa-regl"]')?.click();
            document.dispatchEvent(new CustomEvent('evopro-pay-invoice', { detail: row }));
        }
        if (button.dataset.act === 'supprimer') {
            if (!window.confirm(`Supprimer la facture ${row.reference} ?`)) return;
            const response = await fetch(`/factures-ventes/${row.id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            if (!response.ok) { showError('Impossible de supprimer cette facture.'); return; }
            await load();
        }
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showError('');
        const save = document.getElementById('fvSave');
        linesBody.querySelectorAll('[data-f="price"]').forEach((price) => {
            if (String(price.value).trim()) price.value = moneyDot(price.value);
        });
        const items = [...linesBody.querySelectorAll('tr')].map((row) => {
            const option = row.querySelector('[data-f="desc"]').selectedOptions[0];
            return {
            article_ref: option?.dataset.ref || '',
            description: option?.dataset.desc || '',
            quantity: Number(String(row.querySelector('[data-f="qty"]').value).replace(',', '.')),
            unit_price: Number(String(row.querySelector('[data-f="price"]').value).replace(',', '.')),
            remise: Number(String(row.querySelector('[data-f="remise"]').value).replace(',', '.')) || 0,
            };
        });
        const supplierName = supplier.value.trim();
        if (!supplierName) { showError('Saisissez un client.'); return; }
        const known = (meta.clients || []).find((item) => String(item.name || '').trim().toLowerCase() === supplierName.toLowerCase());
        const supplierId = supplier.dataset.id || (known ? String(known.id) : '');
        if (!/^\d{15}$/.test(ice.value)) { showError('L’ICE doit contenir 15 chiffres.'); return; }
        if (!items.length || items.some((item) => !item.description)) { showError('Choisissez la réf et la désignation dans le stock achats.'); return; }
        const totalTtc = recalc();
        save.disabled = true;
        try {
            const response = await fetch(editingId ? `/factures-ventes/${editingId}` : '/factures-ventes', {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    client_id: supplierId || null,
                    client_name: supplierName,
                    ice: ice.value,
                    invoice_date: document.getElementById('fvDate').value,
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
    document.querySelector('[data-panel="fa-vente"]')?.addEventListener('click', () => {
        load().catch((error) => showError(error.message));
    });
    load().catch((error) => { body.innerHTML = `<tr><td colspan="10">${esc(error.message)}</td></tr>`; });
})();
</script>
