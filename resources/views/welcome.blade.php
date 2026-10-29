<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EvoPro — Système de Gestion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            background:
                linear-gradient(105deg, rgba(4, 12, 28, 0.22) 0%, rgba(6, 18, 38, 0.12) 45%, rgba(8, 22, 48, 0.28) 78%, rgba(4, 12, 28, 0.55) 100%),
                url('{{ asset('images/login-bg-auto-moto.jpg') }}') center / cover no-repeat;
        }

        .login-panel-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 30;
            width: 48px;
            height: 48px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(8, 18, 36, 0.62);
            color: #fff;
            display: grid;
            place-items: center;
            cursor: pointer;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.28);
        }

        .login-panel-toggle svg {
            width: 22px;
            height: 22px;
        }

        .login-panel-toggle svg[hidden],
        .login-form .field-eye svg[hidden] {
            display: none !important;
        }

        .bg-slogan {
            position: fixed;
            z-index: 1;
            top: 50%;
            right: 5vw;
            left: auto;
            transform: translateY(-50%);
            width: min(38vw, 640px);
            margin: 0;
            padding: 0 1rem;
            pointer-events: none;
            text-align: center;
            text-transform: none;
            color: #f7fbff;
            text-shadow:
                0 2px 0 rgba(255, 255, 255, 0.18),
                0 16px 42px rgba(0, 0, 0, 0.5),
                0 0 34px rgba(90, 170, 255, 0.35);
            transition: opacity 0.22s ease;
        }

        .bg-slogan-name {
            position: relative;
            margin: 0;
            font-size: clamp(2.6rem, 5.4vw, 5.4rem);
            font-weight: 700;
            letter-spacing: 0.16em;
            line-height: 1;
            color: #ffffff;
            text-shadow:
                0 0 6px rgba(255, 255, 255, 0.95),
                0 0 16px rgba(210, 230, 255, 0.9),
                0 0 34px rgba(90, 170, 255, 0.85),
                0 0 62px rgba(40, 120, 255, 0.55);
        }

        .bg-slogan-name::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 52%;
            z-index: -1;
            width: 130%;
            height: 220%;
            transform: translate(-50%, -50%);
            background: radial-gradient(ellipse at center, rgba(150, 200, 255, 0.55), rgba(40, 110, 230, 0.18) 42%, transparent 70%);
            filter: blur(6px);
            pointer-events: none;
        }

        .bg-slogan-ar {
            margin: 0.85rem 0 0;
            font-family: 'Cairo', 'Outfit', sans-serif;
            font-size: clamp(1.35rem, 2.6vw, 2.15rem);
            font-weight: 600;
            letter-spacing: 0;
            line-height: 1.45;
            direction: rtl;
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        .bg-slogan-fr {
            margin: 0.7rem 0 0;
            font-size: clamp(0.92rem, 1.5vw, 1.2rem);
            font-weight: 500;
            letter-spacing: 0.04em;
            line-height: 1.45;
            color: rgba(247, 251, 255, 0.88);
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        body.panel-open .bg-slogan {
            opacity: 0;
        }

        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 42% 38% at 58% 48%, rgba(4, 16, 36, 0.22), transparent 72%),
                radial-gradient(ellipse 32% 28% at 90% 16%, rgba(30, 100, 220, 0.12), transparent 65%);
            pointer-events: none;
        }

        .page::after {
            display: none;
        }

        .login-side {
            position: fixed;
            top: 50%;
            right: 2.5rem;
            z-index: 2;
            transform: translateY(-50%);
            transition: opacity 0.22s ease, visibility 0.22s ease;
        }

        .login-side.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
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
            color-scheme: light;
        }

        .field select option {
            background-color: #ffffff;
            color: #122033;
        }

        .field input::placeholder {
            color: #9aa8b8;
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 158, 255, 0.18);
        }

        .secret-wrap {
            position: relative;
        }

        .secret-wrap input {
            padding-right: 2.6rem;
        }

        .js-user {
            -webkit-text-security: disc;
        }

        .login-form .field-eye {
            position: absolute;
            top: 50%;
            right: 0.35rem;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            margin: 0;
            padding: 0;
            border: none;
            border-radius: 8px;
            background: transparent;
            box-shadow: none;
            color: #5a6b80;
            display: grid;
            place-items: center;
            cursor: pointer;
            animation: none;
        }

        .login-form .field-eye:hover,
        .login-form .field-eye:active {
            transform: translateY(-50%);
            filter: none;
            box-shadow: none;
            background: rgba(59, 158, 255, 0.1);
            color: #1e6fd9;
        }

        .login-form .field-eye svg {
            width: 18px;
            height: 18px;
        }

        .login-form button[type="submit"] {
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

        .login-form button[type="submit"]:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 28px rgba(30, 111, 217, 0.42);
        }

        .login-form button[type="submit"]:active {
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
            .login-side {
                right: 1rem;
                left: 1rem;
            }

            .login-frame {
                width: 100%;
            }
        }

        @media (max-width: 520px) {
            .login-panel {
                padding: 1.75rem 1.35rem 2rem;
            }

            .login-brand-logo {
                width: min(180px, 82%);
            }

            .bg-slogan-name {
                font-size: clamp(1.7rem, 8vw, 2.6rem);
                letter-spacing: 0.1em;
            }

            .bg-slogan-fr {
                font-size: 0.82rem;
            }
        }
    </style>
