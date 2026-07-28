<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EvoPro — Tableau de bord</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            opacity: 0.85;
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
            padding: 0.55rem 0.8rem;
            border-radius: 8px;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 500;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .submenu a:hover,
        .submenu a.active {
            background: rgba(59, 158, 255, 0.12);
            color: #fff;
        }

        .side-foot {
            padding: 0.8rem 0.9rem;
            border-top: 1px solid var(--line);
            font-size: 0.78rem;
            color: var(--muted);
        }

        .panel { display: none; }
        .panel.active { display: block; animation: fadeUp 0.35s ease both; }

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

        .data-table th,
        .data-table td {
            padding: 0.75rem 0.85rem;
            text-align: center;
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
        }

        .data-table tbody tr:hover { background: rgba(59, 158, 255, 0.06); }
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

        #projet_statut option[value="actif"] { color: #7ec4ff; }
        #projet_statut option[value="attente"] { color: #ffc857; }
        #projet_statut option[value="annule"] { color: #ff9aa0; }

        #projet_statut.statue-actif { color: #7ec4ff; border-color: rgba(126, 196, 255, 0.45); }
        #projet_statut.statue-attente { color: #ffc857; border-color: rgba(240, 180, 41, 0.45); }
        #projet_statut.statue-annule { color: #ff9aa0; border-color: rgba(240, 113, 120, 0.45); }

        .statue-form {
            display: inline-block;
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
        .modal-body select {
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

        .modal-body select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237ec4ff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.25rem;
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
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, #1e6fd9, #4eb3ff);
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
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
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1.1rem;
            width: 100%;
        }

        .card {
            position: relative;
            overflow: hidden;
            padding: 1.35rem 1.25rem 1.2rem;
            min-height: 148px;
            border-radius: 16px;
            background: linear-gradient(165deg, rgba(16, 32, 54, 0.92), rgba(10, 22, 40, 0.88));
            border: 1px solid rgba(110, 168, 255, 0.2);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            animation: fadeUp 0.45s ease both;
            cursor: default;
            display: flex;
            flex-direction: column;
        }

        .card:nth-child(1) { animation-delay: 0.05s; }
        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.15s; }
        .card:nth-child(4) { animation-delay: 0.2s; }
        .card:nth-child(5) { animation-delay: 0.25s; }

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
        .card.revenu::after { background: linear-gradient(90deg, var(--cyan), transparent); }
        .card.solde::after { background: linear-gradient(90deg, var(--violet), transparent); }

        .card:hover {
            transform: translateY(-2px);
            border-color: rgba(126, 196, 255, 0.28);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.22), 0 0 16px rgba(59, 158, 255, 0.08);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.65rem;
            margin-bottom: 0.85rem;
        }

        .card-label {
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--muted);
            line-height: 1.3;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .card.actif .card-icon { background: rgba(61, 207, 138, 0.12); color: var(--green); }
        .card.attente .card-icon { background: rgba(240, 180, 41, 0.12); color: var(--amber); }
        .card.annule .card-icon { background: rgba(240, 113, 120, 0.12); color: var(--rose); }
        .card.revenu .card-icon { background: rgba(77, 212, 234, 0.12); color: var(--cyan); }
        .card.solde .card-icon { background: rgba(155, 123, 255, 0.12); color: var(--violet); }

        .card-icon svg { width: 20px; height: 20px; }

        .card-value {
            font-size: 2.1rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
            flex: 1;
        }

        .card.actif .card-value { color: #7ee8b0; }
        .card.attente .card-value { color: #ffc857; }
        .card.annule .card-value { color: #ff9aa0; }
        .card.revenu .card-value { color: #7ee8f5; }
        .card.solde .card-value { color: #c4b0ff; }

        .chart-section {
            margin-top: 1.5rem;
            padding: 1.35rem 1.25rem 1.1rem;
            border-radius: 16px;
            background: linear-gradient(165deg, rgba(16, 32, 54, 0.92), rgba(10, 22, 40, 0.88));
            border: 1px solid rgba(110, 168, 255, 0.2);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
            animation: fadeUp 0.5s ease 0.3s both;
        }

        .chart-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .chart-head h2 {
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .chart-head p {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

        .chart-filters {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .chart-filter {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            min-width: 140px;
        }

        .chart-filter label {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 500;
        }

        .chart-filter select {
            padding: 0.45rem 0.55rem;
            border-radius: 8px;
            border: 1px solid rgba(110, 168, 255, 0.25);
            background: rgba(8, 18, 32, 0.85);
            color: var(--text);
            font-family: inherit;
            font-size: 0.78rem;
        }

        .chart-wrap {
            position: relative;
            height: 320px;
            width: 100%;
        }

        .chart-empty {
            display: none;
            align-items: center;
            justify-content: center;
            height: 320px;
            color: var(--muted);
            font-size: 0.85rem;
            border: 1px dashed rgba(110, 168, 255, 0.2);
            border-radius: 12px;
        }

        .chart-empty.visible {
            display: flex;
        }

        .card-hint {
            font-size: 0.75rem;
            color: rgba(210, 224, 245, 0.55);
            font-weight: 400;
            margin-top: 0.35rem;
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

        @media (max-width: 960px) {
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

            .menu-toggle { display: inline-flex; }

            .cards { grid-template-columns: repeat(2, 1fr); }

            .card { min-height: 120px; }
            .card-value { font-size: 1.65rem; }

            .navbar, .content { padding-left: 1.1rem; padding-right: 1.1rem; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
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

            <nav class="nav-list" aria-label="Navigation principale">
                <a href="#dashboard" class="dashboard-nav-btn active" data-panel="dashboard" id="dashboardNavBtn">
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
                <div class="nav-group" id="clientGroup">
                    <button type="button" class="nav-item nav-parent" id="clientToggle">
                        <span class="nav-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Client
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-client" class="submenu-link" data-panel="fiche-client">1 — Fiche Client</a>
                    </div>
                </div>
                <div class="nav-group" id="projetGroup">
                    <button type="button" class="nav-item nav-parent" id="projetToggle">
                        <span class="nav-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/><path d="M8 7v10"/><path d="M16 7v10"/></svg>
                            Projets
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-projet" class="submenu-link" data-panel="fiche-projet">1 — Fiche Projet</a>
                    </div>
                </div>
                <div class="nav-group" id="paiementGroup">
                    <button type="button" class="nav-item nav-parent" id="paiementToggle">
                        <span class="nav-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            Paiement
                        </span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="submenu">
                        <a href="#fiche-paiement" class="submenu-link" data-panel="fiche-paiement">1 — Fiche Paiement</a>
                    </div>
                </div>
                <a href="#charges" class="nav-item" data-panel="dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Charges
                </a>
                <a href="#suivie" class="nav-item" data-panel="dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
                    Suivie Monétaire
                </a>
                <a href="#rapports" class="nav-item" data-panel="dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg>
                    Rapports
                </a>
                <a href="#configuration" class="nav-item" data-panel="dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9c.3.6.9 1 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                    Configuration
                </a>
                </div>
            </nav>

            <div class="side-foot">EvoPro — Système de Gestion</div>
        </aside>

        <div class="main">
            <header class="navbar">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                    <button class="menu-toggle" type="button" id="menuToggle" aria-label="Ouvrir le menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="navbar-brand">
                        <div class="brand-glow">Evo<span>Pro</span></div>
                        <span class="brand-tagline">La Solution qui Gère</span>
                    </div>
                </div>

                <div class="nav-right">
                    <div class="user-chip">
                        <span class="avatar">{{ strtoupper(substr(session('login', 'U'), 0, 1)) }}</span>
                        <span>{{ session('login', 'Utilisateur') }}</span>
                    </div>
                    <form method="post" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-logout" aria-label="Se déconnecter">
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
                </div>
            </header>

            <main class="content">
                <section class="panel active" id="panel-dashboard">
                    <div class="content-head">
                        <h1>Tableau de bord</h1>
                    </div>

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

                        <article class="card annule">
                            <div class="card-top">
                                <span class="card-label">Projets Annulés</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ $dashboardCounts['annule'] ?? 0 }}</div>
                        </article>

                        <article class="card revenu">
                            <div class="card-top">
                                <span class="card-label">Total Revenu</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ number_format($totalRevenu ?? 0, 0, ',', ' ') }}</div>
                            <p class="card-hint">MAD</p>
                        </article>

                        <article class="card solde">
                            <div class="card-top">
                                <span class="card-label">Total Solde</span>
                                <div class="card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                </div>
                            </div>
                            <div class="card-value">{{ number_format($totalSolde ?? 0, 0, ',', ' ') }}</div>
                            <p class="card-hint">MAD</p>
                        </article>
                    </section>

                    <section class="chart-section" aria-label="Diagramme projets">
                        <div class="chart-head">
                            <div>
                                <h2>Projets par Mois / Année</h2>
                                <p>Projets actifs, en attente et annulés chargés par période</p>
                            </div>
                            <div class="chart-filters">
                                <div class="chart-filter">
                                    <label for="chart_filter_annee">Année</label>
                                    <select id="chart_filter_annee">
                                        <option value="">TOUTES LES ANNÉES</option>
                                        @php
                                            $chartYears = collect($chartProjets ?? [])
                                                ->pluck('annee')
                                                ->filter()
                                                ->unique()
                                                ->sort()
                                                ->values();
                                        @endphp
                                        @foreach ($chartYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="chart-filter">
                                    <label for="chart_filter_mois">Mois</label>
                                    <select id="chart_filter_mois">
                                        <option value="">TOUS LES MOIS</option>
                                        @php
                                            $chartMonths = collect($chartProjets ?? [])
                                                ->pluck('mois')
                                                ->filter()
                                                ->unique()
                                                ->sort()
                                                ->values();
                                        @endphp
                                        @foreach ($chartMonths as $month)
                                            <option value="{{ $month }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="chart-wrap" id="projetsChartWrap">
                            <canvas id="projetsChart" aria-label="Diagramme des projets"></canvas>
                        </div>
                        <div class="chart-empty" id="projetsChartEmpty">AUCUN PROJET POUR CETTE PÉRIODE</div>
                    </section>
                </section>

                <section class="panel" id="panel-fiche-client">
                    <div class="section-toolbar">
                        <div class="content-head" style="margin-bottom:0;">
                            <h1>Fiche Client</h1>
                        </div>
                        <button type="button" class="btn-add" id="btnAddClient">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            Ajouter
                        </button>
                    </div>

                    <div class="table-wrap">
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
                                    <tr data-id="{{ $client['id'] }}">
                                        <td>{{ $client['date'] }}</td>
                                        <td>{{ $client['ref'] }}</td>
                                        <td>{{ $client['nom'] }}</td>
                                        <td>{{ $client['ville'] }}</td>
                                        <td>{{ $client['activite'] }}</td>
                                        <td>{{ $client['contact'] }}</td>
                                        <td class="solde-cell">{{ number_format($client['solde'], 2, ',', ' ') }}</td>
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
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-projet">
                    <div class="section-toolbar">
                        <div class="content-head" style="margin-bottom:0;">
                            <h1>Fiche Projet</h1>
                        </div>
                        <button type="button" class="btn-add" id="btnAddProjet">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            Nouveau Projet
                        </button>
                    </div>

                    <div class="table-wrap">
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
                                    @endphp
                                    <tr data-id="{{ $projet['id'] }}">
                                        <td>{{ $projet['date'] }}</td>
                                        <td>{{ $projet['ref'] }}</td>
                                        <td>{{ $projet['nom'] }}</td>
                                        <td>{{ $projet['designation'] }}</td>
                                        <td>{{ $projet['client'] }}</td>
                                        <td>{{ number_format($projet['budget'], 2, ',', ' ') }}</td>
                                        <td>{{ number_format($projet['montant_paye'], 2, ',', ' ') }}</td>
                                        <td class="solde-cell">{{ number_format($projet['solde'], 2, ',', ' ') }}</td>
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
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-fiche-paiement">
                    <div class="section-toolbar">
                        <div class="content-head" style="margin-bottom:0;">
                            <h1>Fiche Paiement</h1>
                        </div>
                        <button type="button" class="btn-add" id="btnAddPaiement">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            Nouveau Paiement
                        </button>
                    </div>

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

                    <div class="table-wrap">
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
                                        <td>{{ number_format($paiement['budget'], 2, ',', ' ') }}</td>
                                        <td>{{ number_format($paiement['montant_paye'], 2, ',', ' ') }}</td>
                                        <td>{{ $paiement['tresorerie'] }}</td>
                                        <td class="solde-cell">{{ number_format($paiement['solde'], 2, ',', ' ') }}</td>
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
            </main>
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
                        <label for="paiement_date">Date</label>
                        <input type="text" id="paiement_date" name="date" readonly>
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
                        <label for="paiement_budget">Budget (auto)</label>
                        <input type="text" id="paiement_budget_display" readonly>
                        <input type="hidden" id="paiement_budget" name="budget">
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
                        <label for="projet_date">Date</label>
                        <input type="text" id="projet_date" name="date" readonly>
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
                        <label for="client_date">Date</label>
                        <input type="text" id="client_date" name="date" readonly>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('menuToggle');
        const clientGroup = document.getElementById('clientGroup');
        const clientToggle = document.getElementById('clientToggle');
        const projetGroup = document.getElementById('projetGroup');
        const projetToggle = document.getElementById('projetToggle');
        const paiementGroup = document.getElementById('paiementGroup');
        const paiementToggle = document.getElementById('paiementToggle');
        const panels = document.querySelectorAll('.panel');
        const modal = document.getElementById('clientModal');
        const projetModal = document.getElementById('projetModal');
        const paiementModal = document.getElementById('paiementModal');
        const btnAdd = document.getElementById('btnAddClient');
        const btnAddProjet = document.getElementById('btnAddProjet');
        const btnAddPaiement = document.getElementById('btnAddPaiement');
        const closeModal = document.getElementById('closeClientModal');
        const cancelModal = document.getElementById('cancelClientModal');
        const closeProjetModal = document.getElementById('closeProjetModal');
        const cancelProjetModal = document.getElementById('cancelProjetModal');
        const closePaiementModal = document.getElementById('closePaiementModal');
        const cancelPaiementModal = document.getElementById('cancelPaiementModal');
        const projetsData = @json($projets ?? []);
        const clientsData = @json($clients ?? []);
        const paiementsData = @json($paiements ?? []);
        const chartProjetsData = @json($chartProjets ?? []);

        const statueLabels = {
            actif: 'EN COURS',
            attente: 'EN ATTENTE',
            annule: 'ANNULÉ',
        };

        function formatPdfValue(value) {
            if (typeof value === 'number') {
                return value.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

        toggle?.addEventListener('click', () => sidebar.classList.toggle('open'));

        clientToggle?.addEventListener('click', () => {
            clientGroup.classList.toggle('open');
        });

        projetToggle?.addEventListener('click', () => {
            projetGroup.classList.toggle('open');
        });

        paiementToggle?.addEventListener('click', () => {
            paiementGroup.classList.toggle('open');
        });

        function showPanel(name) {
            panels.forEach((panel) => panel.classList.toggle('active', panel.id === `panel-${name}`));
            document.querySelectorAll('.nav-item').forEach((el) => el.classList.remove('active'));
            document.querySelectorAll('.submenu-link').forEach((el) => el.classList.remove('active'));
            document.getElementById('dashboardNavBtn')?.classList.toggle('active', name === 'dashboard');

            if (name === 'fiche-client') {
                clientGroup.classList.add('open');
                clientToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-client"]')?.classList.add('active');
            } else if (name === 'fiche-projet') {
                projetGroup.classList.add('open');
                projetToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-projet"]')?.classList.add('active');
            } else if (name === 'fiche-paiement') {
                paiementGroup.classList.add('open');
                paiementToggle.classList.add('active');
                document.querySelector('[data-panel="fiche-paiement"]')?.classList.add('active');
            } else if (name === 'dashboard') {
                document.querySelector(`.nav-item[data-panel="dashboard"]`)?.classList.add('active');
            }

            if (window.innerWidth <= 960) sidebar.classList.remove('open');
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

            document.getElementById('client_date').value = todayFr();
            document.getElementById('client_ref').value = nextRef();
            document.getElementById('client_nom').value = '';
            document.getElementById('client_ville').value = '';
            document.getElementById('client_contact').value = '';
            document.getElementById('client_activite').value = '';

            setClientFormFields('create');

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.getElementById('client_nom').focus();
        }

        const clientFieldIds = ['client_nom', 'client_ville', 'client_contact', 'client_activite'];

        function setClientFormFields(mode) {
            clientFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (field) field.disabled = mode === 'view';
            });
        }

        function fillClientForm(client) {
            document.getElementById('client_date').value = client.date || '';
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

        function nextProjetRef() {
            const rows = document.querySelectorAll('#projetsTableBody tr[data-id]');
            const n = rows.length + 1;
            return 'PRJ-' + String(n).padStart(4, '0');
        }

        function updateProjetStatutColor() {
            const select = document.getElementById('projet_statut');
            if (!select) return;
            select.classList.remove('statue-actif', 'statue-attente', 'statue-annule');
            if (select.value) {
                select.classList.add(`statue-${select.value}`);
            }
        }

        function updateProjetSolde() {
            const budget = parseFloat(document.getElementById('projet_budget')?.value) || 0;
            const avance = parseFloat(document.getElementById('projet_avance')?.value) || 0;
            const solde = budget - avance;
            document.getElementById('projet_solde').value = solde.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const projetForm = document.getElementById('projetForm');
        const projetModalTitle = document.getElementById('projetModalTitle');
        const projetSubmitBtn = document.getElementById('projetSubmitBtn');
        const projetHttpMethod = document.getElementById('projet_http_method');
        const projetAvanceLabel = document.getElementById('projet_avance_label');
        const projetAvanceInput = document.getElementById('projet_avance');
        let projetFormMode = 'create';

        const projetFieldIds = [
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
            document.getElementById('projet_date').value = projet.date || '';
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

            document.getElementById('projet_date').value = todayFr();
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
            document.getElementById('projet_nom').focus();
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
            if (e.target.closest('.statue-form')) return;

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
            return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function applyPaiementClient() {
            const client = document.getElementById('paiement_client')?.value;
            const projet = projetsData.find((p) => p.client === client);
            const titreDisplay = document.getElementById('paiement_titre_display');
            const titre = document.getElementById('paiement_titre');
            const budgetHidden = document.getElementById('paiement_budget');
            const budgetDisplay = document.getElementById('paiement_budget_display');
            const projetIdHidden = document.getElementById('paiement_projet_id');

            if (!projet) {
                titreDisplay.value = '';
                titre.value = '';
                budgetHidden.value = '';
                budgetDisplay.value = '';
                projetIdHidden.value = '';
                updatePaiementSolde();
                return;
            }

            projetIdHidden.value = projet.id;
            titre.value = projet.nom;
            titreDisplay.value = projet.nom;
            budgetHidden.value = projet.budget;
            budgetDisplay.value = formatMontant(parseFloat(projet.budget) || 0);
            updatePaiementSolde();
        }

        function updatePaiementSolde() {
            const projetId = document.getElementById('paiement_projet_id')?.value;
            const projet = projetsData.find((p) => p.id === projetId);
            const montantSaisi = parseFloat(document.getElementById('paiement_montant_paye')?.value) || 0;
            const budget = parseFloat(projet?.budget) || 0;
            let paye = parseFloat(projet?.montant_paye) || 0;

            if (paiementFormMode === 'edit') {
                paye = Math.max(0, paye - paiementEditIncrement);
            }

            const solde = budget - paye - montantSaisi;
            document.getElementById('paiement_solde').value = formatMontant(solde);
        }

        const paiementForm = document.getElementById('paiementForm');
        const paiementModalTitle = document.getElementById('paiementModalTitle');
        const paiementSubmitBtn = document.getElementById('paiementSubmitBtn');
        const paiementHttpMethod = document.getElementById('paiement_http_method');
        let paiementFormMode = 'create';
        let paiementEditIncrement = 0;

        const paiementFieldIds = [
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
            document.getElementById('paiement_date').value = paiement.date || '';
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
            document.getElementById('paiement_budget_display').value = formatMontant(parseFloat(paiement.budget) || 0);
            document.getElementById('paiement_projet_id').value = paiement.projet_id || '';
            document.getElementById('paiement_montant_paye').value = paiement.increment_paye ?? '';
            document.getElementById('paiement_type_reg').value = paiement.type_reg || '';
            document.getElementById('paiement_bnq').value = paiement.bnq || '';
            document.getElementById('paiement_tresorerie').value = paiement.tresorerie || '';
            paiementEditIncrement = parseFloat(paiement.increment_paye) || 0;
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

            document.getElementById('paiement_date').value = todayFr();
            document.getElementById('paiement_ref').value = nextPaiementRef();
            document.getElementById('paiement_client').selectedIndex = 0;
            document.getElementById('paiement_titre_display').value = '';
            document.getElementById('paiement_titre').value = '';
            document.getElementById('paiement_budget').value = '';
            document.getElementById('paiement_budget_display').value = '';
            document.getElementById('paiement_montant_paye').value = '';
            document.getElementById('paiement_projet_id').value = '';
            document.getElementById('paiement_type_reg').selectedIndex = 0;
            document.getElementById('paiement_bnq').value = '';
            document.getElementById('paiement_tresorerie').value = '';
            document.getElementById('paiement_solde').value = '';

            setPaiementFormFields('create');

            paiementModal.classList.add('open');
            paiementModal.setAttribute('aria-hidden', 'false');
            document.getElementById('paiement_client').focus();
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

        let projetsChartInstance = null;

        function sortMoisKey(moisKey) {
            const parts = String(moisKey).split('/');
            if (parts.length < 2) return moisKey;
            return `${parts[1]}${parts[0].padStart(2, '0')}`;
        }

        function buildProjetsChartData(annee, mois) {
            const filtered = chartProjetsData.filter((item) => {
                const matchAnnee = !annee || item.annee === annee;
                const matchMois = !mois || item.mois === mois;
                return matchAnnee && matchMois;
            });

            const labels = [...new Set(filtered.map((item) => item.mois))]
                .sort((a, b) => sortMoisKey(a).localeCompare(sortMoisKey(b)));

            const countFor = (label, statut) =>
                filtered.filter((item) => item.mois === label && item.statut === statut).length;

            return {
                labels,
                datasets: [
                    {
                        label: 'EN COURS',
                        data: labels.map((label) => countFor(label, 'actif')),
                        backgroundColor: 'rgba(61, 207, 138, 0.75)',
                        borderColor: 'rgba(61, 207, 138, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'EN ATTENTE',
                        data: labels.map((label) => countFor(label, 'attente')),
                        backgroundColor: 'rgba(240, 180, 41, 0.75)',
                        borderColor: 'rgba(240, 180, 41, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'ANNULÉS',
                        data: labels.map((label) => countFor(label, 'annule')),
                        backgroundColor: 'rgba(240, 113, 120, 0.75)',
                        borderColor: 'rgba(240, 113, 120, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                ],
                hasData: filtered.length > 0,
            };
        }

        function updateProjetsChart() {
            const annee = document.getElementById('chart_filter_annee')?.value || '';
            const mois = document.getElementById('chart_filter_mois')?.value || '';
            const canvas = document.getElementById('projetsChart');
            const wrap = document.getElementById('projetsChartWrap');
            const empty = document.getElementById('projetsChartEmpty');

            if (!canvas || typeof Chart === 'undefined') return;

            const chartData = buildProjetsChartData(annee, mois);
            const showEmpty = !chartData.hasData;

            if (wrap) wrap.style.display = showEmpty ? 'none' : '';
            if (empty) empty.classList.toggle('visible', showEmpty);

            if (showEmpty) {
                if (projetsChartInstance) {
                    projetsChartInstance.destroy();
                    projetsChartInstance = null;
                }
                return;
            }

            if (projetsChartInstance) {
                projetsChartInstance.data.labels = chartData.labels;
                projetsChartInstance.data.datasets = chartData.datasets;
                projetsChartInstance.update();
                return;
            }

            projetsChartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#b8c9e0',
                                font: { family: 'Outfit, sans-serif', size: 11 },
                            },
                        },
                        tooltip: {
                            callbacks: {
                                title: (items) => `PÉRIODE : ${items[0]?.label || ''}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            stacked: false,
                            ticks: { color: '#8fa8c8', font: { size: 10 } },
                            grid: { color: 'rgba(110, 168, 255, 0.08)' },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#8fa8c8',
                                font: { size: 10 },
                                stepSize: 1,
                                precision: 0,
                            },
                            grid: { color: 'rgba(110, 168, 255, 0.08)' },
                        },
                    },
                },
            });
        }

        function syncChartMonthOptions() {
            const annee = document.getElementById('chart_filter_annee')?.value || '';
            const moisSelect = document.getElementById('chart_filter_mois');
            if (!moisSelect) return;

            const current = moisSelect.value;
            const months = [...new Set(
                chartProjetsData
                    .filter((item) => !annee || item.annee === annee)
                    .map((item) => item.mois)
            )].sort((a, b) => sortMoisKey(a).localeCompare(sortMoisKey(b)));

            moisSelect.innerHTML = '<option value="">TOUS LES MOIS</option>';
            months.forEach((month) => {
                const option = document.createElement('option');
                option.value = month;
                option.textContent = month;
                moisSelect.appendChild(option);
            });

            if (current && months.includes(current)) {
                moisSelect.value = current;
            }
        }

        document.getElementById('chart_filter_annee')?.addEventListener('change', () => {
            syncChartMonthOptions();
            updateProjetsChart();
        });
        document.getElementById('chart_filter_mois')?.addEventListener('change', updateProjetsChart);
        updateProjetsChart();

        @if (session('open_fiche_client'))
            showPanel('fiche-client');
        @endif

        @if (session('open_fiche_projet'))
            showPanel('fiche-projet');
        @endif

        @if (session('open_fiche_paiement'))
            showPanel('fiche-paiement');
        @endif
    </script>
</body>
</html>
