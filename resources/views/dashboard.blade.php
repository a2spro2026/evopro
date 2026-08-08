<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EvoPro — Tableau de bord</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        try {
            if (localStorage.getItem('evopro_dashboard_cards_hidden') === '1') {
                document.documentElement.classList.add('cards-hidden-boot');
            }
        } catch (e) {}
    </script>
    <style>
        :root {
            --bg: #07111f;
            --bg-soft: #0b182b;
            --sidebar: #091525;
            --panel: rgba(14, 28, 48, 0.88);
            --line: rgba(110, 168, 255, 0.18);
            --accent: #3b9eff;
            --accent-soft: #7ec4ff;
            --text: #f4f8ff;
            --muted: rgba(210, 224, 245, 0.68);
            --green: #3dcf8a;
            --amber: #f0b429;
            --rose: #f07178;
            --cyan: #4dd4ea;
            --violet: #9b7bff;
        }

        html[data-theme="light"] {
            --bg: #f4f8ff;
            --bg-soft: #eef4fc;
            --sidebar: #e8f0fb;
            --panel: rgba(255, 255, 255, 0.94);
            --line: rgba(30, 90, 180, 0.16);
            --accent: #1e6fd9;
            --accent-soft: #3b9eff;
            --text: #0a1628;
            --muted: rgba(20, 40, 70, 0.62);
        }

        html[data-theme="light"] body {
            background:
                radial-gradient(ellipse 60% 45% at 10% 0%, rgba(59, 158, 255, 0.12), transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 100%, rgba(30, 111, 217, 0.08), transparent 55%),
                linear-gradient(160deg, #f8fbff 0%, #eef4fc 45%, #f4f8ff 100%);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            text-transform: uppercase;
            background:
                radial-gradient(ellipse 60% 45% at 10% 0%, rgba(40, 120, 255, 0.22), transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 100%, rgba(20, 90, 180, 0.18), transparent 55%),
                linear-gradient(160deg, #050d18 0%, #0a1628 45%, #07111f 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 1.4rem 1rem;
            background: linear-gradient(180deg, rgba(8, 18, 34, 0.98), rgba(6, 14, 26, 0.96));
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .side-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.35rem 0.55rem 1rem;
            border-bottom: 1px solid var(--line);
        }

        .side-brand .mark {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, #1e6fd9, #4eb3ff);
            box-shadow: 0 0 20px rgba(59, 158, 255, 0.45);
            flex-shrink: 0;
        }

        .side-brand .mark svg { width: 22px; height: 22px; }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            flex: 1;
        }

        .dashboard-nav-btn {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            width: 100%;
            padding: 0.95rem 1rem;
            margin-bottom: 0.65rem;
            border-radius: 14px;
            border: 1px solid rgba(126, 196, 255, 0.35);
            background: linear-gradient(135deg, rgba(30, 111, 217, 0.45) 0%, rgba(59, 158, 255, 0.22) 50%, rgba(126, 196, 255, 0.12) 100%);
            box-shadow:
                0 0 0 1px rgba(126, 196, 255, 0.15) inset,
                0 8px 24px rgba(30, 111, 217, 0.25),
                0 0 28px rgba(59, 158, 255, 0.15);
            color: #fff;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .dashboard-nav-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 30%, rgba(255, 255, 255, 0.12) 50%, transparent 70%);
            transform: translateX(-120%);
            transition: transform 0.6s ease;
        }

        .dashboard-nav-btn:hover::before {
            transform: translateX(120%);
        }

        .dashboard-nav-btn:hover {
            transform: translateY(-2px);
            border-color: rgba(174, 214, 255, 0.55);
            box-shadow:
                0 0 0 1px rgba(174, 214, 255, 0.25) inset,
                0 12px 32px rgba(30, 111, 217, 0.35),
                0 0 36px rgba(59, 158, 255, 0.25);
        }

        .dashboard-nav-btn.active {
            border-color: rgba(174, 214, 255, 0.65);
            background: linear-gradient(135deg, rgba(30, 111, 217, 0.65) 0%, rgba(59, 158, 255, 0.38) 100%);
            box-shadow:
                0 0 0 1px rgba(200, 230, 255, 0.3) inset,
                0 10px 28px rgba(30, 111, 217, 0.4),
                0 0 32px rgba(59, 158, 255, 0.3);
        }

        .dashboard-nav-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, #1e6fd9, #5eb8ff);
            box-shadow: 0 0 18px rgba(59, 158, 255, 0.45);
            flex-shrink: 0;
        }

        .dashboard-nav-icon svg {
            width: 22px;
            height: 22px;
            color: #fff;
        }

        .dashboard-nav-text {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            line-height: 1.2;
        }

        .dashboard-nav-text strong {
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            background: linear-gradient(100deg, #ffffff, #c8e7ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .dashboard-nav-text small {
            font-size: 0.72rem;
            color: rgba(210, 230, 255, 0.75);
            font-weight: 400;
        }

        .nav-sections {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.78rem 0.9rem;
            border-radius: 12px;
            color: var(--muted);
            font-size: 0.95rem;
            font-weight: 500;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 1;
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border: 1px solid transparent;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }

        .nav-icon svg {
            width: 16px;
            height: 16px;
            opacity: 1;
        }

        .nav-icon.clients {
            background: linear-gradient(145deg, rgba(126, 196, 255, 0.28), rgba(59, 158, 255, 0.12));
            border-color: rgba(126, 196, 255, 0.35);
            color: #9ad4ff;
        }

        .nav-icon.projets {
            background: linear-gradient(145deg, rgba(155, 123, 255, 0.3), rgba(123, 92, 255, 0.12));
            border-color: rgba(155, 123, 255, 0.35);
            color: #c4b0ff;
        }

        .nav-icon.paiements {
            background: linear-gradient(145deg, rgba(61, 207, 138, 0.28), rgba(47, 158, 95, 0.12));
            border-color: rgba(61, 207, 138, 0.35);
            color: #7ee8b0;
        }

        .nav-icon.charges {
            background: linear-gradient(145deg, rgba(240, 180, 41, 0.28), rgba(200, 140, 20, 0.12));
            border-color: rgba(240, 180, 41, 0.35);
            color: #ffc857;
        }

        .nav-icon.suivie {
            background: linear-gradient(145deg, rgba(77, 212, 234, 0.28), rgba(27, 184, 212, 0.12));
            border-color: rgba(77, 212, 234, 0.35);
            color: #7ee8f5;
        }

        .nav-icon.rapports {
            background: linear-gradient(145deg, rgba(240, 113, 120, 0.28), rgba(200, 60, 80, 0.12));
            border-color: rgba(240, 113, 120, 0.35);
            color: #ff9aa0;
        }

        .nav-icon.config {
            background: linear-gradient(145deg, rgba(90, 140, 220, 0.3), rgba(40, 90, 180, 0.14));
            border-color: rgba(126, 196, 255, 0.32);
            color: #b8d9ff;
        }

        .submenu-icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: rgba(59, 158, 255, 0.12);
            border: 1px solid rgba(126, 196, 255, 0.22);
            color: var(--accent-soft);
        }

        .submenu-icon svg {
            width: 13px;
            height: 13px;
        }

        .nav-item:hover {
            background: rgba(59, 158, 255, 0.1);
            color: var(--text);
            transform: translateX(2px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(30, 111, 217, 0.35), rgba(59, 158, 255, 0.18));
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(126, 196, 255, 0.25);
        }

        .nav-item.active svg { opacity: 1; color: var(--accent-soft); }

        .nav-group { display: flex; flex-direction: column; gap: 0.2rem; }

        .nav-parent {
            width: 100%;
            border: none;
            background: transparent;
            cursor: pointer;
            font: inherit;
            text-align: left;
            justify-content: space-between;
        }

        .nav-parent .chevron {
            width: 14px;
            height: 14px;
            transition: transform 0.2s ease;
            opacity: 0.7;
        }

        .nav-group.open .nav-parent .chevron { transform: rotate(180deg); }

        .nav-parent .nav-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .submenu {
            display: none;
            flex-direction: column;
            gap: 0.15rem;
            padding: 0.15rem 0 0.35rem 0.55rem;
            margin-left: 0.85rem;
            border-left: 1px solid rgba(110, 168, 255, 0.2);
        }

        .nav-group.open .submenu { display: flex; }

        .submenu a {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 500;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .submenu a:hover,
        .submenu a.active {
            background: rgba(59, 158, 255, 0.12);
            color: #fff;
            transform: translateX(2px);
        }

        .side-foot {
            padding: 0.8rem 0.9rem;
            border-top: 1px solid var(--line);
            font-size: 0.78rem;
            color: var(--muted);
        }

        .sidebar-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .sidebar-header .side-brand {
            flex: 1;
            min-width: 0;
        }

        .sidebar-close {
            display: none;
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(59, 158, 255, 0.08);
            color: var(--text);
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .sidebar-close svg { width: 18px; height: 18px; }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 35;
            background: rgba(4, 10, 20, 0.55);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .sidebar-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        .sidebar-footer {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-top: auto;
            padding-top: 0.5rem;
        }

        .btn-theme-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            width: 100%;
            padding: 0.65rem 0.85rem;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: rgba(59, 158, 255, 0.08);
            color: var(--text);
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .btn-theme-toggle:hover {
            background: rgba(59, 158, 255, 0.14);
            border-color: rgba(126, 196, 255, 0.35);
        }

        .btn-theme-toggle svg { width: 18px; height: 18px; flex-shrink: 0; }

        .btn-sidebar-logout {
            width: 100%;
            justify-content: center;
            border-radius: 12px;
            padding: 0.65rem 0.85rem;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .menu-toggle .icon-close { display: none; }
        .menu-toggle.is-open .icon-menu { display: none; }
        .menu-toggle.is-open .icon-close { display: block; }

        .panel { display: none; }
        .panel.active {
            display: block;
            animation: fadeInPanel 0.35s ease both;
        }

        @keyframes fadeInPanel {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .section-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .search-bar {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: rgba(10, 20, 36, 0.65);
        }

        .search-field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .search-field label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--accent-soft);
            letter-spacing: 0.04em;
        }

        .search-field input,
        .search-field select {
            height: 38px;
            padding: 0 0.75rem;
            border-radius: 10px;
            border: 1px solid rgba(110, 168, 255, 0.22);
            background: rgba(6, 14, 26, 0.75);
            color: var(--text);
            font-family: inherit;
            font-size: 0.85rem;
            text-transform: uppercase;
            outline: none;
            width: 100%;
            appearance: none;
        }

        .search-field select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237ec4ff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.25rem;
            cursor: pointer;
        }

        .search-field input:focus,
        .search-field select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 158, 255, 0.15);
        }

        @media (max-width: 768px) {
            .search-bar { grid-template-columns: 1fr; }
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            height: 38px;
            padding: 0 0.95rem;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #1e6fd9, #3b9eff);
            color: #fff;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(30, 111, 217, 0.3);
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .btn-add:hover { transform: translateY(-1px); filter: brightness(1.05); }
        .btn-add svg { width: 16px; height: 16px; }

        .relance-inline-input {
            width: 100%;
            min-width: 0;
            height: 32px;
            padding: 0 0.45rem;
            border-radius: 8px;
            border: 1px solid rgba(110, 168, 255, 0.22);
            background: rgba(6, 14, 26, 0.55);
            color: var(--text);
            font-family: inherit;
            font-size: 0.78rem;
            box-sizing: border-box;
        }

        .relance-inline-input:focus {
            outline: none;
            border-color: rgba(94, 176, 255, 0.65);
            box-shadow: 0 0 0 2px rgba(59, 158, 255, 0.14);
        }

        .relance-inline-input.is-saving {
            opacity: 0.7;
        }

        .relance-inline-textarea {
            height: auto;
            min-height: 52px;
            padding: 0.4rem 0.45rem;
            resize: vertical;
            line-height: 1.35;
        }

        .relance-inline-budget {
            font-variant-numeric: tabular-nums;
            text-align: right;
        }

        .relance-inline-select {
            cursor: pointer;
        }

        html[data-theme="light"] .relance-inline-input {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(30, 111, 217, 0.2);
            color: #12233d;
        }

        .table-wrap {
            overflow: auto;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(10, 20, 36, 0.72);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        /* Relances: tout visible dans la largeur, rien coupé hors écran */
        table.data-table.data-table-relances {
            min-width: 0;
            width: 100%;
            table-layout: fixed;
        }

        table.data-table.data-table-relances th,
        table.data-table.data-table-relances td {
            padding: 0.45rem 0.35rem;
            font-size: 0.72rem;
            white-space: normal;
            overflow: visible;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.25;
            vertical-align: middle;
        }

        table.data-table.data-table-relances th {
            font-size: 0.68rem;
            letter-spacing: 0.02em;
            white-space: normal;
        }

        table.data-table.data-table-relances td.cell-wrap {
            white-space: normal;
            min-width: 0;
            max-width: none;
        }

        table.data-table.data-table-relances .statue-select {
            max-width: 100%;
            font-size: 0.62rem;
            padding: 0.22rem 1.1rem 0.22rem 0.35rem;
            white-space: nowrap;
        }

        table.data-table.data-table-relances .actions {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.25rem;
        }

        #panel-dashboard .balance-section .table-wrap.table-freeze-body,
        #panel-fiche-relance .table-wrap.table-freeze-body {
            max-height: none;
            overflow: visible;
        }

        #panel-dashboard .balance-section .table-wrap,
        #panel-fiche-relance .table-wrap {
            overflow: visible;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem 0.85rem;
            text-align: center !important;
            vertical-align: middle;
            border-bottom: 1px solid rgba(110, 168, 255, 0.1);
            font-size: 0.84rem;
            white-space: nowrap;
        }

        .data-table th {
            color: var(--accent-soft);
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            background: rgba(20, 40, 70, 0.55);
            text-align: center !important;
        }

        .data-table td.cell-wrap {
            white-space: normal;
            max-width: 280px;
        }

        .data-table .empty {
            text-align: center !important;
        }

        .data-table tbody tr:hover { background: rgba(59, 158, 255, 0.06); }
        .data-table tbody tr.row-execute {
            background: rgba(61, 207, 138, 0.18);
        }
        .data-table tbody tr.row-execute:hover {
            background: rgba(61, 207, 138, 0.26);
        }
        .data-table tbody tr.row-execute td {
            color: #c8f5dc;
        }
        .data-table tbody tr.row-relance-a-voir {
            background: rgba(240, 180, 41, 0.24);
        }
        .data-table tbody tr.row-relance-a-voir:hover {
            background: rgba(240, 180, 41, 0.34);
        }
        .data-table tbody tr.row-relance-a-voir td {
            color: #ffe7a8;
        }
        .data-table tbody tr.row-relance-confirme {
            background: rgba(120, 130, 150, 0.28);
            opacity: 0.7;
        }
        .data-table tbody tr.row-relance-confirme:hover {
            background: rgba(120, 130, 150, 0.36);
        }
        .data-table tbody tr.row-relance-confirme td {
            color: rgba(190, 200, 215, 0.72);
        }
        .data-table tbody tr.row-relance-inj {
            background: rgba(220, 60, 70, 0.28);
        }
        .data-table tbody tr.row-relance-inj:hover {
            background: rgba(220, 60, 70, 0.38);
        }
        .data-table tbody tr.row-relance-inj td {
            color: #ffb4b8;
        }
        .data-table td { color: rgba(235, 242, 255, 0.9); }
        .data-table td.solde-cell {
            color: #ff6b78;
            font-weight: 700;
            text-shadow: 0 0 12px rgba(255, 90, 100, 0.25);
        }

        #projet_solde {
            color: #ff6b78;
            font-weight: 700;
        }

        #paiement_solde {
            color: #ff6b78;
            font-weight: 700;
        }

        .statue-badge {
            display: inline-block;
            padding: 0.28rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .statue-badge.actif {
            color: #7ec4ff;
            background: rgba(59, 158, 255, 0.16);
            border: 1px solid rgba(126, 196, 255, 0.4);
            box-shadow: 0 0 10px rgba(59, 158, 255, 0.12);
        }

        .statue-badge.attente {
            color: #ffc857;
            background: rgba(240, 180, 41, 0.16);
            border: 1px solid rgba(240, 180, 41, 0.4);
            box-shadow: 0 0 10px rgba(240, 180, 41, 0.12);
        }

        .statue-badge.annule {
            color: #ff9aa0;
            background: rgba(240, 113, 120, 0.16);
            border: 1px solid rgba(240, 113, 120, 0.4);
            box-shadow: 0 0 10px rgba(240, 113, 120, 0.12);
        }

        .statue-badge.execute {
            color: #7ee8b0;
            background: rgba(61, 207, 138, 0.16);
            border: 1px solid rgba(61, 207, 138, 0.4);
            box-shadow: 0 0 10px rgba(61, 207, 138, 0.12);
        }

        #projet_statut option[value="actif"] { color: #7ec4ff; }
        #projet_statut option[value="attente"] { color: #ffc857; }
        #projet_statut option[value="annule"] { color: #ff9aa0; }
        #projet_statut option[value="execute"] { color: #7ee8b0; }

        #projet_statut.statue-actif { color: #7ec4ff; border-color: rgba(126, 196, 255, 0.45); }
        #projet_statut.statue-attente { color: #ffc857; border-color: rgba(240, 180, 41, 0.45); }
        #projet_statut.statue-annule { color: #ff9aa0; border-color: rgba(240, 113, 120, 0.45); }
        #projet_statut.statue-execute { color: #7ee8b0; border-color: rgba(61, 207, 138, 0.45); }

        .statue-form {
            display: inline-block;
        }

        .rappel-date-form {
            display: inline-flex;
            width: 100%;
            justify-content: center;
        }

        .rappel-date-input {
            width: 100%;
            max-width: 7.2rem;
            height: 30px;
            padding: 0.2rem 0.35rem;
            border-radius: 8px;
            border: 1px solid rgba(110, 168, 255, 0.28);
            background: rgba(8, 18, 34, 0.65);
            color: inherit;
            font-family: inherit;
            font-size: 0.72rem;
            text-align: center;
            letter-spacing: 0.02em;
        }

        .rappel-date-input:focus {
            outline: none;
            border-color: rgba(94, 176, 255, 0.7);
            box-shadow: 0 0 0 2px rgba(59, 158, 255, 0.18);
        }

        html[data-theme="light"] .rappel-date-input {
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(30, 111, 217, 0.28);
            color: #0a1628;
        }

        .statue-select {
            appearance: none;
            -webkit-appearance: none;
            padding: 0.28rem 1.4rem 0.28rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            line-height: 1.2;
            cursor: pointer;
            font-family: inherit;
            text-transform: uppercase;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23b8c9e0' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.45rem center;
            background-size: 10px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .statue-select:hover,
        .statue-select:focus {
            outline: none;
            box-shadow: 0 0 12px rgba(126, 196, 255, 0.2);
        }

        .statue-select.actif {
            color: #7ec4ff;
            background-color: rgba(59, 158, 255, 0.16);
            border: 1px solid rgba(126, 196, 255, 0.4);
        }

        .statue-select.attente {
            color: #ffc857;
            background-color: rgba(240, 180, 41, 0.16);
            border: 1px solid rgba(240, 180, 41, 0.4);
        }

        .statue-select.annule {
            color: #ff9aa0;
            background-color: rgba(240, 113, 120, 0.16);
            border: 1px solid rgba(240, 113, 120, 0.4);
        }

        .statue-select.execute {
            color: #7ee8b0;
            background-color: rgba(61, 207, 138, 0.16);
            border: 1px solid rgba(61, 207, 138, 0.4);
        }

        .statue-select.a_voir {
            color: #ffc857;
            background-color: rgba(240, 180, 41, 0.16);
            border: 1px solid rgba(240, 180, 41, 0.4);
        }

        .statue-select.confirme {
            color: #b8c4d4;
            background-color: rgba(120, 130, 150, 0.22);
            border: 1px solid rgba(140, 150, 165, 0.45);
        }

        .statue-select.inj {
            color: #ff9aa0;
            background-color: rgba(220, 60, 70, 0.18);
            border: 1px solid rgba(220, 60, 70, 0.45);
        }

        .statue-select.lien {
            color: #7ec4ff;
            background-color: rgba(59, 158, 255, 0.16);
            border: 1px solid rgba(126, 196, 255, 0.4);
        }

        .statue-select.conception {
            color: #ffc857;
            background-color: rgba(240, 180, 41, 0.16);
            border: 1px solid rgba(240, 180, 41, 0.4);
        }

        .envoye-switch {
            display: inline-flex;
            align-items: stretch;
            width: 100%;
            max-width: 11.5rem;
            padding: 2px;
            border-radius: 999px;
            background: rgba(8, 18, 34, 0.55);
            border: 1px solid rgba(110, 168, 255, 0.22);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            gap: 2px;
        }

        .envoye-switch .envoye-opt {
            flex: 1 1 0;
            appearance: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            line-height: 1;
            padding: 0.42rem 0.35rem;
            border-radius: 999px;
            color: rgba(210, 224, 245, 0.55);
            background: transparent;
            transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .envoye-switch .envoye-opt:hover:not(.is-active) {
            color: rgba(235, 242, 255, 0.88);
            background: rgba(59, 158, 255, 0.08);
        }

        .envoye-switch .envoye-opt.is-active[data-value="lien"] {
            color: #d9efff;
            background: linear-gradient(135deg, rgba(59, 158, 255, 0.42), rgba(30, 111, 217, 0.55));
            box-shadow: 0 4px 12px rgba(59, 158, 255, 0.28);
        }

        .envoye-switch .envoye-opt.is-active[data-value="conception"] {
            color: #fff1c9;
            background: linear-gradient(135deg, rgba(240, 180, 41, 0.42), rgba(210, 140, 20, 0.55));
            box-shadow: 0 4px 12px rgba(240, 180, 41, 0.25);
        }

        .envoye-switch.is-saving {
            opacity: 0.72;
            pointer-events: none;
        }

        html[data-theme="light"] .envoye-switch {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(30, 111, 217, 0.2);
        }

        html[data-theme="light"] .envoye-switch .envoye-opt {
            color: rgba(20, 40, 70, 0.48);
        }

        html[data-theme="light"] .envoye-switch .envoye-opt:hover:not(.is-active) {
            color: rgba(10, 22, 40, 0.85);
            background: rgba(30, 111, 217, 0.08);
        }

        html.cards-hidden-boot #panel-dashboard .cards,
        html.cards-hidden-boot #panel-dashboard .balance-section .search-bar {
            display: none !important;
        }

        .pull-select {
            appearance: none;
            -webkit-appearance: none;
            padding: 0.28rem 1.4rem 0.28rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            line-height: 1.2;
            cursor: pointer;
            font-family: inherit;
            text-transform: uppercase;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23b8c9e0' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.45rem center;
            background-size: 10px;
        }

        .pull-select.oui {
            color: #7ee8b0;
            background-color: rgba(61, 207, 138, 0.16);
            border: 1px solid rgba(61, 207, 138, 0.4);
        }

        .pull-select.non {
            color: #ff9aa0;
            background-color: rgba(240, 113, 120, 0.16);
            border: 1px solid rgba(240, 113, 120, 0.4);
        }

        .data-table .empty { color: var(--muted); padding: 2rem; }

        .actions {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid transparent;
            display: inline-grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.04);
            color: var(--muted);
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .action-btn svg { width: 14px; height: 14px; }
        .action-btn.voir:hover { color: #7ec4ff; border-color: rgba(126, 196, 255, 0.35); background: rgba(59, 158, 255, 0.12); }
        .action-btn.modifier:hover { color: #ffc857; border-color: rgba(240, 180, 41, 0.35); background: rgba(240, 180, 41, 0.12); }
        .action-btn.supprimer:hover { color: #ff9aa0; border-color: rgba(240, 113, 120, 0.35); background: rgba(240, 113, 120, 0.12); }
        .action-btn.pdf:hover { color: #ff8f7a; border-color: rgba(255, 120, 90, 0.35); background: rgba(255, 120, 90, 0.12); }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(3, 8, 18, 0.72);
            backdrop-filter: blur(6px);
        }

        .modal-backdrop.open { display: flex; }

        .modal {
            width: min(520px, 100%);
            border-radius: 16px;
            background: linear-gradient(165deg, #122038, #0b1728);
            border: 1px solid rgba(126, 196, 255, 0.25);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45), 0 0 30px rgba(59, 158, 255, 0.12);
            overflow: hidden;
            animation: fadeUp 0.3s ease both;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--line);
        }

        .modal-head h2 {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--text);
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
        }

        .modal-body {
            padding: 1.15rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }

        .modal-body .field { display: flex; flex-direction: column; gap: 0.35rem; }
        .modal-body .field.full { grid-column: 1 / -1; }

        .modal-body label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--accent-soft);
        }

        .modal-body input,
        .modal-body select,
        .modal-body textarea {
            height: 40px;
            padding: 0 0.75rem;
            border-radius: 10px;
            border: 1px solid rgba(110, 168, 255, 0.22);
            background: rgba(6, 14, 26, 0.75);
            color: var(--text);
            font-family: inherit;
            font-size: 0.9rem;
            text-transform: uppercase;
            outline: none;
            width: 100%;
            appearance: none;
            cursor: pointer;
        }

        .modal-body textarea {
            height: auto;
            min-height: 96px;
            padding: 0.7rem 0.75rem;
            resize: vertical;
            line-height: 1.45;
            cursor: text;
        }

        .modal-body select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237ec4ff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.25rem;
        }

        .auth-sections {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            grid-column: 1 / -1;
        }

        .auth-section {
            border: 1px solid rgba(110, 168, 255, 0.18);
            border-radius: 12px;
            padding: 0.75rem 0.9rem;
            background: rgba(6, 14, 26, 0.45);
        }

        .auth-section-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--accent-soft);
            margin-bottom: 0.55rem;
            text-transform: uppercase;
        }

        .auth-checks {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem 1.1rem;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.86rem;
            color: var(--text);
            cursor: pointer;
            text-transform: uppercase;
            user-select: none;
        }

        .auth-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: #3d8bfd;
            cursor: pointer;
            appearance: auto;
            flex-shrink: 0;
        }

        .nav-hidden {
            display: none !important;
        }

        .client-select-field select {
            height: 44px;
            border-color: rgba(126, 196, 255, 0.35);
            background-color: rgba(12, 28, 52, 0.85);
            box-shadow: 0 0 0 1px rgba(59, 158, 255, 0.08) inset;
        }

        .client-select-field select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 158, 255, 0.18);
        }

        .client-select-hint {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

        .modal-body input:focus,
        .modal-body select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 158, 255, 0.15);
        }

        .date-parts {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .date-parts input {
            text-align: center;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.04em;
        }

        .date-parts .date-jj,
        .date-parts .date-mm {
            width: 3.2rem;
            flex: 0 0 3.2rem;
        }

        .date-parts .date-aaaa {
            width: 4.6rem;
            flex: 0 0 4.6rem;
        }

        .date-parts .date-sep {
            color: var(--muted);
            font-weight: 700;
            user-select: none;
        }

        .modal-body input[readonly] {
            color: var(--muted);
            background: rgba(6, 14, 26, 0.45);
        }

        .modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.6rem;
            padding: 0 1.15rem 1.15rem;
        }

        .modal-foot-split {
            justify-content: space-between;
            align-items: center;
        }

        .modal-foot-actions {
            display: flex;
            gap: 0.6rem;
            align-items: center;
        }

        .modal.modal-import {
            width: min(560px, 94vw);
        }

        .modal-body-import {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .import-dropzone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 170px;
            padding: 1.25rem 1rem;
            border-radius: 14px;
            border: 1.5px dashed rgba(110, 168, 255, 0.35);
            background:
                linear-gradient(160deg, rgba(59, 158, 255, 0.08), rgba(10, 22, 40, 0.35));
            cursor: pointer;
            text-align: center;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .import-dropzone:hover,
        .import-dropzone.is-dragover {
            border-color: rgba(94, 176, 255, 0.7);
            box-shadow: 0 0 0 3px rgba(59, 158, 255, 0.12);
            background:
                linear-gradient(160deg, rgba(59, 158, 255, 0.14), rgba(10, 22, 40, 0.4));
        }

        .import-dropzone-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: var(--accent-soft);
            background: rgba(59, 158, 255, 0.14);
            border: 1px solid rgba(110, 168, 255, 0.25);
            margin-bottom: 0.25rem;
        }

        .import-dropzone-icon svg {
            width: 22px;
            height: 22px;
        }

        .import-dropzone-title {
            font-size: 0.92rem;
            font-weight: 650;
            color: var(--text);
            text-transform: none;
        }

        .import-dropzone-hint,
        .import-dropzone-file {
            font-size: 0.78rem;
            color: var(--muted);
            text-transform: none;
        }

        .import-dropzone-file:not(:empty) {
            color: var(--accent-soft);
            font-weight: 600;
        }

        .import-status {
            font-size: 0.82rem;
            color: var(--muted);
            text-transform: none;
            padding: 0.65rem 0.8rem;
            border-radius: 10px;
            background: rgba(59, 158, 255, 0.08);
            border: 1px solid rgba(110, 168, 255, 0.18);
        }

        .import-status.is-error {
            color: #ffb3b8;
            background: rgba(240, 113, 120, 0.1);
            border-color: rgba(240, 113, 120, 0.28);
        }

        .import-status.is-ok {
            color: #9be7b5;
            background: rgba(46, 160, 110, 0.12);
            border-color: rgba(46, 160, 110, 0.28);
        }

        .import-phones-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.55rem;
            font-size: 0.8rem;
            font-weight: 650;
            color: var(--accent-soft);
            text-transform: none;
        }

        .import-phones-clear {
            height: 30px;
            padding: 0 0.7rem;
            font-size: 0.72rem;
        }

        .import-phones-list {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 220px;
            overflow: auto;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .import-phones-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.55rem 0.7rem;
            border-radius: 10px;
            background: rgba(8, 18, 34, 0.55);
            border: 1px solid rgba(110, 168, 255, 0.16);
            font-size: 0.86rem;
            font-variant-numeric: tabular-nums;
            text-transform: none;
        }

        .import-phones-list li button {
            border: none;
            background: transparent;
            color: #ff9aa0;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        html[data-theme="light"] .import-dropzone {
            background: linear-gradient(160deg, rgba(30, 111, 217, 0.06), rgba(255, 255, 255, 0.7));
        }

        html[data-theme="light"] .import-phones-list li {
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(30, 111, 217, 0.14);
        }

        .btn-ghost,
        .btn-primary {
            height: 38px;
            padding: 0 0.95rem;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-ghost {
            border: 1px solid var(--line);
            background: transparent;
            color: var(--muted);
        }

        .btn-primary {
            border: none;
            background: linear-gradient(135deg, #1e6fd9, #3b9eff);
            color: #fff;
        }

        @media (max-width: 620px) {
            .modal-body { grid-template-columns: 1fr; }
        }

        /* Main */
        .main {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.1rem 2rem;
            background: rgba(7, 17, 31, 0.72);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }

        .navbar-brand {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .navbar-logo {
            display: none;
        }

        .brand-glow {
            font-size: clamp(1.35rem, 2vw, 1.65rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1;
            background: linear-gradient(100deg, #9ad4ff 0%, #ffffff 35%, #5eb0ff 65%, #c8e7ff 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            filter: drop-shadow(0 0 10px rgba(94, 176, 255, 0.55)) drop-shadow(0 0 28px rgba(59, 158, 255, 0.35));
            animation: brandShine 4.5s ease-in-out infinite;
        }

        .brand-tagline {
            display: block;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(174, 214, 255, 0.85);
            text-shadow: 0 0 12px rgba(59, 158, 255, 0.35);
        }

        .brand-glow span {
            background: linear-gradient(100deg, #4eb3ff, #a8dbff, #ffffff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        @keyframes brandShine {
            0%, 100% { background-position: 0% center; filter: drop-shadow(0 0 10px rgba(94, 176, 255, 0.5)) drop-shadow(0 0 24px rgba(59, 158, 255, 0.3)); }
            50% { background-position: 100% center; filter: drop-shadow(0 0 16px rgba(126, 196, 255, 0.75)) drop-shadow(0 0 36px rgba(59, 158, 255, 0.45)); }
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .user-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.45rem 0.8rem 0.45rem 0.45rem;
            border-radius: 999px;
            background: rgba(59, 158, 255, 0.1);
            border: 1px solid rgba(126, 196, 255, 0.22);
        }

        .user-chip .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: block;
            object-fit: cover;
            object-position: center top;
            background: linear-gradient(145deg, #1e6fd9, #4eb3ff);
            border: 1px solid rgba(126, 196, 255, 0.35);
        }

        .btn-logout {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.48rem 1rem 0.48rem 0.75rem;
            border-radius: 999px;
            border: 1px solid rgba(240, 113, 120, 0.38);
            background:
                linear-gradient(135deg, rgba(240, 113, 120, 0.18), rgba(220, 60, 80, 0.08)),
                rgba(12, 22, 38, 0.65);
            color: #ffb3b8;
            font-size: 0.76rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            cursor: pointer;
            overflow: hidden;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
            box-shadow: 0 4px 14px rgba(240, 113, 120, 0.12);
        }

        .btn-logout::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 25%, rgba(255, 180, 188, 0.16) 50%, transparent 75%);
            transform: translateX(-120%);
            transition: transform 0.45s ease;
        }

        .btn-logout:hover {
            transform: translateY(-1px);
            border-color: rgba(255, 154, 160, 0.55);
            color: #ffd4d7;
            box-shadow: 0 6px 20px rgba(240, 113, 120, 0.28), 0 0 18px rgba(240, 113, 120, 0.15);
        }

        .btn-logout:hover::before {
            transform: translateX(120%);
        }

        .btn-logout:active {
            transform: translateY(0);
        }

        .btn-logout-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: linear-gradient(145deg, rgba(240, 113, 120, 0.35), rgba(200, 50, 70, 0.25));
            border: 1px solid rgba(255, 180, 188, 0.35);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .btn-logout-icon svg {
            width: 15px;
            height: 15px;
        }

        .btn-logout-text {
            line-height: 1;
        }

        @media (max-width: 720px) {
            .btn-logout-text {
                display: none;
            }

            .btn-logout {
                padding: 0.48rem;
            }
        }

        .content {
            padding: 1.75rem 2rem 2.5rem;
        }

        .content-head {
            margin-bottom: 1.1rem;
            animation: fadeUp 0.5s ease both;
        }

        .content-head h1 {
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 0.75rem;
            width: 100%;
            position: relative;
            z-index: 1;
            padding: 0;
            margin: 0 0 0.55rem;
            pointer-events: none;
            user-select: none;
            background: transparent;
            backdrop-filter: none;
        }

        html[data-theme="light"] .cards {
            background: transparent;
        }

        .card {
            position: relative;
            overflow: hidden;
            padding: 0.7rem 0.85rem 0.65rem;
            min-height: 86px;
            height: 86px;
            border-radius: 12px;
            background: linear-gradient(165deg, rgba(16, 32, 54, 0.92), rgba(10, 22, 40, 0.88));
            border: 1px solid rgba(110, 168, 255, 0.2);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            animation: fadeUp 0.45s ease both;
            cursor: default;
            pointer-events: none;
            user-select: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card:nth-child(1) { animation-delay: 0.05s; }
        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.15s; }
        .card:nth-child(4) { animation-delay: 0.2s; }
        .card:nth-child(5) { animation-delay: 0.25s; }
        .card:nth-child(6) { animation-delay: 0.3s; }

        .card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            opacity: 0.9;
        }

        .card.actif::after { background: linear-gradient(90deg, var(--green), transparent); }
        .card.attente::after { background: linear-gradient(90deg, var(--amber), transparent); }
        .card.annule::after { background: linear-gradient(90deg, var(--rose), transparent); }
        .card.execute::after { background: linear-gradient(90deg, var(--green), transparent); }
        .card.revenu::after { background: linear-gradient(90deg, var(--cyan), transparent); }
        .card.brahim::after { background: linear-gradient(90deg, var(--amber), transparent); }
        .card.solde::after { background: linear-gradient(90deg, var(--violet), transparent); }

        .card:hover {
            border-color: rgba(126, 196, 255, 0.28);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2), 0 0 12px rgba(59, 158, 255, 0.08);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .card-label {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--muted);
            line-height: 1.25;
        }

        .card-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .card.actif .card-icon { background: rgba(61, 207, 138, 0.12); color: var(--green); }
        .card.attente .card-icon { background: rgba(240, 180, 41, 0.12); color: var(--amber); }
        .card.annule .card-icon { background: rgba(240, 113, 120, 0.12); color: var(--rose); }
        .card.execute .card-icon { background: rgba(61, 207, 138, 0.12); color: var(--green); }
        .card.revenu .card-icon { background: rgba(77, 212, 234, 0.12); color: var(--cyan); }
        .card.brahim .card-icon { background: rgba(240, 180, 41, 0.12); color: var(--amber); }
        .card.solde .card-icon { background: rgba(155, 123, 255, 0.12); color: var(--violet); }

        .card-icon svg { width: 15px; height: 15px; }

        .card-value {
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
            flex: 0 0 auto;
        }

        .card.actif .card-value { color: #7ee8b0; }
        .card.attente .card-value { color: #ffc857; }
        .card.annule .card-value { color: #ff9aa0; }
        .card.execute .card-value { color: #7ee8b0; }
        .card.revenu .card-value { color: #7ee8f5; }
        .card.brahim .card-value { color: #ffc857; }
        .card.solde .card-value { color: #c4b0ff; }

        /* Bloc figé (toolbar / cartes / recherche) + en-têtes tableau — toutes fiches */
        .panel-freeze {
            position: sticky;
            top: 3.85rem;
            z-index: 19;
            margin: -0.35rem 0 0.75rem;
            padding: 0.35rem 0 0.7rem;
            background:
                linear-gradient(180deg, rgba(7, 17, 31, 0.995) 0%, rgba(7, 17, 31, 0.98) 85%, rgba(7, 17, 31, 0.92) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        html[data-theme="light"] .panel-freeze {
            background:
                linear-gradient(180deg, rgba(244, 248, 255, 0.995) 0%, rgba(244, 248, 255, 0.98) 85%, rgba(244, 248, 255, 0.92) 100%);
        }

        .panel-freeze .section-toolbar {
            margin-bottom: 0.55rem;
            pointer-events: auto;
        }

        .panel-freeze .search-bar {
            margin-bottom: 0;
            pointer-events: auto;
            user-select: auto;
        }

        .panel-freeze .paiement-cards {
            margin-bottom: 0.65rem;
            pointer-events: none;
            user-select: none;
        }

        .panel-freeze .paiement-card {
            min-height: 92px;
            padding: 0.8rem 0.95rem 0.75rem;
        }

        .panel-freeze .paiement-card .card-value {
            font-size: 1.4rem;
        }

        .table-wrap.table-freeze-body {
            max-height: calc(100vh - 14.5rem);
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-wrap.table-freeze-body .data-table thead th {
            position: sticky;
            top: 0;
            z-index: 6;
            background: rgba(14, 30, 54, 0.98);
            box-shadow: 0 1px 0 rgba(110, 168, 255, 0.18);
        }

        html[data-theme="light"] .table-wrap.table-freeze-body .data-table thead th {
            background: rgba(248, 251, 255, 0.98);
            box-shadow: 0 1px 0 rgba(30, 90, 180, 0.14);
        }

        .balance-section .panel-freeze {
            position: sticky;
            top: 3.85rem;
            z-index: 16;
            margin: 0 0 0.75rem;
            padding: 0 0 0.55rem;
            background:
                linear-gradient(180deg, rgba(16, 32, 54, 0.98) 0%, rgba(12, 26, 46, 0.96) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        html[data-theme="light"] .balance-section .panel-freeze {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(243, 247, 253, 0.96) 100%);
        }

        .balance-section .table-wrap.table-freeze-body {
            max-height: none;
        }

        #panel-fiche-relance .table-wrap.table-freeze-body {
            max-height: none;
        }

        .paiement-sticky-lock {
            position: sticky;
            top: 3.85rem;
            z-index: 19;
            margin: 0 0 1.15rem;
            padding: 0.65rem 0 0.85rem;
            pointer-events: none;
            user-select: none;
            touch-action: none;
            background:
                linear-gradient(180deg, rgba(7, 17, 31, 0.99) 0%, rgba(7, 17, 31, 0.97) 78%, rgba(7, 17, 31, 0.9) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        html[data-theme="light"] .paiement-sticky-lock {
            background:
                linear-gradient(180deg, rgba(244, 248, 255, 0.99) 0%, rgba(244, 248, 255, 0.97) 78%, rgba(244, 248, 255, 0.9) 100%);
        }

        .paiement-cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
            margin: 0;
            pointer-events: none;
            user-select: none;
        }

        .paiement-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 108px;
            padding: 1rem 1.1rem 0.95rem;
            border-radius: 14px;
            border: 1px solid transparent;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
            pointer-events: none;
            cursor: default;
            user-select: none;
        }

        .paiement-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.14), transparent 42%),
                radial-gradient(circle at 0% 100%, rgba(0, 0, 0, 0.18), transparent 50%);
            pointer-events: none;
        }

        .paiement-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .paiement-card.budget {
            background: linear-gradient(145deg, rgba(18, 48, 92, 0.95), rgba(12, 28, 54, 0.92));
            border-color: rgba(126, 196, 255, 0.35);
        }
        .paiement-card.budget::after { background: linear-gradient(90deg, #5aa8ff, transparent); }

        .paiement-card.paye {
            background: linear-gradient(145deg, rgba(14, 58, 42, 0.95), rgba(10, 32, 28, 0.92));
            border-color: rgba(61, 207, 138, 0.35);
        }
        .paiement-card.paye::after { background: linear-gradient(90deg, #3dcf8a, transparent); }

        .paiement-card.soldes {
            background: linear-gradient(145deg, rgba(62, 22, 40, 0.95), rgba(36, 14, 28, 0.92));
            border-color: rgba(240, 113, 120, 0.35);
        }
        .paiement-card.soldes::after { background: linear-gradient(90deg, #ff6b78, transparent); }

        .paiement-card:hover {
            transform: none;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
        }

        .paiement-card .card-top {
            position: relative;
            z-index: 1;
        }

        .paiement-card .card-label {
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: rgba(235, 242, 255, 0.78);
        }

        .paiement-card .card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
        }

        .paiement-card.budget .card-icon {
            background: rgba(90, 168, 255, 0.18);
            color: #8ec5ff;
        }
        .paiement-card.paye .card-icon {
            background: rgba(61, 207, 138, 0.18);
            color: #7ee8b0;
        }
        .paiement-card.soldes .card-icon {
            background: rgba(255, 107, 120, 0.18);
            color: #ff9aa0;
        }

        .paiement-card .card-value {
            position: relative;
            z-index: 1;
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .paiement-card.budget .card-value { color: #9fd0ff; }
        .paiement-card.paye .card-value { color: #7ee8b0; }
        .paiement-card.soldes .card-value { color: #ff8a94; }

        html[data-theme="light"] .paiement-card.budget {
            background: linear-gradient(145deg, #eaf3ff, #d7e8ff);
            border-color: rgba(30, 111, 217, 0.28);
        }
        html[data-theme="light"] .paiement-card.paye {
            background: linear-gradient(145deg, #e8f8f0, #d4f0e2);
            border-color: rgba(20, 150, 92, 0.28);
        }
        html[data-theme="light"] .paiement-card.soldes {
            background: linear-gradient(145deg, #ffecee, #ffd8dc);
            border-color: rgba(209, 43, 58, 0.25);
        }

        html[data-theme="light"] .paiement-card .card-label { color: #3a4f6a; }
        html[data-theme="light"] .paiement-card.budget .card-value { color: #1565c0; }
        html[data-theme="light"] .paiement-card.paye .card-value { color: #14965c; }
        html[data-theme="light"] .paiement-card.soldes .card-value { color: #d12b3a; }

        .balance-section {
            margin-top: 0;
            margin-bottom: 0;
            padding: 0.85rem 1.15rem 1rem;
            border-radius: 16px;
            background: linear-gradient(165deg, rgba(16, 32, 54, 0.92), rgba(10, 22, 40, 0.88));
            border: 1px solid rgba(110, 168, 255, 0.2);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
            animation: fadeUp 0.5s ease 0.15s both;
        }

        html[data-theme="light"] .balance-section {
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.96), rgba(238, 244, 252, 0.92));
        }

        .balance-toolbar {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .balance-head {
            margin-bottom: 0;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .balance-head h2 {
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .btn-toggle-cards {
            height: 34px;
            padding: 0 0.85rem;
            border-radius: 10px;
            border: 1px solid rgba(110, 168, 255, 0.28);
            background: rgba(59, 158, 255, 0.12);
            color: var(--accent-soft);
            font-family: inherit;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-toggle-cards:hover {
            background: rgba(59, 158, 255, 0.2);
        }

        .balance-section .search-bar {
            margin: 0;
            padding: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            flex: 1 1 420px;
            max-width: 760px;
        }

        html[data-theme="light"] .balance-section .search-bar {
            background: transparent;
            border: none;
        }

        .balance-section .table-wrap {
            margin: 0.75rem 0 0;
            border: 1px solid rgba(110, 168, 255, 0.14);
            background: rgba(6, 14, 26, 0.35);
        }

        html[data-theme="light"] .balance-section .table-wrap {
            background: rgba(255, 255, 255, 0.55);
            border-color: rgba(30, 90, 180, 0.12);
        }

        #panel-dashboard.cards-hidden .cards {
            display: none !important;
        }

        #panel-dashboard.cards-hidden .balance-section .search-bar {
            display: none !important;
        }

        #panel-dashboard.active.cards-hidden {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 5.5rem);
        }

        #panel-dashboard.active.cards-hidden .balance-section {
            margin-top: 0;
            margin-bottom: 0;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: visible;
        }

        #panel-dashboard.active.cards-hidden .balance-section .panel-freeze {
            flex: 0 0 auto;
            position: relative;
            top: auto;
            z-index: 2;
            margin: 0 0 0.55rem;
            padding: 0 0 0.35rem;
        }

        #panel-dashboard.active.cards-hidden .balance-section .table-wrap.table-freeze-body {
            flex: 1 1 auto;
            max-height: none;
            height: auto;
            min-height: 0;
            overflow: visible;
        }

        html[data-theme="light"] .btn-toggle-cards {
            background: rgba(30, 111, 217, 0.08);
            border-color: rgba(30, 111, 217, 0.22);
            color: #1e6fd9;
        }

        .statue-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.28rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            border: 1px solid transparent;
        }

        .statue-badge.actif {
            color: #7ee8b0;
            background: rgba(61, 207, 138, 0.14);
            border-color: rgba(61, 207, 138, 0.35);
        }

        .statue-badge.attente {
            color: #ffc857;
            background: rgba(240, 180, 41, 0.14);
            border-color: rgba(240, 180, 41, 0.35);
        }

        .statue-badge.annule {
            color: #ff9aa0;
            background: rgba(240, 113, 120, 0.14);
            border-color: rgba(240, 113, 120, 0.35);
        }

        .statue-badge.execute {
            color: #7ee8b0;
            background: rgba(61, 207, 138, 0.14);
            border-color: rgba(61, 207, 138, 0.35);
        }

        .card-hint {
            font-size: 0.65rem;
            color: rgba(210, 224, 245, 0.55);
            font-weight: 400;
            margin-top: 0.15rem;
        }

        /* —— Mode clair : surfaces & textes —— */
        html[data-theme="light"] .sidebar {
            background: linear-gradient(180deg, #eef4fc 0%, #e4eef9 100%);
            box-shadow: 4px 0 24px rgba(30, 90, 180, 0.06);
        }

        html[data-theme="light"] .nav-item {
            color: rgba(15, 35, 65, 0.78);
        }

        html[data-theme="light"] .nav-item:hover,
        html[data-theme="light"] .nav-item.active,
        html[data-theme="light"] .nav-parent.active {
            background: rgba(30, 111, 217, 0.1);
            color: #0a1628;
        }

        html[data-theme="light"] .submenu a {
            color: rgba(20, 45, 80, 0.7);
        }

        html[data-theme="light"] .submenu a:hover,
        html[data-theme="light"] .submenu a.active {
            background: rgba(30, 111, 217, 0.1);
            color: #0a1628;
        }

        html[data-theme="light"] .side-foot {
            color: rgba(20, 40, 70, 0.55);
        }

        html[data-theme="light"] .navbar {
            background: rgba(244, 248, 255, 0.88);
        }

        html[data-theme="light"] .brand-tagline {
            color: rgba(20, 55, 110, 0.65);
            text-shadow: none;
        }

        html[data-theme="light"] .brand-glow {
            background: linear-gradient(100deg, #1e6fd9 0%, #0a1628 40%, #3b9eff 70%, #1e6fd9 100%);
            -webkit-background-clip: text;
            background-clip: text;
            filter: none;
        }

        html[data-theme="light"] .submenu-icon {
            background: rgba(30, 111, 217, 0.08);
            border-color: rgba(30, 111, 217, 0.2);
            color: #1e6fd9;
        }

        html[data-theme="light"] .user-chip {
            background: rgba(30, 111, 217, 0.08);
            border-color: rgba(30, 111, 217, 0.2);
            color: #0a1628;
        }

        html[data-theme="light"] .btn-theme-toggle {
            background: rgba(30, 111, 217, 0.08);
            border-color: rgba(30, 111, 217, 0.2);
            color: #0a1628;
        }

        html[data-theme="light"] .menu-toggle,
        html[data-theme="light"] .sidebar-close {
            background: rgba(30, 111, 217, 0.08);
            border-color: rgba(30, 111, 217, 0.2);
            color: #0a1628;
        }

        html[data-theme="light"] .card {
            background: linear-gradient(165deg, #ffffff 0%, #f3f7fd 100%);
            border-color: rgba(30, 90, 180, 0.14);
            box-shadow: 0 8px 24px rgba(30, 90, 180, 0.08);
        }

        html[data-theme="light"] .card:hover {
            border-color: rgba(30, 111, 217, 0.28);
            box-shadow: 0 10px 28px rgba(30, 90, 180, 0.12);
        }

        html[data-theme="light"] .card.actif .card-value { color: #14965c; }
        html[data-theme="light"] .card.attente .card-value { color: #b8860b; }
        html[data-theme="light"] .card.annule .card-value { color: #c9434d; }
        html[data-theme="light"] .card.execute .card-value { color: #14965c; }
        html[data-theme="light"] .card.revenu .card-value { color: #0f8fa8; }
        html[data-theme="light"] .card.brahim .card-value { color: #b8860b; }
        html[data-theme="light"] .card.solde .card-value { color: #6b4fd6; }

        html[data-theme="light"] .card-hint {
            color: rgba(20, 40, 70, 0.5);
        }

        html[data-theme="light"] .balance-section {
            background: linear-gradient(165deg, #ffffff 0%, #f3f7fd 100%);
            border-color: rgba(30, 90, 180, 0.14);
            box-shadow: 0 8px 24px rgba(30, 90, 180, 0.08);
        }

        html[data-theme="light"] .table-wrap {
            background: #ffffff;
            border-color: rgba(30, 90, 180, 0.14);
            box-shadow: 0 6px 20px rgba(30, 90, 180, 0.07);
        }

        html[data-theme="light"] .data-table th {
            background: #eaf1fb;
            color: #1e6fd9;
            border-bottom-color: rgba(30, 90, 180, 0.12);
        }

        html[data-theme="light"] .data-table td {
            color: #15263f;
            border-bottom-color: rgba(30, 90, 180, 0.1);
        }

        html[data-theme="light"] .data-table tbody tr:hover {
            background: rgba(30, 111, 217, 0.05);
        }

        html[data-theme="light"] .data-table tbody tr.row-execute {
            background: rgba(20, 150, 92, 0.16);
        }

        html[data-theme="light"] .data-table tbody tr.row-execute:hover {
            background: rgba(20, 150, 92, 0.24);
        }

        html[data-theme="light"] .data-table tbody tr.row-execute td {
            color: #0f5132;
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-a-voir {
            background: rgba(240, 180, 41, 0.28);
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-a-voir:hover {
            background: rgba(240, 180, 41, 0.38);
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-a-voir td {
            color: #7a5a00;
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-confirme {
            background: rgba(140, 148, 160, 0.28);
            opacity: 0.72;
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-confirme:hover {
            background: rgba(140, 148, 160, 0.36);
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-confirme td {
            color: #6b7280;
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-inj {
            background: rgba(220, 53, 69, 0.22);
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-inj:hover {
            background: rgba(220, 53, 69, 0.32);
        }

        html[data-theme="light"] .data-table tbody tr.row-relance-inj td {
            color: #842029;
        }

        html[data-theme="light"] .data-table td.solde-cell {
            color: #d12b3a;
            text-shadow: none;
        }

        html[data-theme="light"] .search-bar {
            background: #ffffff;
            border-color: rgba(30, 90, 180, 0.14);
        }

        html[data-theme="light"] .search-field input,
        html[data-theme="light"] .search-field select {
            background: #f4f8ff;
            border-color: rgba(30, 90, 180, 0.2);
            color: #0a1628;
        }

        html[data-theme="light"] .action-btn {
            background: rgba(30, 111, 217, 0.05);
            border-color: rgba(30, 90, 180, 0.16);
            color: #3a5578;
        }

        html[data-theme="light"] .btn-ghost {
            background: #eef4fc;
            border-color: rgba(30, 90, 180, 0.2);
            color: #0a1628;
        }

        html[data-theme="light"] .modal {
            background: linear-gradient(165deg, #ffffff, #eef4fc);
            border-color: rgba(30, 90, 180, 0.18);
            box-shadow: 0 24px 60px rgba(30, 90, 180, 0.18);
        }

        html[data-theme="light"] .modal-backdrop {
            background: rgba(20, 40, 70, 0.35);
        }

        html[data-theme="light"] .modal-close {
            background: rgba(30, 111, 217, 0.08);
            color: #0a1628;
        }

        html[data-theme="light"] .modal-body input,
        html[data-theme="light"] .modal-body select,
        html[data-theme="light"] .modal-body textarea {
            background: #f4f8ff;
            border-color: rgba(30, 90, 180, 0.2);
            color: #0a1628;
        }

        html[data-theme="light"] .modal-body input[readonly] {
            background: #e8eef7;
            color: rgba(15, 35, 65, 0.7);
        }

        html[data-theme="light"] .btn-logout {
            background:
                linear-gradient(135deg, rgba(240, 113, 120, 0.12), rgba(220, 60, 80, 0.06)),
                #ffffff;
            color: #c9434d;
        }

        html[data-theme="light"] .sidebar-backdrop {
            background: rgba(20, 40, 70, 0.35);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(59, 158, 255, 0.08);
            color: var(--text);
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .menu-toggle svg { width: 20px; height: 20px; }

        @media (max-width: 1100px) {
            .shell { grid-template-columns: 1fr; }

            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                z-index: 40;
                width: min(280px, 86vw);
                transform: translateX(-105%);
                transition: transform 0.25s ease;
                box-shadow: 20px 0 50px rgba(0, 0, 0, 0.4);
            }

            .sidebar.open { transform: translateX(0); }

            .sidebar-close { display: inline-flex; }

            .sidebar-backdrop { display: block; }

            .menu-toggle { display: inline-flex; }

            .cards {
                grid-template-columns: repeat(2, 1fr);
                position: sticky;
                top: 3.6rem;
            }

            .paiement-cards {
                grid-template-columns: 1fr;
            }

            .panel-freeze {
                top: 3.6rem;
            }

            .table-wrap.table-freeze-body {
                max-height: calc(100vh - 16rem);
            }

            #panel-dashboard .balance-section .table-wrap.table-freeze-body,
            #panel-fiche-relance .table-wrap.table-freeze-body {
                max-height: none;
                overflow: visible;
            }

            #panel-fiche-paiement .table-wrap.table-freeze-body {
                max-height: calc(100vh - 22rem);
            }

            .paiement-sticky-lock {
                position: sticky;
                top: 3.6rem;
                margin-bottom: 1rem;
            }

            .card {
                min-height: 78px;
                height: 78px;
            }
            .card-value { font-size: 1.3rem; }

            .table-wrap {
                -webkit-overflow-scrolling: touch;
            }

            .navbar, .content { padding-left: 1.1rem; padding-right: 1.1rem; }
        }
    </style>
</head>
<body>
    <script>
        (function () {
            const theme = localStorage.getItem('evopro-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="side-brand">
                    <div class="mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 7h8.5a3.5 3.5 0 0 1 0 7H11" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M7 12h6.5a3 3 0 0 1 0 6H7" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M15 17l3 0 0-3" stroke="#b8e0ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="brand-glow" style="font-size:1.25rem;">Evo<span>Pro</span></div>
                </div>
                <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Fermer le menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="nav-list" aria-label="Navigation principale">
                @php
                    $can = fn (string $key): bool => in_array($key, $userPermissions ?? [], true);
                @endphp
                <a href="#dashboard" class="dashboard-nav-btn active{{ $can('dashboard') ? '' : ' nav-hidden' }}" data-panel="dashboard" data-auth="dashboard" id="dashboardNavBtn">
                    <span class="dashboard-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                    </span>
                    <span class="dashboard-nav-text">
                        <strong>Tableau de Bord</strong>
                        <small>Interface principale</small>
                    </span>
                </a>

                <div class="nav-sections">
                <div class="nav-group{{ ($can('fiche-client') || $can('fiche-relance')) ? '' : ' nav-hidden' }}" id="clientGroup">
                    <button type="button" class="nav-item nav-parent" id="clientToggle">
                        <span class="nav-left">
                            <span class="nav-icon clients" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </span>
                            Client
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-client" class="submenu-link{{ $can('fiche-client') ? '' : ' nav-hidden' }}" data-panel="fiche-client" data-auth="fiche-client">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><circle cx="10" cy="13" r="2"/><path d="M14 17a4 4 0 0 0-8 0"/></svg>
                            </span>
                            Fiche Client
                        </a>
                        <a href="#fiche-relance" class="submenu-link{{ $can('fiche-relance') ? '' : ' nav-hidden' }}" data-panel="fiche-relance" data-auth="fiche-relance">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8"/><path d="M8 13h5"/></svg>
                            </span>
                            Relance
                        </a>
                    </div>
                </div>
                <div class="nav-group{{ ($can('fiche-projet') || $can('fiche-evolution')) ? '' : ' nav-hidden' }}" id="projetGroup">
                    <button type="button" class="nav-item nav-parent" id="projetToggle">
                        <span class="nav-left">
                            <span class="nav-icon projets" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/><path d="M8 7v10"/><path d="M16 7v10"/></svg>
                            </span>
                            Projets
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-projet" class="submenu-link{{ $can('fiche-projet') ? '' : ' nav-hidden' }}" data-panel="fiche-projet" data-auth="fiche-projet">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            </span>
                            Fiche Projet
                        </a>
                        <a href="#fiche-evolution" class="submenu-link{{ $can('fiche-evolution') ? '' : ' nav-hidden' }}" data-panel="fiche-evolution" data-auth="fiche-evolution">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>
                            </span>
                            Evolution Travaux
                        </a>
                    </div>
                </div>
                <div class="nav-group{{ $can('fiche-paiement') ? '' : ' nav-hidden' }}" id="paiementGroup">
                    <button type="button" class="nav-item nav-parent" id="paiementToggle">
                        <span class="nav-left">
                            <span class="nav-icon paiements" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            </span>
                            Paiement
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-paiement" class="submenu-link{{ $can('fiche-paiement') ? '' : ' nav-hidden' }}" data-panel="fiche-paiement" data-auth="fiche-paiement">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </span>
                            Fiche Paiement
                        </a>
                    </div>
                </div>
                <a href="#charges" class="nav-item{{ $can('charges') ? '' : ' nav-hidden' }}" data-panel="dashboard" data-auth="charges">
                    <span class="nav-icon charges" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                    </span>
                    Charges
                </a>
                <a href="#suivie" class="nav-item{{ $can('suivie') ? '' : ' nav-hidden' }}" data-panel="dashboard" data-auth="suivie">
                    <span class="nav-icon suivie" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
                    </span>
                    Suivie Monétaire
                </a>
                <a href="#rapports" class="nav-item{{ $can('rapports') ? '' : ' nav-hidden' }}" data-panel="dashboard" data-auth="rapports">
                    <span class="nav-icon rapports" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg>
                    </span>
                    Rapports
                </a>
                <div class="nav-group{{ ($can('fiche-utilisateur') || $can('fiche-autorisation')) ? '' : ' nav-hidden' }}" id="configGroup">
                    <button type="button" class="nav-item nav-parent" id="configToggle">
                        <span class="nav-left">
                            <span class="nav-icon config" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9c.3.6.9 1 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                            </span>
                            Configuration
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-utilisateur" class="submenu-link{{ $can('fiche-utilisateur') ? '' : ' nav-hidden' }}" data-panel="fiche-utilisateur" data-auth="fiche-utilisateur">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            Utilisateur
                        </a>
                        <a href="#fiche-autorisation" class="submenu-link{{ $can('fiche-autorisation') ? '' : ' nav-hidden' }}" data-panel="fiche-autorisation" data-auth="fiche-autorisation">
                            <span class="submenu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            Autorisation
                        </a>
                    </div>
                </div>
                </div>
            </nav>

            <div class="sidebar-footer">
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout btn-sidebar-logout" aria-label="Se déconnecter">
                        <span class="btn-logout-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </span>
                        <span class="btn-logout-text">Se Déconnecter</span>
                    </button>
                </form>
                <div class="side-foot">EvoPro — Système de Gestion</div>
            </div>
        </aside>

        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        <div class="main">
            <header class="navbar">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                    <button class="menu-toggle" type="button" id="menuToggle" aria-label="Ouvrir le menu">
                        <svg class="icon-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                    <div class="navbar-brand">
                        <div class="brand-glow">Evo<span>Pro</span></div>
                        <span class="brand-tagline">La Solution qui Gère</span>
                    </div>
                </div>

                <div class="nav-right">
                    <button type="button" class="btn-theme-toggle" id="themeToggleNav" aria-label="Changer le thème" style="width:auto;padding:0.48rem 0.85rem;">
                        <span class="theme-icon-dark-nav" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        </span>
                        <span class="theme-icon-light-nav" aria-hidden="true" style="display:none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                        </span>
                    </button>
                    <div class="user-chip">
                        <img class="avatar" src="{{ asset('images/profile-zerragui.png') }}" alt="Zerragui Abdelilah" width="28" height="28">
                        <span>Zerragui Abdelilah</span>
                    </div>
                </div>
            </header>

            <main class="content">
                <section class="panel active" id="panel-dashboard">
                    <section class="cards" aria-label="Statistiques">
                        <article class="card actif">
                            <div class="card-top">
                                <span class="card-label">Projets Actif</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ $dashboardCounts['actif'] ?? 0 }}</div>
                        </article>

                        <article class="card attente">
                            <div class="card-top">
                                <span class="card-label">Projets en Attente</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ $dashboardCounts['attente'] ?? 0 }}</div>
                        </article>

                        <article class="card execute">
                            <div class="card-top">
                                <span class="card-label">Projets Exécutés</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ $dashboardCounts['execute'] ?? 0 }}</div>
                        </article>

                        <article class="card revenu ayda">
                            <div class="card-top">
                                <span class="card-label">Revenu Ayda</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ number_format($revenuAyda ?? 0, 2, '.', ' ') }}</div>
                        </article>

                        <article class="card revenu brahim">
                            <div class="card-top">
                                <span class="card-label">Revenue Brahim</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ number_format($revenuBrahim ?? 0, 2, '.', ' ') }}</div>
                        </article>

                        <article class="card solde">
                            <div class="card-top">
                                <span class="card-label">Total Solde</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ number_format($totalSolde ?? 0, 2, '.', ' ') }}</div>
                        </article>
                    </section>

                    <section class="balance-section" aria-label="Tableau des relances">
                        <div class="panel-freeze">
                            <div class="balance-toolbar">
                                <div class="balance-head">
                                    <h2>Relances</h2>
                                    <button type="button" class="btn-toggle-cards" id="btnToggleDashboardCards" aria-pressed="false">Masquer</button>
                                </div>
                                <div class="search-bar balance-search" aria-label="Recherche relances" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
                                    <div class="search-field">
                                        <label for="filter_dashboard_relance_mois">Mois</label>
                                        <select id="filter_dashboard_relance_mois">
                                            <option value="">TOUS LES MOIS</option>
                                            @php
                                                $moisDashboardRelances = collect($relances ?? [])
                                                    ->map(function ($r) {
                                                        $parts = explode('/', $r['date'] ?? '');
                                                        return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                                    })
                                                    ->filter()
                                                    ->unique()
                                                    ->sort()
                                                    ->values();
                                            @endphp
                                            @foreach ($moisDashboardRelances as $mois)
                                                <option value="{{ $mois }}">{{ $mois }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="search-field">
                                        <label for="filter_dashboard_relance_statue">Statue</label>
                                        <select id="filter_dashboard_relance_statue">
                                            <option value="">TOUTES LES STATUES</option>
                                            <option value="a_voir">A VOIR</option>
                                            <option value="confirme">CONFIRME</option>
                                            <option value="inj">INJ</option>
                                            <option value="nv_tab">Nv Tab</option>
                                        </select>
                                    </div>
                                    <div class="search-field">
                                        <label for="filter_dashboard_relance_de">De</label>
                                        <input type="text" id="filter_dashboard_relance_de" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                                    </div>
                                    <div class="search-field">
                                        <label for="filter_dashboard_relance_a">A</label>
                                        <input type="text" id="filter_dashboard_relance_a" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-wrap table-freeze-body">
                            <table class="data-table data-table-relances">
                                <colgroup>
                                    <col style="width:7.5%">
                                    <col style="width:6%">
                                    <col style="width:8%">
                                    <col style="width:11%">
                                    <col style="width:10%">
                                    <col style="width:14%">
                                    <col style="width:7%">
                                    <col style="width:7%">
                                    <col style="width:9%">
                                    <col style="width:7%">
                                    <col style="width:8.5%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>ID</th>
                                        <th>Téléphone</th>
                                        <th>Nom Complet</th>
                                        <th>Titre Projet</th>
                                        <th>Description</th>
                                        <th>Budget</th>
                                        <th>Envoyé</th>
                                        <th>Statue</th>
                                        <th>A Rappeler</th>
                                        <th>Date Rappel</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboardRelancesTableBody">
                                    @forelse (($relances ?? []) as $relance)
                                        @php
                                            $statueRelanceDash = $relance['statue'] ?? '';
                                            $envoyeRelanceDash = $relance['envoye'] ?? '';
                                            $rappelerRelanceDash = $relance['a_rappeler'] ?? '';
                                            $rappelerRelanceDashLabel = $rappelerRelanceDash === 'oui' ? 'Oui' : ($rappelerRelanceDash === 'non' ? 'Non' : $rappelerRelanceDash);
                                            $partsRelanceDash = explode('/', $relance['date'] ?? '');
                                            $moisRelanceDash = count($partsRelanceDash) >= 3 ? $partsRelanceDash[1].'/'.$partsRelanceDash[2] : '';
                                        @endphp
                                        <tr
                                            data-id="{{ $relance['id'] ?? '' }}"
                                            data-mois="{{ $moisRelanceDash }}"
                                            data-statue="{{ $statueRelanceDash }}"
                                            data-date="{{ $relance['date'] ?? '' }}"
                                            data-import="{{ !empty($relance['from_import']) ? '1' : '0' }}"
                                            @class([
                                                'row-relance-a-voir' => $statueRelanceDash === 'a_voir',
                                                'row-relance-confirme' => $statueRelanceDash === 'confirme',
                                                'row-relance-inj' => $statueRelanceDash === 'inj',
                                            ])
                                        >
                                            <td>{{ $relance['date'] ?? '' }}</td>
                                            <td>{{ $relance['ref'] ?? '' }}</td>
                                            <td>
                                                <input
                                                    type="text"
                                                    class="relance-inline-input"
                                                    data-field="telephone"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    value="{{ $relance['telephone'] ?? '' }}"
                                                    aria-label="Téléphone"
                                                >
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    class="relance-inline-input"
                                                    data-field="nom_complet"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    value="{{ $relance['nom_complet'] ?? '' }}"
                                                    aria-label="Nom Complet"
                                                >
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    class="relance-inline-input"
                                                    data-field="titre_projet"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    value="{{ $relance['titre_projet'] ?? '' }}"
                                                    aria-label="Titre Projet"
                                                >
                                            </td>
                                            <td class="cell-wrap">
                                                <textarea
                                                    class="relance-inline-input relance-inline-textarea"
                                                    data-field="description"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    rows="2"
                                                    aria-label="Description"
                                                >{{ $relance['description'] ?? '' }}</textarea>
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    class="relance-inline-input relance-inline-budget"
                                                    data-field="budget"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    value="{{ number_format((float) ($relance['budget'] ?? 0), 2, '.', '') }}"
                                                    inputmode="decimal"
                                                    aria-label="Budget"
                                                >
                                            </td>
                                            <td>
                                                <div
                                                    class="envoye-switch"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    data-value="{{ $envoyeRelanceDash }}"
                                                    role="group"
                                                    aria-label="Envoyé"
                                                >
                                                    <button type="button" class="envoye-opt{{ $envoyeRelanceDash === 'lien' ? ' is-active' : '' }}" data-value="lien">Lien</button>
                                                    <button type="button" class="envoye-opt{{ $envoyeRelanceDash === 'conception' ? ' is-active' : '' }}" data-value="conception">Concep</button>
                                                </div>
                                            </td>
                                            <td>
                                                <form method="post" action="{{ url('/relances/'.$relance['id'].'/statue') }}" class="statue-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="from_dashboard" value="1">
                                                    <select
                                                        name="statue"
                                                        class="statue-select {{ $statueRelanceDash }}"
                                                        aria-label="Choisir la statue de la relance"
                                                        onchange="this.form.submit()"
                                                    >
                                                        <option value="a_voir" @selected($statueRelanceDash === 'a_voir')>A VOIR</option>
                                                        <option value="confirme" @selected($statueRelanceDash === 'confirme')>CONFIRME</option>
                                                        <option value="inj" @selected($statueRelanceDash === 'inj')>INJ</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <select
                                                    class="relance-inline-input relance-inline-select"
                                                    data-field="a_rappeler"
                                                    data-id="{{ $relance['id'] ?? '' }}"
                                                    aria-label="A Rappeler"
                                                >
                                                    <option value="oui" @selected($rappelerRelanceDash === 'oui')>Oui</option>
                                                    <option value="non" @selected($rappelerRelanceDash === 'non')>Non</option>
                                                </select>
                                            </td>
                                            <td>
                                                <form method="post" action="{{ url('/relances/'.$relance['id'].'/date-rappel') }}" class="rappel-date-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="from_dashboard" value="1">
                                                    <input
                                                        type="text"
                                                        name="date_rappel"
                                                        class="rappel-date-input"
                                                        value="{{ $relance['date_rappel'] ?? '' }}"
                                                        placeholder="JJ/MM/AAAA"
                                                        maxlength="10"
                                                        inputmode="numeric"
                                                        autocomplete="off"
                                                        aria-label="Modifier la date de rappel"
                                                    >
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="11" class="empty">Aucune relance enregistrée.</td>
                                        </tr>
                                    @endforelse
                                    <tr id="dashboardRelancesNoResult" class="empty-row" style="display:none;">
                                        <td colspan="11" class="empty">Aucun résultat pour cette recherche.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                </section>

                <section class="panel" id="panel-fiche-client">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Fiche Client</h1>
                            </div>
                            <button type="button" class="btn-add" id="btnAddClient">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Ajouter
                            </button>
                        </div>

                        <div class="search-bar" aria-label="Recherche clients" style="grid-template-columns: repeat(1, minmax(0, 1fr)); max-width: 280px;">
                            <div class="search-field">
                                <label for="filter_client_mois">Mois</label>
                                <select id="filter_client_mois">
                                    <option value="">TOUS LES MOIS</option>
                                    @php
                                        $moisClients = collect($clients ?? [])
                                            ->map(function ($c) {
                                                $parts = explode('/', $c['date'] ?? '');
                                                return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                            })
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($moisClients as $mois)
                                        <option value="{{ $mois }}">{{ $mois }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Réf</th>
                                    <th>Nom Client</th>
                                    <th>Ville</th>
                                    <th>Activité</th>
                                    <th>Contact</th>
                                    <th>Solde</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="clientsTableBody">
                                @forelse (($clients ?? []) as $client)
                                    @php
                                        $partsClient = explode('/', $client['date'] ?? '');
                                        $moisClient = count($partsClient) >= 3 ? $partsClient[1].'/'.$partsClient[2] : '';
                                    @endphp
                                    <tr data-id="{{ $client['id'] }}" data-mois="{{ $moisClient }}">
                                        <td>{{ $client['date'] }}</td>
                                        <td>{{ $client['ref'] }}</td>
                                        <td>{{ $client['nom'] }}</td>
                                        <td>{{ $client['ville'] }}</td>
                                        <td>{{ $client['activite'] }}</td>
                                        <td>{{ $client['contact'] }}</td>
                                        <td class="solde-cell">{{ number_format($client['solde'], 2, '.', ' ') }}</td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/clients/'.$client['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce client ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                                <button type="button" class="action-btn pdf" title="PDF" aria-label="PDF">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="empty">Aucun client enregistré. Cliquez sur Ajouter.</td>
                                    </tr>
                                @endforelse
                                <tr id="clientsNoResult" class="empty-row" style="display:none;">
                                    <td colspan="8" class="empty">Aucun résultat pour cette recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-relance">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Relance</h1>
                            </div>
                            <button type="button" class="btn-add" id="btnAddRelance">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Nouveau Prospect
                            </button>
                        </div>

                        <div class="search-bar" aria-label="Recherche relances" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
                            <div class="search-field">
                                <label for="filter_relance_mois">Mois</label>
                                <select id="filter_relance_mois">
                                    <option value="">TOUS LES MOIS</option>
                                    @php
                                        $moisRelances = collect($relances ?? [])
                                            ->map(function ($r) {
                                                $parts = explode('/', $r['date'] ?? '');
                                                return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                            })
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($moisRelances as $mois)
                                        <option value="{{ $mois }}">{{ $mois }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="filter_relance_statue">Statue</label>
                                <select id="filter_relance_statue">
                                    <option value="">TOUTES LES STATUES</option>
                                    <option value="a_voir">A VOIR</option>
                                    <option value="confirme">CONFIRME</option>
                                    <option value="inj">INJ</option>
                                    <option value="nv_tab">Nv Tab</option>
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="filter_relance_de">De</label>
                                <input type="text" id="filter_relance_de" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                            </div>
                            <div class="search-field">
                                <label for="filter_relance_a">A</label>
                                <input type="text" id="filter_relance_a" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table data-table-relances">
                            <colgroup>
                                <col style="width:7%">
                                <col style="width:5.5%">
                                <col style="width:7.5%">
                                <col style="width:10%">
                                <col style="width:9%">
                                <col style="width:12%">
                                <col style="width:6.5%">
                                <col style="width:6.5%">
                                <col style="width:8%">
                                <col style="width:6.5%">
                                <col style="width:8%">
                                <col style="width:7.5%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>ID</th>
                                    <th>Téléphone</th>
                                    <th>Nom Complet</th>
                                    <th>Titre Projet</th>
                                    <th>Description</th>
                                    <th>Budget</th>
                                    <th>Envoyé</th>
                                    <th>Statue</th>
                                    <th>A Rappeler</th>
                                    <th>Date Rappel</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="relancesTableBody">
                                @forelse (($relances ?? []) as $relance)
                                    @php
                                        $statueRelance = $relance['statue'] ?? '';
                                        $envoyeRelance = $relance['envoye'] ?? '';
                                        $rappelerRelance = $relance['a_rappeler'] ?? '';
                                        $rappelerRelanceLabel = $rappelerRelance === 'oui' ? 'Oui' : ($rappelerRelance === 'non' ? 'Non' : $rappelerRelance);
                                        $partsRelance = explode('/', $relance['date'] ?? '');
                                        $moisRelance = count($partsRelance) >= 3 ? $partsRelance[1].'/'.$partsRelance[2] : '';
                                    @endphp
                                    <tr
                                        data-id="{{ $relance['id'] }}"
                                        data-mois="{{ $moisRelance }}"
                                        data-statue="{{ $statueRelance }}"
                                        data-date="{{ $relance['date'] ?? '' }}"
                                        data-import="{{ !empty($relance['from_import']) ? '1' : '0' }}"
                                        @class([
                                            'row-relance-a-voir' => $statueRelance === 'a_voir',
                                            'row-relance-confirme' => $statueRelance === 'confirme',
                                            'row-relance-inj' => $statueRelance === 'inj',
                                        ])
                                    >
                                        <td>{{ $relance['date'] ?? '' }}</td>
                                        <td>{{ $relance['ref'] ?? '' }}</td>
                                        <td>
                                            <input
                                                type="text"
                                                class="relance-inline-input"
                                                data-field="telephone"
                                                data-id="{{ $relance['id'] }}"
                                                value="{{ $relance['telephone'] ?? '' }}"
                                                aria-label="Téléphone"
                                            >
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="relance-inline-input"
                                                data-field="nom_complet"
                                                data-id="{{ $relance['id'] }}"
                                                value="{{ $relance['nom_complet'] ?? '' }}"
                                                aria-label="Nom Complet"
                                            >
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="relance-inline-input"
                                                data-field="titre_projet"
                                                data-id="{{ $relance['id'] }}"
                                                value="{{ $relance['titre_projet'] ?? '' }}"
                                                aria-label="Titre Projet"
                                            >
                                        </td>
                                        <td class="cell-wrap">
                                            <textarea
                                                class="relance-inline-input relance-inline-textarea"
                                                data-field="description"
                                                data-id="{{ $relance['id'] }}"
                                                rows="2"
                                                aria-label="Description"
                                            >{{ $relance['description'] ?? '' }}</textarea>
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="relance-inline-input relance-inline-budget"
                                                data-field="budget"
                                                data-id="{{ $relance['id'] }}"
                                                value="{{ number_format((float) ($relance['budget'] ?? 0), 2, '.', '') }}"
                                                inputmode="decimal"
                                                aria-label="Budget"
                                            >
                                        </td>
                                        <td>
                                            <div
                                                class="envoye-switch"
                                                data-id="{{ $relance['id'] }}"
                                                data-value="{{ $envoyeRelance }}"
                                                role="group"
                                                aria-label="Envoyé"
                                            >
                                                <button type="button" class="envoye-opt{{ $envoyeRelance === 'lien' ? ' is-active' : '' }}" data-value="lien">Lien</button>
                                                <button type="button" class="envoye-opt{{ $envoyeRelance === 'conception' ? ' is-active' : '' }}" data-value="conception">Concep</button>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="post" action="{{ url('/relances/'.$relance['id'].'/statue') }}" class="statue-form">
                                                @csrf
                                                @method('PATCH')
                                                <select
                                                    name="statue"
                                                    class="statue-select {{ $statueRelance }}"
                                                    aria-label="Choisir la statue de la relance"
                                                    onchange="this.form.submit()"
                                                >
                                                    <option value="a_voir" @selected($statueRelance === 'a_voir')>A VOIR</option>
                                                    <option value="confirme" @selected($statueRelance === 'confirme')>CONFIRME</option>
                                                    <option value="inj" @selected($statueRelance === 'inj')>INJ</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <select
                                                class="relance-inline-input relance-inline-select"
                                                data-field="a_rappeler"
                                                data-id="{{ $relance['id'] }}"
                                                aria-label="A Rappeler"
                                            >
                                                <option value="oui" @selected($rappelerRelance === 'oui')>Oui</option>
                                                <option value="non" @selected($rappelerRelance === 'non')>Non</option>
                                            </select>
                                        </td>
                                        <td>
                                            <form method="post" action="{{ url('/relances/'.$relance['id'].'/date-rappel') }}" class="rappel-date-form">
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="text"
                                                    name="date_rappel"
                                                    class="rappel-date-input"
                                                    value="{{ $relance['date_rappel'] ?? '' }}"
                                                    placeholder="JJ/MM/AAAA"
                                                    maxlength="10"
                                                    inputmode="numeric"
                                                    autocomplete="off"
                                                    aria-label="Modifier la date de rappel"
                                                >
                                            </form>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/relances/'.$relance['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette relance ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="12" class="empty">Aucune relance enregistrée. Cliquez sur Nouveau Prospect.</td>
                                    </tr>
                                @endforelse
                                <tr id="relancesNoResult" class="empty-row" style="display:none;">
                                    <td colspan="12" class="empty">Aucun résultat pour cette recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-projet">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Fiche Projet</h1>
                            </div>
                            <button type="button" class="btn-add" id="btnAddProjet">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Nouveau Projet
                            </button>
                        </div>

                        <div class="search-bar" aria-label="Recherche projets" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                        <div class="search-field">
                            <label for="filter_projet_mois">Mois</label>
                            <select id="filter_projet_mois">
                                <option value="">TOUS LES MOIS</option>
                                @php
                                    $moisProjets = collect($projets ?? [])
                                        ->map(function ($p) {
                                            $parts = explode('/', $p['date'] ?? '');
                                            return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                        })
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach ($moisProjets as $mois)
                                    <option value="{{ $mois }}">{{ $mois }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="filter_projet_client">Client</label>
                            <select id="filter_projet_client">
                                <option value="">TOUS LES CLIENTS</option>
                                @php
                                    $clientsProjetFilter = collect($clients ?? [])
                                        ->pluck('nom')
                                        ->merge(collect($projets ?? [])->pluck('client'))
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach ($clientsProjetFilter as $clientNom)
                                    <option value="{{ mb_strtolower($clientNom) }}">{{ $clientNom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="filter_projet_statue">Statue</label>
                            <select id="filter_projet_statue">
                                <option value="">TOUTES LES STATUES</option>
                                <option value="actif">EN COURS</option>
                                <option value="attente">EN ATTENTE</option>
                                <option value="annule">ANNULÉ</option>
                                <option value="execute">EXÉCUTÉ</option>
                            </select>
                        </div>
                    </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Réf</th>
                                    <th>Nom Projet</th>
                                    <th>Désignation</th>
                                    <th>Client</th>
                                    <th>Budget</th>
                                    <th>Montant payé</th>
                                    <th>Solde</th>
                                    <th>Statue</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="projetsTableBody">
                                @forelse (($projets ?? []) as $projet)
                                    @php
                                        $statueKey = $projet['statut'] ?? 'attente';
                                        $dateParts = explode('/', $projet['date'] ?? '');
                                        $moisKey = count($dateParts) >= 3 ? $dateParts[1].'/'.$dateParts[2] : '';
                                    @endphp
                                    <tr
                                        data-id="{{ $projet['id'] }}"
                                        data-mois="{{ $moisKey }}"
                                        data-client="{{ mb_strtolower($projet['client'] ?? '') }}"
                                        data-statue="{{ $statueKey }}"
                                        @class(['row-execute' => $statueKey === 'execute'])
                                    >
                                        <td>{{ $projet['date'] }}</td>
                                        <td>{{ $projet['ref'] }}</td>
                                        <td>{{ $projet['nom'] }}</td>
                                        <td>{{ $projet['designation'] }}</td>
                                        <td>{{ $projet['client'] }}</td>
                                        <td>{{ number_format($projet['budget'], 2, '.', ' ') }}</td>
                                        <td>{{ number_format($projet['montant_paye'], 2, '.', ' ') }}</td>
                                        <td class="solde-cell">{{ number_format($projet['solde'], 2, '.', ' ') }}</td>
                                        <td>
                                            <form method="post" action="{{ url('/projets/'.$projet['id'].'/statut') }}" class="statue-form">
                                                @csrf
                                                @method('PATCH')
                                                <select
                                                    name="statut"
                                                    class="statue-select {{ $statueKey }}"
                                                    aria-label="Choisir la statue du projet"
                                                    onchange="this.form.submit()"
                                                >
                                                    <option value="actif" @selected($statueKey === 'actif')>EN COURS</option>
                                                    <option value="attente" @selected($statueKey === 'attente')>EN ATTENTE</option>
                                                    <option value="annule" @selected($statueKey === 'annule')>ANNULÉ</option>
                                                    <option value="execute" @selected($statueKey === 'execute')>EXÉCUTÉ</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" data-action="voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" data-action="modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/projets/'.$projet['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce projet ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                                <button type="button" class="action-btn pdf" title="PDF" aria-label="PDF">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="10" class="empty">Aucun projet enregistré. Cliquez sur Nouveau Projet.</td>
                                    </tr>
                                @endforelse
                                <tr id="projetsNoResult" class="empty-row" style="display:none;">
                                    <td colspan="10" class="empty">Aucun projet ne correspond à la recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-evolution">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Evolution Travaux</h1>
                            </div>
                            <div class="toolbar-actions">
                                <button type="button" class="btn-add" id="btnAddEvolution">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    Ajouter
                                </button>
                                <button type="button" class="btn-ghost" id="btnCloseEvolution">Fermer</button>
                            </div>
                        </div>

                        <div class="search-bar" aria-label="Recherche évolutions" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                        <div class="search-field">
                            <label for="filter_evolution_mois">Mois</label>
                            <select id="filter_evolution_mois">
                                <option value="">TOUS LES MOIS</option>
                                @php
                                    $moisEvolutions = collect($evolutions ?? [])
                                        ->map(function ($e) {
                                            $parts = explode('/', $e['date'] ?? '');
                                            return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                        })
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach ($moisEvolutions as $mois)
                                    <option value="{{ $mois }}">{{ $mois }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="filter_evolution_projet">Titre Projet</label>
                            <select id="filter_evolution_projet">
                                <option value="">TOUS LES PROJETS</option>
                                @php
                                    $projetsEvolutionFilter = collect($projets ?? [])
                                        ->pluck('nom')
                                        ->merge(collect($evolutions ?? [])->pluck('titre_projet'))
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach ($projetsEvolutionFilter as $titre)
                                    <option value="{{ mb_strtolower($titre) }}">{{ $titre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Titre Projet</th>
                                    <th>Description</th>
                                    <th>Pull</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="evolutionsTableBody">
                                @forelse (($evolutions ?? []) as $evolution)
                                    @php
                                        $pullKey = $evolution['pull'] ?? 'non';
                                        $dateParts = explode('/', $evolution['date'] ?? '');
                                        $moisKey = count($dateParts) >= 3 ? $dateParts[1].'/'.$dateParts[2] : '';
                                    @endphp
                                    <tr
                                        data-id="{{ $evolution['id'] }}"
                                        data-mois="{{ $moisKey }}"
                                        data-projet="{{ mb_strtolower($evolution['titre_projet'] ?? '') }}"
                                    >
                                        <td>{{ $evolution['date'] }}</td>
                                        <td>{{ $evolution['titre_projet'] }}</td>
                                        <td class="cell-wrap">{{ $evolution['description'] }}</td>
                                        <td>
                                            <form method="post" action="{{ url('/evolutions/'.$evolution['id'].'/pull') }}" class="statue-form">
                                                @csrf
                                                @method('PATCH')
                                                <select
                                                    name="pull"
                                                    class="pull-select {{ $pullKey }}"
                                                    aria-label="Pull"
                                                    onchange="this.form.submit()"
                                                >
                                                    <option value="oui" @selected($pullKey === 'oui')>OUI</option>
                                                    <option value="non" @selected($pullKey === 'non')>NON</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/evolutions/'.$evolution['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette évolution ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="5" class="empty">Aucune évolution enregistrée. Cliquez sur Ajouter.</td>
                                    </tr>
                                @endforelse
                                <tr id="evolutionsNoResult" class="empty-row" style="display:none;">
                                    <td colspan="5" class="empty">AUCUN RÉSULTAT POUR CETTE RECHERCHE.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-paiement">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Fiche Paiement</h1>
                            </div>
                            <button type="button" class="btn-add" id="btnAddPaiement">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Nouveau Paiement
                            </button>
                        </div>

                        <section class="paiement-cards" aria-label="Totaux paiements">
                            <article class="paiement-card budget">
                                <div class="card-top">
                                    <span class="card-label">Total des Budgets</span>
                                    <div class="card-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M12 12v4"/><path d="M10 14h4"/></svg>
                                    </div>
                                </div>
                                <div class="card-value">{{ number_format($paiementTotalBudgets ?? 0, 2, '.', ' ') }}</div>
                            </article>

                            <article class="paiement-card paye">
                                <div class="card-top">
                                    <span class="card-label">Total Montant Payés</span>
                                    <div class="card-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    </div>
                                </div>
                                <div class="card-value">{{ number_format($paiementTotalMontants ?? 0, 2, '.', ' ') }}</div>
                            </article>

                            <article class="paiement-card soldes">
                                <div class="card-top">
                                    <span class="card-label">Total Soldes</span>
                                    <div class="card-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 3 5-6"/></svg>
                                    </div>
                                </div>
                                <div class="card-value">{{ number_format($paiementTotalSoldes ?? 0, 2, '.', ' ') }}</div>
                            </article>
                        </section>

                        <div class="search-bar" aria-label="Recherche paiements">
                            <div class="search-field">
                                <label for="filter_paiement_mois">Mois</label>
                                <select id="filter_paiement_mois">
                                    <option value="">TOUS LES MOIS</option>
                                    @php
                                        $moisPaiements = collect($paiements ?? [])
                                            ->map(function ($p) {
                                                $parts = explode('/', $p['date'] ?? '');
                                                return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                            })
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($moisPaiements as $mois)
                                        <option value="{{ $mois }}">{{ $mois }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="filter_paiement_client">Nom Client</label>
                                <select id="filter_paiement_client">
                                    <option value="">TOUS LES CLIENTS</option>
                                    @php
                                        $clientsFilter = collect($clients ?? [])
                                            ->pluck('nom')
                                            ->merge(collect($paiements ?? [])->pluck('client'))
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($clientsFilter as $clientNom)
                                        <option value="{{ mb_strtolower($clientNom) }}">{{ $clientNom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="filter_paiement_budget">Budget</label>
                                <input type="text" id="filter_paiement_budget" placeholder="Rechercher un budget">
                            </div>
                            <div class="search-field">
                                <label for="filter_paiement_tresorerie">Trésorerie</label>
                                <select id="filter_paiement_tresorerie">
                                    <option value="">TOUTES LES TRÉSORERIES</option>
                                    @php
                                        $tresoreriesFilter = collect($paiements ?? [])
                                            ->pluck('tresorerie')
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($tresoreriesFilter as $tresorerie)
                                        <option value="{{ mb_strtolower($tresorerie) }}">{{ $tresorerie }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Titre Projet</th>
                                    <th>Nom Client</th>
                                    <th>Budget</th>
                                    <th>Montant payé</th>
                                    <th>Trésorerie</th>
                                    <th>Solde</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="paiementsTableBody">
                                @forelse (($paiements ?? []) as $paiement)
                                    @php
                                        $dateParts = explode('/', $paiement['date'] ?? '');
                                        $moisKey = count($dateParts) >= 3 ? $dateParts[1].'/'.$dateParts[2] : '';
                                    @endphp
                                    <tr
                                        data-id="{{ $paiement['id'] }}"
                                        data-mois="{{ $moisKey }}"
                                        data-client="{{ mb_strtolower($paiement['client']) }}"
                                        data-budget="{{ $paiement['budget'] }}"
                                        data-tresorerie="{{ mb_strtolower($paiement['tresorerie']) }}"
                                    >
                                        <td>{{ $paiement['date'] }}</td>
                                        <td>{{ $paiement['titre_projet'] }}</td>
                                        <td>{{ $paiement['client'] }}</td>
                                        <td>{{ number_format($paiement['budget'], 2, '.', ' ') }}</td>
                                        <td>{{ number_format((float) ($paiement['increment_paye'] ?? $paiement['montant_paye'] ?? 0), 2, '.', ' ') }}</td>
                                        <td>{{ $paiement['tresorerie'] }}</td>
                                        <td class="solde-cell">{{ number_format($paiement['solde'], 2, '.', ' ') }}</td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/paiements/'.$paiement['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce paiement ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                                <button type="button" class="action-btn pdf" title="PDF" aria-label="PDF">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="empty">Aucun paiement enregistré. Cliquez sur Nouveau Paiement.</td>
                                    </tr>
                                @endforelse
                                <tr id="paiementsNoResult" class="empty-row" style="display:none;">
                                    <td colspan="8" class="empty">AUCUN RÉSULTAT POUR CETTE RECHERCHE.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-utilisateur">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Utilisateur</h1>
                            </div>
                            <div class="toolbar-actions">
                                <button type="button" class="btn-add" id="btnAddUtilisateur">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    Ajouter
                                </button>
                                <button type="button" class="btn-ghost" id="btnCloseUtilisateur">Fermer</button>
                            </div>
                        </div>

                        <div class="search-bar" aria-label="Recherche utilisateurs" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                            <div class="search-field">
                                <label for="filter_utilisateur_mois">Mois</label>
                                <select id="filter_utilisateur_mois">
                                    <option value="">TOUS LES MOIS</option>
                                    @php
                                        $moisUtilisateurs = collect($utilisateurs ?? [])
                                            ->map(function ($u) {
                                                $parts = explode('/', $u['date'] ?? '');
                                                return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                            })
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($moisUtilisateurs as $mois)
                                        <option value="{{ $mois }}">{{ $mois }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="filter_utilisateur_statue">Statue</label>
                                <select id="filter_utilisateur_statue">
                                    <option value="">TOUTES LES STATUES</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="manager">Manager</option>
                                    <option value="comptable">Comptable</option>
                                    <option value="vendeur">Vendeur</option>
                                    <option value="stock">Responsable stock</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Nom Complet</th>
                                    <th>Statue</th>
                                    <th>Login</th>
                                    <th>Mot de Passe</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="utilisateursTableBody">
                                @php
                                    $userStatueLabels = [
                                        'admin' => 'Administrateur',
                                        'manager' => 'Manager',
                                        'comptable' => 'Comptable',
                                        'vendeur' => 'Vendeur',
                                        'stock' => 'Responsable stock',
                                    ];
                                @endphp
                                @forelse (($utilisateurs ?? []) as $utilisateur)
                                    @php
                                        $partsUser = explode('/', $utilisateur['date'] ?? '');
                                        $moisUser = count($partsUser) >= 3 ? $partsUser[1].'/'.$partsUser[2] : '';
                                    @endphp
                                    <tr data-id="{{ $utilisateur['id'] }}" data-mois="{{ $moisUser }}" data-statue="{{ $utilisateur['statue'] ?? '' }}">
                                        <td>{{ $utilisateur['date'] }}</td>
                                        <td>{{ $utilisateur['nom_complet'] }}</td>
                                        <td>{{ $userStatueLabels[$utilisateur['statue']] ?? strtoupper($utilisateur['statue']) }}</td>
                                        <td>{{ $utilisateur['login'] }}</td>
                                        <td>••••••••</td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/utilisateurs/'.$utilisateur['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="6" class="empty">Aucun utilisateur enregistré. Cliquez sur Ajouter.</td>
                                    </tr>
                                @endforelse
                                <tr id="utilisateursNoResult" class="empty-row" style="display:none;">
                                    <td colspan="6" class="empty">Aucun résultat pour cette recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-autorisation">
                    <div class="panel-freeze">
                        <div class="section-toolbar">
                            <div class="content-head" style="margin-bottom:0;">
                                <h1>Autorisation</h1>
                            </div>
                            <div class="toolbar-actions">
                                <button type="button" class="btn-add" id="btnAddAutorisation">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    Ajouter
                                </button>
                                <button type="button" class="btn-ghost" id="btnCloseAutorisation">Fermer</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap table-freeze-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Sections</th>
                                    <th>Autorisations</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="autorisationsTableBody">
                                @php
                                    $permissionLabels = collect($menuSections ?? [])
                                        ->flatMap(fn ($section) => collect($section['items'])->mapWithKeys(
                                            fn ($item) => [$item['key'] => $item['label']]
                                        ))
                                        ->all();
                                    $sectionByPermission = [];
                                    foreach (($menuSections ?? []) as $section) {
                                        foreach ($section['items'] as $item) {
                                            $sectionByPermission[$item['key']] = $section['label'];
                                        }
                                    }
                                @endphp
                                @forelse (($autorisations ?? []) as $autorisation)
                                    @php
                                        $perms = $autorisation['permissions'] ?? [];
                                        $sectionsLabels = collect($perms)
                                            ->map(fn ($p) => $sectionByPermission[$p] ?? null)
                                            ->filter()
                                            ->unique()
                                            ->values()
                                            ->implode(', ');
                                        $authLabels = collect($perms)
                                            ->map(fn ($p) => $permissionLabels[$p] ?? $p)
                                            ->filter()
                                            ->implode(', ');
                                    @endphp
                                    <tr data-id="{{ $autorisation['id'] }}">
                                        <td>{{ $autorisation['utilisateur_nom'] ?? '' }}{{ !empty($autorisation['utilisateur_login']) ? ' ('.$autorisation['utilisateur_login'].')' : '' }}</td>
                                        <td class="cell-wrap">{{ $sectionsLabels !== '' ? $sectionsLabels : '—' }}</td>
                                        <td class="cell-wrap">{{ $authLabels !== '' ? $authLabels : 'Aucune' }}</td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <form method="post" action="{{ url('/autorisations/'.$autorisation['id']) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette autorisation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="4" class="empty">Aucune autorisation enregistrée. Cliquez sur Ajouter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <div class="modal-backdrop" id="autorisationModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="autorisationModalTitle" style="max-width:720px;">
            <div class="modal-head">
                <h2 id="autorisationModalTitle">Ajouter Autorisation</h2>
                <button type="button" class="modal-close" id="closeAutorisationModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/autorisations') }}" id="autorisationForm">
                @csrf
                <input type="hidden" name="_method" id="autorisation_http_method" value="POST" disabled>
                <div class="modal-body">
                    <div class="field full">
                        <label for="autorisation_utilisateur_id">Utilisateur</label>
                        <select id="autorisation_utilisateur_id" name="utilisateur_id" required>
                            <option value="" disabled selected>Sélectionner un utilisateur</option>
                            @foreach (($utilisateurs ?? []) as $utilisateur)
                                <option value="{{ $utilisateur['id'] }}">{{ $utilisateur['nom_complet'] }} ({{ $utilisateur['login'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="auth-sections" id="autorisationSections">
                        @foreach (($menuSections ?? []) as $section)
                            <div class="auth-section" data-section="{{ $section['key'] }}">
                                <div class="auth-section-title">{{ $section['label'] }}</div>
                                <div class="auth-checks">
                                    @foreach ($section['items'] as $item)
                                        <label class="auth-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $item['key'] }}" class="auth-permission" data-section="{{ $section['key'] }}">
                                            <span>{{ $item['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelAutorisationModal">Fermer</button>
                    <button type="submit" class="btn-primary" id="autorisationSubmitBtn">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="paiementModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="paiementModalTitle">
            <div class="modal-head">
                <h2 id="paiementModalTitle">Nouveau Paiement</h2>
                <button type="button" class="modal-close" id="closePaiementModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/paiements') }}" id="paiementForm">
                @csrf
                <input type="hidden" name="_method" id="paiement_http_method" value="POST" disabled>
                <input type="hidden" id="paiement_projet_id" name="projet_id">
                <input type="hidden" id="paiement_titre" name="titre_projet">
                <div class="modal-body">
                    <div class="field">
                        <label for="paiement_date_jj">Date</label>
                        <div class="date-parts" role="group" aria-label="Date JJ/MM/AAAA">
                            <input type="text" id="paiement_date_jj" class="date-jj" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="JJ" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="paiement_date_mm" class="date-mm" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="MM" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="paiement_date_aaaa" class="date-aaaa" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="AAAA" autocomplete="off" required>
                        </div>
                        <input type="hidden" id="paiement_date" name="date" required>
                    </div>
                    <div class="field">
                        <label for="paiement_ref">Réf</label>
                        <input type="text" id="paiement_ref" name="ref" readonly>
                    </div>
                    <div class="field full client-select-field">
                        <label for="paiement_client">Nom Client (liste)</label>
                        <select id="paiement_client" name="client" required>
                            <option value="" disabled selected>— SÉLECTIONNER UN CLIENT —</option>
                            @forelse (($clients ?? []) as $client)
                                <option value="{{ $client['nom'] }}">{{ $client['ref'] }} — {{ $client['nom'] }}</option>
                            @empty
                                <option value="" disabled>AUCUN CLIENT DISPONIBLE</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="field full">
                        <label for="paiement_titre_display">Titre Projet</label>
                        <input type="text" id="paiement_titre_display" readonly placeholder="—">
                    </div>
                    <div class="field">
                        <label for="paiement_budget_display">Solde restant</label>
                        <input type="text" id="paiement_budget_display" readonly>
                        <input type="hidden" id="paiement_budget" name="budget">
                        <input type="hidden" id="paiement_solde_actuel" value="0">
                    </div>
                    <div class="field">
                        <label for="paiement_montant_paye">Montant payé</label>
                        <input type="number" id="paiement_montant_paye" name="montant_paye" min="0" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="field">
                        <label for="paiement_type_reg">Type Règ</label>
                        <select id="paiement_type_reg" name="type_reg" required>
                            <option value="" disabled selected>— TYPE —</option>
                            <option value="ESPECES">ESPÈCES</option>
                            <option value="CHEQUE">CHÈQUE</option>
                            <option value="VIREMENT">VIREMENT</option>
                            <option value="CARTE">CARTE BANCAIRE</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="paiement_bnq">Bnq</label>
                        <input type="text" id="paiement_bnq" name="bnq" required placeholder="Banque">
                    </div>
                    <div class="field">
                        <label for="paiement_tresorerie">Trésorerie</label>
                        <input type="text" id="paiement_tresorerie" name="tresorerie" required placeholder="Trésorerie">
                    </div>
                    <div class="field">
                        <label for="paiement_solde">Solde</label>
                        <input type="text" id="paiement_solde" readonly>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelPaiementModal">Fermer</button>
                    <button type="submit" class="btn-primary" id="paiementSubmitBtn">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="projetModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="projetModalTitle">
            <div class="modal-head">
                <h2 id="projetModalTitle">Nouveau Projet</h2>
                <button type="button" class="modal-close" id="closeProjetModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/projets') }}" id="projetForm">
                @csrf
                <input type="hidden" name="_method" id="projet_http_method" value="POST" disabled>
                <div class="modal-body">
                    <div class="field">
                        <label for="projet_date_jj">Date</label>
                        <div class="date-parts" role="group" aria-label="Date JJ/MM/AAAA">
                            <input type="text" id="projet_date_jj" class="date-jj" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="JJ" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="projet_date_mm" class="date-mm" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="MM" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="projet_date_aaaa" class="date-aaaa" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="AAAA" autocomplete="off" required>
                        </div>
                        <input type="hidden" id="projet_date" name="date" required>
                    </div>
                    <div class="field">
                        <label for="projet_ref">Réf</label>
                        <input type="text" id="projet_ref" name="ref" readonly>
                    </div>
                    <div class="field full">
                        <label for="projet_nom">Nom Projet</label>
                        <input type="text" id="projet_nom" name="nom" required placeholder="Nom du projet">
                    </div>
                    <div class="field full">
                        <label for="projet_designation">Désignation</label>
                        <input type="text" id="projet_designation" name="designation" required placeholder="Désignation">
                    </div>
                    <div class="field full client-select-field">
                        <label for="projet_client">Client (liste)</label>
                        <select id="projet_client" name="client" required>
                            <option value="" disabled selected>— SÉLECTIONNER UN CLIENT —</option>
                            @forelse (($clients ?? []) as $client)
                                <option value="{{ $client['nom'] }}">{{ $client['ref'] }} — {{ $client['nom'] }} ({{ $client['ville'] }})</option>
                            @empty
                                <option value="" disabled>AUCUN CLIENT DISPONIBLE — AJOUTEZ UN CLIENT</option>
                            @endforelse
                        </select>
                        @if (empty($clients))
                            <p class="client-select-hint">Ajoutez d’abord un client dans Fiche Client.</p>
                        @endif
                    </div>
                    <div class="field">
                        <label for="projet_delai">Délai</label>
                        <input type="text" id="projet_delai" name="delai" required placeholder="Ex: 30 jours">
                    </div>
                    <div class="field">
                        <label for="projet_statut">Statue (liste)</label>
                        <select id="projet_statut" name="statut" required>
                            <option value="" disabled selected>— CHOISIR LA STATUE —</option>
                            <option value="actif">EN COURS</option>
                            <option value="attente">EN ATTENTE</option>
                            <option value="annule">ANNULÉ</option>
                            <option value="execute">EXÉCUTÉ</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="projet_budget">Budget</label>
                        <input type="number" id="projet_budget" name="budget" min="0" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="field" id="projet_avance_field">
                        <label for="projet_avance" id="projet_avance_label">Avance</label>
                        <input type="number" id="projet_avance" name="avance" min="0" step="0.01" value="0" placeholder="0.00">
                    </div>
                    <div class="field">
                        <label for="projet_solde">Solde</label>
                        <input type="text" id="projet_solde" readonly>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelProjetModal">Fermer</button>
                    <button type="submit" class="btn-primary" id="projetSubmitBtn">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="clientModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="clientModalTitle">
            <div class="modal-head">
                <h2 id="clientModalTitle">Ajouter un client</h2>
                <button type="button" class="modal-close" id="closeClientModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/clients') }}" id="clientForm">
                @csrf
                <input type="hidden" name="_method" id="client_http_method" value="POST" disabled>
                <div class="modal-body">
                    <div class="field">
                        <label for="client_date_jj">Date</label>
                        <div class="date-parts" role="group" aria-label="Date JJ/MM/AAAA">
                            <input type="text" id="client_date_jj" class="date-jj" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="JJ" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="client_date_mm" class="date-mm" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="MM" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="client_date_aaaa" class="date-aaaa" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="AAAA" autocomplete="off" required>
                        </div>
                        <input type="hidden" id="client_date" name="date" required>
                    </div>
                    <div class="field">
                        <label for="client_ref">Réf</label>
                        <input type="text" id="client_ref" name="ref" readonly>
                    </div>
                    <div class="field full">
                        <label for="client_nom">Nom Client</label>
                        <input type="text" id="client_nom" name="nom" required placeholder="Nom du client">
                    </div>
                    <div class="field">
                        <label for="client_ville">Ville</label>
                        <input type="text" id="client_ville" name="ville" required placeholder="Ville">
                    </div>
                    <div class="field">
                        <label for="client_contact">Contact</label>
                        <input type="text" id="client_contact" name="contact" required placeholder="Téléphone / email">
                    </div>
                    <div class="field full">
                        <label for="client_activite">Activité</label>
                        <input type="text" id="client_activite" name="activite" required placeholder="Activité">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelClientModal">Annuler</button>
                    <button type="submit" class="btn-primary" id="clientSubmitBtn">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="utilisateurModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="utilisateurModalTitle">
            <div class="modal-head">
                <h2 id="utilisateurModalTitle">Ajouter Utilisateur</h2>
                <button type="button" class="modal-close" id="closeUtilisateurModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/utilisateurs') }}" id="utilisateurForm">
                @csrf
                <input type="hidden" name="_method" id="utilisateur_http_method" value="POST" disabled>
                <div class="modal-body">
                    <div class="field">
                        <label for="utilisateur_date">Date</label>
                        <input type="text" id="utilisateur_date" name="date" readonly>
                    </div>
                    <div class="field full">
                        <label for="utilisateur_nom_complet">Nom Complet</label>
                        <input type="text" id="utilisateur_nom_complet" name="nom_complet" required placeholder="Nom complet">
                    </div>
                    <div class="field full">
                        <label for="utilisateur_statue">Statue</label>
                        <select id="utilisateur_statue" name="statue" required>
                            <option value="" disabled selected>Sélectionner une statue</option>
                            <option value="admin">Administrateur</option>
                            <option value="manager">Manager</option>
                            <option value="comptable">Comptable</option>
                            <option value="vendeur">Vendeur</option>
                            <option value="stock">Responsable stock</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="utilisateur_login">Login</label>
                        <input type="text" id="utilisateur_login" name="login" required placeholder="Identifiant" autocomplete="username">
                    </div>
                    <div class="field">
                        <label for="utilisateur_password">Mot de Passe</label>
                        <input type="password" id="utilisateur_password" name="password" placeholder="Mot de passe" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelUtilisateurModal">Fermer</button>
                    <button type="submit" class="btn-primary" id="utilisateurSubmitBtn">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="relanceModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="relanceModalTitle">
            <div class="modal-head">
                <h2 id="relanceModalTitle">Nouveau Prospect</h2>
                <button type="button" class="modal-close" id="closeRelanceModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/relances') }}" id="relanceForm">
                @csrf
                <input type="hidden" name="_method" id="relance_http_method" value="POST" disabled>
                <div class="modal-body">
                    <div class="field">
                        <label for="relance_date">Date</label>
                        <input type="text" id="relance_date" name="date" readonly>
                    </div>
                    <div class="field">
                        <label for="relance_ref">ID</label>
                        <input type="text" id="relance_ref" name="ref" readonly>
                    </div>
                    <div class="field full">
                        <label for="relance_nom_complet">Nom Complet</label>
                        <input type="text" id="relance_nom_complet" name="nom_complet" required placeholder="Nom complet">
                    </div>
                    <div class="field">
                        <label for="relance_telephone">Téléphone</label>
                        <input type="text" id="relance_telephone" name="telephone" required placeholder="Téléphone" inputmode="tel">
                    </div>
                    <div class="field">
                        <label for="relance_ville">Ville</label>
                        <input type="text" id="relance_ville" name="ville" required placeholder="Ville">
                    </div>
                    <div class="field">
                        <label for="relance_titre_projet">Titre Projet</label>
                        <input type="text" id="relance_titre_projet" name="titre_projet" required placeholder="Titre du projet">
                    </div>
                    <div class="field full">
                        <label for="relance_description">Description</label>
                        <textarea id="relance_description" name="description" required placeholder="Description" rows="3"></textarea>
                    </div>
                    <div class="field">
                        <label for="relance_budget">Budget</label>
                        <input type="number" id="relance_budget" name="budget" required min="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="field">
                        <label for="relance_envoye">Envoyé</label>
                        <select id="relance_envoye" name="envoye" required>
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="lien">Lien</option>
                            <option value="conception">Concep</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="relance_statue">Statue</label>
                        <select id="relance_statue" name="statue" required>
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="a_voir">A VOIR</option>
                            <option value="confirme">CONFIRME</option>
                            <option value="inj">INJ</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="relance_a_rappeler">A Rappeler</label>
                        <select id="relance_a_rappeler" name="a_rappeler" required>
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="oui">Oui</option>
                            <option value="non">Non</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="relance_rappel_date_jj">Date</label>
                        <div class="date-parts" role="group" aria-label="Date JJ/MM/AAAA">
                            <input type="text" id="relance_rappel_date_jj" class="date-jj" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="JJ" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="relance_rappel_date_mm" class="date-mm" inputmode="numeric" maxlength="2" pattern="\d{2}" placeholder="MM" autocomplete="off" required>
                            <span class="date-sep">/</span>
                            <input type="text" id="relance_rappel_date_aaaa" class="date-aaaa" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="AAAA" autocomplete="off" required>
                        </div>
                        <input type="hidden" id="relance_rappel_date" name="date_rappel" required>
                    </div>
                </div>
                <div class="modal-foot modal-foot-split">
                    <button type="button" class="btn-ghost" id="btnOpenRelanceImport">Importer</button>
                    <div class="modal-foot-actions">
                        <button type="button" class="btn-ghost" id="cancelRelanceModal">Fermer</button>
                        <button type="submit" class="btn-primary" id="relanceSubmitBtn">Valider</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="relanceImportModal" aria-hidden="true">
        <div class="modal modal-import" role="dialog" aria-modal="true" aria-labelledby="relanceImportModalTitle">
            <div class="modal-head">
                <h2 id="relanceImportModalTitle">Importer des numéros</h2>
                <button type="button" class="modal-close" id="closeRelanceImportModal" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body modal-body-import">
                <div class="import-dropzone" id="relanceImportDropzone" tabindex="0" role="button" aria-label="Choisir une photo ou un PDF">
                    <input type="file" id="relanceImportFile" accept="image/*,application/pdf,.pdf" hidden>
                    <div class="import-dropzone-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
                    </div>
                    <p class="import-dropzone-title">Photo ou PDF de numéros</p>
                    <p class="import-dropzone-hint">Glisser-déposer ici, ou cliquer pour choisir un fichier</p>
                    <p class="import-dropzone-file" id="relanceImportFileName"></p>
                </div>
                <div class="import-status" id="relanceImportStatus" hidden></div>
                <div class="import-phones" id="relanceImportPhonesWrap" hidden>
                    <div class="import-phones-head">
                        <span id="relanceImportPhonesCount">0 numéro détecté</span>
                        <button type="button" class="btn-ghost import-phones-clear" id="btnClearRelanceImportPhones">Tout retirer</button>
                    </div>
                    <ul class="import-phones-list" id="relanceImportPhoneList"></ul>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" id="cancelRelanceImportModal">Fermer</button>
                <button type="button" class="btn-primary" id="btnConfirmRelanceImport" disabled>Classer dans le tableau</button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="evolutionModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="evolutionModalTitle">
            <div class="modal-head">
                <h2 id="evolutionModalTitle">Ajouter Evolution Travaux</h2>
                <button type="button" class="modal-close" id="closeEvolutionModal" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/evolutions') }}" id="evolutionForm">
                @csrf
                <input type="hidden" name="_method" id="evolution_http_method" value="POST" disabled>
                <input type="hidden" name="pull" id="evolution_pull_hidden" value="non">
                <div class="modal-body">
                    <div class="field">
                        <label for="evolution_date">Date</label>
                        <input type="text" id="evolution_date" name="date" required placeholder="JJ/MM/AAAA">
                    </div>
                    <div class="field full">
                        <label for="evolution_titre_projet">Titre Projet (liste)</label>
                        <select id="evolution_titre_projet" name="titre_projet" required>
                            <option value="" disabled selected>Sélectionner un projet</option>
                            @foreach (($projets ?? []) as $projet)
                                <option value="{{ $projet['nom'] }}">{{ $projet['nom'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field full">
                        <label for="evolution_description">Description</label>
                        <textarea id="evolution_description" name="description" required placeholder="Description des travaux" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelEvolutionModal">Fermer</button>
                    <button type="submit" class="btn-primary" id="evolutionSubmitBtn">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    </script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('menuToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const clientGroup = document.getElementById('clientGroup');
        const clientToggle = document.getElementById('clientToggle');
        const projetGroup = document.getElementById('projetGroup');
        const projetToggle = document.getElementById('projetToggle');
        const paiementGroup = document.getElementById('paiementGroup');
        const paiementToggle = document.getElementById('paiementToggle');
        const configGroup = document.getElementById('configGroup');
        const configToggle = document.getElementById('configToggle');
        const panels = document.querySelectorAll('.panel');
        const modal = document.getElementById('clientModal');
        const projetModal = document.getElementById('projetModal');
        const paiementModal = document.getElementById('paiementModal');
        const utilisateurModal = document.getElementById('utilisateurModal');
        const evolutionModal = document.getElementById('evolutionModal');
        const relanceModal = document.getElementById('relanceModal');
        const autorisationModal = document.getElementById('autorisationModal');
        const btnAdd = document.getElementById('btnAddClient');
        const btnAddProjet = document.getElementById('btnAddProjet');
        const btnAddPaiement = document.getElementById('btnAddPaiement');
        const btnAddUtilisateur = document.getElementById('btnAddUtilisateur');
        const btnCloseUtilisateur = document.getElementById('btnCloseUtilisateur');
        const btnAddEvolution = document.getElementById('btnAddEvolution');
        const btnCloseEvolution = document.getElementById('btnCloseEvolution');
        const btnAddRelance = document.getElementById('btnAddRelance');
        const btnAddAutorisation = document.getElementById('btnAddAutorisation');
        const btnCloseAutorisation = document.getElementById('btnCloseAutorisation');
        const closeModal = document.getElementById('closeClientModal');
        const cancelModal = document.getElementById('cancelClientModal');
        const closeProjetModal = document.getElementById('closeProjetModal');
        const cancelProjetModal = document.getElementById('cancelProjetModal');
        const closePaiementModal = document.getElementById('closePaiementModal');
        const cancelPaiementModal = document.getElementById('cancelPaiementModal');
        const closeUtilisateurModal = document.getElementById('closeUtilisateurModal');
        const cancelUtilisateurModal = document.getElementById('cancelUtilisateurModal');
        const closeEvolutionModal = document.getElementById('closeEvolutionModal');
        const cancelEvolutionModal = document.getElementById('cancelEvolutionModal');
        const closeRelanceModal = document.getElementById('closeRelanceModal');
        const cancelRelanceModal = document.getElementById('cancelRelanceModal');
        const closeAutorisationModal = document.getElementById('closeAutorisationModal');
        const cancelAutorisationModal = document.getElementById('cancelAutorisationModal');
        const projetsData = @json($projets ?? []);
        const clientsData = @json($clients ?? []);
        const paiementsData = @json($paiements ?? []);
        const utilisateursData = @json($utilisateurs ?? []);
        const evolutionsData = @json($evolutions ?? []);
        const relancesData = @json($relances ?? []);
        const autorisationsData = @json($autorisations ?? []);
        const menuSectionsData = @json($menuSections ?? []);

        const userStatueLabels = {
            admin: 'Administrateur',
            manager: 'Manager',
            comptable: 'Comptable',
            vendeur: 'Vendeur',
            stock: 'Responsable stock',
        };

        const relanceStatueLabels = {
            a_voir: 'A VOIR',
            confirme: 'CONFIRME',
            inj: 'INJ',
        };

        const statueLabels = {
            actif: 'EN COURS',
            attente: 'EN ATTENTE',
            annule: 'ANNULÉ',
            execute: 'EXÉCUTÉ',
        };

        function formatPdfValue(value) {
            if (typeof value === 'number') {
                return Number(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            return value ?? '';
        }

        function openRecordPdf(title, ref, fields) {
            const rows = fields
                .map(([label, value]) => `<tr><th>${label}</th><td>${formatPdfValue(value)}</td></tr>`)
                .join('');

            const html = `<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>${title}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 24px; text-transform: uppercase; color: #111; }
                    h1 { font-size: 18px; margin-bottom: 4px; }
                    p { color: #555; margin: 0 0 16px; }
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; font-size: 13px; }
                    th { background: #f3f6fb; width: 38%; font-weight: 700; }
                </style></head><body>
                <h1>EVOPRO — ${title}</h1>
                <p>Réf : ${ref}</p>
                <table>${rows}</table>
                </body></html>`;

            const printWindow = window.open('', '_blank');
            if (!printWindow) return;

            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.onload = () => {
                printWindow.focus();
                printWindow.print();
            };
        }

        function isMobileSidebar() {
            return window.innerWidth <= 1100;
        }

        function updateMenuToggleState(isOpen) {
            if (!toggle) return;
            toggle.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
        }

        function openSidebar() {
            sidebar?.classList.add('open');
            sidebarBackdrop?.classList.add('open');
            sidebarBackdrop?.setAttribute('aria-hidden', 'false');
            if (isMobileSidebar()) document.body.style.overflow = 'hidden';
            updateMenuToggleState(true);
        }

        function closeSidebar() {
            sidebar?.classList.remove('open');
            sidebarBackdrop?.classList.remove('open');
            sidebarBackdrop?.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            updateMenuToggleState(false);
        }

        function toggleSidebar() {
            if (sidebar?.classList.contains('open')) closeSidebar();
            else openSidebar();
        }

        toggle?.addEventListener('click', toggleSidebar);
        sidebarClose?.addEventListener('click', closeSidebar);
        sidebarBackdrop?.addEventListener('click', closeSidebar);

        clientToggle?.addEventListener('click', () => {
            clientGroup.classList.toggle('open');
        });

        projetToggle?.addEventListener('click', () => {
            projetGroup.classList.toggle('open');
        });

        paiementToggle?.addEventListener('click', () => {
            paiementGroup.classList.toggle('open');
        });

        configToggle?.addEventListener('click', () => {
            configGroup.classList.toggle('open');
        });

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('evopro-theme', theme);
            const isLight = theme === 'light';

            document.querySelectorAll('.theme-icon-dark, .theme-icon-dark-nav').forEach((el) => {
                el.style.display = isLight ? 'none' : '';
            });
            document.querySelectorAll('.theme-icon-light, .theme-icon-light-nav').forEach((el) => {
                el.style.display = isLight ? '' : 'none';
            });
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            applyTheme(current === 'light' ? 'dark' : 'light');
        }

        document.getElementById('themeToggleNav')?.addEventListener('click', toggleTheme);
        applyTheme(document.documentElement.getAttribute('data-theme') || 'dark');

        function showPanel(name) {
            panels.forEach((panel) => panel.classList.toggle('active', panel.id === `panel-${name}`));
            document.querySelectorAll('.nav-item').forEach((el) => el.classList.remove('active'));
            document.querySelectorAll('.submenu-link').forEach((el) => el.classList.remove('active'));
            document.getElementById('dashboardNavBtn')?.classList.toggle('active', name === 'dashboard');

            if (name === 'fiche-client') {
                clientGroup.classList.add('open');
                clientToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-client"]')?.classList.add('active');
            } else if (name === 'fiche-relance') {
                clientGroup.classList.add('open');
                clientToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-relance"]')?.classList.add('active');
            } else if (name === 'fiche-projet') {
                projetGroup.classList.add('open');
                projetToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-projet"]')?.classList.add('active');
            } else if (name === 'fiche-evolution') {
                projetGroup.classList.add('open');
                projetToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-evolution"]')?.classList.add('active');
            } else if (name === 'fiche-paiement') {
                paiementGroup.classList.add('open');
                paiementToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-paiement"]')?.classList.add('active');
            } else if (name === 'fiche-utilisateur') {
                configGroup.classList.add('open');
                configToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-utilisateur"]')?.classList.add('active');
            } else if (name === 'fiche-autorisation') {
                configGroup.classList.add('open');
                configToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-autorisation"]')?.classList.add('active');
            } else if (name === 'dashboard') {
                document.querySelector(`.nav-item[data-panel="dashboard"]`)?.classList.add('active');
            }

            if (isMobileSidebar()) closeSidebar();
        }

        document.querySelectorAll('[data-panel]').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                showPanel(link.dataset.panel);
            });
        });

        function todayFr() {
            const d = new Date();
            return d.toLocaleDateString('fr-FR');
        }

        function setupDateParts(prefix) {
            const jj = document.getElementById(`${prefix}_date_jj`);
            const mm = document.getElementById(`${prefix}_date_mm`);
            const aaaa = document.getElementById(`${prefix}_date_aaaa`);
            const hidden = document.getElementById(`${prefix}_date`);

            function sync() {
                const day = (jj?.value || '').trim();
                const month = (mm?.value || '').trim();
                const year = (aaaa?.value || '').trim();
                if (hidden) {
                    hidden.value = (day.length === 2 && month.length === 2 && year.length === 4)
                        ? `${day}/${month}/${year}`
                        : '';
                }
            }

            function setParts(dateStr, { emptyDayMonth = false } = {}) {
                const year = String(new Date().getFullYear());
                if (emptyDayMonth) {
                    if (jj) jj.value = '';
                    if (mm) mm.value = '';
                    if (aaaa) aaaa.value = year;
                    sync();
                    return;
                }

                const parts = String(dateStr || '').split('/');
                if (jj) jj.value = (parts[0] || '').replace(/\D/g, '').slice(0, 2);
                if (mm) mm.value = (parts[1] || '').replace(/\D/g, '').slice(0, 2);
                if (aaaa) aaaa.value = (parts[2] || '').replace(/\D/g, '').slice(0, 4);
                sync();
            }

            function bindPart(input, maxLen, nextInput) {
                if (!input) return;
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\D/g, '').slice(0, maxLen);
                    sync();
                    if (input.value.length === maxLen && nextInput) {
                        nextInput.focus();
                        nextInput.select();
                    }
                });
                input.addEventListener('blur', sync);
            }

            bindPart(jj, 2, mm);
            bindPart(mm, 2, aaaa);
            bindPart(aaaa, 4, null);

            return { sync, setParts, jj, mm, aaaa, fieldIds: [`${prefix}_date_jj`, `${prefix}_date_mm`, `${prefix}_date_aaaa`] };
        }

        function validateDatePartsSubmit(form, dateApi, label) {
            form?.addEventListener('submit', (e) => {
                dateApi.sync();
                const day = (dateApi.jj?.value || '').trim();
                const month = (dateApi.mm?.value || '').trim();
                const year = (dateApi.aaaa?.value || '').trim();
                if (!/^\d{2}$/.test(day) || !/^\d{2}$/.test(month) || !/^\d{4}$/.test(year)) {
                    e.preventDefault();
                    alert(`${label} invalide : JJ (2 chiffres) / MM (2 chiffres) / AAAA (4 chiffres).`);
                    dateApi.jj?.focus();
                }
            });
        }

        const clientDateApi = setupDateParts('client');
        const projetDateApi = setupDateParts('projet');
        const paiementDateApi = setupDateParts('paiement');
        const relanceRappelDateApi = setupDateParts('relance_rappel');
        validateDatePartsSubmit(document.getElementById('clientForm'), clientDateApi, 'Date');
        validateDatePartsSubmit(document.getElementById('projetForm'), projetDateApi, 'Date');
        validateDatePartsSubmit(document.getElementById('paiementForm'), paiementDateApi, 'Date');
        validateDatePartsSubmit(document.getElementById('relanceForm'), relanceRappelDateApi, 'Date');

        function nextRef() {
            const rows = document.querySelectorAll('#clientsTableBody tr[data-id]');
            const n = rows.length + 1;
            return 'CLI-' + String(n).padStart(4, '0');
        }

        function openClientModal() {
            const clientForm = document.getElementById('clientForm');
            const clientModalTitle = document.getElementById('clientModalTitle');
            const clientSubmitBtn = document.getElementById('clientSubmitBtn');
            const clientHttpMethod = document.getElementById('client_http_method');

            clientForm.action = '{{ url('/clients') }}';
            clientHttpMethod.disabled = true;
            clientHttpMethod.value = 'POST';
            clientModalTitle.textContent = 'Ajouter un client';
            clientSubmitBtn.style.display = '';

            clientDateApi.setParts('', { emptyDayMonth: true });
            document.getElementById('client_ref').value = nextRef();
            document.getElementById('client_nom').value = '';
            document.getElementById('client_ville').value = '';
            document.getElementById('client_contact').value = '';
            document.getElementById('client_activite').value = '';

            setClientFormFields('create');

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            clientDateApi.jj?.focus();
        }

        const clientFieldIds = [...clientDateApi.fieldIds, 'client_nom', 'client_ville', 'client_contact', 'client_activite'];

        function setClientFormFields(mode) {
            clientFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (field) field.disabled = mode === 'view';
            });
        }

        function fillClientForm(client) {
            clientDateApi.setParts(client.date || '');
            document.getElementById('client_ref').value = client.ref || '';
            document.getElementById('client_nom').value = client.nom || '';
            document.getElementById('client_ville').value = client.ville || '';
            document.getElementById('client_contact').value = client.contact || '';
            document.getElementById('client_activite').value = client.activite || '';
        }

        function openClientView(client) {
            const clientModalTitle = document.getElementById('clientModalTitle');
            const clientSubmitBtn = document.getElementById('clientSubmitBtn');

            fillClientForm(client);
            clientModalTitle.textContent = 'Voir Client';
            clientSubmitBtn.style.display = 'none';
            setClientFormFields('view');

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
        }

        function openClientEdit(client) {
            const clientForm = document.getElementById('clientForm');
            const clientModalTitle = document.getElementById('clientModalTitle');
            const clientSubmitBtn = document.getElementById('clientSubmitBtn');
            const clientHttpMethod = document.getElementById('client_http_method');

            clientForm.action = `{{ url('/clients') }}/${client.id}`;
            clientHttpMethod.disabled = false;
            clientHttpMethod.value = 'PUT';
            fillClientForm(client);
            clientModalTitle.textContent = 'Modifier Client';
            clientSubmitBtn.style.display = '';
            setClientFormFields('edit');

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.getElementById('client_nom').focus();
        }

        function openClientPdf(client) {
            openRecordPdf('FICHE CLIENT', client.ref || '', [
                ['Date', client.date],
                ['Nom Client', client.nom],
                ['Ville', client.ville],
                ['Contact', client.contact],
                ['Activité', client.activite],
                ['Solde', client.solde],
            ]);
        }

        document.getElementById('clientsTableBody')?.addEventListener('click', (e) => {
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const client = clientsData.find((c) => c.id === row.dataset.id);
            if (!client) return;

            if (e.target.closest('.action-btn.voir')) openClientView(client);
            if (e.target.closest('.action-btn.modifier')) openClientEdit(client);
            if (e.target.closest('.action-btn.pdf')) openClientPdf(client);
        });

        function closeClientModal() {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        }

        btnAdd?.addEventListener('click', openClientModal);
        closeModal?.addEventListener('click', closeClientModal);
        cancelModal?.addEventListener('click', closeClientModal);
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeClientModal();
        });

        function fillUtilisateurForm(utilisateur) {
            document.getElementById('utilisateur_date').value = utilisateur.date || '';
            document.getElementById('utilisateur_nom_complet').value = utilisateur.nom_complet || '';
            document.getElementById('utilisateur_statue').value = utilisateur.statue || '';
            document.getElementById('utilisateur_login').value = utilisateur.login || '';
            document.getElementById('utilisateur_password').value = utilisateur.password || '';
        }

        const utilisateurFieldIds = ['utilisateur_nom_complet', 'utilisateur_statue', 'utilisateur_login', 'utilisateur_password'];

        function setUtilisateurFormFields(mode) {
            utilisateurFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (field) field.disabled = mode === 'view';
            });
        }

        function openUtilisateurModal() {
            const utilisateurForm = document.getElementById('utilisateurForm');
            const utilisateurModalTitle = document.getElementById('utilisateurModalTitle');
            const utilisateurSubmitBtn = document.getElementById('utilisateurSubmitBtn');
            const utilisateurHttpMethod = document.getElementById('utilisateur_http_method');
            const passwordField = document.getElementById('utilisateur_password');

            utilisateurForm.action = '{{ url('/utilisateurs') }}';
            utilisateurHttpMethod.disabled = true;
            utilisateurHttpMethod.value = 'POST';
            utilisateurModalTitle.textContent = 'Ajouter Utilisateur';
            utilisateurSubmitBtn.style.display = '';
            passwordField.required = true;

            document.getElementById('utilisateur_date').value = todayFr();
            document.getElementById('utilisateur_nom_complet').value = '';
            document.getElementById('utilisateur_statue').value = '';
            document.getElementById('utilisateur_login').value = '';
            passwordField.value = '';

            setUtilisateurFormFields('create');

            utilisateurModal.classList.add('open');
            utilisateurModal.setAttribute('aria-hidden', 'false');
            document.getElementById('utilisateur_nom_complet').focus();
        }

        function openUtilisateurView(utilisateur) {
            const utilisateurModalTitle = document.getElementById('utilisateurModalTitle');
            const utilisateurSubmitBtn = document.getElementById('utilisateurSubmitBtn');

            fillUtilisateurForm(utilisateur);
            utilisateurModalTitle.textContent = 'Voir Utilisateur';
            utilisateurSubmitBtn.style.display = 'none';
            setUtilisateurFormFields('view');

            utilisateurModal.classList.add('open');
            utilisateurModal.setAttribute('aria-hidden', 'false');
        }

        function openUtilisateurEdit(utilisateur) {
            const utilisateurForm = document.getElementById('utilisateurForm');
            const utilisateurModalTitle = document.getElementById('utilisateurModalTitle');
            const utilisateurSubmitBtn = document.getElementById('utilisateurSubmitBtn');
            const utilisateurHttpMethod = document.getElementById('utilisateur_http_method');
            const passwordField = document.getElementById('utilisateur_password');

            utilisateurForm.action = `{{ url('/utilisateurs') }}/${utilisateur.id}`;
            utilisateurHttpMethod.disabled = false;
            utilisateurHttpMethod.value = 'PUT';
            fillUtilisateurForm(utilisateur);
            utilisateurModalTitle.textContent = 'Modifier Utilisateur';
            utilisateurSubmitBtn.style.display = '';
            passwordField.required = false;
            passwordField.value = '';
            passwordField.placeholder = 'Laisser vide pour ne pas changer';
            setUtilisateurFormFields('edit');

            utilisateurModal.classList.add('open');
            utilisateurModal.setAttribute('aria-hidden', 'false');
            document.getElementById('utilisateur_nom_complet').focus();
        }

        function closeUtilisateurModalFn() {
            const passwordField = document.getElementById('utilisateur_password');
            passwordField.placeholder = 'Mot de passe';
            utilisateurModal.classList.remove('open');
            utilisateurModal.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('utilisateursTableBody')?.addEventListener('click', (e) => {
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const utilisateur = utilisateursData.find((u) => u.id === row.dataset.id);
            if (!utilisateur) return;

            if (e.target.closest('.action-btn.voir')) openUtilisateurView(utilisateur);
            if (e.target.closest('.action-btn.modifier')) openUtilisateurEdit(utilisateur);
        });

        btnAddUtilisateur?.addEventListener('click', openUtilisateurModal);
        btnCloseUtilisateur?.addEventListener('click', () => showPanel('dashboard'));
        closeUtilisateurModal?.addEventListener('click', closeUtilisateurModalFn);
        cancelUtilisateurModal?.addEventListener('click', closeUtilisateurModalFn);
        utilisateurModal?.addEventListener('click', (e) => {
            if (e.target === utilisateurModal) closeUtilisateurModalFn();
        });

        const evolutionFieldIds = ['evolution_date', 'evolution_titre_projet', 'evolution_description'];

        function setEvolutionFormFields(mode) {
            evolutionFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (field) field.disabled = mode === 'view';
            });
        }

        function fillEvolutionForm(evolution) {
            document.getElementById('evolution_date').value = evolution.date || '';
            const titreSelect = document.getElementById('evolution_titre_projet');
            titreSelect.value = evolution.titre_projet || '';
            if (!titreSelect.value && evolution.titre_projet) {
                const option = document.createElement('option');
                option.value = evolution.titre_projet;
                option.textContent = evolution.titre_projet;
                option.selected = true;
                titreSelect.appendChild(option);
            }
            document.getElementById('evolution_description').value = evolution.description || '';
            document.getElementById('evolution_pull_hidden').value = evolution.pull || 'non';
        }

        function openEvolutionModal() {
            const evolutionForm = document.getElementById('evolutionForm');
            const evolutionModalTitle = document.getElementById('evolutionModalTitle');
            const evolutionSubmitBtn = document.getElementById('evolutionSubmitBtn');
            const evolutionHttpMethod = document.getElementById('evolution_http_method');

            evolutionForm.action = '{{ url('/evolutions') }}';
            evolutionHttpMethod.disabled = true;
            evolutionHttpMethod.value = 'POST';
            evolutionModalTitle.textContent = 'Ajouter Evolution Travaux';
            evolutionSubmitBtn.style.display = '';

            document.getElementById('evolution_date').value = todayFr();
            document.getElementById('evolution_titre_projet').value = '';
            document.getElementById('evolution_titre_projet').selectedIndex = 0;
            document.getElementById('evolution_description').value = '';
            document.getElementById('evolution_pull_hidden').value = 'non';

            setEvolutionFormFields('create');
            evolutionModal.classList.add('open');
            evolutionModal.setAttribute('aria-hidden', 'false');
            document.getElementById('evolution_titre_projet').focus();
        }

        function openEvolutionView(evolution) {
            fillEvolutionForm(evolution);
            document.getElementById('evolutionModalTitle').textContent = 'Voir Evolution Travaux';
            document.getElementById('evolutionSubmitBtn').style.display = 'none';
            setEvolutionFormFields('view');
            evolutionModal.classList.add('open');
            evolutionModal.setAttribute('aria-hidden', 'false');
        }

        function openEvolutionEdit(evolution) {
            const evolutionForm = document.getElementById('evolutionForm');
            const evolutionHttpMethod = document.getElementById('evolution_http_method');

            evolutionForm.action = `{{ url('/evolutions') }}/${evolution.id}`;
            evolutionHttpMethod.disabled = false;
            evolutionHttpMethod.value = 'PUT';
            fillEvolutionForm(evolution);
            document.getElementById('evolutionModalTitle').textContent = 'Modifier Evolution Travaux';
            document.getElementById('evolutionSubmitBtn').style.display = '';
            setEvolutionFormFields('edit');
            evolutionModal.classList.add('open');
            evolutionModal.setAttribute('aria-hidden', 'false');
            document.getElementById('evolution_description').focus();
        }

        function closeEvolutionModalFn() {
            evolutionModal.classList.remove('open');
            evolutionModal.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('evolutionsTableBody')?.addEventListener('click', (e) => {
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const evolution = evolutionsData.find((item) => item.id === row.dataset.id);
            if (!evolution) return;

            if (e.target.closest('.action-btn.voir')) openEvolutionView(evolution);
            if (e.target.closest('.action-btn.modifier')) openEvolutionEdit(evolution);
        });

        btnAddEvolution?.addEventListener('click', openEvolutionModal);
        btnCloseEvolution?.addEventListener('click', () => showPanel('dashboard'));
        closeEvolutionModal?.addEventListener('click', closeEvolutionModalFn);
        cancelEvolutionModal?.addEventListener('click', closeEvolutionModalFn);
        evolutionModal?.addEventListener('click', (e) => {
            if (e.target === evolutionModal) closeEvolutionModalFn();
        });

        window.addEventListener('resize', () => {
            if (!isMobileSidebar()) closeSidebar();
        });

        function nextProjetRef() {
            const rows = document.querySelectorAll('#projetsTableBody tr[data-id]');
            const n = rows.length + 1;
            return 'PRJ-' + String(n).padStart(4, '0');
        }

        function updateProjetStatutColor() {
            const select = document.getElementById('projet_statut');
            if (!select) return;
            select.classList.remove('statue-actif', 'statue-attente', 'statue-annule', 'statue-execute');
            if (select.value) {
                select.classList.add(`statue-${select.value}`);
            }
        }

        function updateProjetSolde() {
            const budget = parseFloat(document.getElementById('projet_budget')?.value) || 0;
            const avance = parseFloat(document.getElementById('projet_avance')?.value) || 0;
            const solde = budget - avance;
            document.getElementById('projet_solde').value = formatMontant(solde);
        }

        const projetForm = document.getElementById('projetForm');
        const projetModalTitle = document.getElementById('projetModalTitle');
        const projetSubmitBtn = document.getElementById('projetSubmitBtn');
        const projetHttpMethod = document.getElementById('projet_http_method');
        const projetAvanceLabel = document.getElementById('projet_avance_label');
        const projetAvanceInput = document.getElementById('projet_avance');
        let projetFormMode = 'create';

        const projetFieldIds = [
            ...projetDateApi.fieldIds,
            'projet_nom',
            'projet_designation',
            'projet_client',
            'projet_delai',
            'projet_statut',
            'projet_budget',
            'projet_avance',
        ];

        function setProjetFormFields(mode) {
            projetFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (!field) return;

                if (mode === 'view') {
                    field.disabled = true;
                    return;
                }

                if (mode === 'edit' && id === 'projet_avance') {
                    field.disabled = true;
                    return;
                }

                field.disabled = false;
            });
        }

        function fillProjetForm(projet) {
            projetDateApi.setParts(projet.date || '');
            document.getElementById('projet_ref').value = projet.ref || '';
            document.getElementById('projet_nom').value = projet.nom || '';
            document.getElementById('projet_designation').value = projet.designation || '';

            const clientSelect = document.getElementById('projet_client');
            clientSelect.value = projet.client || '';
            if (!clientSelect.value && projet.client) {
                const option = Array.from(clientSelect.options).find((opt) => opt.value === projet.client);
                if (option) option.selected = true;
            }

            document.getElementById('projet_delai').value = projet.delai || '';
            document.getElementById('projet_statut').value = projet.statut || 'attente';
            updateProjetStatutColor();
            document.getElementById('projet_budget').value = projet.budget ?? '';
            projetAvanceInput.value = projet.montant_paye ?? 0;
            updateProjetSolde();
        }

        function openProjetModal() {
            projetFormMode = 'create';
            projetForm.action = '{{ url('/projets') }}';
            projetHttpMethod.disabled = true;
            projetHttpMethod.value = 'POST';
            projetModalTitle.textContent = 'Nouveau Projet';
            projetSubmitBtn.style.display = '';
            projetAvanceLabel.textContent = 'Avance';
            projetAvanceInput.name = 'avance';

            projetDateApi.setParts('', { emptyDayMonth: true });
            document.getElementById('projet_ref').value = nextProjetRef();
            document.getElementById('projet_nom').value = '';
            document.getElementById('projet_designation').value = '';
            document.getElementById('projet_client').selectedIndex = 0;
            document.getElementById('projet_delai').value = '';
            document.getElementById('projet_statut').value = '';
            document.getElementById('projet_statut').selectedIndex = 0;
            updateProjetStatutColor();
            document.getElementById('projet_budget').value = '';
            projetAvanceInput.value = '0';
            setProjetFormFields('create');
            updateProjetSolde();

            projetModal.classList.add('open');
            projetModal.setAttribute('aria-hidden', 'false');
            projetDateApi.jj?.focus();
        }

        function openProjetView(projet) {
            projetFormMode = 'view';
            fillProjetForm(projet);
            projetModalTitle.textContent = 'Voir Projet';
            projetSubmitBtn.style.display = 'none';
            projetAvanceLabel.textContent = 'Montant payé';
            projetAvanceInput.name = '';
            setProjetFormFields('view');

            projetModal.classList.add('open');
            projetModal.setAttribute('aria-hidden', 'false');
        }

        function openProjetEdit(projet) {
            projetFormMode = 'edit';
            projetForm.action = `{{ url('/projets') }}/${projet.id}`;
            projetHttpMethod.disabled = false;
            projetHttpMethod.value = 'PUT';
            fillProjetForm(projet);
            projetModalTitle.textContent = 'Modifier Projet';
            projetSubmitBtn.style.display = '';
            projetAvanceLabel.textContent = 'Montant payé';
            projetAvanceInput.name = '';
            setProjetFormFields('edit');

            projetModal.classList.add('open');
            projetModal.setAttribute('aria-hidden', 'false');
            document.getElementById('projet_nom').focus();
        }

        document.getElementById('projetsTableBody')?.addEventListener('click', (e) => {
            if (e.target.closest('.statue-form') || e.target.closest('.rappel-date-form') || e.target.closest('.envoye-switch') || e.target.closest('.relance-inline-input')) return;

            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const projet = projetsData.find((p) => p.id === row.dataset.id);
            if (!projet) return;

            if (e.target.closest('.action-btn.voir')) {
                openProjetView(projet);
            }

            if (e.target.closest('.action-btn.modifier')) {
                openProjetEdit(projet);
            }

            if (e.target.closest('.action-btn.pdf')) {
                openRecordPdf('FICHE PROJET', projet.ref || '', [
                    ['Date', projet.date],
                    ['Nom Projet', projet.nom],
                    ['Désignation', projet.designation],
                    ['Client', projet.client],
                    ['Délai', projet.delai],
                    ['Statue', statueLabels[projet.statut] || projet.statut],
                    ['Budget', projet.budget],
                    ['Montant payé', projet.montant_paye],
                    ['Solde', projet.solde],
                ]);
            }
        });

        function closeProjetModalFn() {
            projetModal.classList.remove('open');
            projetModal.setAttribute('aria-hidden', 'true');
        }

        btnAddProjet?.addEventListener('click', openProjetModal);
        closeProjetModal?.addEventListener('click', closeProjetModalFn);
        cancelProjetModal?.addEventListener('click', closeProjetModalFn);
        projetModal?.addEventListener('click', (e) => {
            if (e.target === projetModal) closeProjetModalFn();
        });

        document.getElementById('projet_budget')?.addEventListener('input', updateProjetSolde);
        document.getElementById('projet_avance')?.addEventListener('input', updateProjetSolde);
        document.getElementById('projet_statut')?.addEventListener('change', updateProjetStatutColor);
        updateProjetStatutColor();

        function nextPaiementRef() {
            const rows = document.querySelectorAll('#paiementsTableBody tr[data-id]');
            const n = rows.length + 1;
            return 'PAY-' + String(n).padStart(4, '0');
        }

        function formatMontant(n) {
            const num = Number(n) || 0;
            const fixed = num.toFixed(2);
            const parts = fixed.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            return parts.join('.');
        }

        function applyPaiementClient() {
            const client = document.getElementById('paiement_client')?.value;
            const projet = projetsData.find((p) => p.client === client);
            const titreDisplay = document.getElementById('paiement_titre_display');
            const titre = document.getElementById('paiement_titre');
            const budgetHidden = document.getElementById('paiement_budget');
            const budgetDisplay = document.getElementById('paiement_budget_display');
            const soldeActuel = document.getElementById('paiement_solde_actuel');
            const projetIdHidden = document.getElementById('paiement_projet_id');

            if (!projet) {
                titreDisplay.value = '';
                titre.value = '';
                budgetHidden.value = '';
                budgetDisplay.value = '';
                soldeActuel.value = '0';
                projetIdHidden.value = '';
                updatePaiementSolde();
                return;
            }

            const budget = parseFloat(projet.budget) || 0;
            const dejaPaye = parseFloat(projet.montant_paye) || 0;
            const restant = Math.max(0, parseFloat(projet.solde ?? (budget - dejaPaye)) || 0);

            projetIdHidden.value = projet.id;
            titre.value = projet.nom;
            titreDisplay.value = projet.nom;
            budgetHidden.value = budget;
            soldeActuel.value = restant;
            budgetDisplay.value = formatMontant(restant);
            updatePaiementSolde();
        }

        function updatePaiementSolde() {
            const projetId = document.getElementById('paiement_projet_id')?.value;
            const projet = projetsData.find((p) => p.id === projetId);
            const montantSaisi = parseFloat(document.getElementById('paiement_montant_paye')?.value) || 0;
            const budget = parseFloat(projet?.budget) || 0;
            const dejaPaye = parseFloat(projet?.montant_paye) || 0;
            let soldeRestant = parseFloat(projet?.solde);

            if (Number.isNaN(soldeRestant)) {
                soldeRestant = Math.max(0, budget - dejaPaye);
            }

            if (paiementFormMode === 'edit') {
                soldeRestant = Math.max(0, soldeRestant + (paiementEditIncrement || 0));
            }

            const solde = Math.max(0, soldeRestant - montantSaisi);
            document.getElementById('paiement_solde').value = formatMontant(solde);

            const budgetDisplay = document.getElementById('paiement_budget_display');
            if (budgetDisplay && paiementFormMode === 'create') {
                budgetDisplay.value = formatMontant(soldeRestant);
            }
        }

        const paiementForm = document.getElementById('paiementForm');
        const paiementModalTitle = document.getElementById('paiementModalTitle');
        const paiementSubmitBtn = document.getElementById('paiementSubmitBtn');
        const paiementHttpMethod = document.getElementById('paiement_http_method');
        let paiementFormMode = 'create';
        let paiementEditIncrement = 0;

        const paiementFieldIds = [
            ...paiementDateApi.fieldIds,
            'paiement_client',
            'paiement_montant_paye',
            'paiement_type_reg',
            'paiement_bnq',
            'paiement_tresorerie',
        ];

        function setPaiementFormFields(mode) {
            paiementFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (!field) return;

                if (mode === 'view') {
                    field.disabled = true;
                    return;
                }

                if ((mode === 'edit' || mode === 'view') && id === 'paiement_client') {
                    field.disabled = true;
                    return;
                }

                field.disabled = false;
            });
        }

        function fillPaiementForm(paiement) {
            paiementDateApi.setParts(paiement.date || '');
            document.getElementById('paiement_ref').value = paiement.ref || '';

            const clientSelect = document.getElementById('paiement_client');
            clientSelect.value = paiement.client || '';
            if (!clientSelect.value && paiement.client) {
                const option = Array.from(clientSelect.options).find((opt) => opt.value === paiement.client);
                if (option) option.selected = true;
            }

            document.getElementById('paiement_titre').value = paiement.titre_projet || '';
            document.getElementById('paiement_titre_display').value = paiement.titre_projet || '';
            document.getElementById('paiement_budget').value = paiement.budget ?? '';
            document.getElementById('paiement_projet_id').value = paiement.projet_id || '';
            document.getElementById('paiement_montant_paye').value = paiement.increment_paye ?? '';
            document.getElementById('paiement_type_reg').value = paiement.type_reg || '';
            document.getElementById('paiement_bnq').value = paiement.bnq || '';
            document.getElementById('paiement_tresorerie').value = paiement.tresorerie || '';
            paiementEditIncrement = parseFloat(paiement.increment_paye) || 0;

            const projet = projetsData.find((p) => p.id === paiement.projet_id);
            const budget = parseFloat(projet?.budget ?? paiement.budget) || 0;
            const dejaPaye = parseFloat(projet?.montant_paye) || 0;
            let soldeRestant = parseFloat(projet?.solde);
            if (Number.isNaN(soldeRestant)) {
                soldeRestant = Math.max(0, budget - dejaPaye);
            }
            soldeRestant = Math.max(0, soldeRestant + paiementEditIncrement);
            document.getElementById('paiement_solde_actuel').value = soldeRestant;
            document.getElementById('paiement_budget_display').value = formatMontant(soldeRestant);
            updatePaiementSolde();
        }

        function openPaiementModal() {
            paiementFormMode = 'create';
            paiementEditIncrement = 0;
            paiementForm.action = '{{ url('/paiements') }}';
            paiementHttpMethod.disabled = true;
            paiementHttpMethod.value = 'POST';
            paiementModalTitle.textContent = 'Nouveau Paiement';
            paiementSubmitBtn.style.display = '';

            paiementDateApi.setParts('', { emptyDayMonth: true });
            document.getElementById('paiement_ref').value = nextPaiementRef();
            document.getElementById('paiement_client').selectedIndex = 0;
            document.getElementById('paiement_titre_display').value = '';
            document.getElementById('paiement_titre').value = '';
            document.getElementById('paiement_budget').value = '';
            document.getElementById('paiement_budget_display').value = '';
            document.getElementById('paiement_solde_actuel').value = '0';
            document.getElementById('paiement_montant_paye').value = '';
            document.getElementById('paiement_projet_id').value = '';
            document.getElementById('paiement_type_reg').selectedIndex = 0;
            document.getElementById('paiement_bnq').value = '';
            document.getElementById('paiement_tresorerie').value = '';
            document.getElementById('paiement_solde').value = '';

            setPaiementFormFields('create');

            paiementModal.classList.add('open');
            paiementModal.setAttribute('aria-hidden', 'false');
            paiementDateApi.jj?.focus();
        }

        function openPaiementView(paiement) {
            paiementFormMode = 'view';
            fillPaiementForm(paiement);
            paiementModalTitle.textContent = 'Voir Paiement';
            paiementSubmitBtn.style.display = 'none';
            setPaiementFormFields('view');

            paiementModal.classList.add('open');
            paiementModal.setAttribute('aria-hidden', 'false');
        }

        function openPaiementEdit(paiement) {
            paiementFormMode = 'edit';
            paiementForm.action = `{{ url('/paiements') }}/${paiement.id}`;
            paiementHttpMethod.disabled = false;
            paiementHttpMethod.value = 'PUT';
            fillPaiementForm(paiement);
            paiementModalTitle.textContent = 'Modifier Paiement';
            paiementSubmitBtn.style.display = '';
            setPaiementFormFields('edit');

            paiementModal.classList.add('open');
            paiementModal.setAttribute('aria-hidden', 'false');
            document.getElementById('paiement_montant_paye').focus();
        }

        function openPaiementPdf(paiement) {
            openRecordPdf('FICHE PAIEMENT', paiement.ref || '', [
                ['Date', paiement.date],
                ['Titre Projet', paiement.titre_projet],
                ['Nom Client', paiement.client],
                ['Budget', paiement.budget],
                ['Montant ce paiement', paiement.increment_paye],
                ['Total payé projet', paiement.montant_paye],
                ['Trésorerie', paiement.tresorerie],
                ['Type règlement', paiement.type_reg],
                ['Banque', paiement.bnq],
                ['Solde', paiement.solde],
            ]);
        }

        document.getElementById('paiementsTableBody')?.addEventListener('click', (e) => {
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const paiement = paiementsData.find((p) => p.id === row.dataset.id);
            if (!paiement) return;

            if (e.target.closest('.action-btn.voir')) openPaiementView(paiement);
            if (e.target.closest('.action-btn.modifier')) openPaiementEdit(paiement);
            if (e.target.closest('.action-btn.pdf')) openPaiementPdf(paiement);
        });

        function closePaiementModalFn() {
            paiementModal.classList.remove('open');
            paiementModal.setAttribute('aria-hidden', 'true');
        }

        btnAddPaiement?.addEventListener('click', openPaiementModal);
        closePaiementModal?.addEventListener('click', closePaiementModalFn);
        cancelPaiementModal?.addEventListener('click', closePaiementModalFn);
        paiementModal?.addEventListener('click', (e) => {
            if (e.target === paiementModal) closePaiementModalFn();
        });

        document.getElementById('paiement_client')?.addEventListener('change', applyPaiementClient);
        document.getElementById('paiement_montant_paye')?.addEventListener('input', updatePaiementSolde);

        function parseDateFrToKey(value) {
            const raw = String(value || '').trim();
            const match = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!match) return null;
            return Number(`${match[3]}${match[2]}${match[1]}`);
        }

        function filterRelancesRows(tbodySelector, options, noResultId) {
            const mois = document.getElementById(options.moisId)?.value || '';
            const statue = document.getElementById(options.statueId)?.value || '';
            const deKey = parseDateFrToKey(document.getElementById(options.deId)?.value || '');
            const aKey = parseDateFrToKey(document.getElementById(options.aId)?.value || '');
            const importOnly = statue === 'nv_tab';
            const rows = document.querySelectorAll(`${tbodySelector} tr[data-id]`);
            let visible = 0;

            rows.forEach((row) => {
                const rowMois = row.dataset.mois || '';
                const rowStatue = row.dataset.statue || '';
                const rowDateKey = parseDateFrToKey(row.dataset.date || '');
                const rowImport = row.dataset.import === '1';
                const matchMois = !mois || rowMois === mois;
                const matchStatue = !statue || importOnly || rowStatue === statue;
                const matchDe = !deKey || (rowDateKey !== null && rowDateKey >= deKey);
                const matchA = !aKey || (rowDateKey !== null && rowDateKey <= aKey);
                const matchImport = !importOnly || rowImport;
                const show = matchMois && matchStatue && matchDe && matchA && matchImport;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector(`${tbodySelector} tr.empty-row:not(#${noResultId})`);
            const noResultRow = document.getElementById(noResultId);
            if (noResultRow) noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            if (emptyRow) emptyRow.style.display = rows.length === 0 ? '' : 'none';
        }

        function filterDashboardRelancesTable() {
            filterRelancesRows('#dashboardRelancesTableBody', {
                moisId: 'filter_dashboard_relance_mois',
                statueId: 'filter_dashboard_relance_statue',
                deId: 'filter_dashboard_relance_de',
                aId: 'filter_dashboard_relance_a',
            }, 'dashboardRelancesNoResult');
        }

        function bindDateMask(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', () => {
                let v = input.value.replace(/\D/g, '').slice(0, 8);
                if (v.length >= 5) v = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
                else if (v.length >= 3) v = `${v.slice(0, 2)}/${v.slice(2)}`;
                input.value = v;
                filterDashboardRelancesTable();
            });
            input.addEventListener('change', filterDashboardRelancesTable);
        }

        document.getElementById('filter_dashboard_relance_mois')?.addEventListener('change', filterDashboardRelancesTable);
        document.getElementById('filter_dashboard_relance_statue')?.addEventListener('change', filterDashboardRelancesTable);
        bindDateMask('filter_dashboard_relance_de');
        bindDateMask('filter_dashboard_relance_a');

        document.querySelectorAll('.rappel-date-input').forEach((input) => {
            const initial = input.value;
            input.dataset.initial = initial;

            input.addEventListener('input', () => {
                let v = input.value.replace(/\D/g, '').slice(0, 8);
                if (v.length >= 5) v = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
                else if (v.length >= 3) v = `${v.slice(0, 2)}/${v.slice(2)}`;
                input.value = v;
            });

            const trySubmit = () => {
                const value = (input.value || '').trim();
                if (!/^\d{2}\/\d{2}\/\d{4}$/.test(value)) return;
                if (value === (input.dataset.initial || '')) return;
                input.form?.submit();
            };

            input.addEventListener('change', trySubmit);
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    trySubmit();
                }
            });
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        document.querySelectorAll('.envoye-switch').forEach((group) => {
            group.addEventListener('click', async (e) => {
                const btn = e.target.closest('.envoye-opt');
                if (!btn || group.classList.contains('is-saving')) return;

                const value = btn.dataset.value;
                const id = group.dataset.id;
                if (!value || !id || group.dataset.value === value) return;

                const previous = group.dataset.value || '';
                group.classList.add('is-saving');
                group.dataset.value = value;
                group.querySelectorAll('.envoye-opt').forEach((opt) => {
                    opt.classList.toggle('is-active', opt.dataset.value === value);
                });

                try {
                    const response = await fetch(`{{ url('/relances') }}/${encodeURIComponent(id)}/envoye`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ envoye: value }),
                    });

                    if (!response.ok) throw new Error('save_failed');

                    const item = (typeof relancesData !== 'undefined')
                        ? relancesData.find((r) => r.id === id)
                        : null;
                    if (item) item.envoye = value;
                } catch (err) {
                    group.dataset.value = previous;
                    group.querySelectorAll('.envoye-opt').forEach((opt) => {
                        opt.classList.toggle('is-active', opt.dataset.value === previous);
                    });
                } finally {
                    group.classList.remove('is-saving');
                }
            });
        });

        document.querySelectorAll('.relance-inline-input').forEach((el) => {
            const initial = el.tagName === 'SELECT' ? el.value : (el.value || '').trim();
            el.dataset.initial = initial;

            const saveInline = async () => {
                const id = el.dataset.id;
                const field = el.dataset.field;
                if (!id || !field || el.classList.contains('is-saving')) return;

                let value = el.tagName === 'SELECT' ? el.value : (el.value || '').trim();
                if (field === 'budget') {
                    value = value.replace(',', '.').replace(/\s+/g, '');
                }
                if (value === (el.dataset.initial || '')) return;

                el.classList.add('is-saving');
                try {
                    const response = await fetch(`{{ url('/relances') }}/${encodeURIComponent(id)}/inline`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ field, value }),
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'save_failed');

                    const saved = data.value ?? value;
                    if (field === 'budget') {
                        el.value = Number(saved).toFixed(2);
                        el.dataset.initial = el.value;
                    } else {
                        if (el.tagName !== 'SELECT') el.value = saved;
                        el.dataset.initial = String(saved);
                    }

                    const item = (typeof relancesData !== 'undefined')
                        ? relancesData.find((r) => r.id === id)
                        : null;
                    if (item) item[field] = saved;

                    document.querySelectorAll(`.relance-inline-input[data-id="${CSS.escape(id)}"][data-field="${field}"]`).forEach((twin) => {
                        if (twin === el) return;
                        if (field === 'budget') twin.value = Number(saved).toFixed(2);
                        else twin.value = String(saved);
                        twin.dataset.initial = twin.tagName === 'SELECT' ? twin.value : (twin.value || '').trim();
                    });
                } catch (err) {
                    if (el.tagName === 'SELECT') el.value = el.dataset.initial || '';
                    else el.value = el.dataset.initial || '';
                } finally {
                    el.classList.remove('is-saving');
                }
            };

            el.addEventListener('change', saveInline);
            el.addEventListener('blur', () => {
                if (el.tagName !== 'SELECT') saveInline();
            });
            el.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && el.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    el.blur();
                }
            });
        });

        const btnToggleDashboardCards = document.getElementById('btnToggleDashboardCards');
        const panelDashboard = document.getElementById('panel-dashboard');

        function applyDashboardCardsVisibility(hidden) {
            panelDashboard?.classList.toggle('cards-hidden', hidden);
            document.documentElement.classList.toggle('cards-hidden-boot', hidden);
            if (btnToggleDashboardCards) {
                btnToggleDashboardCards.textContent = hidden ? 'Afficher' : 'Masquer';
                btnToggleDashboardCards.setAttribute('aria-pressed', hidden ? 'true' : 'false');
            }
            try {
                localStorage.setItem('evopro_dashboard_cards_hidden', hidden ? '1' : '0');
            } catch (e) {}
        }

        btnToggleDashboardCards?.addEventListener('click', () => {
            const hidden = !panelDashboard?.classList.contains('cards-hidden');
            applyDashboardCardsVisibility(hidden);
        });

        try {
            if (localStorage.getItem('evopro_dashboard_cards_hidden') === '1') {
                applyDashboardCardsVisibility(true);
            }
        } catch (e) {}

        function filterProjetsTable() {
            const mois = document.getElementById('filter_projet_mois')?.value || '';
            const client = document.getElementById('filter_projet_client')?.value || '';
            const statue = document.getElementById('filter_projet_statue')?.value || '';

            const rows = document.querySelectorAll('#projetsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const rowMois = row.dataset.mois || '';
                const rowClient = row.dataset.client || '';
                const rowStatue = row.dataset.statue || '';

                const matchMois = !mois || rowMois === mois;
                const matchClient = !client || rowClient === client;
                const matchStatue = !statue || rowStatue === statue;

                const show = matchMois && matchClient && matchStatue;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#projetsTableBody tr.empty-row:not(#projetsNoResult)');
            const noResultRow = document.getElementById('projetsNoResult');

            if (noResultRow) {
                noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            }

            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 ? '' : 'none';
            }
        }

        document.getElementById('filter_projet_mois')?.addEventListener('change', filterProjetsTable);
        document.getElementById('filter_projet_client')?.addEventListener('change', filterProjetsTable);
        document.getElementById('filter_projet_statue')?.addEventListener('change', filterProjetsTable);

        function filterPaiementsTable() {
            const mois = document.getElementById('filter_paiement_mois')?.value || '';
            const client = document.getElementById('filter_paiement_client')?.value || '';
            const budget = (document.getElementById('filter_paiement_budget')?.value || '').trim().replace(/\s/g, '');
            const tresorerie = document.getElementById('filter_paiement_tresorerie')?.value || '';

            const rows = document.querySelectorAll('#paiementsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const rowMois = row.dataset.mois || '';
                const rowClient = row.dataset.client || '';
                const rowBudget = String(row.dataset.budget || '').replace(/\s/g, '');
                const rowTresorerie = row.dataset.tresorerie || '';

                const matchMois = !mois || rowMois === mois;
                const matchClient = !client || rowClient === client;
                const matchBudget = !budget || rowBudget.includes(budget);
                const matchTresorerie = !tresorerie || rowTresorerie === tresorerie;

                const show = matchMois && matchClient && matchBudget && matchTresorerie;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#paiementsTableBody tr.empty-row:not(#paiementsNoResult)');
            const noResultRow = document.getElementById('paiementsNoResult');

            if (noResultRow) {
                noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            }

            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 ? '' : 'none';
            }
        }

        document.getElementById('filter_paiement_mois')?.addEventListener('change', filterPaiementsTable);
        document.getElementById('filter_paiement_client')?.addEventListener('change', filterPaiementsTable);
        document.getElementById('filter_paiement_budget')?.addEventListener('input', filterPaiementsTable);
        document.getElementById('filter_paiement_tresorerie')?.addEventListener('change', filterPaiementsTable);

        function filterEvolutionsTable() {
            const mois = document.getElementById('filter_evolution_mois')?.value || '';
            const projet = document.getElementById('filter_evolution_projet')?.value || '';

            const rows = document.querySelectorAll('#evolutionsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const matchMois = !mois || row.dataset.mois === mois;
                const matchProjet = !projet || row.dataset.projet === projet;
                const show = matchMois && matchProjet;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#evolutionsTableBody tr.empty-row:not(#evolutionsNoResult)');
            const noResultRow = document.getElementById('evolutionsNoResult');

            if (noResultRow) {
                noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            }

            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 ? '' : 'none';
            }
        }

        document.getElementById('filter_evolution_mois')?.addEventListener('change', filterEvolutionsTable);
        document.getElementById('filter_evolution_projet')?.addEventListener('change', filterEvolutionsTable);

        function filterTableByMoisStatue(tbodySelector, moisId, statueId, noResultId) {
            const mois = document.getElementById(moisId)?.value || '';
            const statue = document.getElementById(statueId)?.value || '';
            const rows = document.querySelectorAll(`${tbodySelector} tr[data-id]`);
            let visible = 0;

            rows.forEach((row) => {
                const matchMois = !mois || (row.dataset.mois || '') === mois;
                const matchStatue = !statue || (row.dataset.statue || '') === statue;
                const show = matchMois && matchStatue;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector(`${tbodySelector} tr.empty-row:not(#${noResultId})`);
            const noResultRow = document.getElementById(noResultId);

            if (noResultRow) {
                noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            }
            if (emptyRow) {
                emptyRow.style.display = rows.length === 0 ? '' : 'none';
            }
        }

        function filterClientsTable() {
            const mois = document.getElementById('filter_client_mois')?.value || '';
            const rows = document.querySelectorAll('#clientsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const show = !mois || (row.dataset.mois || '') === mois;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#clientsTableBody tr.empty-row:not(#clientsNoResult)');
            const noResultRow = document.getElementById('clientsNoResult');
            if (noResultRow) noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            if (emptyRow) emptyRow.style.display = rows.length === 0 ? '' : 'none';
        }

        function filterRelancesTable() {
            filterRelancesRows('#relancesTableBody', {
                moisId: 'filter_relance_mois',
                statueId: 'filter_relance_statue',
                deId: 'filter_relance_de',
                aId: 'filter_relance_a',
            }, 'relancesNoResult');
        }

        function filterUtilisateursTable() {
            filterTableByMoisStatue('#utilisateursTableBody', 'filter_utilisateur_mois', 'filter_utilisateur_statue', 'utilisateursNoResult');
        }

        function bindRelancePanelDateMask(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', () => {
                let v = input.value.replace(/\D/g, '').slice(0, 8);
                if (v.length >= 5) v = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
                else if (v.length >= 3) v = `${v.slice(0, 2)}/${v.slice(2)}`;
                input.value = v;
                filterRelancesTable();
            });
            input.addEventListener('change', filterRelancesTable);
        }

        document.getElementById('filter_client_mois')?.addEventListener('change', filterClientsTable);
        document.getElementById('filter_relance_mois')?.addEventListener('change', filterRelancesTable);
        document.getElementById('filter_relance_statue')?.addEventListener('change', filterRelancesTable);
        bindRelancePanelDateMask('filter_relance_de');
        bindRelancePanelDateMask('filter_relance_a');
        document.getElementById('filter_utilisateur_mois')?.addEventListener('change', filterUtilisateursTable);
        document.getElementById('filter_utilisateur_statue')?.addEventListener('change', filterUtilisateursTable);

        const relanceFieldIds = [
            ...relanceRappelDateApi.fieldIds,
            'relance_nom_complet',
            'relance_telephone',
            'relance_ville',
            'relance_titre_projet',
            'relance_description',
            'relance_budget',
            'relance_envoye',
            'relance_statue',
            'relance_a_rappeler',
        ];

        function setRelanceFormFields(mode) {
            relanceFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (field) field.disabled = mode === 'view';
            });
        }

        function nextRelanceRef() {
            const rows = document.querySelectorAll('#relancesTableBody tr[data-id]');
            const n = rows.length + 1;
            return 'REL-' + String(n).padStart(4, '0');
        }

        function fillRelanceForm(relance) {
            document.getElementById('relance_date').value = relance.date || '';
            document.getElementById('relance_ref').value = relance.ref || '';
            document.getElementById('relance_nom_complet').value = relance.nom_complet || '';
            document.getElementById('relance_telephone').value = relance.telephone || '';
            document.getElementById('relance_ville').value = relance.ville || '';
            document.getElementById('relance_titre_projet').value = relance.titre_projet || '';
            document.getElementById('relance_description').value = relance.description || '';
            document.getElementById('relance_budget').value = relance.budget ?? '';
            document.getElementById('relance_envoye').value = relance.envoye || '';
            document.getElementById('relance_statue').value = relance.statue || '';
            document.getElementById('relance_a_rappeler').value = relance.a_rappeler || '';
            relanceRappelDateApi.setParts(relance.date_rappel || '');
        }

        function setRelanceImportBtnVisible(visible) {
            const btn = document.getElementById('btnOpenRelanceImport');
            if (btn) btn.style.display = visible ? '' : 'none';
        }

        function openRelanceModal() {
            const relanceForm = document.getElementById('relanceForm');
            const relanceModalTitle = document.getElementById('relanceModalTitle');
            const relanceSubmitBtn = document.getElementById('relanceSubmitBtn');
            const relanceHttpMethod = document.getElementById('relance_http_method');

            relanceForm.action = '{{ url('/relances') }}';
            relanceHttpMethod.disabled = true;
            relanceHttpMethod.value = 'POST';
            relanceModalTitle.textContent = 'Nouveau Prospect';
            relanceSubmitBtn.style.display = '';
            setRelanceImportBtnVisible(true);

            document.getElementById('relance_date').value = todayFr();
            document.getElementById('relance_ref').value = nextRelanceRef();
            document.getElementById('relance_nom_complet').value = '';
            document.getElementById('relance_telephone').value = '';
            document.getElementById('relance_ville').value = '';
            document.getElementById('relance_titre_projet').value = '';
            document.getElementById('relance_description').value = '';
            document.getElementById('relance_budget').value = '';
            document.getElementById('relance_envoye').selectedIndex = 0;
            document.getElementById('relance_statue').selectedIndex = 0;
            document.getElementById('relance_a_rappeler').selectedIndex = 0;
            relanceRappelDateApi.setParts('', { emptyDayMonth: true });
            setRelanceFormFields('create');

            relanceModal.classList.add('open');
            relanceModal.setAttribute('aria-hidden', 'false');
            document.getElementById('relance_nom_complet').focus();
        }

        function openRelanceView(relance) {
            fillRelanceForm(relance);
            document.getElementById('relanceModalTitle').textContent = 'Voir Relance';
            document.getElementById('relanceSubmitBtn').style.display = 'none';
            setRelanceImportBtnVisible(false);
            setRelanceFormFields('view');

            relanceModal.classList.add('open');
            relanceModal.setAttribute('aria-hidden', 'false');
        }

        function openRelanceEdit(relance) {
            const relanceForm = document.getElementById('relanceForm');
            const relanceHttpMethod = document.getElementById('relance_http_method');

            relanceForm.action = `{{ url('/relances') }}/${relance.id}`;
            relanceHttpMethod.disabled = false;
            relanceHttpMethod.value = 'PUT';
            fillRelanceForm(relance);
            document.getElementById('relanceModalTitle').textContent = 'Modifier Relance';
            document.getElementById('relanceSubmitBtn').style.display = '';
            setRelanceImportBtnVisible(false);
            setRelanceFormFields('edit');

            relanceModal.classList.add('open');
            relanceModal.setAttribute('aria-hidden', 'false');
            document.getElementById('relance_nom_complet').focus();
        }

        function closeRelanceModalFn() {
            relanceModal.classList.remove('open');
            relanceModal.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('relancesTableBody')?.addEventListener('click', (e) => {
            if (e.target.closest('.rappel-date-form') || e.target.closest('.statue-form') || e.target.closest('.envoye-switch') || e.target.closest('.relance-inline-input')) return;
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const relance = relancesData.find((item) => item.id === row.dataset.id);
            if (!relance) return;

            if (e.target.closest('.action-btn.voir')) openRelanceView(relance);
            if (e.target.closest('.action-btn.modifier')) openRelanceEdit(relance);
        });

        btnAddRelance?.addEventListener('click', openRelanceModal);
        closeRelanceModal?.addEventListener('click', closeRelanceModalFn);
        cancelRelanceModal?.addEventListener('click', closeRelanceModalFn);
        relanceModal?.addEventListener('click', (e) => {
            if (e.target === relanceModal) closeRelanceModalFn();
        });

        (function initRelanceImport() {
            const importModal = document.getElementById('relanceImportModal');
            const dropzone = document.getElementById('relanceImportDropzone');
            const fileInput = document.getElementById('relanceImportFile');
            const fileNameEl = document.getElementById('relanceImportFileName');
            const statusEl = document.getElementById('relanceImportStatus');
            const phonesWrap = document.getElementById('relanceImportPhonesWrap');
            const phonesCountEl = document.getElementById('relanceImportPhonesCount');
            const phoneListEl = document.getElementById('relanceImportPhoneList');
            const btnConfirm = document.getElementById('btnConfirmRelanceImport');
            const btnClear = document.getElementById('btnClearRelanceImportPhones');
            const btnOpenImport = document.getElementById('btnOpenRelanceImport');
            if (!importModal || !dropzone || !fileInput) return;

            let detectedPhones = [];

            function setStatus(msg, type) {
                if (!statusEl) return;
                if (!msg) {
                    statusEl.hidden = true;
                    statusEl.textContent = '';
                    statusEl.className = 'import-status';
                    return;
                }
                statusEl.hidden = false;
                statusEl.textContent = msg;
                statusEl.className = 'import-status' + (type ? ' is-' + type : '');
            }

            function formatPhoneFr(digits) {
                let d = digits;
                if (d.startsWith('212') && d.length >= 11) {
                    d = '0' + d.slice(3);
                } else if (d.startsWith('33') && d.length >= 11) {
                    d = '0' + d.slice(2);
                }
                if (d.length === 10 && d.startsWith('0')) {
                    return d.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
                }
                if (digits.startsWith('212')) return '+' + digits;
                return digits;
            }

            function extractPhones(text) {
                const raw = String(text || '');
                const candidates = [];
                const re = /(?:\+|00)?(?:212|33)?[\s.\-()]*(?:0?[5-7]|0?[6-7])(?:[\s.\-()]*\d){8,10}|\b0[5-7](?:[\s.\-]*\d){8}\b|(?:\+|00)212[\s.\-]*[5-7](?:[\s.\-]*\d){8}/g;
                let m;
                while ((m = re.exec(raw)) !== null) {
                    candidates.push(m[0]);
                }
                const loose = raw.match(/(?:\+?\d[\d\s.\-()]{8,18}\d)/g) || [];
                loose.forEach((c) => candidates.push(c));

                const seen = new Set();
                const out = [];
                candidates.forEach((c) => {
                    let digits = c.replace(/\D+/g, '');
                    if (digits.startsWith('00')) digits = digits.slice(2);
                    if (digits.startsWith('212') && digits.length >= 11) {
                        digits = '0' + digits.slice(3);
                    } else if (digits.startsWith('33') && digits.length === 11) {
                        digits = '0' + digits.slice(2);
                    }
                    if (!(digits.length === 10 && /^0[5-7]\d{8}$/.test(digits))) {
                        const alt = c.replace(/\D+/g, '');
                        if (/^212[5-7]\d{8}$/.test(alt)) {
                            digits = '0' + alt.slice(3);
                        } else {
                            return;
                        }
                    }
                    if (seen.has(digits)) return;
                    seen.add(digits);
                    out.push(formatPhoneFr(digits));
                });
                return out;
            }

            function renderPhones() {
                if (!phoneListEl || !phonesWrap || !phonesCountEl || !btnConfirm) return;
                phoneListEl.innerHTML = '';
                const n = detectedPhones.length;
                phonesWrap.hidden = n === 0;
                phonesCountEl.textContent = n === 0
                    ? '0 numéro détecté'
                    : (n === 1 ? '1 numéro détecté' : n + ' numéros détectés');
                btnConfirm.disabled = n === 0;
                detectedPhones.forEach((phone, idx) => {
                    const li = document.createElement('li');
                    li.className = 'import-phone-item';
                    const span = document.createElement('span');
                    span.textContent = phone;
                    const rm = document.createElement('button');
                    rm.type = 'button';
                    rm.className = 'import-phone-remove';
                    rm.setAttribute('aria-label', 'Retirer');
                    rm.textContent = '×';
                    rm.addEventListener('click', () => {
                        detectedPhones.splice(idx, 1);
                        renderPhones();
                    });
                    li.appendChild(span);
                    li.appendChild(rm);
                    phoneListEl.appendChild(li);
                });
            }

            function resetImportUi() {
                detectedPhones = [];
                fileInput.value = '';
                if (fileNameEl) fileNameEl.textContent = '';
                setStatus('');
                renderPhones();
            }

            function openImportModal() {
                resetImportUi();
                closeRelanceModalFn();
                importModal.classList.add('open');
                importModal.setAttribute('aria-hidden', 'false');
            }

            function closeImportModal() {
                importModal.classList.remove('open');
                importModal.setAttribute('aria-hidden', 'true');
            }

            async function ocrImageSource(src) {
                if (typeof Tesseract === 'undefined') {
                    throw new Error('OCR indisponible');
                }
                const result = await Tesseract.recognize(src, 'fra+eng', {
                    logger: (info) => {
                        if (info.status === 'recognizing text' && info.progress != null) {
                            setStatus('Analyse OCR… ' + Math.round(info.progress * 100) + '%');
                        }
                    },
                });
                return result?.data?.text || '';
            }

            async function extractTextFromImageFile(file) {
                setStatus('Analyse de l’image…', 'info');
                return ocrImageSource(file);
            }

            async function extractTextFromPdf(file) {
                if (!window.pdfjsLib) {
                    throw new Error('PDF.js indisponible');
                }
                setStatus('Lecture du PDF…', 'info');
                const buf = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({ data: buf }).promise;
                let fullText = '';
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    setStatus('PDF page ' + pageNum + '/' + pdf.numPages + '…', 'info');
                    const page = await pdf.getPage(pageNum);
                    const content = await page.getTextContent();
                    const pageText = content.items.map((it) => it.str || '').join(' ');
                    fullText += '\n' + pageText;
                }
                const phonesFromText = extractPhones(fullText);
                if (phonesFromText.length > 0 || fullText.replace(/\s+/g, '').length > 40) {
                    return fullText;
                }
                setStatus('PDF scanné détecté, OCR…', 'info');
                let ocrText = '';
                for (let pageNum = 1; pageNum <= Math.min(pdf.numPages, 8); pageNum++) {
                    const page = await pdf.getPage(pageNum);
                    const viewport = page.getViewport({ scale: 2 });
                    const canvas = document.createElement('canvas');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    const ctx = canvas.getContext('2d');
                    await page.render({ canvasContext: ctx, viewport }).promise;
                    ocrText += '\n' + await ocrImageSource(canvas);
                }
                return ocrText || fullText;
            }

            async function processFile(file) {
                if (!file) return;
                if (fileNameEl) fileNameEl.textContent = file.name;
                detectedPhones = [];
                renderPhones();
                btnConfirm.disabled = true;
                try {
                    const isPdf = /pdf$/i.test(file.type) || /\.pdf$/i.test(file.name);
                    const isImage = /^image\//i.test(file.type) || /\.(jpe?g|png|webp|gif|bmp)$/i.test(file.name);
                    if (!isPdf && !isImage) {
                        setStatus('Format non supporté. Utilisez une image ou un PDF.', 'error');
                        return;
                    }
                    const text = isPdf
                        ? await extractTextFromPdf(file)
                        : await extractTextFromImageFile(file);
                    detectedPhones = extractPhones(text);
                    renderPhones();
                    if (detectedPhones.length === 0) {
                        setStatus('Aucun numéro détecté. Essayez une image plus nette.', 'error');
                    } else {
                        setStatus(detectedPhones.length + ' numéro(s) prêt(s) à classer.', 'ok');
                    }
                } catch (err) {
                    console.error(err);
                    setStatus('Échec de l’analyse : ' + (err.message || 'erreur'), 'error');
                }
            }

            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    fileInput.click();
                }
            });
            fileInput.addEventListener('change', () => {
                const f = fileInput.files && fileInput.files[0];
                if (f) processFile(f);
            });
            ['dragenter', 'dragover'].forEach((ev) => {
                dropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('is-dragover');
                });
            });
            ['dragleave', 'drop'].forEach((ev) => {
                dropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('is-dragover');
                });
            });
            dropzone.addEventListener('drop', (e) => {
                const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                if (f) processFile(f);
            });

            btnOpenImport?.addEventListener('click', openImportModal);
            document.getElementById('closeRelanceImportModal')?.addEventListener('click', closeImportModal);
            document.getElementById('cancelRelanceImportModal')?.addEventListener('click', closeImportModal);
            importModal.addEventListener('click', (e) => {
                if (e.target === importModal) closeImportModal();
            });
            btnClear?.addEventListener('click', () => {
                detectedPhones = [];
                renderPhones();
                setStatus('Liste vidée.', 'info');
            });

            btnConfirm?.addEventListener('click', async () => {
                if (!detectedPhones.length) return;
                btnConfirm.disabled = true;
                setStatus('Création des prospects…', 'info');
                try {
                    const res = await fetch('{{ url('/relances/import') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ telephones: detectedPhones }),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        throw new Error(data.message || 'Import refusé');
                    }
                    const created = data.created ?? detectedPhones.length;
                    const skipped = data.skipped || 0;
                    setStatus(created + ' créé(s)' + (skipped ? ', ' + skipped + ' déjà présent(s)' : '') + '. Redirection…', 'ok');
                    const url = new URL('{{ url('/dashboard') }}', window.location.origin);
                    url.searchParams.set('open', 'relance');
                    window.location.href = url.toString();
                } catch (err) {
                    console.error(err);
                    setStatus('Import impossible : ' + (err.message || 'erreur'), 'error');
                    btnConfirm.disabled = false;
                }
            });
        })();

        function setAutorisationFormFields(mode) {
            const userSelect = document.getElementById('autorisation_utilisateur_id');
            if (userSelect) userSelect.disabled = mode === 'view';
            document.querySelectorAll('#autorisationSections .auth-permission').forEach((cb) => {
                cb.disabled = mode === 'view';
            });
        }

        function clearAutorisationChecks() {
            document.querySelectorAll('#autorisationSections .auth-permission').forEach((cb) => {
                cb.checked = false;
            });
        }

        function fillAutorisationForm(autorisation) {
            document.getElementById('autorisation_utilisateur_id').value = autorisation.utilisateur_id || '';
            const perms = new Set(autorisation.permissions || []);
            document.querySelectorAll('#autorisationSections .auth-permission').forEach((cb) => {
                cb.checked = perms.has(cb.value);
            });
        }

        function openAutorisationModal() {
            const form = document.getElementById('autorisationForm');
            const method = document.getElementById('autorisation_http_method');

            form.action = '{{ url('/autorisations') }}';
            method.disabled = true;
            method.value = 'POST';
            document.getElementById('autorisationModalTitle').textContent = 'Ajouter Autorisation';
            document.getElementById('autorisationSubmitBtn').style.display = '';
            document.getElementById('autorisation_utilisateur_id').selectedIndex = 0;
            clearAutorisationChecks();
            setAutorisationFormFields('create');

            autorisationModal.classList.add('open');
            autorisationModal.setAttribute('aria-hidden', 'false');
            document.getElementById('autorisation_utilisateur_id').focus();
        }

        function openAutorisationView(autorisation) {
            fillAutorisationForm(autorisation);
            document.getElementById('autorisationModalTitle').textContent = 'Voir Autorisation';
            document.getElementById('autorisationSubmitBtn').style.display = 'none';
            setAutorisationFormFields('view');

            autorisationModal.classList.add('open');
            autorisationModal.setAttribute('aria-hidden', 'false');
        }

        function openAutorisationEdit(autorisation) {
            const form = document.getElementById('autorisationForm');
            const method = document.getElementById('autorisation_http_method');

            form.action = `{{ url('/autorisations') }}/${autorisation.id}`;
            method.disabled = false;
            method.value = 'PUT';
            fillAutorisationForm(autorisation);
            document.getElementById('autorisationModalTitle').textContent = 'Modifier Autorisation';
            document.getElementById('autorisationSubmitBtn').style.display = '';
            setAutorisationFormFields('edit');

            autorisationModal.classList.add('open');
            autorisationModal.setAttribute('aria-hidden', 'false');
        }

        function closeAutorisationModalFn() {
            autorisationModal.classList.remove('open');
            autorisationModal.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('autorisationsTableBody')?.addEventListener('click', (e) => {
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const autorisation = autorisationsData.find((item) => item.id === row.dataset.id);
            if (!autorisation) return;

            if (e.target.closest('.action-btn.voir')) openAutorisationView(autorisation);
            if (e.target.closest('.action-btn.modifier')) openAutorisationEdit(autorisation);
        });

        btnAddAutorisation?.addEventListener('click', openAutorisationModal);
        btnCloseAutorisation?.addEventListener('click', () => showPanel('dashboard'));
        closeAutorisationModal?.addEventListener('click', closeAutorisationModalFn);
        cancelAutorisationModal?.addEventListener('click', closeAutorisationModalFn);
        autorisationModal?.addEventListener('click', (e) => {
            if (e.target === autorisationModal) closeAutorisationModalFn();
        });

        @if (session('open_fiche_client'))
            showPanel('fiche-client');
        @endif

        @if (session('open_fiche_projet'))
            showPanel('fiche-projet');
        @endif

        @if (session('open_fiche_evolution'))
            showPanel('fiche-evolution');
        @endif

        @if (session('open_fiche_paiement'))
            showPanel('fiche-paiement');
        @endif

        @if (session('open_fiche_utilisateur'))
            showPanel('fiche-utilisateur');
        @endif

        @if (session('open_fiche_relance'))
            showPanel('fiche-relance');
        @endif

        if (new URLSearchParams(window.location.search).get('open') === 'relance') {
            showPanel('fiche-relance');
            const clean = window.location.pathname + window.location.hash;
            window.history.replaceState({}, '', clean);
        }

        @if (session('open_fiche_autorisation'))
            showPanel('fiche-autorisation');
        @endif
    </script>
</body>
</html>
