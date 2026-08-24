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
            grid-template-columns: 1fr minmax(320px, 420px);
            gap: 2rem;
            align-items: center;
            padding: 2.5rem 3.5rem 7.5rem;
            background:
                linear-gradient(105deg, rgba(4, 12, 28, 0.28) 0%, rgba(6, 18, 38, 0.18) 40%, rgba(8, 22, 48, 0.22) 70%, rgba(4, 12, 28, 0.4) 100%),
                url('{{ asset('images/login-bg.png') }}') center / cover no-repeat;
        }

        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 40% 35% at 55% 48%, rgba(4, 16, 36, 0.35), transparent 70%),
                radial-gradient(ellipse 35% 30% at 88% 18%, rgba(30, 100, 220, 0.1), transparent 65%);
            pointer-events: none;
        }

        .page::after {
            display: none;
        }

        .slogan-center {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.5rem 2rem;
            padding-left: clamp(4rem, 18vw, 12rem);
            pointer-events: none;
        }

        .slogan-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.15rem;
            max-width: 42rem;
        }

        .slogan-line {
            width: min(280px, 55vw);
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, rgba(126, 196, 255, 0.95), transparent);
            box-shadow: 0 0 14px rgba(94, 176, 255, 0.55);
        }

        .slogan-words {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.55rem;
            width: 100%;
        }

        .slogan-word {
            margin: 0;
            font-size: clamp(2.4rem, 5.6vw, 4.4rem);
            font-weight: 700;
            letter-spacing: 0.14em;
            line-height: 1.05;
            background: linear-gradient(100deg, #ffffff 0%, #9ad4ff 35%, #ffffff 55%, #5eb0ff 100%);
            background-size: 220% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            filter: drop-shadow(0 4px 18px rgba(0, 0, 0, 0.35)) drop-shadow(0 0 22px rgba(59, 158, 255, 0.35));
            animation: sloganShine 5s ease-in-out infinite;
        }

        .slogan-word:nth-child(2) { animation-delay: 0.2s; }
        .slogan-word:nth-child(3) { animation-delay: 0.4s; }
        .slogan-word:nth-child(4) { animation-delay: 0.6s; }

        .slogan-word span {
            color: var(--accent-soft);
            -webkit-text-fill-color: var(--accent-soft);
            background: none;
            filter: drop-shadow(0 0 12px rgba(94, 176, 255, 0.7));
        }

        @keyframes sloganShine {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 100% center; }
        }

        .features-bottom {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.1rem 3.5rem 1.4rem;
            background: linear-gradient(0deg, rgba(4, 12, 28, 0.82), rgba(4, 12, 28, 0.45) 70%, transparent);
            backdrop-filter: blur(4px);
        }

        .feature {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.55rem;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
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
            line-height: 1.3;
            color: rgba(255, 255, 255, 0.88);
            font-weight: 500;
            letter-spacing: 0.06em;
        }

        .login-side {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            min-height: auto;
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
            padding: 2.2rem 2.2rem 2.4rem;
            color: var(--label);
        }

        .login-panel header {
            margin-bottom: 1.25rem;
            text-align: center;
            animation: rise 0.6s ease-out 0.15s both;
        }

        .login-brand-logo {
            width: min(260px, 88%);
            height: auto;
            display: block;
            margin: 0 auto;
            object-fit: contain;
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
                padding: 1.5rem 1.5rem 8.5rem;
                gap: 1.25rem;
            }

            .slogan-center {
                order: 1;
                padding: 0.75rem 0.5rem 0;
                padding-left: clamp(1.5rem, 12vw, 4rem);
            }

            .slogan-word {
                font-size: clamp(1.55rem, 7.5vw, 2.35rem);
                letter-spacing: 0.1em;
            }

            .slogan-block {
                gap: 0.85rem;
            }

            .slogan-line {
                width: min(200px, 50vw);
            }

            .login-side {
                order: 2;
                justify-content: stretch;
            }

            .login-frame {
                width: 100%;
            }

            .features-bottom {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                padding: 0.9rem 1.25rem 1.1rem;
                gap: 0.55rem;
            }

            .feature span {
                font-size: 0.62rem;
            }
        }

        @media (max-width: 520px) {
            .features-bottom {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.85rem;
            }

            .login-panel {
                padding: 1.75rem 1.35rem 2rem;
            }

            .login-brand-logo {
                width: min(180px, 82%);
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="slogan-center">
            <div class="slogan-block">
                <div class="slogan-line" aria-hidden="true"></div>
                <div class="slogan-words">
                    <p class="slogan-word">Concevoir<span>.</span></p>
                    <p class="slogan-word">Développer<span>.</span></p>
                    <p class="slogan-word">Gérer<span>.</span></p>
                    <p class="slogan-word">Évoluer<span>.</span></p>
                </div>
                <div class="slogan-line" aria-hidden="true"></div>
            </div>
        </div>

        <aside class="login-side">
            <div class="login-frame">
                <div class="login-panel">
                    <header>
                        <img
                            class="login-brand-logo"
                            src="{{ asset('images/logo-a2s-evopro.png') }}"
                            alt="A2S-EvoPro"
                        >
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
                                @foreach (\App\Support\UtilisateurHelper::statueLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('statue', 'administrateur') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label for="login">Login</label>
                            <input
                                type="text"
                                id="login"
                                name="login"
                                value="{{ old('login', 'zerraguiabdelilah') }}"
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
                                value="{{ old('password', '0661755048') }}"
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

        <div class="features-bottom" aria-label="Piliers A2S-EvoPro">
            <div class="feature">
                <div class="feature-icon purple" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                    </svg>
                </div>
                <span>Concevoir</span>
            </div>
            <div class="feature">
                <div class="feature-icon blue" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                    </svg>
                </div>
                <span>Développer</span>
            </div>
            <div class="feature">
                <div class="feature-icon cyan" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </div>
                <span>Gérer</span>
            </div>
            <div class="feature">
                <div class="feature-icon green" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/>
                    </svg>
                </div>
                <span>Évoluer</span>
            </div>
        </div>
    </div>
</body>
</html>