</head>
<body data-force-panel="{{ ($errors->any() || session('status')) ? '1' : '0' }}">
    <button type="button" class="login-panel-toggle" id="loginPanelToggle" title="Afficher le panneau de connexion" aria-label="Afficher le panneau de connexion" aria-pressed="false">
        <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M1 1l22 22"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
        </svg>
    </button>

    <div class="page">
        <div class="bg-slogan">
            <p class="bg-slogan-name">AUTO SIHAP</p>
            <p class="bg-slogan-ar" lang="ar" dir="rtl">قطع الغيار بثقة وجودة</p>
            <p class="bg-slogan-fr">Pièces auto &amp; moto — Qualité • Confiance • Prix compétitifs</p>
        </div>
        <aside class="login-side is-hidden" id="loginSide" aria-hidden="true">
            <div class="login-frame">
                <div class="login-panel">
                    <header>
                        <img
                            class="login-brand-logo"
                            src="{{ asset('images/logo-a2s-evopro.png') }}"
                            alt="A2S-EvoPro"
                        >
                    </header>

                    <form class="login-form" id="loginForm" method="post" action="/connexion" autocomplete="off" data-lpignore="true" data-1p-ignore>
                        @csrf
                        <input type="hidden" name="login" value="">
                        <input type="hidden" name="password" value="">

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
                            <select id="statue" name="statue" autocomplete="off">
                                <option value="" disabled selected>Choisir une statue…</option>
                                @foreach (\App\Support\UtilisateurHelper::statueLabels() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label for="loginVisible">Login</label>
                            <div class="secret-wrap">
                                <input
                                    type="text"
                                    id="loginVisible"
                                    class="js-user"
                                    value=""
                                    placeholder="Identifiant"
                                    required
                                    readonly
                                    autocomplete="off"
                                    data-lpignore="true"
                                    data-1p-ignore="true"
                                >
                                <button type="button" class="field-eye" data-target="loginVisible" data-show-label="Afficher le login" data-hide-label="Masquer le login" aria-label="Afficher le login" title="Afficher le login" aria-pressed="false">
                                    <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="field">
                            <label for="passwordVisible">Mot de Passe</label>
                            <div class="secret-wrap">
                                <input
                                    type="password"
                                    id="passwordVisible"
                                    class="js-pass"
                                    value=""
                                    placeholder="Mot de passe"
                                    required
                                    readonly
                                    autocomplete="new-password"
                                    data-lpignore="true"
                                    data-1p-ignore="true"
                                >
                                <button type="button" class="field-eye" data-target="passwordVisible" data-show-label="Afficher le mot de passe" data-hide-label="Masquer le mot de passe" aria-label="Afficher le mot de passe" title="Afficher le mot de passe" aria-pressed="false">
                                    <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit">Connexion</button>
                    </form>

                </div>
            </div>
        </aside>
    </div>
    <script>
        (function () {
            const panel = document.getElementById('loginSide');
            const toggle = document.getElementById('loginPanelToggle');
            const form = document.getElementById('loginForm');
            const user = document.querySelector('.js-user');
            const pass = document.querySelector('.js-pass');
            const statue = document.getElementById('statue');
            const maskCss = window.CSS && CSS.supports('-webkit-text-security', 'disc');
            const showIcon = toggle.querySelector('.icon-show');
            const hideIcon = toggle.querySelector('.icon-hide');

            function setSvgHidden(el, hide) {
                if (!el) return;
                if (hide) el.setAttribute('hidden', '');
                else el.removeAttribute('hidden');
            }

            if (!maskCss && user) {
                user.type = 'password';
                user.setAttribute('autocomplete', 'new-password');
            }

            function unlock(field) {
                field.addEventListener('focus', () => field.removeAttribute('readonly'));
                field.addEventListener('pointerdown', () => field.removeAttribute('readonly'));
            }
            if (user) unlock(user);
            if (pass) unlock(pass);

            function maskFields() {
                if (user) {
                    if (maskCss) user.style.webkitTextSecurity = 'disc';
                    else user.type = 'password';
                }
                if (pass) pass.type = 'password';
                document.querySelectorAll('.field-eye').forEach((btn) => {
                    btn.setAttribute('aria-pressed', 'false');
                    const label = btn.dataset.showLabel || 'Afficher';
                    btn.setAttribute('aria-label', label);
                    btn.title = label;
                    setSvgHidden(btn.querySelector('.icon-show'), false);
                    setSvgHidden(btn.querySelector('.icon-hide'), true);
                });
            }

            function clearFields() {
                if (user) user.value = '';
                if (pass) pass.value = '';
                if (statue) statue.selectedIndex = 0;
                const hiddenLogin = form.querySelector('input[type="hidden"][name="login"]');
                const hiddenPass = form.querySelector('input[type="hidden"][name="password"]');
                if (hiddenLogin) hiddenLogin.value = '';
                if (hiddenPass) hiddenPass.value = '';
                maskFields();
            }

            function setPanel(open) {
                panel.classList.toggle('is-hidden', !open);
                panel.setAttribute('aria-hidden', open ? 'false' : 'true');
                const label = open ? 'Masquer le panneau de connexion' : 'Afficher le panneau de connexion';
                toggle.title = label;
                toggle.setAttribute('aria-label', label);
                toggle.setAttribute('aria-pressed', open ? 'true' : 'false');
                document.body.classList.toggle('panel-open', open);
                setSvgHidden(showIcon, open);
                setSvgHidden(hideIcon, !open);
                if (!open) maskFields();
            }

            toggle.addEventListener('click', () => {
                setPanel(panel.classList.contains('is-hidden'));
            });

            document.querySelectorAll('.field-eye').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const field = document.getElementById(btn.dataset.target);
                    if (!field) return;
                    const revealed = btn.getAttribute('aria-pressed') === 'true';
                    const next = !revealed;
                    if (field.classList.contains('js-user') && maskCss) {
                        field.style.webkitTextSecurity = next ? 'none' : 'disc';
                    } else {
                        field.type = next ? 'text' : 'password';
                    }
                    btn.setAttribute('aria-pressed', next ? 'true' : 'false');
                    const label = next ? btn.dataset.hideLabel : btn.dataset.showLabel;
                    btn.setAttribute('aria-label', label);
                    btn.title = label;
                    setSvgHidden(btn.querySelector('.icon-show'), next);
                    setSvgHidden(btn.querySelector('.icon-hide'), !next);
                });
            });

            form.addEventListener('submit', () => {
                form.querySelector('input[type="hidden"][name="login"]').value = user.value;
                form.querySelector('input[type="hidden"][name="password"]').value = pass.value;
                user.value = '';
                pass.value = '';
            });

            window.addEventListener('pageshow', (event) => {
                clearFields();
                const showErrors = !event.persisted && document.body.dataset.forcePanel === '1';
                setPanel(showErrors);
            });
        })();
    </script>
</body>
</html>
