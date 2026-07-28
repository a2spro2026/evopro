<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EvoPro — Système de Gestion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #06101f;
            --bg-panel: #0a1628;
            --accent: #3b9eff;
            --accent-soft: #5eb0ff;
            --accent-glow: rgba(59, 158, 255, 0.55);
            --text: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.72);
            --text-dim: rgba(255, 255, 255, 0.55);
            --frame: #f4f7fb;
            --input-border: #d5dde8;
            --input-bg: #ffffff;
            --label: #1a2a3f;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            text-transform: uppercase;
            background: var(--bg-deep);
            overflow-x: hidden;
        }

        .page {
            position: relative;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 2rem;
            padding: 2.5rem 3.5rem;
            background:
                linear-gradient(105deg, rgba(4, 12, 28, 0.92) 0%, rgba(6, 18, 38, 0.78) 48%, rgba(8, 22, 48, 0.55) 100%),
                url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=80') center / cover no-repeat;
        }

        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 50% 40% at 15% 85%, rgba(40, 140, 255, 0.25), transparent 70%),
                radial-gradient(ellipse 40% 35% at 80% 20%, rgba(30, 100, 220, 0.18), transparent 65%);
            pointer-events: none;
        }

        .page::after {
            content: '';
            position: absolute;
            left: 0;
            right: 45%;
            bottom: 0;
            height: 180px;
            background:
                radial-gradient(ellipse 80% 100% at 20% 100%, rgba(59, 158, 255, 0.35), transparent 70%),
                linear-gradient(0deg, rgba(20, 90, 200, 0.15), transparent);
            pointer-events: none;
            filter: blur(2px);
        }

        .brand-side,
        .login-side {
            position: relative;
            z-index: 1;
        }

        .brand-side {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0.5rem 1rem 0.5rem 0;
            max-width: 640px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .logo-mark {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(145deg, #1e6fd9, #4eb3ff);
            display: grid;
            place-items: center;
            box-shadow: 0 0 24px rgba(59, 158, 255, 0.45);
            flex-shrink: 0;
        }

        .logo-mark svg {
            width: 26px;
            height: 26px;
        }

        .logo-text strong {
            display: block;
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .logo-text strong span {
            color: var(--accent-soft);
        }

        .logo-text small {
            display: block;
            margin-top: 0.15rem;
            font-size: 0.68rem;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
        }

        .hero {
            margin-top: 3.5rem;
        }

        .hero h1 {
            font-size: clamp(2rem, 3.4vw, 2.85rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1.1rem;
        }

        .hero h1 .hl {
            color: var(--accent-soft);
        }

        .hero p {
            max-width: 36rem;
            font-size: 1.02rem;
            font-weight: 300;
            line-height: 1.65;
            color: var(--text-muted);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.25rem;
            margin-top: 2.75rem;
        }

        .feature {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.7rem;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
        }

        .feature-icon svg {
            width: 22px;
            height: 22px;
        }

        .feature-icon.purple { background: linear-gradient(145deg, #7b5cff, #9b7bff); }
        .feature-icon.blue { background: linear-gradient(145deg, #2f7dff, #5aa0ff); }
        .feature-icon.cyan { background: linear-gradient(145deg, #1bb8d4, #4dd4ea); }
        .feature-icon.green { background: linear-gradient(145deg, #2f9e5f, #4fc98a); }

        .feature span {
            font-size: 0.78rem;
            line-height: 1.35;
            color: var(--text-muted);
            font-weight: 400;
        }

        .security {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            margin-top: 2.5rem;
            padding: 0.7rem 1rem;
            border: 1px solid rgba(91, 160, 255, 0.35);
            border-radius: 10px;
            background: rgba(10, 30, 60, 0.45);
            backdrop-filter: blur(8px);
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .security svg {
            width: 18px;
            height: 18px;
            color: var(--accent-soft);
            flex-shrink: 0;
        }

        .login-side {
            display: flex;
            align-items: stretch;
            justify-content: flex-end;
            min-height: calc(100vh - 5rem);
        }

        .login-frame {
            width: min(100%, 420px);
            display: flex;
            flex-direction: column;
            background: var(--frame);
            border-radius: 28px;
            box-shadow:
                0 0 0 3px rgba(90, 170, 255, 0.85),
                0 0 40px var(--accent-glow),
                0 0 80px rgba(59, 158, 255, 0.25),
                0 24px 60px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            animation: frameIn 0.7s ease-out both;
        }

        @keyframes frameIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.75rem 2.4rem;
            color: var(--label);
        }

        .login-panel header {
            margin-bottom: 2rem;
            animation: rise 0.6s ease-out 0.15s both;
        }

        .login-panel header h2 {
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0d1b2e;
            margin-bottom: 0.4rem;
        }

        .login-panel header p {
            font-size: 0.92rem;
            color: #5a6b80;
            font-weight: 400;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .form-status {
            padding: 0.75rem 0.9rem;
            border-radius: 10px;
            background: rgba(47, 158, 95, 0.12);
            border: 1px solid rgba(47, 158, 95, 0.35);
            color: #1f7a4a;
            font-size: 0.85rem;
        }

        .form-errors {
            padding: 0.75rem 0.9rem;
            border-radius: 10px;
            background: rgba(220, 60, 60, 0.1);
            border: 1px solid rgba(220, 60, 60, 0.3);
            color: #b42318;
            font-size: 0.85rem;
        }

        .form-errors p + p {
            margin-top: 0.35rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            animation: rise 0.55s ease-out both;
        }

        .field:nth-child(1) { animation-delay: 0.22s; }
        .field:nth-child(2) { animation-delay: 0.3s; }
        .field:nth-child(3) { animation-delay: 0.38s; }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .field label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #243447;
            letter-spacing: 0.01em;
        }

        .field input,
        .field select {
            width: 100%;
            height: 48px;
            padding: 0 0.95rem;
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            background: var(--input-bg);
            color: #122033;
            font-family: inherit;
            font-size: 0.95rem;
            text-transform: uppercase;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            appearance: none;
        }

        .field select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235a6b80' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.9rem center;
            padding-right: 2.4rem;
            cursor: pointer;
        }

        .field input::placeholder {
            color: #9aa8b8;
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 158, 255, 0.18);
        }

        .login-form button {
            margin-top: 0.65rem;
            height: 50px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e6fd9 0%, #3b9eff 55%, #5eb8ff 100%);
            color: #fff;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(30, 111, 217, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            animation: rise 0.55s ease-out 0.46s both;
        }

        .login-form button:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 28px rgba(30, 111, 217, 0.42);
        }

        .login-form button:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #7a8a9c;
            animation: rise 0.55s ease-out 0.52s both;
        }

        @media (max-width: 960px) {
            .page {
                grid-template-columns: 1fr;
                padding: 1.5rem;
                gap: 1.5rem;
            }

            .brand-side {
                max-width: none;
                padding: 0;
            }

            .hero {
                margin-top: 2rem;
            }

            .features {
                grid-template-columns: repeat(2, 1fr);
            }

            .login-side {
                min-height: auto;
                justify-content: stretch;
            }

            .login-frame {
                width: 100%;
                min-height: 520px;
            }
        }

        @media (max-width: 520px) {
            .features {
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            .login-panel {
                padding: 2rem 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <section class="brand-side">
            <div>
                <div class="logo">
                    <div class="logo-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 7h8.5a3.5 3.5 0 0 1 0 7H11" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M7 12h6.5a3 3 0 0 1 0 6H7" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M15 17l3 0 0-3" stroke="#b8e0ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <strong>Evo<span>Pro</span></strong>
                        <small>Système de Gestion</small>
                    </div>
                </div>

                <div class="hero">
                    <h1>Gérez <span class="hl">aujourd’hui</span>,<br>Réussissez <span class="hl">demain.</span></h1>
                    <p>
                        EvoPro est une solution complète de gestion d’entreprise :
                        suivez vos ventes, stocks, clients et finances depuis une seule plateforme moderne.
                    </p>
                </div>

                <div class="features">
                    <div class="feature">
                        <div class="feature-icon purple" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-3"/>
                            </svg>
                        </div>
                        <span>Tableaux de bord<br>en temps réel</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <span>Gestion des clients<br>et fournisseurs</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon cyan" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/>
                            </svg>
                        </div>
                        <span>Gestion des stocks<br>et produits</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon green" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/>
                            </svg>
                        </div>
                        <span>Facturation<br>et paiements</span>
                    </div>
                </div>
            </div>

            <div class="security">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span>Sécurisé • Fiable • Performant</span>
            </div>
        </section>

        <aside class="login-side">
            <div class="login-frame">
                <div class="login-panel">
                    <header>
                        <h2>Connexion</h2>
                        <p>Accédez à votre espace EvoPro</p>
                    </header>

                    <form class="login-form" method="post" action="/connexion" autocomplete="on">
                        @csrf

                        @if (session('status'))
                            <p class="form-status">{{ session('status') }}</p>
                        @endif

                        @if ($errors->any())
                            <div class="form-errors">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div class="field">
                            <label for="statue">Statue</label>
                            <select id="statue" name="statue" required>
                                <option value="" disabled selected>Sélectionner une statue</option>
                                <option value="admin">Administrateur</option>
                                <option value="manager">Manager</option>
                                <option value="comptable">Comptable</option>
                                <option value="vendeur">Vendeur</option>
                                <option value="stock">Responsable stock</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="login">Login</label>
                            <input
                                type="text"
                                id="login"
                                name="login"
                                placeholder="Identifiant ou e-mail"
                                required
                                autocomplete="username"
                            >
                        </div>

                        <div class="field">
                            <label for="password">Mot de Passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                        </div>

                        <button type="submit">Connexion</button>
                    </form>

                    <p class="login-footer">Espace réservé aux utilisateurs autorisés</p>
                </div>
            </div>
        </aside>
    </div>
</body>
</html>
