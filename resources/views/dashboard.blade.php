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
    <style>
        :root {
            --bg: #07111f;
            --sidebar: #091525;
            --panel: rgba(14, 28, 48, 0.88);
            --line: rgba(110, 168, 255, 0.18);
            --accent: #3b9eff;
            --accent-soft: #7ec4ff;
            --text: #f4f8ff;
            --muted: rgba(210, 224, 245, 0.68);
            --topbar-bg: rgba(9, 21, 37, 0.92);
            --input-bg: rgba(255, 255, 255, 0.04);
            --hover-bg: rgba(59, 158, 255, 0.1);
            --table-head: rgba(9, 21, 37, 0.95);
            --shadow: rgba(0, 0, 0, 0.35);
        }

        [data-theme="light"] {
            --bg: #edf3fb;
            --sidebar: #f7faff;
            --panel: rgba(255, 255, 255, 0.95);
            --line: rgba(59, 120, 200, 0.2);
            --accent: #1a7fd4;
            --accent-soft: #3b9eff;
            --text: #0f1f35;
            --muted: rgba(30, 50, 80, 0.62);
            --topbar-bg: rgba(255, 255, 255, 0.92);
            --input-bg: rgba(15, 31, 53, 0.04);
            --hover-bg: rgba(59, 158, 255, 0.12);
            --table-head: rgba(237, 243, 251, 0.98);
            --shadow: rgba(15, 31, 53, 0.08);
        }

        [data-theme="light"] .card-stat {
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.96), rgba(245, 250, 255, 0.92));
            box-shadow: 0 8px 24px var(--shadow);
        }

        [data-theme="light"] .table-wrap {
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 8px 28px var(--shadow);
        }

        [data-theme="light"] .panel .data-table th {
            background: var(--table-head);
        }

        [data-theme="light"] .panel .data-table tbody tr:hover {
            background: rgba(59, 158, 255, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            background: var(--bg);
            text-transform: uppercase;
        }

        input, textarea, select, button {
            text-transform: uppercase;
        }

        input::placeholder, textarea::placeholder {
            text-transform: uppercase;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
            transition: grid-template-columns 0.25s ease;
        }

        .shell.sidebar-hidden {
            grid-template-columns: 0 1fr;
        }

        .shell.sidebar-hidden .sidebar {
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            border-right-color: transparent;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--line);
            background: linear-gradient(180deg, color-mix(in srgb, var(--sidebar) 98%, transparent), color-mix(in srgb, var(--bg) 98%, transparent));
            transition: opacity 0.25s ease, border-color 0.25s ease;
        }

        .sidebar-brand {
            position: relative;
            padding: 1rem 0.75rem 0.9rem;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .sidebar-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 90% 70% at 50% 35%, rgba(59, 158, 255, 0.2), transparent 68%),
                radial-gradient(circle at 20% 80%, rgba(155, 123, 255, 0.08), transparent 45%);
            pointer-events: none;
        }

        .logo-stage {
            position: relative;
            width: 100%;
            max-width: 220px;
            padding: 0.7rem 0.55rem 0.65rem;
            border-radius: 18px;
            background:
                linear-gradient(155deg, rgba(18, 42, 78, 0.85) 0%, rgba(10, 24, 48, 0.92) 55%, rgba(14, 32, 62, 0.88) 100%);
            border: 1px solid rgba(126, 196, 255, 0.32);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.04) inset,
                0 0 28px rgba(59, 158, 255, 0.28),
                0 0 56px rgba(59, 158, 255, 0.12),
                0 10px 28px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            isolation: isolate;
            transition: box-shadow 0.35s ease, transform 0.35s ease, border-color 0.35s ease;
        }

        .logo-stage:hover {
            transform: translateY(-1px);
            border-color: rgba(158, 210, 255, 0.5);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.06) inset,
                0 0 36px rgba(94, 176, 255, 0.42),
                0 0 72px rgba(59, 158, 255, 0.2),
                0 14px 32px rgba(0, 0, 0, 0.38);
        }

        .logo-glow {
            position: absolute;
            inset: -25%;
            z-index: 0;
            background:
                radial-gradient(circle at 50% 28%, rgba(126, 196, 255, 0.55), transparent 52%),
                radial-gradient(circle at 72% 78%, rgba(155, 123, 255, 0.22), transparent 40%);
            filter: blur(8px);
            animation: logoPulse 4.5s ease-in-out infinite;
            pointer-events: none;
        }

        .logo-stage img {
            position: relative;
            z-index: 1;
            display: block;
            width: 100%;
            height: auto;
            margin: 0;
            padding: 0;
            object-fit: contain;
            filter:
                brightness(1.18)
                contrast(1.1)
                saturate(1.35)
                drop-shadow(0 0 10px rgba(126, 196, 255, 0.75))
                drop-shadow(0 0 22px rgba(59, 158, 255, 0.45))
                drop-shadow(0 4px 14px rgba(0, 0, 0, 0.35));
            transition: transform 0.35s ease, filter 0.35s ease;
        }

        .logo-stage:hover img {
            transform: scale(1.03);
            filter:
                brightness(1.28)
                contrast(1.12)
                saturate(1.45)
                drop-shadow(0 0 14px rgba(158, 220, 255, 0.9))
                drop-shadow(0 0 32px rgba(94, 176, 255, 0.6))
                drop-shadow(0 6px 18px rgba(0, 0, 0, 0.4));
        }

        .logo-shine {
            position: absolute;
            top: -60%;
            left: -70%;
            z-index: 2;
            width: 42%;
            height: 220%;
            background: linear-gradient(
                105deg,
                transparent 0%,
                rgba(255, 255, 255, 0.03) 35%,
                rgba(255, 255, 255, 0.28) 50%,
                rgba(255, 255, 255, 0.03) 65%,
                transparent 100%
            );
            transform: rotate(22deg);
            animation: logoShine 6s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes logoPulse {
            0%, 100% { opacity: 0.72; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.06); }
        }

        @keyframes logoShine {
            0%, 72%, 100% { left: -70%; opacity: 0; }
            38% { opacity: 1; }
            58% { left: 130%; opacity: 0.85; }
        }

        .nav-list {
            flex: 1;
            padding: 0.85rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            width: 100%;
            padding: 0.78rem 0.9rem;
            border: none;
            border-radius: 12px;
            background: transparent;
            color: var(--muted);
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .nav-item-text { flex: 1; min-width: 0; }

        .nav-chevron {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.55;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .nav-group.expanded > .nav-item .nav-chevron {
            transform: rotate(180deg);
            opacity: 0.85;
        }

        .nav-item:hover {
            background: rgba(59, 158, 255, 0.1);
            color: var(--text);
        }

        .nav-item.active {
            background: rgba(59, 158, 255, 0.16);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(110, 168, 255, 0.22);
        }

        .nav-group {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .nav-sublist {
            display: none;
            flex-direction: column;
            gap: 0.2rem;
            padding: 0 0 0.15rem 0.35rem;
        }

        .nav-group.expanded .nav-sublist { display: flex; }

        .nav-subitem {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            width: 100%;
            padding: 0.62rem 0.85rem 0.62rem 1.1rem;
            border: none;
            border-radius: 10px;
            background: transparent;
            color: var(--muted);
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-subitem:hover {
            background: rgba(59, 158, 255, 0.08);
            color: var(--text);
        }

        .nav-subitem.active {
            background: rgba(59, 158, 255, 0.14);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(110, 168, 255, 0.2);
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border: 1px solid transparent;
        }

        .nav-icon svg { width: 16px; height: 16px; }

        .nav-subicon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border: 1px solid transparent;
        }

        .nav-subicon svg { width: 13px; height: 13px; }

        .nav-subicon.relance { background: rgba(240, 180, 41, 0.12); color: #ffc857; border-color: rgba(240, 180, 41, 0.22); }
        .nav-subicon.commercial { background: rgba(61, 207, 138, 0.12); color: #7ee8b0; border-color: rgba(61, 207, 138, 0.22); }
        .nav-subicon.utilisateur { background: rgba(126, 196, 255, 0.12); color: #9ad4ff; border-color: rgba(126, 196, 255, 0.22); }
        .nav-subicon.fiche-ste { background: rgba(155, 123, 255, 0.12); color: #c4b0ff; border-color: rgba(155, 123, 255, 0.22); }

        .nav-icon.dashboard { background: rgba(59, 158, 255, 0.14); color: #9ad4ff; border-color: rgba(126, 196, 255, 0.25); }
        .nav-icon.prospection { background: rgba(240, 180, 41, 0.14); color: #ffc857; border-color: rgba(240, 180, 41, 0.25); }
        .nav-icon.client { background: rgba(126, 196, 255, 0.14); color: #9ad4ff; border-color: rgba(126, 196, 255, 0.25); }
        .nav-icon.projet { background: rgba(155, 123, 255, 0.14); color: #c4b0ff; border-color: rgba(155, 123, 255, 0.25); }
        .nav-icon.paiement { background: rgba(61, 207, 138, 0.14); color: #7ee8b0; border-color: rgba(61, 207, 138, 0.25); }
        .nav-icon.charge { background: rgba(240, 113, 120, 0.14); color: #ff9aa0; border-color: rgba(240, 113, 120, 0.25); }
        .nav-icon.config { background: rgba(77, 212, 234, 0.14); color: #7ee8f5; border-color: rgba(77, 212, 234, 0.25); }

        .sidebar-foot {
            padding: 0.85rem 0.75rem 1rem;
            border-top: 1px solid var(--line);
        }

        .btn-logout {
            width: 100%;
            appearance: none;
            border: 1px solid rgba(240, 113, 120, 0.35);
            background: rgba(240, 113, 120, 0.12);
            color: #ffb3b8;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .main {
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.75rem;
            border-bottom: 1px solid var(--line);
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
        }

        .topbar-left,
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-shrink: 0;
        }

        .commercial-presence-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            flex: 1;
            min-width: 0;
            padding: 0 0.5rem;
        }

        .presence-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.28rem 0.65rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--input-bg);
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .presence-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .presence-dot.unknown {
            background: #8a96a8;
        }

        .presence-dot.online {
            background: #3dcf8a;
            animation: presencePulseGreen 1.1s ease-in-out infinite;
        }

        .presence-dot.offline {
            background: #f07178;
            box-shadow: 0 0 6px rgba(240, 113, 120, 0.45);
        }

        @keyframes presencePulseGreen {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(61, 207, 138, 0.65);
                opacity: 1;
            }
            50% {
                box-shadow: 0 0 0 5px rgba(61, 207, 138, 0);
                opacity: 0.82;
            }
        }

        .topbar-btn {
            display: inline-grid;
            place-items: center;
            appearance: none;
            border: 1px solid var(--line);
            background: var(--input-bg);
            color: var(--text);
            border-radius: 10px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .topbar-btn:hover {
            background: var(--hover-bg);
            border-color: color-mix(in srgb, var(--accent) 35%, var(--line));
        }

        .topbar-btn svg {
            width: 18px;
            height: 18px;
        }

        .topbar-btn .icon-hide { display: none; }
        .shell.sidebar-hidden .topbar-btn[data-toggle="sidebar"] .icon-show { display: none; }
        .shell.sidebar-hidden .topbar-btn[data-toggle="sidebar"] .icon-hide { display: block; }

        .topbar-btn[data-toggle="theme"] .icon-light { display: none; }
        [data-theme="light"] .topbar-btn[data-toggle="theme"] .icon-dark { display: none; }
        [data-theme="light"] .topbar-btn[data-toggle="theme"] .icon-light { display: block; }

        .welcome {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .welcome small {
            color: var(--muted);
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .welcome strong {
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .user-badge {
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--input-bg);
            font-size: 0.82rem;
            color: var(--muted);
            text-transform: uppercase;
        }

        .content {
            flex: 1;
            padding: 1.75rem;
        }

        .panel { display: none; animation: fadeIn 0.3s ease both; }
        .panel.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-head {
            margin-bottom: 1.25rem;
        }

        .content-head h1 {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .content-head p {
            margin-top: 0.35rem;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .card-stat {
            padding: 1rem 1.05rem;
            min-height: 96px;
            border-radius: 14px;
            border: 1px solid rgba(110, 168, 255, 0.2);
            background: linear-gradient(165deg, rgba(16, 32, 54, 0.92), rgba(10, 22, 40, 0.88));
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-stat small {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 500;
        }

        .card-stat strong {
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .card-stat.confirme strong { color: #7ee8b0; }
        .card-stat.attente strong { color: #ffc857; }
        .card-stat.annule strong { color: #ff9aa0; }
        .card-stat.charges strong { color: #c4b0ff; }
        .card-stat.nombre-projet {
            border-color: rgba(61, 207, 138, 0.28);
            background: linear-gradient(165deg, rgba(16, 42, 34, 0.92), rgba(10, 28, 22, 0.88));
        }
        .card-stat.nombre-projet strong { color: #7ee8b0; }
        .card-stat.total-budgets {
            border-color: rgba(255, 159, 67, 0.28);
            background: linear-gradient(165deg, rgba(42, 30, 16, 0.92), rgba(28, 20, 10, 0.88));
        }
        .card-stat.total-budgets strong { color: #ffb366; }

        .client-cards {
            grid-template-columns: repeat(2, minmax(130px, 160px));
            gap: 0.65rem;
            margin-bottom: 0;
        }

        .client-cards .card-stat {
            min-height: 78px;
            padding: 0.75rem 0.85rem;
        }

        .client-cards .card-stat strong {
            font-size: 1.25rem;
        }

        .client-head-row {
            display: flex;
            align-items: stretch;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        #panel-client .client-search-bar {
            flex: 1 1 420px;
            min-width: min(100%, 320px);
            margin-bottom: 0;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .placeholder {
            margin-top: 1.5rem;
            padding: 2rem;
            border-radius: 16px;
            border: 1px dashed rgba(110, 168, 255, 0.22);
            background: rgba(10, 20, 36, 0.55);
            color: var(--muted);
            text-align: center;
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
            grid-template-columns: repeat(5, minmax(0, 1fr));
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
            min-width: 0;
        }

        .search-field label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--accent-soft);
            letter-spacing: 0.04em;
            text-transform: uppercase;
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

        .table-wrap {
            overflow: auto;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(10, 20, 36, 0.72);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        /* Tous les onglets : en-têtes et données centrés */
        .panel .data-table th,
        .panel .data-table td {
            padding: 0.72rem 0.8rem;
            text-align: center !important;
            vertical-align: middle;
            border-bottom: 1px solid rgba(110, 168, 255, 0.1);
            font-size: 0.84rem;
        }

        .panel .data-table th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: rgba(14, 30, 54, 0.98);
            color: var(--accent-soft);
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .panel .data-table tbody tr:hover {
            background: rgba(59, 158, 255, 0.06);
        }

        .panel .data-table td.cell-wrap {
            white-space: normal;
            max-width: 220px;
            word-break: break-word;
        }

        .panel .data-table .empty {
            color: var(--muted);
            padding: 2rem 1rem;
            text-align: center !important;
        }

        .panel .data-table td .statue-form {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .statue-select {
            appearance: none;
            padding: 0.32rem 1.35rem 0.32rem 0.65rem;
            border-radius: 999px;
            font-size: 0.72rem;
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
            border: 1px solid transparent;
        }

        .statue-select.valide {
            color: #7ee8b0;
            background-color: rgba(61, 207, 138, 0.16);
            border-color: rgba(61, 207, 138, 0.4);
        }

        .statue-select.en_attente {
            color: #ffc857;
            background-color: rgba(240, 180, 41, 0.16);
            border-color: rgba(240, 180, 41, 0.4);
        }

        .statue-select.annule {
            color: #ff9aa0;
            background-color: rgba(240, 113, 120, 0.16);
            border-color: rgba(240, 113, 120, 0.4);
        }

        .statue-select.reporte {
            color: #c4b0ff;
            background-color: rgba(155, 123, 255, 0.16);
            border-color: rgba(155, 123, 255, 0.4);
        }

        #panel-prospection .table-wrap {
            max-height: calc(100vh - 17rem);
        }

        #panel-prospection .data-table th:nth-child(7),
        #panel-prospection .data-table td.cell-remarque {
            width: 18%;
            min-width: 160px;
            max-width: none;
        }

        .remarque-input {
            width: 100%;
            min-height: 2.6rem;
            padding: 0;
            margin: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            color: var(--text);
            font-family: inherit;
            font-size: 0.84rem;
            line-height: 1.45;
            resize: vertical;
            text-align: center;
            box-sizing: border-box;
            box-shadow: none;
            outline: none;
        }

        .remarque-input:focus {
            outline: none;
            border: none;
            box-shadow: none;
        }

        .remarque-input.is-saving { opacity: 0.65; }

        .prospection-date-input {
            width: 100%;
            max-width: 6.5rem;
            padding: 0;
            margin: 0 auto;
            border: none;
            background: transparent;
            color: var(--text);
            font-family: inherit;
            font-size: 0.84rem;
            text-align: center;
            outline: none;
            box-shadow: none;
        }

        .prospection-date-input.is-saving { opacity: 0.65; }

        .prospection-text-input {
            width: 100%;
            min-width: 88px;
            border: 1px solid transparent;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text);
            font-family: inherit;
            font-size: 0.82rem;
            padding: 0.45rem 0.55rem;
            transition: border-color 0.2s ease, background 0.2s ease, opacity 0.2s ease;
        }

        .prospection-text-input:hover,
        .prospection-text-input:focus {
            outline: none;
            border-color: rgba(110, 168, 255, 0.35);
            background: rgba(59, 158, 255, 0.08);
        }

        .prospection-text-input.is-saving { opacity: 0.65; }

        .commercial-relance-table .cell-remarque-preview {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #prospectionsTableBody tr.row-prospection-valide td {
            background: rgba(61, 207, 138, 0.14);
            color: rgba(210, 245, 224, 0.88);
        }

        #prospectionsTableBody tr.row-prospection-valide {
            opacity: 0.82;
        }

        body.role-commercial #prospectionsTableBody tr.row-prospection-valide td {
            background: rgba(110, 118, 130, 0.22) !important;
            color: rgba(165, 172, 182, 0.88) !important;
        }

        body.role-commercial #prospectionsTableBody tr.row-prospection-valide {
            opacity: 0.58;
            filter: grayscale(0.9);
        }

        body.role-commercial #prospectionsTableBody tr.row-prospection-valide .prospection-text-input,
        body.role-commercial #prospectionsTableBody tr.row-prospection-valide .remarque-input,
        body.role-commercial #prospectionsTableBody tr.row-prospection-valide .prospection-date-input {
            color: rgba(150, 158, 168, 0.9);
            background: rgba(110, 118, 130, 0.12);
            border-color: rgba(110, 118, 130, 0.2);
        }

        body.role-commercial #prospectionsTableBody tr.row-prospection-valide .statue-select.valide {
            color: #9aa3b0;
            background-color: rgba(110, 118, 130, 0.2);
            border-color: rgba(110, 118, 130, 0.35);
        }

        [data-theme="light"] body.role-commercial #prospectionsTableBody tr.row-prospection-valide td {
            background: rgba(120, 128, 140, 0.18) !important;
            color: rgba(90, 98, 110, 0.82) !important;
        }

        #prospectionsTableBody tr.row-prospection-annule td {
            background: rgba(240, 113, 120, 0.16);
            color: rgba(255, 210, 214, 0.82);
        }

        #prospectionsTableBody tr.row-prospection-annule {
            opacity: 0.62;
            filter: grayscale(0.25);
        }

        #prospectionsTableBody tr.row-prospection-reporte td {
            background: rgba(155, 123, 255, 0.2);
            color: rgba(228, 214, 255, 0.95);
        }

        #prospectionsTableBody tr.row-prospection-rappel-du .prospection-date-input,
        #prospectionsTableBody tr.row-prospection-rappel-du td.cell-rappel {
            background: rgba(255, 200, 87, 0.28) !important;
            color: #ffe8a3 !important;
            box-shadow: inset 0 0 0 1px rgba(255, 200, 87, 0.45);
            border-radius: 6px;
        }

        #prospectionsTableBody tr.row-prospection-rappel-du .prospection-date-input {
            font-weight: 600;
        }

        .toolbar-actions { display: flex; gap: 0.55rem; align-items: center; flex-wrap: wrap; }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(126, 196, 255, 0.35);
            background: linear-gradient(135deg, rgba(59, 158, 255, 0.22), rgba(59, 158, 255, 0.08));
            color: var(--text);
            font-family: inherit;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-add svg { width: 16px; height: 16px; }

        .actions {
            display: flex;
            justify-content: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted);
            cursor: pointer;
            padding: 0;
            font: inherit;
        }

        .action-btn svg { width: 14px; height: 14px; pointer-events: none; }

        .action-btn.voir:hover { color: #7ec4ff; border-color: rgba(126, 196, 255, 0.35); background: rgba(59, 158, 255, 0.12); }
        .action-btn.modifier:hover { color: #ffc857; border-color: rgba(240, 180, 41, 0.35); background: rgba(240, 180, 41, 0.12); }
        .action-btn.supprimer:hover { color: #ff9aa0; border-color: rgba(240, 113, 120, 0.35); background: rgba(240, 113, 120, 0.12); }
        .action-btn.imprimer:hover { color: #ff8f7a; border-color: rgba(255, 120, 90, 0.35); background: rgba(255, 120, 90, 0.12); }
        .action-btn.suspendre:hover { color: #c9a0ff; border-color: rgba(155, 123, 255, 0.35); background: rgba(155, 123, 255, 0.12); }

        .config-section { display: none; }
        .config-section.active { display: block; }

        .config-form-panel {
            max-width: 560px;
            padding: 1.1rem 1.15rem 1rem;
            border-radius: 14px;
            border: 1px solid rgba(126, 196, 255, 0.22);
            background: rgba(255, 255, 255, 0.03);
        }

        .config-form-foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.55rem;
            margin-top: 0.35rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--line);
        }

        .prospection-view { display: none; }
        .prospection-view.active { display: block; }

        .commercial-picker {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.65rem;
            margin-bottom: 1rem;
        }

        .commercial-pick-btn {
            appearance: none;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text);
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-family: inherit;
            font-size: 0.86rem;
            font-weight: 600;
            cursor: pointer;
            text-align: left;
        }

        .commercial-pick-btn:hover,
        .commercial-pick-btn.active {
            border-color: rgba(61, 207, 138, 0.35);
            background: rgba(61, 207, 138, 0.12);
        }

        #commercialImportStatus {
            margin: 0.75rem 0 0;
            font-size: 0.82rem;
            color: var(--muted);
        }

        #commercialImportStatus.is-error { color: #ffb3b8; }
        #commercialImportStatus.is-success { color: #9ef0c4; }

        .user-statue-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.22rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: rgba(255, 255, 255, 0.04);
        }

        .user-statue-badge.administrateur { color: #7ec4ff; border-color: rgba(126, 196, 255, 0.35); }
        .user-statue-badge.assistante { color: #ffc857; border-color: rgba(240, 180, 41, 0.35); }
        .user-statue-badge.commercial { color: #3dcf8a; border-color: rgba(61, 207, 138, 0.35); }

        #utilisateursTableBody tr.is-suspended td {
            opacity: 0.55;
            background: rgba(240, 113, 120, 0.08);
        }

        .side-panel-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 110;
            background: rgba(3, 8, 18, 0.55);
            backdrop-filter: blur(2px);
        }

        .side-panel-backdrop.open { display: block; }

        .side-panel {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 111;
            width: min(420px, 100%);
            height: 100%;
            transform: translateX(100%);
            transition: transform 0.28s ease;
            border-left: 1px solid rgba(126, 196, 255, 0.25);
            background: linear-gradient(180deg, rgba(14, 28, 48, 0.98), rgba(9, 18, 34, 0.98));
            box-shadow: -20px 0 50px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
        }

        .side-panel-backdrop.open .side-panel { transform: translateX(0); }

        .side-panel.side-panel-wide {
            width: min(540px, 100%);
        }

        .side-panel-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }

        .side-panel-form-grid .field.span-2 {
            grid-column: 1 / -1;
        }

        @media (max-width: 560px) {
            .side-panel-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .projet-lookup-hint {
            margin-top: 0.35rem;
            font-size: 0.72rem;
            color: var(--muted);
        }

        .projet-lookup-hint.ok { color: #3dcf8a; }
        .projet-lookup-hint.warn { color: #f0b86e; }

        .projet-solde-readonly {
            font-weight: 700;
            color: var(--accent-soft);
        }

        .side-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--line);
        }

        .side-panel-head h2 { font-size: 1.02rem; font-weight: 600; }

        .side-panel-body {
            flex: 1;
            overflow: auto;
            padding: 1rem 1.1rem;
        }

        .side-panel-foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.55rem;
            padding: 0.85rem 1.1rem 1rem;
            border-top: 1px solid var(--line);
        }

        .field select {
            width: 100%;
            padding: 0.62rem 0.72rem;
            border-radius: 10px;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            font-family: inherit;
            font-size: 0.88rem;
        }

        .btn-close-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: transparent;
            color: var(--muted);
            font-family: inherit;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .commercial-picker { grid-template-columns: 1fr; }
        }

        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(3, 8, 18, 0.72);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-backdrop.open { display: flex; }

        .modal {
            width: min(520px, 100%);
            max-height: 90vh;
            overflow: auto;
            border-radius: 16px;
            border: 1px solid rgba(126, 196, 255, 0.25);
            background: linear-gradient(180deg, rgba(14, 28, 48, 0.98), rgba(9, 18, 34, 0.98));
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--line);
        }

        .modal-head h2 { font-size: 1.05rem; font-weight: 600; }

        .modal-close {
            appearance: none;
            border: none;
            background: transparent;
            color: var(--muted);
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
        }

        .modal-body { padding: 1rem 1.1rem; }

        .modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.55rem;
            padding: 0.85rem 1.1rem 1rem;
            border-top: 1px solid var(--line);
        }

        .field { display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 0.85rem; }

        .field label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .field input, .field textarea {
            width: 100%;
            padding: 0.62rem 0.72rem;
            border-radius: 10px;
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            font-family: inherit;
            font-size: 0.88rem;
        }

        .field input:read-only { opacity: 0.85; }

        .btn-primary, .btn-ghost {
            padding: 0.58rem 0.95rem;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary {
            border: 1px solid rgba(126, 196, 255, 0.35);
            background: linear-gradient(135deg, rgba(59, 158, 255, 0.35), rgba(59, 158, 255, 0.15));
            color: var(--text);
        }

        .btn-ghost {
            border: 1px solid rgba(110, 168, 255, 0.18);
            background: transparent;
            color: var(--muted);
        }

        .print-sheet { color: #111; background: #fff; padding: 1.25rem; border-radius: 8px; }
        .print-sheet h3 { margin-bottom: 0.75rem; }
        .print-sheet dl { display: grid; grid-template-columns: 140px 1fr; gap: 0.45rem 0.75rem; }
        .print-sheet dt { font-weight: 600; }
        .print-sheet dd { margin: 0; }

        @media print {
            body * { visibility: hidden; }
            #clientPrintArea, #clientPrintArea * { visibility: visible; }
            #clientPrintArea { position: absolute; inset: 0; padding: 1rem; background: #fff; color: #111; }
        }

        @media (max-width: 1100px) {
            .search-bar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 560px) {
            .search-bar { grid-template-columns: 1fr; }
        }

        .menu-toggle {
            display: none;
        }

        @media (max-width: 900px) {
            .shell.sidebar-hidden {
                grid-template-columns: 1fr;
            }

            .shell.sidebar-hidden .sidebar {
                transform: translateX(-105%);
            }
        }

        @media (max-width: 1100px) {
            .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 40;
                width: min(280px, 86vw);
                transform: translateX(-105%);
                transition: transform 0.25s ease;
            }
            .sidebar.open { transform: translateX(0); opacity: 1; pointer-events: auto; }
            .topbar-btn[data-toggle="sidebar"] { display: inline-grid; }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 35;
                background: rgba(4, 10, 20, 0.55);
            }
            .sidebar-backdrop.open { display: block; }
        }

        @media (max-width: 560px) {
            .cards { grid-template-columns: 1fr; }
            .content { padding: 1.25rem; }
            .topbar { padding: 0.85rem 1rem; }
        }
        @media (min-width: 901px) {
            .topbar-btn[data-toggle="sidebar"] { display: inline-grid; }
        }
    </style>
    <script>
        (function () {
            const theme = localStorage.getItem('evopro-theme');
            if (theme) document.documentElement.setAttribute('data-theme', theme);
            const sidebarHidden = localStorage.getItem('evopro-sidebar') === 'hidden';
            if (sidebarHidden) document.documentElement.classList.add('sidebar-hidden-init');
        })();
    </script>
</head>
<body @class(['role-commercial' => $isCommercialRole ?? false])>
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div class="shell" id="appShell">
        <aside class="sidebar" id="sidebar" aria-label="Navigation principale">
            <div class="sidebar-brand">
                <div class="logo-stage">
                    <div class="logo-glow" aria-hidden="true"></div>
                    <div class="logo-shine" aria-hidden="true"></div>
                    <img src="{{ asset('images/evopro-logo.png') }}" alt="EvoPro — Prospection. Convertir. Gagner." width="210" height="205">
                </div>
            </div>

            <nav class="nav-list">
                @if ($isAdministrateur ?? false)
                <button type="button" class="nav-item {{ ($defaultPanel ?? 'dashboard') === 'dashboard' ? 'active' : '' }}" data-panel="dashboard">
                    <span class="nav-icon dashboard" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                    </span>
                    Tableau de Bord
                </button>
                @endif

                @if ($isCommercialRole ?? false)
                <button type="button" class="nav-item {{ ($defaultPanel ?? '') === 'prospection' ? 'active' : '' }}" data-panel="prospection">
                    <span class="nav-icon prospection" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    <span class="nav-item-text">Relance</span>
                </button>
                @else
                <div class="nav-group {{ ($defaultPanel ?? '') === 'prospection' ? 'expanded' : '' }}" data-nav-group="prospection">
                    <button type="button" class="nav-item has-sublist {{ ($defaultPanel ?? '') === 'prospection' ? 'active' : '' }}" data-panel="prospection" data-toggle-group="prospection">
                        <span class="nav-icon prospection" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </span>
                        <span class="nav-item-text">Prospection</span>
                        <span class="nav-chevron" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </span>
                    </button>
                    <div class="nav-sublist" aria-label="Sous-menu prospection">
                        <button type="button" class="nav-subitem {{ ($defaultPanel ?? '') === 'prospection' ? 'active' : '' }}" data-panel="prospection" data-prospection="liste">
                            <span class="nav-subicon relance" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            Relance
                        </button>
                        @if ($canManageProspectionCommercial ?? false)
                            <button type="button" class="nav-subitem" data-panel="prospection" data-prospection="commercial">
                                <span class="nav-subicon commercial" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>
                                </span>
                                Commercial
                            </button>
                        @endif
                    </div>
                </div>
                @endif

                @if ($isAdministrateur ?? false)
                <button type="button" class="nav-item" data-panel="client">
                    <span class="nav-icon client" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </span>
                    Client
                </button>
                <button type="button" class="nav-item" data-panel="projet">
                    <span class="nav-icon projet" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18"/><path d="M3 12h18"/><path d="M8 7v10"/><path d="M16 7v10"/></svg>
                    </span>
                    Projet
                </button>
                <button type="button" class="nav-item" data-panel="paiement">
                    <span class="nav-icon paiement" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    </span>
                    Paiement
                </button>
                <button type="button" class="nav-item" data-panel="charge">
                    <span class="nav-icon charge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                    </span>
                    Charge
                </button>
                <div class="nav-group" data-nav-group="configuration">
                    <button type="button" class="nav-item has-sublist" data-panel="configuration" data-toggle-group="configuration">
                        <span class="nav-icon config" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9c.3.6.9 1 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                        </span>
                        <span class="nav-item-text">Configuration</span>
                        <span class="nav-chevron" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </span>
                    </button>
                    <div class="nav-sublist" aria-label="Sous-menu configuration">
                        <button type="button" class="nav-subitem" data-panel="configuration" data-config="utilisateur">
                            <span class="nav-subicon utilisateur" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </span>
                            Utilisateur
                        </button>
                        <button type="button" class="nav-subitem" data-panel="configuration" data-config="fiche-ste">
                            <span class="nav-subicon fiche-ste" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 12v.01"/><path d="M9 15v.01"/><path d="M9 18v.01"/></svg>
                            </span>
                            Fiche Ste
                        </button>
                    </div>
                </div>
                @endif
            </nav>

            <div class="sidebar-foot">
                <form method="post" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="submit" class="btn-logout">Se déconnecter</button>
                </form>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="topbar-btn" id="sidebarToggle" data-toggle="sidebar" aria-label="Afficher ou masquer le menu latéral" title="Menu latéral">
                        <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/><path d="M14 9h5"/><path d="M14 15h5"/>
                        </svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/><path d="M16 9l3 3-3 3"/>
                        </svg>
                    </button>
                    <div class="welcome">
                        <strong>Bienvenu {{ $welcomeName }}</strong>
                    </div>
                </div>

                @if ($canViewCommercialPresence ?? false)
                <div class="commercial-presence-bar" id="commercialPresenceBar" aria-label="Présence des commerciaux">
                    @foreach (($commerciauxPresenceUsers ?? []) as $commercialUser)
                        <span
                            class="presence-pill"
                            data-commercial-key="{{ mb_strtolower($commercialUser['id'] ?? '') }}"
                            title="{{ $commercialUser['nom_complet'] ?? '' }} — Hors ligne"
                        >
                            <span class="presence-dot offline" aria-hidden="true"></span>
                            <span class="presence-label">{{ $commercialUser['nom_complet'] ?? '' }}</span>
                        </span>
                    @endforeach
                </div>
                @endif

                <div class="topbar-actions">
                    <button type="button" class="topbar-btn" id="themeToggle" data-toggle="theme" aria-label="Basculer mode clair ou sombre" title="Mode clair / sombre">
                        <svg class="icon-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                        </svg>
                        <svg class="icon-light" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                        </svg>
                    </button>
                    <div class="user-badge">{{ strtoupper($authUserStatue ?: '—') }}</div>
                </div>
            </header>

            <main class="content">
                <section class="panel {{ ($defaultPanel ?? 'dashboard') === 'dashboard' ? 'active' : '' }}" id="panel-dashboard">
                    <div class="content-head">
                        <h1>Tableau de Bord</h1>
                        <p>Vue d’ensemble des projets et charges.</p>
                    </div>

                    <div class="cards" aria-label="Statistiques">
                        <article class="card-stat confirme">
                            <small>Projets Confirmé</small>
                            <strong>{{ $dashboardCounts['confirme'] ?? 0 }}</strong>
                        </article>
                        <article class="card-stat attente">
                            <small>Projets En Attente</small>
                            <strong>{{ $dashboardCounts['attente'] ?? 0 }}</strong>
                        </article>
                        <article class="card-stat annule">
                            <small>Projet Annulés</small>
                            <strong>{{ $dashboardCounts['annule'] ?? 0 }}</strong>
                        </article>
                        <article class="card-stat charges">
                            <small>Total Charges</small>
                            <strong>{{ number_format($totalCharges ?? 0, 2, '.', ' ') }}</strong>
                        </article>
                    </div>
                </section>

                <section class="panel {{ ($defaultPanel ?? 'dashboard') === 'prospection' ? 'active' : '' }}" id="panel-prospection">
                    <div class="content-head">
                        <h1>{{ ($isCommercialRole ?? false) || ($isAssistante ?? false) ? 'Tableau de relance' : 'Prospection' }}</h1>
                        <p>
                            @if ($isCommercialRole ?? false)
                                Complétez les numéros importés pour vous (nom, ville, projet, remarque, statue, rappel).
                            @elseif (($isAdministrateur ?? false) || ($isAssistante ?? false))
                                Vue en direct de toutes les relances remplies par les commerciaux.
                            @else
                                Suivi des prospects et des contacts commerciaux.
                            @endif
                        </p>
                    </div>

                    <div class="prospection-view active" id="prospection-liste">
                    @php $relanceColspan = ($isCommercialRole ?? false) ? 8 : 9; @endphp

                    <div class="search-bar" aria-label="Recherche prospection" style="grid-template-columns: repeat({{ ($isCommercialRole ?? false) ? 5 : 6 }}, minmax(0, 1fr));">
                        <div class="search-field">
                            <label for="filter_prospection_num">Num</label>
                            <input type="text" id="filter_prospection_num" placeholder="Ex. 06…" maxlength="20" autocomplete="off" inputmode="tel">
                        </div>
                        <div class="search-field">
                            <label for="filter_prospection_mois">Mois</label>
                            <select id="filter_prospection_mois">
                                <option value="">TOUS LES MOIS</option>
                                @php
                                    $moisProspections = collect($prospections ?? [])
                                        ->map(function ($row) {
                                            $parts = explode('/', $row['date'] ?? '');
                                            return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                        })
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach ($moisProspections as $mois)
                                    <option value="{{ $mois }}">{{ $mois }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="search-field">
                            <label for="filter_prospection_de">De</label>
                            <input type="text" id="filter_prospection_de" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                        </div>
                        <div class="search-field">
                            <label for="filter_prospection_a">A</label>
                            <input type="text" id="filter_prospection_a" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                        </div>
                        @if (! ($isCommercialRole ?? false))
                        <div class="search-field">
                            <label for="filter_prospection_commercial">Commercial</label>
                            <select id="filter_prospection_commercial">
                                <option value="">TOUS LES COMMERCIAUX</option>
                                @foreach (($commerciaux ?? []) as $commercial)
                                    <option value="{{ mb_strtolower($commercial) }}">{{ $commercial }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="search-field">
                            <label for="filter_prospection_statue">Statue</label>
                            <select id="filter_prospection_statue">
                                <option value="">TOUTES LES STATUES</option>
                                <option value="valide">Validé</option>
                                <option value="en_attente">En Attente</option>
                                <option value="annule">Annulé</option>
                                <option value="reporte">Reporté</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    @if (! ($isCommercialRole ?? false))
                                        <th>Commercial</th>
                                    @endif
                                    <th>Numéro Téléphone</th>
                                    <th>Nom Prospect</th>
                                    <th>Ville</th>
                                    <th>Titre Projet</th>
                                    <th>Remarque</th>
                                    <th>Statue</th>
                                    <th>Date Rappel</th>
                                </tr>
                            </thead>
                            <tbody id="prospectionsTableBody">
                                @forelse (($prospections ?? []) as $row)
                                    @php
                                        $statue = $row['statue'] ?? 'en_attente';
                                        $parts = explode('/', $row['date'] ?? '');
                                        $mois = count($parts) >= 3 ? $parts[1].'/'.$parts[2] : '';
                                        $dateRappel = trim((string) ($row['date_rappel'] ?? ''));
                                        $rappelDu = \App\Support\ContactsArchive::isDateRappelDue($dateRappel);
                                        $rowClasses = collect([
                                            match ($statue) {
                                                'valide' => 'row-prospection-valide',
                                                'annule' => 'row-prospection-annule',
                                                'reporte' => 'row-prospection-reporte',
                                                default => '',
                                            },
                                            $rappelDu && in_array($statue, ['en_attente', 'reporte'], true) ? 'row-prospection-rappel-du' : '',
                                        ])->filter()->implode(' ');
                                    @endphp
                                    <tr
                                        data-id="{{ $row['id'] }}"
                                        data-mois="{{ $mois }}"
                                        data-date="{{ $row['date'] ?? '' }}"
                                        data-commercial="{{ mb_strtolower(trim((string) ($row['commercial'] ?? ''))) }}"
                                        data-statue="{{ $statue }}"
                                        data-telephone="{{ preg_replace('/\D+/', '', (string) ($row['telephone'] ?? '')) }}"
                                        data-date-rappel="{{ $dateRappel }}"
                                        @if ($rowClasses !== '') class="{{ $rowClasses }}" @endif
                                    >
                                        <td>{{ $row['date'] ?? '' }}</td>
                                        @if (! ($isCommercialRole ?? false))
                                            <td>{{ $row['commercial'] ?? '' }}</td>
                                        @endif
                                        <td>{{ $row['telephone'] ?? '' }}</td>
                                        <td>
                                            <input
                                                type="text"
                                                class="prospection-text-input prospection-inline"
                                                data-field="nom_prospect"
                                                data-id="{{ $row['id'] }}"
                                                value="{{ $row['nom_prospect'] ?? '' }}"
                                                maxlength="255"
                                                placeholder="Nom prospect"
                                                aria-label="Nom prospect"
                                            >
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="prospection-text-input prospection-inline"
                                                data-field="ville"
                                                data-id="{{ $row['id'] }}"
                                                value="{{ $row['ville'] ?? '' }}"
                                                maxlength="255"
                                                placeholder="Ville"
                                                aria-label="Ville"
                                            >
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="prospection-text-input prospection-inline"
                                                data-field="projet"
                                                data-id="{{ $row['id'] }}"
                                                value="{{ $row['projet'] ?? '' }}"
                                                maxlength="255"
                                                placeholder="Titre projet"
                                                aria-label="Titre projet"
                                            >
                                        </td>
                                        <td class="cell-remarque">
                                            <textarea
                                                class="remarque-input prospection-inline"
                                                data-field="remarque"
                                                data-id="{{ $row['id'] }}"
                                                rows="2"
                                                placeholder="Note d'appel avec le client…"
                                                aria-label="Remarque"
                                            >{{ $row['remarque'] ?? '' }}</textarea>
                                        </td>
                                        <td>
                                            <select
                                                class="statue-select {{ $statue }}"
                                                data-id="{{ $row['id'] }}"
                                                aria-label="Statue prospection"
                                            >
                                                <option value="valide" @selected($statue === 'valide')>Validé</option>
                                                <option value="en_attente" @selected($statue === 'en_attente')>En Attente</option>
                                                <option value="annule" @selected($statue === 'annule')>Annulé</option>
                                                <option value="reporte" @selected($statue === 'reporte')>Reporté</option>
                                            </select>
                                        </td>
                                        <td class="cell-rappel">
                                            <input
                                                type="text"
                                                class="prospection-date-input prospection-inline{{ $rappelDu && in_array($statue, ['en_attente', 'reporte'], true) ? ' is-rappel-du' : '' }}"
                                                data-field="date_rappel"
                                                data-id="{{ $row['id'] }}"
                                                value="{{ $row['date_rappel'] ?? '' }}"
                                                placeholder="JJ/MM/AAAA"
                                                maxlength="10"
                                                inputmode="numeric"
                                                autocomplete="off"
                                                aria-label="Date Rappel"
                                            >
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="{{ $relanceColspan }}" class="empty">
                                            {{ ($isCommercialRole ?? false) ? 'Aucun numéro importé pour vous pour le moment.' : 'Aucune prospection enregistrée.' }}
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="empty-row" id="prospectionsNoResult" style="display:none;">
                                    <td colspan="{{ $relanceColspan }}" class="empty">Aucun résultat pour cette recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>

                    @if ($canManageProspectionCommercial ?? false)
                    <div class="prospection-view" id="prospection-commercial">
                        <div class="section-toolbar" style="margin-bottom:1rem;">
                            <div class="content-head" style="margin-bottom:0;">
                                <h2 style="font-size:1.05rem;">Commercial</h2>
                                <p>Gestion des numéros par commercial.</p>
                            </div>
                            <div class="toolbar-actions">
                                <button type="button" class="btn-add" id="btnCommercialAjouter">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    Ajouter
                                </button>
                                <button type="button" class="btn-add" id="btnCommercialImporter">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                                    Importer
                                </button>
                                <button type="button" class="btn-close-toolbar" id="btnCommercialFermer">Fermer</button>
                            </div>
                        </div>

                        <div class="search-bar" aria-label="Recherche commercial" style="grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom:1rem;">
                            <div class="search-field">
                                <label for="filter_commercial_mois">Mois</label>
                                <select id="filter_commercial_mois">
                                    <option value="">TOUS LES MOIS</option>
                                    @php
                                        $moisCommercial = collect($prospectionsAll ?? $prospections ?? [])
                                            ->filter(fn ($row) => trim((string) ($row['commercial'] ?? '')) !== '')
                                            ->map(function ($row) {
                                                $parts = explode('/', $row['date'] ?? '');
                                                return count($parts) >= 3 ? $parts[1].'/'.$parts[2] : null;
                                            })
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($moisCommercial as $mois)
                                        <option value="{{ $mois }}">{{ $mois }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="filter_commercial_de">De</label>
                                <input type="text" id="filter_commercial_de" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                            </div>
                            <div class="search-field">
                                <label for="filter_commercial_a">A</label>
                                <input type="text" id="filter_commercial_a" placeholder="JJ/MM/AAAA" maxlength="10" autocomplete="off">
                            </div>
                            <div class="search-field">
                                <label for="filter_commercial_commercial">Commercial</label>
                                <select id="filter_commercial_commercial">
                                    <option value="">TOUS LES COMMERCIAUX</option>
                                    @foreach (($commerciauxUsers ?? []) as $commercialUser)
                                        <option value="{{ mb_strtolower($commercialUser) }}">{{ $commercialUser }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-wrap">
                            <table class="data-table commercial-relance-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Num</th>
                                        <th>Commercial</th>
                                        <th>Nom Prospect</th>
                                        <th>Ville</th>
                                        <th>Titre Projet</th>
                                        <th>Remarque</th>
                                        <th>Statue</th>
                                        <th>Date Rappel</th>
                                    </tr>
                                </thead>
                                <tbody id="commercialNumerosBody">
                                    <tr class="empty-row" id="commercialNumerosEmpty">
                                        <td colspan="9" class="empty">Aucun numéro commercial.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p id="commercialImportStatus" aria-live="polite"></p>
                    </div>
                    @endif
                </section>

                <section class="panel" id="panel-client">
                    <div class="section-toolbar">
                        <div class="content-head" style="margin-bottom:0;">
                            <h1>Fiche Clients</h1>
                            <p>Fiches clients et projets associés.</p>
                        </div>
                        <div class="toolbar-actions">
                            <button type="button" class="btn-add" id="btnAddClient">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Ajouter
                            </button>
                        </div>
                    </div>

                    <div class="client-head-row">
                        <div class="cards client-cards" aria-label="Statistiques clients">
                            <article class="card-stat nombre-projet">
                                <small>Nombre Projet</small>
                                <strong>{{ $clientStats['nombre_projets'] ?? 0 }}</strong>
                            </article>
                            <article class="card-stat total-budgets">
                                <small>Total Budgets</small>
                                <strong>{{ number_format($clientStats['total_budgets'] ?? 0, 2, '.', ' ') }}</strong>
                            </article>
                        </div>

                        <div class="search-bar client-search-bar" aria-label="Recherche clients">
                            <div class="search-field">
                                <label for="filter_client_mois">Mois</label>
                                <select id="filter_client_mois">
                                    <option value="">TOUS LES MOIS</option>
                                    @php
                                        $moisClients = collect($clients ?? [])
                                            ->map(function ($row) {
                                                $parts = explode('/', $row['date'] ?? '');
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
                            <div class="search-field">
                                <label for="filter_client_nom">Nom Client</label>
                                <input type="text" id="filter_client_nom" placeholder="Rechercher un client…" maxlength="80" autocomplete="off">
                            </div>
                            <div class="search-field">
                                <label for="filter_client_num">Num</label>
                                <input type="text" id="filter_client_num" placeholder="Ex. 06…" maxlength="20" autocomplete="off" inputmode="tel">
                            </div>
                            <div class="search-field">
                                <label for="filter_client_titre">Titre Projet</label>
                                <input type="text" id="filter_client_titre" placeholder="Rechercher un projet…" maxlength="80" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Nom Client</th>
                                    <th>Numéro</th>
                                    <th>Ville</th>
                                    <th>Titre Projet</th>
                                    <th>Délai travail</th>
                                    <th>Budget</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="clientsTableBody">
                                @forelse (($clients ?? []) as $client)
                                    @php
                                        $partsClient = explode('/', $client['date'] ?? '');
                                        $moisClient = count($partsClient) >= 3 ? $partsClient[1].'/'.$partsClient[2] : '';
                                        $titreProjet = trim((string) ($client['titre_projet'] ?? $client['activite'] ?? ''));
                                    @endphp
                                    <tr
                                        data-id="{{ $client['id'] }}"
                                        data-mois="{{ $moisClient }}"
                                        data-nom="{{ mb_strtolower(trim((string) ($client['nom'] ?? ''))) }}"
                                        data-titre="{{ mb_strtolower($titreProjet) }}"
                                        data-numero="{{ preg_replace('/\D+/', '', (string) ($client['contact'] ?? '')) }}"
                                    >
                                        <td>{{ $client['date'] ?? '' }}</td>
                                        <td>{{ $client['nom'] ?? '' }}</td>
                                        <td>{{ $client['contact'] ?? '' }}</td>
                                        <td>{{ $client['ville'] ?? '' }}</td>
                                        <td>{{ $titreProjet }}</td>
                                        <td>{{ \App\Support\ContactsArchive::formatDelaiTravail($client['delai_travail'] ?? '') }}</td>
                                        <td>{{ number_format((float) ($client['budget'] ?? 0), 2, '.', ' ') }}</td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <button type="button" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                </button>
                                                <button type="button" class="action-btn imprimer" title="Imprimer" aria-label="Imprimer">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="empty">Aucun client enregistré.</td>
                                    </tr>
                                @endforelse
                                <tr class="empty-row" id="clientsNoResult" style="display:none;">
                                    <td colspan="8" class="empty">Aucun résultat pour cette recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-projet">
                    <div class="section-toolbar">
                        <div class="content-head" style="margin-bottom:0;">
                            <h1>Projet</h1>
                            <p>Suivi des projets confirmés et paiements.</p>
                        </div>
                        <div class="toolbar-actions">
                            <button type="button" class="btn-add" id="btnAddProjet">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Ajouter
                            </button>
                            <button type="button" class="btn-close-toolbar" id="btnCloseProjetPanel">Fermer</button>
                        </div>
                    </div>

                    <div class="search-bar" aria-label="Recherche projets" style="margin-bottom:1rem;">
                        <div class="search-field">
                            <label for="filter_projet_mois">Mois</label>
                            <select id="filter_projet_mois">
                                <option value="">TOUS LES MOIS</option>
                                @php
                                    $moisProjets = collect($projets ?? [])
                                        ->map(function ($row) {
                                            $parts = explode('/', $row['date'] ?? '');
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
                            <label for="filter_projet_commercial">Commercial</label>
                            <select id="filter_projet_commercial">
                                <option value="">TOUS LES COMMERCIAUX</option>
                                @foreach (($commerciaux ?? []) as $commercial)
                                    <option value="{{ mb_strtolower($commercial) }}">{{ $commercial }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Réf</th>
                                    <th>Commercial</th>
                                    <th>Titre Projet</th>
                                    <th>Nom Client</th>
                                    <th>Ville</th>
                                    <th>Contact</th>
                                    <th>Budget</th>
                                    <th>Avance</th>
                                    <th>Mode</th>
                                    <th>Solde</th>
                                    <th>Part Commercial</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="projetsTableBody">
                                @forelse (($projets ?? []) as $projet)
                                    @php
                                        $partsProjet = explode('/', $projet['date'] ?? '');
                                        $moisProjet = count($partsProjet) >= 3 ? $partsProjet[1].'/'.$partsProjet[2] : '';
                                    @endphp
                                    <tr
                                        data-id="{{ $projet['id'] }}"
                                        data-mois="{{ $moisProjet }}"
                                        data-commercial="{{ mb_strtolower(trim((string) ($projet['commercial'] ?? ''))) }}"
                                    >
                                        <td>{{ $projet['date'] ?? '' }}</td>
                                        <td>{{ $projet['ref'] ?? '' }}</td>
                                        <td>{{ $projet['commercial'] ?? '' }}</td>
                                        <td>{{ $projet['titre_projet'] ?? '' }}</td>
                                        <td>{{ $projet['nom_client'] ?? '' }}</td>
                                        <td>{{ $projet['ville'] ?? '' }}</td>
                                        <td>{{ $projet['contact'] ?? '' }}</td>
                                        <td>{{ number_format((float) ($projet['budget'] ?? 0), 2, '.', ' ') }}</td>
                                        <td>{{ number_format((float) ($projet['avance'] ?? 0), 2, '.', ' ') }}</td>
                                        <td>{{ $projet['mode'] ?? 'Vir' }}</td>
                                        <td>{{ number_format((float) ($projet['solde'] ?? 0), 2, '.', ' ') }}</td>
                                        <td>{{ (int) ($projet['part_commercial'] ?? 10) }}%</td>
                                        <td>
                                            <div class="actions">
                                                <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                                <button type="button" class="action-btn imprimer" title="Imprimer" aria-label="Imprimer">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                                                </button>
                                                <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                </button>
                                                <button type="button" class="action-btn supprimer" title="Supprimer" aria-label="Supprimer">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="13" class="empty">Aucun projet enregistré.</td>
                                    </tr>
                                @endforelse
                                <tr class="empty-row" id="projetsNoResult" style="display:none;">
                                    <td colspan="13" class="empty">Aucun résultat pour cette recherche.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel" id="panel-paiement">
                    <div class="content-head">
                        <h1>Paiement</h1>
                        <p>Module en cours de construction.</p>
                    </div>
                    <div class="placeholder">Contenu Paiement à venir.</div>
                </section>

                <section class="panel" id="panel-charge">
                    <div class="content-head">
                        <h1>Charge</h1>
                        <p>Module en cours de construction.</p>
                    </div>
                    <div class="placeholder">Contenu Charge à venir.</div>
                </section>

                <section class="panel" id="panel-configuration">
                    <div class="content-head">
                        <h1>Configuration</h1>
                        <p>Paramètres, utilisateurs et fiche société.</p>
                    </div>

                    <div class="config-content">
                        <section class="config-section active" id="config-utilisateur">
                                <div class="section-toolbar" style="margin-bottom:1rem;">
                                    <div class="content-head" style="margin-bottom:0;">
                                        <h1 style="font-size:1.15rem;">Utilisateur</h1>
                                        <p>Comptes, accès et rôles.</p>
                                    </div>
                                    <div class="toolbar-actions">
                                        <button type="button" class="btn-add" id="btnAddUtilisateur">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                            Ajouter
                                        </button>
                                        <button type="button" class="btn-close-toolbar" id="btnCloseUtilisateurPanel">Fermer</button>
                                    </div>
                                </div>

                                @if ($errors->has('utilisateur_login') || $errors->has('utilisateur_suspend'))
                                    <div style="margin-bottom:0.85rem;padding:0.75rem 0.9rem;border-radius:10px;border:1px solid rgba(240,113,120,0.35);background:rgba(240,113,120,0.12);color:#ffd2d6;font-size:0.84rem;">
                                        {{ $errors->first('utilisateur_login') ?: $errors->first('utilisateur_suspend') }}
                                    </div>
                                @endif

                                <div class="table-wrap">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>ID</th>
                                                <th>Nom Complet</th>
                                                <th>Ville</th>
                                                <th>Statue</th>
                                                <th>Login</th>
                                                <th>Mot de Passe</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="utilisateursTableBody">
                                            @forelse (($utilisateurs ?? []) as $utilisateur)
                                                @php
                                                    $userStatue = \App\Support\UtilisateurHelper::normalizeStatue($utilisateur['statue'] ?? '');
                                                @endphp
                                                <tr
                                                    data-id="{{ $utilisateur['id'] }}"
                                                    @if ($utilisateur['suspendu'] ?? false) class="is-suspended" @endif
                                                >
                                                    <td>{{ $utilisateur['date'] ?? '' }}</td>
                                                    <td>{{ $utilisateur['ref'] ?? $utilisateur['id'] }}</td>
                                                    <td>{{ $utilisateur['nom_complet'] ?? '' }}</td>
                                                    <td>{{ $utilisateur['ville'] ?? '' }}</td>
                                                    <td>
                                                        <span class="user-statue-badge {{ $userStatue }}">
                                                            {{ \App\Support\UtilisateurHelper::statueLabel($userStatue) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $utilisateur['login'] ?? '' }}</td>
                                                    <td>{{ $utilisateur['password'] ?? '' }}</td>
                                                    <td>
                                                        <div class="actions">
                                                            <button type="button" class="action-btn voir" title="Voir" aria-label="Voir">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                            </button>
                                                            <button type="button" class="action-btn modifier" title="Modifier" aria-label="Modifier">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="action-btn suspendre"
                                                                title="{{ ($utilisateur['suspendu'] ?? false) ? 'Réactiver' : 'Suspendre' }}"
                                                                aria-label="{{ ($utilisateur['suspendu'] ?? false) ? 'Réactiver' : 'Suspendre' }}"
                                                                data-suspended="{{ ($utilisateur['suspendu'] ?? false) ? '1' : '0' }}"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M10 15V9"/><path d="M14 15V9"/></svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="empty-row">
                                                    <td colspan="8" class="empty">Aucun utilisateur enregistré.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <section class="config-section" id="config-fiche-ste">
                                <div class="content-head" style="margin-bottom:1rem;">
                                    <h1 style="font-size:1.15rem;">Fiche Ste</h1>
                                    <p>Informations de la société.</p>
                                </div>

                                <div class="config-form-panel">
                                    <form method="post" action="{{ url('/configuration/fiche-ste') }}" id="ficheSteForm" novalidate>
                                        @csrf
                                        @method('PUT')
                                        <div class="field">
                                            <label for="fiche_ste_nom_societe">Nom Société</label>
                                            <input type="text" id="fiche_ste_nom_societe" name="nom_societe" maxlength="255" value="{{ $ficheSte['nom_societe'] ?? '' }}">
                                        </div>
                                        <div class="field">
                                            <label for="fiche_ste_nom_gerant">Nom Gérant</label>
                                            <input type="text" id="fiche_ste_nom_gerant" name="nom_gerant" maxlength="255" value="{{ $ficheSte['nom_gerant'] ?? '' }}">
                                        </div>
                                        <div class="field">
                                            <label for="fiche_ste_contact">Contact</label>
                                            <input type="text" id="fiche_ste_contact" name="contact" maxlength="255" value="{{ $ficheSte['contact'] ?? '' }}" inputmode="tel" autocomplete="off">
                                        </div>
                                        <div class="field">
                                            <label for="fiche_ste_ville">Ville</label>
                                            <input type="text" id="fiche_ste_ville" name="ville" maxlength="255" value="{{ $ficheSte['ville'] ?? '' }}">
                                        </div>
                                        <div class="field">
                                            <label for="fiche_ste_whatsapp">WhatsApp</label>
                                            <input type="text" id="fiche_ste_whatsapp" name="whatsapp" maxlength="255" value="{{ $ficheSte['whatsapp'] ?? '' }}" inputmode="tel" autocomplete="off">
                                        </div>
                                        <div class="field">
                                            <label for="fiche_ste_email">E-mail</label>
                                            <input type="text" id="fiche_ste_email" name="email" maxlength="255" value="{{ $ficheSte['email'] ?? '' }}" autocomplete="off">
                                        </div>
                                        <div class="config-form-foot">
                                            <button type="button" class="btn-primary" id="btnFicheSteFermer">Fermer</button>
                                        </div>
                                    </form>
                                </div>
                            </section>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <div class="modal-backdrop" id="commercialNumeroModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="commercialNumeroModalTitle">
            <div class="modal-head">
                <h2 id="commercialNumeroModalTitle">Ajouter un numéro</h2>
                <button type="button" class="modal-close" id="closeCommercialNumeroModal" aria-label="Fermer">×</button>
            </div>
            <form id="commercialNumeroForm">
                <div class="modal-body">
                    <div class="field">
                        <label for="commercial_numero_commercial">Commercial</label>
                        <select id="commercial_numero_commercial" name="commercial" required>
                            <option value="">Choisir un commercial…</option>
                            @foreach (($commerciauxUsers ?? []) as $commercialUser)
                                <option value="{{ $commercialUser }}">{{ $commercialUser }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="commercial_numero_date">Date</label>
                        <input type="text" id="commercial_numero_date" name="date" placeholder="JJ/MM/AAAA" maxlength="10" inputmode="numeric" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <label for="commercial_numero_telephone">Numéro</label>
                        <input type="text" id="commercial_numero_telephone" name="telephone" maxlength="255" placeholder="Ex. 06…" inputmode="tel" autocomplete="off" required>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelCommercialNumeroModal">Annuler</button>
                    <button type="submit" class="btn-primary">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="commercialImportModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="commercialImportModalTitle">
            <div class="modal-head">
                <h2 id="commercialImportModalTitle">Importer des numéros</h2>
                <button type="button" class="modal-close" id="closeCommercialImportModal" aria-label="Fermer">×</button>
            </div>
            <form id="commercialImportForm">
                <div class="modal-body">
                    <div class="field">
                        <label for="commercial_import_commercial">Commercial</label>
                        <select id="commercial_import_commercial" name="commercial" required>
                            <option value="">Choisir un commercial…</option>
                            @foreach (($commerciauxUsers ?? []) as $commercialUser)
                                <option value="{{ $commercialUser }}">{{ $commercialUser }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="commercialImportFile">Capture / Image</label>
                        <input type="file" id="commercialImportFile" name="image" accept="image/*" required>
                    </div>
                    <p id="commercialImportModalStatus" aria-live="polite" style="font-size:0.82rem;color:var(--muted);margin:0;"></p>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" id="cancelCommercialImportModal">Annuler</button>
                    <button type="submit" class="btn-primary" id="submitCommercialImport">Importer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="side-panel-backdrop" id="utilisateurSidePanel" aria-hidden="true">
        <div class="side-panel" role="dialog" aria-modal="true" aria-labelledby="utilisateurSidePanelTitle">
            <div class="side-panel-head">
                <h2 id="utilisateurSidePanelTitle">Ajouter un utilisateur</h2>
                <button type="button" class="modal-close" id="closeUtilisateurSidePanel" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/utilisateurs') }}" id="utilisateurForm">
                @csrf
                <div class="side-panel-body">
                    <div class="field">
                        <label for="utilisateur_date">Date</label>
                        <input type="text" id="utilisateur_date" name="date" placeholder="JJ/MM/AAAA" maxlength="10" inputmode="numeric" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <label for="utilisateur_nom_complet">Nom Complet</label>
                        <input type="text" id="utilisateur_nom_complet" name="nom_complet" maxlength="255" required>
                    </div>
                    <div class="field">
                        <label for="utilisateur_ville">Ville</label>
                        <input type="text" id="utilisateur_ville" name="ville" maxlength="255">
                    </div>
                    <div class="field">
                        <label for="utilisateur_statue">Statue</label>
                        <select id="utilisateur_statue" name="statue" required>
                            <option value="administrateur">Administrateur</option>
                            <option value="assistante">Assistante</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="utilisateur_login">Login</label>
                        <input type="text" id="utilisateur_login" name="login" maxlength="255" required autocomplete="off">
                    </div>
                    <div class="field">
                        <label for="utilisateur_password">Mot de Passe</label>
                        <input type="text" id="utilisateur_password" name="password" maxlength="255" required autocomplete="off">
                    </div>
                </div>
                <div class="side-panel-foot" id="utilisateurFormActions">
                    <button type="button" class="btn-ghost" id="cancelUtilisateurSidePanel">Fermer</button>
                    <button type="submit" class="btn-primary" id="saveUtilisateurSidePanel">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="side-panel-backdrop" id="projetSidePanel" aria-hidden="true">
        <div class="side-panel side-panel-wide" role="dialog" aria-modal="true" aria-labelledby="projetSidePanelTitle">
            <div class="side-panel-head">
                <h2 id="projetSidePanelTitle">Ajouter un projet</h2>
                <button type="button" class="modal-close" id="closeProjetSidePanel" aria-label="Fermer">×</button>
            </div>
            <form method="post" action="{{ url('/projets') }}" id="projetForm">
                @csrf
                <input type="hidden" name="prospection_id" id="projet_prospection_id" value="">
                <div class="side-panel-body">
                    <div class="side-panel-form-grid">
                        <div class="field">
                            <label for="projet_date">Date</label>
                            <input type="text" id="projet_date" name="date" placeholder="JJ/MM/AAAA" maxlength="10" inputmode="numeric" autocomplete="off" required>
                        </div>
                        <div class="field">
                            <label for="projet_ref">Réf</label>
                            <input type="text" id="projet_ref" readonly tabindex="-1" aria-readonly="true">
                        </div>
                        <div class="field span-2">
                            <label for="projet_commercial">Commercial</label>
                            <select id="projet_commercial" name="commercial" required>
                                <option value="">Choisir…</option>
                                @foreach (($commerciauxUsers ?? []) as $commercialUser)
                                    <option value="{{ $commercialUser }}">{{ $commercialUser }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field span-2">
                            <label for="projet_contact">N° contact</label>
                            <input type="text" id="projet_contact" name="contact" maxlength="255" placeholder="Ex. 06…" inputmode="tel" autocomplete="off">
                            <p class="projet-lookup-hint" id="projetLookupHint">Saisissez un numéro pour importer les données de prospection.</p>
                        </div>
                        <div class="field span-2">
                            <label for="projet_nom_client">Nom Client</label>
                            <input type="text" id="projet_nom_client" name="nom_client" maxlength="255" required>
                        </div>
                        <div class="field">
                            <label for="projet_ville">Ville</label>
                            <input type="text" id="projet_ville" name="ville" maxlength="255">
                        </div>
                        <div class="field">
                            <label for="projet_titre_projet">Titre Projet</label>
                            <input type="text" id="projet_titre_projet" name="titre_projet" maxlength="255" required>
                        </div>
                        <div class="field">
                            <label for="projet_budget">Budget</label>
                            <input type="number" id="projet_budget" name="budget" min="0" step="0.01" value="0">
                        </div>
                        <div class="field">
                            <label for="projet_avance">Avance</label>
                            <input type="number" id="projet_avance" name="avance" min="0" step="0.01" value="0">
                        </div>
                        <div class="field">
                            <label for="projet_mode">Mode</label>
                            <select id="projet_mode" name="mode" required>
                                <option value="Vir">Vir</option>
                                <option value="Esp">Esp</option>
                                <option value="Chq">Chq</option>
                                <option value="Vers">Vers</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="projet_part_commercial">Part Commercial</label>
                            <select id="projet_part_commercial" name="part_commercial" required>
                                <option value="10">10%</option>
                                <option value="15">15%</option>
                                <option value="20">20%</option>
                                <option value="30">30%</option>
                                <option value="50">50%</option>
                            </select>
                        </div>
                        <div class="field span-2">
                            <label>Solde</label>
                            <div class="projet-solde-readonly" id="projet_solde_display">0.00</div>
                        </div>
                    </div>
                </div>
                <div class="side-panel-foot" id="projetFormActions">
                    <button type="button" class="btn-ghost" id="cancelProjetSidePanel">Fermer</button>
                    <button type="submit" class="btn-primary" id="saveProjetSidePanel">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="projetPrintModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="projetPrintTitle">
            <div class="modal-head">
                <h2 id="projetPrintTitle">Fiche projet</h2>
                <button type="button" class="modal-close" id="closeProjetPrintModal" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                <div class="print-sheet" id="projetPrintArea"></div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" id="closeProjetPrintBtn">Fermer</button>
                <button type="button" class="btn-primary" id="printProjetBtn">Imprimer</button>
            </div>
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
                <div class="modal-body">
                    <div class="field">
                        <label for="client_date">Date</label>
                        <input type="text" id="client_date" name="date" placeholder="JJ/MM/AAAA" maxlength="10" inputmode="numeric" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <label for="client_nom">Nom Client</label>
                        <input type="text" id="client_nom" name="nom" maxlength="255" required>
                    </div>
                    <div class="field">
                        <label for="client_ville">Ville</label>
                        <input type="text" id="client_ville" name="ville" maxlength="255">
                    </div>
                    <div class="field">
                        <label for="client_contact">Numéro</label>
                        <input type="text" id="client_contact" name="contact" maxlength="255" placeholder="Ex. 06…" inputmode="tel" autocomplete="off">
                    </div>
                    <div class="field">
                        <label for="client_titre_projet">Titre Projet</label>
                        <input type="text" id="client_titre_projet" name="titre_projet" maxlength="255" required>
                    </div>
                    <div class="field">
                        <label for="client_delai_travail">Délai travail</label>
                        <input type="text" id="client_delai_travail" name="delai_travail" maxlength="255" placeholder="Ex. 30 JRS">
                    </div>
                    <div class="field">
                        <label for="client_budget">Budget</label>
                        <input type="number" id="client_budget" name="budget" min="0" step="0.01" value="0">
                    </div>
                </div>
                <div class="modal-foot" id="clientFormActions">
                    <button type="button" class="btn-ghost" id="cancelClientModal">Annuler</button>
                    <button type="submit" class="btn-primary" id="saveClientModal">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="clientPrintModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="clientPrintTitle">
            <div class="modal-head">
                <h2 id="clientPrintTitle">Fiche client</h2>
                <button type="button" class="modal-close" id="closeClientPrintModal" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                <div class="print-sheet" id="clientPrintArea"></div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" id="closeClientPrintBtn">Fermer</button>
                <button type="button" class="btn-primary" id="printClientBtn">Imprimer</button>
            </div>
        </div>
    </div>

    @if ($canManageProspectionCommercial ?? false)
        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    @endif
    <script>
        const clientsData = @json($clients ?? []);
        const projetsData = @json($projets ?? []);
        const prospectionsAllData = @json($prospectionsAll ?? $prospections ?? []);
        const canManageProspectionCommercial = @json($canManageProspectionCommercial ?? false);
        const defaultPanel = @json($defaultPanel ?? 'dashboard');
        const isCommercialRole = @json($isCommercialRole ?? false);
        const canViewCommercialPresence = @json($canViewCommercialPresence ?? false);
        const liveSyncIntervalMs = canViewCommercialPresence ? 2000 : 3000;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const utilisateursData = @json($utilisateurs ?? []);
        const ficheSteData = @json($ficheSte ?? []);
        const sidebar = document.getElementById('sidebar');
        const appShell = document.getElementById('appShell');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const themeToggle = document.getElementById('themeToggle');
        const navParents = document.querySelectorAll('.nav-item[data-panel]:not(.nav-subitem)');
        const navSubitems = document.querySelectorAll('.nav-subitem');
        const navGroups = document.querySelectorAll('.nav-group');
        const panels = document.querySelectorAll('.panel');

        function collapseAllNavGroups(except = null) {
            navGroups.forEach((group) => {
                if (group !== except) {
                    group.classList.remove('expanded');
                }
            });
        }

        function expandNavGroup(name, forceOpen = true) {
            navGroups.forEach((group) => {
                if (group.dataset.navGroup === name) {
                    group.classList.toggle('expanded', forceOpen);
                } else if (forceOpen) {
                    group.classList.remove('expanded');
                }
            });
        }

        function setActiveNavSubitem(type, value) {
            navSubitems.forEach((item) => {
                const match = type === 'config'
                    ? item.dataset.config === value
                    : item.dataset.prospection === value;
                item.classList.toggle('active', match);
            });
        }

        function showPanel(name, { expandGroup = true } = {}) {
            panels.forEach((panel) => panel.classList.toggle('active', panel.id === `panel-${name}`));
            navParents.forEach((item) => item.classList.toggle('active', item.dataset.panel === name));

            const group = document.querySelector(`.nav-group[data-nav-group="${name}"]`);
            if (group && expandGroup) {
                expandNavGroup(name, true);
            } else if (!group) {
                collapseAllNavGroups();
            }

            sidebar?.classList.remove('open');
            sidebarBackdrop?.classList.remove('open');
        }

        navParents.forEach((item) => {
            item.addEventListener('click', () => {
                const panel = item.dataset.panel;
                const group = item.closest('.nav-group');

                if (group && item.classList.contains('has-sublist')) {
                    const isExpanded = group.classList.contains('expanded');
                    const isActive = item.classList.contains('active');

                    if (isExpanded && isActive) {
                        group.classList.remove('expanded');
                        return;
                    }

                    showPanel(panel);
                    if (panel === 'configuration') {
                        showConfigSection('utilisateur');
                    }
                    if (panel === 'prospection') {
                        showProspectionView('liste');
                    }
                    return;
                }

                showPanel(panel);
                if (panel === 'prospection') {
                    showProspectionView('liste');
                }
            });
        });

        navSubitems.forEach((item) => {
            item.addEventListener('click', () => {
                const panel = item.dataset.panel;
                const group = item.closest('.nav-group');
                if (group) {
                    group.classList.add('expanded');
                    collapseAllNavGroups(group);
                }

                showPanel(panel);
                if (item.dataset.config) {
                    showConfigSection(item.dataset.config);
                }
                if (item.dataset.prospection) {
                    showProspectionView(item.dataset.prospection);
                }
            });
        });

        function isMobileSidebar() {
            return window.matchMedia('(max-width: 900px)').matches;
        }

        function setSidebarHidden(hidden) {
            appShell?.classList.toggle('sidebar-hidden', hidden);
            if (!isMobileSidebar()) {
                localStorage.setItem('evopro-sidebar', hidden ? 'hidden' : 'visible');
            }
            sidebarToggle?.setAttribute('aria-label', hidden ? 'Afficher le menu latéral' : 'Masquer le menu latéral');
        }

        if (document.documentElement.classList.contains('sidebar-hidden-init') && !isMobileSidebar()) {
            setSidebarHidden(true);
            document.documentElement.classList.remove('sidebar-hidden-init');
        }

        sidebarToggle?.addEventListener('click', () => {
            if (isMobileSidebar()) {
                sidebar?.classList.toggle('open');
                sidebarBackdrop?.classList.toggle('open');
                return;
            }
            setSidebarHidden(!appShell?.classList.contains('sidebar-hidden'));
        });

        sidebarBackdrop?.addEventListener('click', () => {
            sidebar?.classList.remove('open');
            sidebarBackdrop?.classList.remove('open');
        });

        function applyTheme(theme) {
            if (theme === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            } else {
                document.documentElement.removeAttribute('data-theme');
                theme = 'dark';
            }
            localStorage.setItem('evopro-theme', theme);
            themeToggle?.setAttribute('aria-label', theme === 'light' ? 'Passer en mode sombre' : 'Passer en mode clair');
        }

        themeToggle?.addEventListener('click', () => {
            const isLight = document.documentElement.getAttribute('data-theme') === 'light';
            applyTheme(isLight ? 'dark' : 'light');
        });

        applyTheme(localStorage.getItem('evopro-theme') || 'dark');

        function parseDateFrToKey(value) {
            const raw = String(value || '').trim();
            const match = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!match) return null;
            return Number(`${match[3]}${match[2]}${match[1]}`);
        }

        function bindDateMask(inputId, onChange) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', () => {
                let v = input.value.replace(/\D/g, '').slice(0, 8);
                if (v.length >= 5) v = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
                else if (v.length >= 3) v = `${v.slice(0, 2)}/${v.slice(2)}`;
                input.value = v;
                onChange?.();
            });
            if (onChange) input.addEventListener('change', onChange);
        }

        function filterProspectionsTable() {
            const mois = document.getElementById('filter_prospection_mois')?.value || '';
            const commercial = document.getElementById('filter_prospection_commercial')?.value || '';
            const statue = document.getElementById('filter_prospection_statue')?.value || '';
            const num = (document.getElementById('filter_prospection_num')?.value || '').replace(/\D/g, '');
            const deKey = parseDateFrToKey(document.getElementById('filter_prospection_de')?.value || '');
            const aKey = parseDateFrToKey(document.getElementById('filter_prospection_a')?.value || '');

            const rows = document.querySelectorAll('#prospectionsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const rowMois = row.dataset.mois || '';
                const rowCommercial = row.dataset.commercial || '';
                const rowStatue = row.dataset.statue || '';
                const rowTelephone = row.dataset.telephone || '';
                const rowDateKey = parseDateFrToKey(row.dataset.date || '');

                const matchMois = !mois || rowMois === mois;
                const matchCommercial = !commercial || rowCommercial === commercial;
                const matchStatue = !statue || rowStatue === statue;
                const matchNum = !num || rowTelephone.includes(num);
                const matchDe = !deKey || (rowDateKey !== null && rowDateKey >= deKey);
                const matchA = !aKey || (rowDateKey !== null && rowDateKey <= aKey);

                const show = matchMois && matchCommercial && matchStatue && matchNum && matchDe && matchA;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#prospectionsTableBody tr.empty-row:not(#prospectionsNoResult)');
            const noResultRow = document.getElementById('prospectionsNoResult');
            if (noResultRow) noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            if (emptyRow) emptyRow.style.display = rows.length === 0 ? '' : 'none';
        }

        function filterClientsTable() {
            const mois = document.getElementById('filter_client_mois')?.value || '';
            const nom = (document.getElementById('filter_client_nom')?.value || '').trim().toLowerCase();
            const num = (document.getElementById('filter_client_num')?.value || '').replace(/\D/g, '');
            const titre = (document.getElementById('filter_client_titre')?.value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#clientsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const rowMois = row.dataset.mois || '';
                const rowNom = row.dataset.nom || '';
                const rowTitre = row.dataset.titre || '';
                const rowNumero = row.dataset.numero || '';
                const show = (!mois || rowMois === mois)
                    && (!nom || rowNom.includes(nom))
                    && (!num || rowNumero.includes(num))
                    && (!titre || rowTitre.includes(titre));
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#clientsTableBody tr.empty-row:not(#clientsNoResult)');
            const noResultRow = document.getElementById('clientsNoResult');
            if (noResultRow) noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            if (emptyRow) emptyRow.style.display = rows.length === 0 ? '' : 'none';
        }

        document.getElementById('filter_client_mois')?.addEventListener('change', filterClientsTable);
        document.getElementById('filter_client_nom')?.addEventListener('input', filterClientsTable);
        document.getElementById('filter_client_num')?.addEventListener('input', filterClientsTable);
        document.getElementById('filter_client_titre')?.addEventListener('input', filterClientsTable);

        const clientModal = document.getElementById('clientModal');
        const clientForm = document.getElementById('clientForm');
        const clientModalTitle = document.getElementById('clientModalTitle');
        const clientFormActions = document.getElementById('clientFormActions');
        const clientPrintModal = document.getElementById('clientPrintModal');
        const clientPrintArea = document.getElementById('clientPrintArea');

        function openModal(el) {
            el?.classList.add('open');
            el?.setAttribute('aria-hidden', 'false');
        }

        function closeModalEl(el) {
            el?.classList.remove('open');
            el?.setAttribute('aria-hidden', 'true');
        }

        function openSidePanel(el) {
            el?.classList.add('open');
            el?.setAttribute('aria-hidden', 'false');
        }

        function closeSidePanelEl(el) {
            el?.classList.remove('open');
            el?.setAttribute('aria-hidden', 'true');
        }

        function setClientFormMode(mode) {
            const readonly = mode === 'view';
            ['client_date', 'client_nom', 'client_ville', 'client_contact', 'client_titre_projet', 'client_delai_travail', 'client_budget'].forEach((id) => {
                const input = document.getElementById(id);
                if (input) input.readOnly = readonly;
            });
            clientFormActions.style.display = mode === 'view' ? 'none' : 'flex';
        }

        function formatDelaiTravail(value) {
            let v = String(value || '').trim();
            if (!v) return '';
            v = v.replace(/\s*(jrs?|jours?)\s*$/i, '').trim();
            if (!v) return '';
            return `${v} JRS`;
        }

        function fillClientForm(client) {
            document.getElementById('client_date').value = client.date || '';
            document.getElementById('client_nom').value = client.nom || '';
            document.getElementById('client_ville').value = client.ville || '';
            document.getElementById('client_contact').value = client.contact || '';
            document.getElementById('client_titre_projet').value = client.titre_projet || client.activite || '';
            document.getElementById('client_delai_travail').value = formatDelaiTravail(client.delai_travail || '');
            document.getElementById('client_budget').value = Number(client.budget || 0);
        }

        function openClientCreate() {
            clientForm.action = '{{ url('/clients') }}';
            clientForm.querySelector('input[name="_method"]')?.remove();
            clientModalTitle.textContent = 'Ajouter un client';
            fillClientForm({
                date: new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }),
                nom: '',
                titre_projet: '',
                delai_travail: '',
                budget: 0,
                ville: '',
                contact: '',
            });
            setClientFormMode('edit');
            openModal(clientModal);
        }

        function openClientView(client) {
            clientForm.querySelector('input[name="_method"]')?.remove();
            clientForm.action = '{{ url('/clients') }}';
            clientModalTitle.textContent = 'Voir Client';
            fillClientForm(client);
            setClientFormMode('view');
            openModal(clientModal);
        }

        function openClientEdit(client) {
            clientForm.action = `{{ url('/clients') }}/${encodeURIComponent(client.id)}`;
            let method = clientForm.querySelector('input[name="_method"]');
            if (!method) {
                method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                clientForm.appendChild(method);
            }
            method.value = 'PUT';
            clientModalTitle.textContent = 'Modifier Client';
            fillClientForm(client);
            setClientFormMode('edit');
            openModal(clientModal);
        }

        function buildClientPrintHtml(client) {
            const budget = Number(client.budget || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return `
                <h3>Fiche client — ${String(client.nom || '').replace(/</g, '&lt;')}</h3>
                <dl>
                    <dt>Date</dt><dd>${String(client.date || '—').replace(/</g, '&lt;')}</dd>
                    <dt>Nom Client</dt><dd>${String(client.nom || '—').replace(/</g, '&lt;')}</dd>
                    <dt>Ville</dt><dd>${String(client.ville || '—').replace(/</g, '&lt;')}</dd>
                    <dt>Numéro</dt><dd>${String(client.contact || '—').replace(/</g, '&lt;')}</dd>
                    <dt>Titre Projet</dt><dd>${String(client.titre_projet || client.activite || '—').replace(/</g, '&lt;')}</dd>
                    <dt>Délai travail</dt><dd>${String(formatDelaiTravail(client.delai_travail || '') || '—').replace(/</g, '&lt;')}</dd>
                    <dt>Budget</dt><dd>${budget}</dd>
                </dl>
            `;
        }

        function openClientPrint(client) {
            clientPrintArea.innerHTML = buildClientPrintHtml(client);
            openModal(clientPrintModal);
        }

        async function deleteClient(client) {
            const label = client.nom || 'ce client';
            if (!confirm(`Supprimer ${label} ?`)) return;

            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            fd.append('_method', 'DELETE');

            try {
                const response = await fetch(`{{ url('/clients') }}/${encodeURIComponent(client.id)}`, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'delete_failed');
                window.location.href = '{{ route('dashboard') }}?open_panel=client';
            } catch (_) {
                alert('Suppression impossible. Réessayez.');
            }
        }

        document.getElementById('btnAddClient')?.addEventListener('click', openClientCreate);
        document.getElementById('closeClientModal')?.addEventListener('click', () => closeModalEl(clientModal));
        document.getElementById('cancelClientModal')?.addEventListener('click', () => closeModalEl(clientModal));
        document.getElementById('closeClientPrintModal')?.addEventListener('click', () => closeModalEl(clientPrintModal));
        document.getElementById('closeClientPrintBtn')?.addEventListener('click', () => closeModalEl(clientPrintModal));
        document.getElementById('printClientBtn')?.addEventListener('click', () => window.print());

        const clientDateInput = document.getElementById('client_date');
        clientDateInput?.addEventListener('input', () => {
            let v = clientDateInput.value.replace(/\D/g, '').slice(0, 8);
            if (v.length >= 5) v = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
            else if (v.length >= 3) v = `${v.slice(0, 2)}/${v.slice(2)}`;
            clientDateInput.value = v;
        });

        const clientDelaiInput = document.getElementById('client_delai_travail');
        clientDelaiInput?.addEventListener('blur', () => {
            clientDelaiInput.value = formatDelaiTravail(clientDelaiInput.value);
        });
        clientForm?.addEventListener('submit', () => {
            if (clientDelaiInput) {
                clientDelaiInput.value = formatDelaiTravail(clientDelaiInput.value);
            }
        });

        document.getElementById('clientsTableBody')?.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.action-btn');
            if (!actionBtn) return;

            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const client = clientsData.find((item) => item.id === row.dataset.id);
            if (!client) return;

            e.preventDefault();
            e.stopPropagation();

            if (actionBtn.classList.contains('voir')) openClientView(client);
            else if (actionBtn.classList.contains('modifier')) openClientEdit(client);
            else if (actionBtn.classList.contains('imprimer')) openClientPrint(client);
            else if (actionBtn.classList.contains('supprimer')) deleteClient(client);
        });

        const projetSidePanel = document.getElementById('projetSidePanel');
        const projetForm = document.getElementById('projetForm');
        const projetSidePanelTitle = document.getElementById('projetSidePanelTitle');
        const projetFormActions = document.getElementById('projetFormActions');
        const projetPrintModal = document.getElementById('projetPrintModal');
        const projetPrintArea = document.getElementById('projetPrintArea');
        const projetLookupHint = document.getElementById('projetLookupHint');

        function normalizePhoneDigits(value) {
            return String(value || '').replace(/\D/g, '');
        }

        function nextProjetRef() {
            const year = new Date().getFullYear();
            const sameYear = (projetsData || []).filter((row) => String(row.ref || '').endsWith(`/${year}`));
            let max = 0;
            sameYear.forEach((row) => {
                const match = String(row.ref || '').match(/^PR(\d+)\/\d{4}$/);
                if (match) max = Math.max(max, parseInt(match[1], 10));
            });
            return `PR${String(max + 1).padStart(2, '0')}/${year}`;
        }

        function findProspectionByPhone(phone) {
            const key = normalizePhoneDigits(phone);
            if (!key) return null;
            return (prospectionsAllData || []).find((row) => normalizePhoneDigits(row.telephone) === key) || null;
        }

        function findClientByPhone(phone) {
            const key = normalizePhoneDigits(phone);
            if (!key) return null;
            return (clientsData || []).find((row) => normalizePhoneDigits(row.contact) === key) || null;
        }

        function updateProjetSoldeDisplay() {
            const budget = Number(document.getElementById('projet_budget')?.value || 0);
            const avance = Number(document.getElementById('projet_avance')?.value || 0);
            const solde = Math.max(0, budget - avance);
            const el = document.getElementById('projet_solde_display');
            if (el) {
                el.textContent = solde.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        function importProjetFromContact() {
            const phone = document.getElementById('projet_contact')?.value || '';
            const prospection = findProspectionByPhone(phone);
            const client = findClientByPhone(phone);

            if (!normalizePhoneDigits(phone)) {
                if (projetLookupHint) {
                    projetLookupHint.textContent = 'Saisissez un numéro pour importer les données de prospection.';
                    projetLookupHint.className = 'projet-lookup-hint';
                }
                return;
            }

            if (prospection) {
                document.getElementById('projet_nom_client').value = prospection.nom_prospect || '';
                document.getElementById('projet_ville').value = prospection.ville || '';
                document.getElementById('projet_titre_projet').value = prospection.projet || '';
                document.getElementById('projet_prospection_id').value = prospection.id || '';
                if (prospection.commercial) {
                    document.getElementById('projet_commercial').value = prospection.commercial;
                }
                if (projetLookupHint) {
                    projetLookupHint.textContent = 'Données importées depuis la prospection.';
                    projetLookupHint.className = 'projet-lookup-hint ok';
                }
            } else if (projetLookupHint) {
                projetLookupHint.textContent = 'Aucune prospection trouvée pour ce numéro.';
                projetLookupHint.className = 'projet-lookup-hint warn';
                document.getElementById('projet_prospection_id').value = '';
            }

            const budget = Number(prospection?.budget ?? client?.budget ?? 0);
            document.getElementById('projet_budget').value = budget;
            updateProjetSoldeDisplay();
        }

        function setProjetFormMode(mode) {
            const readonly = mode === 'view';
            [
                'projet_date', 'projet_contact', 'projet_nom_client', 'projet_ville',
                'projet_titre_projet', 'projet_budget', 'projet_avance',
            ].forEach((id) => {
                const input = document.getElementById(id);
                if (input) input.readOnly = readonly;
            });
            ['projet_commercial', 'projet_mode', 'projet_part_commercial'].forEach((id) => {
                const select = document.getElementById(id);
                if (select) select.disabled = readonly;
            });
            const saveBtn = document.getElementById('saveProjetSidePanel');
            if (saveBtn) saveBtn.style.display = mode === 'view' ? 'none' : '';
            if (projetFormActions) projetFormActions.style.display = 'flex';
        }

        function fillProjetForm(projet) {
            document.getElementById('projet_date').value = projet.date || '';
            document.getElementById('projet_ref').value = projet.ref || '';
            document.getElementById('projet_commercial').value = projet.commercial || '';
            document.getElementById('projet_contact').value = projet.contact || '';
            document.getElementById('projet_nom_client').value = projet.nom_client || '';
            document.getElementById('projet_ville').value = projet.ville || '';
            document.getElementById('projet_titre_projet').value = projet.titre_projet || '';
            document.getElementById('projet_budget').value = Number(projet.budget || 0);
            document.getElementById('projet_avance').value = Number(projet.avance || 0);
            document.getElementById('projet_mode').value = projet.mode || 'Vir';
            document.getElementById('projet_part_commercial').value = String(projet.part_commercial || 10);
            document.getElementById('projet_prospection_id').value = projet.prospection_id || '';
            updateProjetSoldeDisplay();
            if (projetLookupHint) {
                projetLookupHint.textContent = 'Saisissez un numéro pour importer les données de prospection.';
                projetLookupHint.className = 'projet-lookup-hint';
            }
        }

        function openProjetCreate() {
            projetForm.action = '{{ url('/projets') }}';
            projetForm.querySelector('input[name="_method"]')?.remove();
            projetSidePanelTitle.textContent = 'Ajouter un projet';
            fillProjetForm({
                date: new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }),
                ref: nextProjetRef(),
                commercial: '',
                contact: '',
                nom_client: '',
                ville: '',
                titre_projet: '',
                budget: 0,
                avance: 0,
                mode: 'Vir',
                part_commercial: 10,
            });
            document.getElementById('projet_ref').value = nextProjetRef();
            setProjetFormMode('edit');
            openSidePanel(projetSidePanel);
        }

        function openProjetView(projet) {
            projetForm.querySelector('input[name="_method"]')?.remove();
            projetForm.action = '{{ url('/projets') }}';
            projetSidePanelTitle.textContent = 'Voir un projet';
            fillProjetForm(projet);
            setProjetFormMode('view');
            openSidePanel(projetSidePanel);
        }

        function openProjetEdit(projet) {
            projetForm.action = `{{ url('/projets') }}/${encodeURIComponent(projet.id)}`;
            let method = projetForm.querySelector('input[name="_method"]');
            if (!method) {
                method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                projetForm.appendChild(method);
            }
            method.value = 'PUT';
            projetSidePanelTitle.textContent = 'Modifier un projet';
            fillProjetForm(projet);
            setProjetFormMode('edit');
            openSidePanel(projetSidePanel);
        }

        function buildProjetPrintHtml(projet) {
            const fmt = (n) => Number(n || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const esc = (v) => String(v ?? '—').replace(/</g, '&lt;');
            return `
                <h3>Fiche projet — ${esc(projet.ref)}</h3>
                <dl>
                    <dt>Date</dt><dd>${esc(projet.date)}</dd>
                    <dt>Réf</dt><dd>${esc(projet.ref)}</dd>
                    <dt>Commercial</dt><dd>${esc(projet.commercial)}</dd>
                    <dt>Titre Projet</dt><dd>${esc(projet.titre_projet)}</dd>
                    <dt>Nom Client</dt><dd>${esc(projet.nom_client)}</dd>
                    <dt>Ville</dt><dd>${esc(projet.ville)}</dd>
                    <dt>Contact</dt><dd>${esc(projet.contact)}</dd>
                    <dt>Budget</dt><dd>${fmt(projet.budget)}</dd>
                    <dt>Avance</dt><dd>${fmt(projet.avance)}</dd>
                    <dt>Mode</dt><dd>${esc(projet.mode)}</dd>
                    <dt>Solde</dt><dd>${fmt(projet.solde)}</dd>
                    <dt>Part Commercial</dt><dd>${esc(projet.part_commercial)}%</dd>
                </dl>
            `;
        }

        function openProjetPrint(projet) {
            projetPrintArea.innerHTML = buildProjetPrintHtml(projet);
            openModal(projetPrintModal);
        }

        async function deleteProjet(projet) {
            const label = projet.ref || projet.nom_client || 'ce projet';
            if (!confirm(`Supprimer ${label} ?`)) return;

            const fd = new FormData();
            fd.append('_token', csrfToken);
            fd.append('_method', 'DELETE');

            try {
                const response = await fetch(`{{ url('/projets') }}/${encodeURIComponent(projet.id)}`, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'delete_failed');
                window.location.href = '{{ route('dashboard') }}?open_panel=projet';
            } catch (_) {
                alert('Suppression impossible. Réessayez.');
            }
        }

        function filterProjetsTable() {
            const mois = document.getElementById('filter_projet_mois')?.value || '';
            const commercial = document.getElementById('filter_projet_commercial')?.value || '';
            const rows = document.querySelectorAll('#projetsTableBody tr[data-id]');
            let visible = 0;

            rows.forEach((row) => {
                const rowMois = row.dataset.mois || '';
                const rowCommercial = row.dataset.commercial || '';
                const show = (!mois || rowMois === mois)
                    && (!commercial || rowCommercial === commercial);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.querySelector('#projetsTableBody tr.empty-row:not(#projetsNoResult)');
            const noResultRow = document.getElementById('projetsNoResult');
            if (noResultRow) noResultRow.style.display = rows.length > 0 && visible === 0 ? '' : 'none';
            if (emptyRow) emptyRow.style.display = rows.length === 0 ? '' : 'none';
        }

        document.getElementById('filter_projet_mois')?.addEventListener('change', filterProjetsTable);
        document.getElementById('filter_projet_commercial')?.addEventListener('change', filterProjetsTable);

        document.getElementById('btnAddProjet')?.addEventListener('click', openProjetCreate);
        document.getElementById('btnCloseProjetPanel')?.addEventListener('click', () => showPanel('dashboard'));
        document.getElementById('closeProjetSidePanel')?.addEventListener('click', () => closeSidePanelEl(projetSidePanel));
        document.getElementById('cancelProjetSidePanel')?.addEventListener('click', () => closeSidePanelEl(projetSidePanel));
        document.getElementById('closeProjetPrintModal')?.addEventListener('click', () => closeModalEl(projetPrintModal));
        document.getElementById('closeProjetPrintBtn')?.addEventListener('click', () => closeModalEl(projetPrintModal));
        document.getElementById('printProjetBtn')?.addEventListener('click', () => window.print());

        bindDateMask('projet_date');
        document.getElementById('projet_contact')?.addEventListener('blur', importProjetFromContact);
        document.getElementById('projet_contact')?.addEventListener('change', importProjetFromContact);
        document.getElementById('projet_budget')?.addEventListener('input', updateProjetSoldeDisplay);
        document.getElementById('projet_avance')?.addEventListener('input', updateProjetSoldeDisplay);

        document.getElementById('projetsTableBody')?.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.action-btn');
            if (!actionBtn) return;

            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const projet = projetsData.find((item) => item.id === row.dataset.id);
            if (!projet) return;

            e.preventDefault();
            e.stopPropagation();

            if (actionBtn.classList.contains('voir')) openProjetView(projet);
            else if (actionBtn.classList.contains('imprimer')) openProjetPrint(projet);
            else if (actionBtn.classList.contains('modifier')) openProjetEdit(projet);
            else if (actionBtn.classList.contains('supprimer')) deleteProjet(projet);
        });

        document.getElementById('filter_prospection_num')?.addEventListener('input', filterProspectionsTable);
        document.getElementById('filter_prospection_mois')?.addEventListener('change', filterProspectionsTable);
        document.getElementById('filter_prospection_commercial')?.addEventListener('change', filterProspectionsTable);
        document.getElementById('filter_prospection_statue')?.addEventListener('change', filterProspectionsTable);
        bindDateMask('filter_prospection_de', filterProspectionsTable);
        bindDateMask('filter_prospection_a', filterProspectionsTable);

        function isDateRappelDue(value) {
            const match = String(value || '').trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!match) return false;
            const due = new Date(`${match[3]}-${match[2]}-${match[1]}T00:00:00`);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return due <= today;
        }

        function applyProspectionRowStyle(row) {
            if (!row) return;
            row.classList.remove('row-prospection-valide', 'row-prospection-annule', 'row-prospection-reporte', 'row-prospection-rappel-du');

            const statue = row.dataset.statue || 'en_attente';
            const dateRappel = row.dataset.dateRappel || row.querySelector('.prospection-date-input')?.value || '';

            if (statue === 'valide') row.classList.add('row-prospection-valide');
            if (statue === 'annule') row.classList.add('row-prospection-annule');
            if (statue === 'reporte') row.classList.add('row-prospection-reporte');
            if (isDateRappelDue(dateRappel) && (statue === 'en_attente' || statue === 'reporte')) {
                row.classList.add('row-prospection-rappel-du');
            }

            const dateInput = row.querySelector('.prospection-date-input');
            if (dateInput) {
                dateInput.classList.toggle('is-rappel-du', row.classList.contains('row-prospection-rappel-du'));
            }
        }

        document.querySelectorAll('#prospectionsTableBody tr[data-id]').forEach(applyProspectionRowStyle);

        document.querySelectorAll('#prospectionsTableBody .statue-select').forEach((select) => {
            select.addEventListener('change', async () => {
                const id = select.dataset.id || select.closest('tr[data-id]')?.dataset.id;
                const statue = select.value;
                const row = select.closest('tr[data-id]');
                const previous = row?.dataset.statue || 'en_attente';

                select.className = `statue-select ${statue}`;
                if (row) {
                    row.dataset.statue = statue;
                    applyProspectionRowStyle(row);
                }

                if (!id) return;

                try {
                    const response = await fetch(`{{ url('/prospections') }}/${encodeURIComponent(id)}/statue`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ statue }),
                    });
                    if (!response.ok) throw new Error('save_failed');
                } catch (_) {
                    select.value = previous;
                    select.className = `statue-select ${previous}`;
                    if (row) {
                        row.dataset.statue = previous;
                        applyProspectionRowStyle(row);
                    }
                }
            });
        });

        document.querySelectorAll('.prospection-inline').forEach((el) => {
            el.dataset.initial = (el.value || '').trim();

            if (el.classList.contains('prospection-date-input')) {
                el.addEventListener('input', () => {
                    let v = el.value.replace(/\D/g, '').slice(0, 8);
                    if (v.length >= 5) v = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
                    else if (v.length >= 3) v = `${v.slice(0, 2)}/${v.slice(2)}`;
                    el.value = v;
                });
            }

            const saveInline = async () => {
                const id = el.dataset.id;
                const field = el.dataset.field;
                let value = (el.value || '').trim();
                if (!id || !field || value === (el.dataset.initial || '') || el.classList.contains('is-saving')) return;

                if (field === 'date_rappel' && value !== '' && !/^\d{2}\/\d{2}\/\d{4}$/.test(value)) return;

                el.classList.add('is-saving');
                try {
                    const response = await fetch(`{{ url('/prospections') }}/${encodeURIComponent(id)}/inline`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ field, value }),
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'save_failed');
                    el.dataset.initial = String(data.value ?? value);
                    if (field === 'date_rappel') {
                        const row = el.closest('tr[data-id]');
                        if (row) {
                            row.dataset.dateRappel = el.dataset.initial;
                            applyProspectionRowStyle(row);
                        }
                    }
                } catch (_) {
                    el.value = el.dataset.initial || '';
                } finally {
                    el.classList.remove('is-saving');
                }
            };

            el.addEventListener('blur', saveInline);
            el.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && el.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    el.blur();
                }
            });
        });

        function applyLiveField(rowEl, field, value) {
            const el = rowEl.querySelector(`.prospection-inline[data-field="${field}"]`);
            if (!el) return;
            if (document.activeElement === el || el.classList.contains('is-saving')) return;
            const next = String(value ?? '');
            if ((el.value || '') === next && (el.dataset.initial || '') === next.trim()) return;
            el.value = next;
            el.dataset.initial = next.trim();
            if (field === 'date_rappel') {
                rowEl.dataset.dateRappel = next.trim();
                applyProspectionRowStyle(rowEl);
            }
        }

        function applyLiveStatue(rowEl, statue) {
            const select = rowEl.querySelector('.statue-select');
            if (!select) return;
            if (document.activeElement === select) return;
            const next = statue || 'en_attente';
            if (select.value === next && rowEl.dataset.statue === next) return;
            select.value = next;
            select.className = `statue-select ${next}`;
            rowEl.dataset.statue = next;
            applyProspectionRowStyle(rowEl);
        }

        async function syncProspectionsLive() {
            const panel = document.getElementById('panel-prospection');
            if (!panel?.classList.contains('active')) return;

            try {
                const response = await fetch('{{ route('prospections.live') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (!response.ok) return;
                const data = await response.json().catch(() => ({}));
                const rows = Array.isArray(data.rows) ? data.rows : [];

                if (Array.isArray(prospectionsAllData)) {
                    const byId = Object.fromEntries(prospectionsAllData.map((row) => [row.id, row]));
                    rows.forEach((row) => {
                        byId[row.id] = { ...(byId[row.id] || {}), ...row };
                    });
                    prospectionsAllData.length = 0;
                    Object.values(byId).forEach((row) => prospectionsAllData.push(row));
                    if (typeof filterCommercialTable === 'function' && document.getElementById('prospection-commercial')?.classList.contains('active')) {
                        filterCommercialTable();
                    }
                }

                rows.forEach((row) => {
                    const rowEl = document.querySelector(`#prospectionsTableBody tr[data-id="${CSS.escape(row.id)}"]`);
                    if (!rowEl) return;
                    applyLiveField(rowEl, 'nom_prospect', row.nom_prospect);
                    applyLiveField(rowEl, 'ville', row.ville);
                    applyLiveField(rowEl, 'projet', row.projet);
                    applyLiveField(rowEl, 'remarque', row.remarque);
                    applyLiveField(rowEl, 'date_rappel', row.date_rappel);
                    applyLiveStatue(rowEl, row.statue);
                });
            } catch (_) {
                // ignore network blips
            }
        }

        syncProspectionsLive();
        setInterval(syncProspectionsLive, liveSyncIntervalMs);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) syncProspectionsLive();
        });

        async function syncCommercialPresence() {
            if (!canViewCommercialPresence) return;

            try {
                const response = await fetch('{{ route('presence.live') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (!response.ok) return;
                const data = await response.json().catch(() => ({}));
                (data.commercials || []).forEach((commercial) => {
                    const pill = document.querySelector(`#commercialPresenceBar .presence-pill[data-commercial-key="${CSS.escape(commercial.key)}"]`);
                    if (!pill) return;
                    const dot = pill.querySelector('.presence-dot');
                    const isOnline = commercial.status === 'online';
                    dot?.classList.remove('online', 'offline', 'unknown');
                    dot?.classList.add(isOnline ? 'online' : 'offline');
                    pill.title = `${commercial.nom_complet} — ${isOnline ? 'En ligne' : 'Hors ligne'}`;
                });
            } catch (_) {
                // ignore network blips
            }
        }

        if (canViewCommercialPresence) {
            syncCommercialPresence();
            setInterval(syncCommercialPresence, 2500);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) syncCommercialPresence();
            });
        }

        async function sendPresenceHeartbeat() {
            if (!isCommercialRole) return;
            try {
                await fetch('{{ route('presence.heartbeat') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
            } catch (_) {
                // ignore
            }
        }

        function sendPresenceOffline() {
            if (!isCommercialRole) return;
            const body = new URLSearchParams({ _token: csrfToken });
            navigator.sendBeacon('{{ route('presence.offline') }}', body);
        }

        if (isCommercialRole) {
            sendPresenceHeartbeat();
            setInterval(sendPresenceHeartbeat, 4000);
            window.addEventListener('pagehide', sendPresenceOffline);
            document.getElementById('logoutForm')?.addEventListener('submit', sendPresenceOffline);
        }

        function showConfigSection(name) {
            setActiveNavSubitem('config', name);
            document.querySelectorAll('#panel-configuration .config-section').forEach((section) => {
                section.classList.toggle('active', section.id === `config-${name}`);
            });
            expandNavGroup('configuration');
            showPanel('configuration');
        }

        async function saveFicheSte(closeAfterSave = true) {
            const form = document.getElementById('ficheSteForm');
            if (!form) return false;

            const fd = new FormData(form);
            fd.append('_method', 'PUT');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'save_failed');

                if (data.fiche_ste) {
                    Object.assign(ficheSteData, data.fiche_ste);
                }

                if (closeAfterSave) {
                    showConfigSection('utilisateur');
                }

                return true;
            } catch (error) {
                window.alert(error.message || 'Impossible d’enregistrer la fiche société.');
                return false;
            }
        }

        document.getElementById('btnFicheSteFermer')?.addEventListener('click', () => saveFicheSte(true));

        const utilisateurSidePanel = document.getElementById('utilisateurSidePanel');
        const utilisateurForm = document.getElementById('utilisateurForm');
        const utilisateurSidePanelTitle = document.getElementById('utilisateurSidePanelTitle');
        const utilisateurFormActions = document.getElementById('utilisateurFormActions');

        function setUtilisateurFormMode(mode) {
            const readonly = mode === 'view';
            ['utilisateur_date', 'utilisateur_nom_complet', 'utilisateur_ville', 'utilisateur_login', 'utilisateur_password'].forEach((id) => {
                const input = document.getElementById(id);
                if (input) input.readOnly = readonly;
            });
            const statue = document.getElementById('utilisateur_statue');
            if (statue) statue.disabled = readonly;
            const saveBtn = document.getElementById('saveUtilisateurSidePanel');
            if (saveBtn) saveBtn.style.display = mode === 'view' ? 'none' : '';
            utilisateurFormActions.style.display = 'flex';
        }

        function fillUtilisateurForm(user) {
            document.getElementById('utilisateur_date').value = user.date || '';
            document.getElementById('utilisateur_nom_complet').value = user.nom_complet || '';
            document.getElementById('utilisateur_ville').value = user.ville || '';
            document.getElementById('utilisateur_statue').value = user.statue || 'commercial';
            document.getElementById('utilisateur_login').value = user.login || '';
            document.getElementById('utilisateur_password').value = user.password || '';
        }

        function openUtilisateurCreate() {
            utilisateurForm.action = '{{ url('/utilisateurs') }}';
            utilisateurForm.querySelector('input[name="_method"]')?.remove();
            utilisateurSidePanelTitle.textContent = 'Ajouter un utilisateur';
            fillUtilisateurForm({
                date: new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }),
                nom_complet: '',
                ville: '',
                statue: 'commercial',
                login: '',
                password: '',
            });
            setUtilisateurFormMode('edit');
            openSidePanel(utilisateurSidePanel);
        }

        function openUtilisateurView(user) {
            utilisateurForm.querySelector('input[name="_method"]')?.remove();
            utilisateurForm.action = '{{ url('/utilisateurs') }}';
            utilisateurSidePanelTitle.textContent = 'Voir un utilisateur';
            fillUtilisateurForm(user);
            setUtilisateurFormMode('view');
            openSidePanel(utilisateurSidePanel);
        }

        function openUtilisateurEdit(user) {
            utilisateurForm.action = `{{ url('/utilisateurs') }}/${encodeURIComponent(user.id)}`;
            let method = utilisateurForm.querySelector('input[name="_method"]');
            if (!method) {
                method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                utilisateurForm.appendChild(method);
            }
            method.value = 'PUT';
            utilisateurSidePanelTitle.textContent = 'Modifier un utilisateur';
            fillUtilisateurForm(user);
            setUtilisateurFormMode('edit');
            openSidePanel(utilisateurSidePanel);
        }

        document.getElementById('btnAddUtilisateur')?.addEventListener('click', openUtilisateurCreate);
        document.getElementById('btnCloseUtilisateurPanel')?.addEventListener('click', () => closeSidePanelEl(utilisateurSidePanel));
        document.getElementById('closeUtilisateurSidePanel')?.addEventListener('click', () => closeSidePanelEl(utilisateurSidePanel));
        document.getElementById('cancelUtilisateurSidePanel')?.addEventListener('click', () => closeSidePanelEl(utilisateurSidePanel));
        utilisateurSidePanel?.addEventListener('click', (event) => {
            if (event.target === utilisateurSidePanel) closeSidePanelEl(utilisateurSidePanel);
        });

        bindDateMask('utilisateur_date');

        document.getElementById('utilisateursTableBody')?.addEventListener('click', async (event) => {
            const btn = event.target.closest('.action-btn');
            if (!btn) return;
            const row = btn.closest('tr[data-id]');
            if (!row) return;
            const user = utilisateursData.find((item) => item.id === row.dataset.id);
            if (!user) return;

            if (btn.classList.contains('voir')) {
                openUtilisateurView(user);
                return;
            }

            if (btn.classList.contains('modifier')) {
                openUtilisateurEdit(user);
                return;
            }

            if (btn.classList.contains('suspendre')) {
                const label = btn.dataset.suspended === '1' ? 'réactiver' : 'suspendre';
                if (!window.confirm(`Voulez-vous ${label} cet utilisateur ?`)) return;

                try {
                    const response = await fetch(`{{ url('/utilisateurs') }}/${encodeURIComponent(user.id)}/suspendre`, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'suspend_failed');
                    window.location.href = '{{ route('dashboard') }}?open_panel=configuration&open_config=utilisateur';
                } catch (error) {
                    window.alert(error.message || 'Impossible de modifier le statut.');
                }
            }
        });

        function showProspectionView(name) {
            setActiveNavSubitem('prospection', name);
            document.querySelectorAll('#panel-prospection .prospection-view').forEach((view) => {
                view.classList.toggle('active', view.id === `prospection-${name}`);
            });
            expandNavGroup('prospection');
            showPanel('prospection');
            if (name === 'commercial') {
                filterCommercialTable();
            }
        }

        function showProspectionListe() {
            showProspectionView('liste');
        }

        function commercialKey(name) {
            return String(name || '').trim().toLowerCase();
        }

        const prospectionStatueLabels = {
            valide: 'Validé',
            en_attente: 'En Attente',
            annule: 'Annulé',
            reporte: 'Reporté',
        };

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function getFilterCommercialName() {
            const select = document.getElementById('filter_commercial_commercial');
            if (!select || !select.value) return '';
            const option = select.options[select.selectedIndex];
            return option ? option.text.trim() : '';
        }

        function filterCommercialTable() {
            const body = document.getElementById('commercialNumerosBody');
            if (!body) return;

            const mois = document.getElementById('filter_commercial_mois')?.value || '';
            const commercial = document.getElementById('filter_commercial_commercial')?.value || '';
            const deKey = parseDateFrToKey(document.getElementById('filter_commercial_de')?.value || '');
            const aKey = parseDateFrToKey(document.getElementById('filter_commercial_a')?.value || '');

            const rows = prospectionsAllData.filter((row) => trimCommercial(row.commercial) !== '');
            body.querySelectorAll('tr:not(#commercialNumerosEmpty)').forEach((row) => row.remove());

            let visible = 0;
            rows.forEach((row) => {
                const parts = String(row.date || '').split('/');
                const rowMois = parts.length >= 3 ? `${parts[1]}/${parts[2]}` : '';
                const rowCommercial = commercialKey(row.commercial);
                const rowDateKey = parseDateFrToKey(row.date || '');

                const matchMois = !mois || rowMois === mois;
                const matchCommercial = !commercial || rowCommercial === commercial;
                const matchDe = !deKey || (rowDateKey !== null && rowDateKey >= deKey);
                const matchA = !aKey || (rowDateKey !== null && rowDateKey <= aKey);

                if (!(matchMois && matchCommercial && matchDe && matchA)) return;

                const tr = document.createElement('tr');
                tr.dataset.id = row.id;
                const statue = row.statue || 'en_attente';
                tr.innerHTML = `
                    <td>${escapeHtml(row.date || '')}</td>
                    <td>${escapeHtml(row.telephone || '')}</td>
                    <td>${escapeHtml(row.commercial || '')}</td>
                    <td>${escapeHtml(row.nom_prospect || '')}</td>
                    <td>${escapeHtml(row.ville || '')}</td>
                    <td>${escapeHtml(row.projet || '')}</td>
                    <td class="cell-remarque-preview" title="${escapeHtml(row.remarque || '')}">${escapeHtml(row.remarque || '')}</td>
                    <td>${escapeHtml(prospectionStatueLabels[statue] || statue)}</td>
                    <td>${escapeHtml(row.date_rappel || '')}</td>
                `;
                body.appendChild(tr);
                visible++;
            });

            const emptyRow = document.getElementById('commercialNumerosEmpty');
            if (emptyRow) {
                emptyRow.style.display = visible === 0 ? '' : 'none';
            }
        }

        function trimCommercial(value) {
            return String(value || '').trim();
        }

        document.getElementById('filter_commercial_mois')?.addEventListener('change', filterCommercialTable);
        document.getElementById('filter_commercial_commercial')?.addEventListener('change', filterCommercialTable);
        bindDateMask('filter_commercial_de', filterCommercialTable);
        bindDateMask('filter_commercial_a', filterCommercialTable);

        document.getElementById('btnCommercialFermer')?.addEventListener('click', () => {
            showProspectionView('liste');
        });

        const commercialNumeroModal = document.getElementById('commercialNumeroModal');
        const commercialNumeroForm = document.getElementById('commercialNumeroForm');

        function openCommercialNumeroModal() {
            const filterCommercial = getFilterCommercialName();
            const select = document.getElementById('commercial_numero_commercial');
            if (select && filterCommercial) {
                select.value = filterCommercial;
            }
            document.getElementById('commercial_numero_date').value = new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            document.getElementById('commercial_numero_telephone').value = '';
            openModal(commercialNumeroModal);
        }

        document.getElementById('btnCommercialAjouter')?.addEventListener('click', openCommercialNumeroModal);
        document.getElementById('closeCommercialNumeroModal')?.addEventListener('click', () => closeModalEl(commercialNumeroModal));
        document.getElementById('cancelCommercialNumeroModal')?.addEventListener('click', () => closeModalEl(commercialNumeroModal));
        bindDateMask('commercial_numero_date');

        commercialNumeroForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const commercial = document.getElementById('commercial_numero_commercial')?.value.trim() || '';
            const date = document.getElementById('commercial_numero_date').value.trim();
            const telephone = document.getElementById('commercial_numero_telephone').value.trim();
            if (!commercial || !/^\d{2}\/\d{2}\/\d{4}$/.test(date) || telephone === '') return;

            try {
                const fd = new FormData();
                fd.append('commercial', commercial);
                fd.append('date', date);
                fd.append('telephone', telephone);
                fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

                const response = await fetch('{{ route('prospections.commercial.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'save_failed');

                (data.rows || []).forEach((row) => prospectionsAllData.push(row));
                closeModalEl(commercialNumeroModal);
                filterCommercialTable();
            } catch (error) {
                window.alert(error.message || 'Impossible d’ajouter le numéro.');
            }
        });

        function extractPhoneNumbersFromText(text) {
            const found = [];
            const raw = String(text || '')
                .replace(/[Oo]/g, '0')
                .replace(/[–—−]/g, '-');

            const patterns = [
                /(?:\+212|00212|212)[\s\-]*[567]\d(?:[\s\-]?\d){7}/g,
                /(?<!\d)0[567]\d(?:[\s\-]?\d){7}(?!\d)/g,
                /(?<!\d)[567]\d(?:[\s\-]?\d){7}(?!\d)/g,
            ];

            const normalizeMatch = (match) => {
                let digits = String(match).replace(/\D/g, '');
                if (digits.startsWith('212') && digits.length >= 12) {
                    digits = '0' + digits.slice(3);
                } else if (digits.startsWith('00212') && digits.length >= 14) {
                    digits = '0' + digits.slice(5);
                } else if (digits.length === 9 && ['5', '6', '7'].includes(digits[0])) {
                    digits = '0' + digits;
                }
                if (digits.length === 10 && digits.startsWith('0') && ['5', '6', '7'].includes(digits[1])) {
                    return `${digits.slice(0, 4)} ${digits.slice(4, 6)} ${digits.slice(6, 8)} ${digits.slice(8, 10)}`;
                }
                return '';
            };

            patterns.forEach((regex) => {
                const matches = raw.match(regex) || [];
                matches.forEach((match) => {
                    const normalized = normalizeMatch(match);
                    if (normalized) found.push(normalized);
                });
            });

            (raw.match(/[\d\s\-+().]{9,22}/g) || []).forEach((block) => {
                if (!/[567]/.test(block)) return;
                const digits = block.replace(/\D/g, '');
                if (digits.length < 9 || digits.length > 12) return;
                if (digits.startsWith('212') && digits.length !== 12) return;
                if (!digits.startsWith('212') && digits.length !== 9 && digits.length !== 10) return;
                const normalized = normalizeMatch(digits);
                if (normalized) found.push(normalized);
            });

            return [...new Set(found)];
        }

        async function preprocessImageForOcr(file) {
            const bitmap = await createImageBitmap(file);
            const scale = Math.max(2, 1400 / Math.max(bitmap.width, bitmap.height, 1));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(bitmap.width * scale);
            canvas.height = Math.round(bitmap.height * scale);
            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            ctx.filter = 'contrast(1.4)';
            ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const { data } = imageData;
            for (let i = 0; i < data.length; i += 4) {
                const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                const value = gray > 165 ? 255 : gray < 95 ? 0 : gray;
                data[i] = data[i + 1] = data[i + 2] = value;
            }
            ctx.putImageData(imageData, 0, 0);
            bitmap.close?.();

            return canvas;
        }

        async function recognizePhonesFromImage(file) {
            const canvas = await preprocessImageForOcr(file);
            const worker = await Tesseract.createWorker('eng', 1, { logger: () => {} });
            try {
                await worker.setParameters({
                    tessedit_pageseg_mode: Tesseract.PSM?.SPARSE_TEXT ?? '11',
                    tessedit_char_whitelist: '0123456789+- ',
                });
                const result = await worker.recognize(canvas);
                return result.data?.text || '';
            } finally {
                await worker.terminate();
            }
        }

        const commercialImportModal = document.getElementById('commercialImportModal');
        const commercialImportForm = document.getElementById('commercialImportForm');
        const commercialImportFile = document.getElementById('commercialImportFile');
        const commercialImportStatus = document.getElementById('commercialImportStatus');
        const commercialImportModalStatus = document.getElementById('commercialImportModalStatus');
        const submitCommercialImport = document.getElementById('submitCommercialImport');

        function openCommercialImportModal() {
            const filterCommercial = getFilterCommercialName();
            const select = document.getElementById('commercial_import_commercial');
            if (select && filterCommercial) {
                select.value = filterCommercial;
            } else if (select) {
                select.value = '';
            }
            if (commercialImportFile) commercialImportFile.value = '';
            if (commercialImportModalStatus) commercialImportModalStatus.textContent = '';
            openModal(commercialImportModal);
        }

        document.getElementById('btnCommercialImporter')?.addEventListener('click', openCommercialImportModal);
        document.getElementById('closeCommercialImportModal')?.addEventListener('click', () => closeModalEl(commercialImportModal));
        document.getElementById('cancelCommercialImportModal')?.addEventListener('click', () => closeModalEl(commercialImportModal));

        commercialImportForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const commercial = document.getElementById('commercial_import_commercial')?.value.trim() || '';
            const file = commercialImportFile?.files?.[0];
            if (!commercial || !file) return;

            if (commercialImportModalStatus) {
                commercialImportModalStatus.textContent = 'Analyse de l’image en cours…';
                commercialImportModalStatus.style.color = 'var(--muted)';
            }
            if (submitCommercialImport) submitCommercialImport.disabled = true;

            try {
                if (typeof Tesseract === 'undefined') {
                    throw new Error('Module OCR indisponible. Vérifiez votre connexion internet et rechargez la page.');
                }

                const ocrText = await recognizePhonesFromImage(file);
                const numeros = extractPhoneNumbersFromText(ocrText);

                if (commercialImportModalStatus) {
                    commercialImportModalStatus.textContent = numeros.length
                        ? `${numeros.length} numéro(s) détecté(s), enregistrement…`
                        : 'Analyse terminée, extraction des numéros…';
                }

                const response = await fetch('{{ route('prospections.commercial.import') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        commercial,
                        date: new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }),
                        ocr_text: ocrText,
                        numeros,
                    }),
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'import_failed'));
                }

                if ((data.created ?? 0) === 0 && (data.skipped ?? 0) > 0) {
                    throw new Error('Tous les numéros détectés existent déjà pour ce commercial.');
                }

                (data.rows || []).forEach((row) => prospectionsAllData.push(row));
                filterCommercialTable();
                closeModalEl(commercialImportModal);

                const message = `${data.created || 0} numéro(s) importé(s), ${data.skipped || 0} ignoré(s).`;
                if (commercialImportStatus) {
                    commercialImportStatus.textContent = message;
                    commercialImportStatus.className = 'is-success';
                }
            } catch (error) {
                const message = error.message || 'Import impossible.';
                if (commercialImportModalStatus) {
                    commercialImportModalStatus.textContent = message;
                    commercialImportModalStatus.style.color = '#ffb3b8';
                }
                if (commercialImportStatus) {
                    commercialImportStatus.textContent = message;
                    commercialImportStatus.className = 'is-error';
                }
            } finally {
                if (submitCommercialImport) submitCommercialImport.disabled = false;
            }
        });

        @if (session('open_config'))
            showConfigSection(@json(session('open_config')));
        @elseif (session('open_panel') === 'configuration')
            showConfigSection('utilisateur');
        @elseif (session('open_panel'))
            showPanel(@json(session('open_panel')));
        @else
            const openPanelFromUrl = new URLSearchParams(window.location.search).get('open_panel');
            const openConfigFromUrl = new URLSearchParams(window.location.search).get('open_config');
            if (openConfigFromUrl) {
                showConfigSection(openConfigFromUrl);
            } else if (openPanelFromUrl === 'configuration') {
                showConfigSection('utilisateur');
            } else if (openPanelFromUrl) {
                showPanel(openPanelFromUrl);
            } else {
                showPanel(defaultPanel);
            }
        @endif

        @if (session('open_prospection'))
            showProspectionView(@json(session('open_prospection')));
        @elseif (session('open_panel') === 'prospection')
            showProspectionView('liste');
        @else
            const openProspectionFromUrl = new URLSearchParams(window.location.search).get('open_prospection');
            if (openProspectionFromUrl) {
                showProspectionView(openProspectionFromUrl);
            } else if (defaultPanel === 'prospection') {
                showProspectionView('liste');
            }
        @endif
    </script>
</body>
</html>
