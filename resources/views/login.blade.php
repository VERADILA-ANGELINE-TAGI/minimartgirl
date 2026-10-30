
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fff7fb">

    <title>
        {{ ($registrationMode ?? false) ? 'Daftar akun' : 'Masuk' }} - MINIMARTGIRL
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            --pink: #e94b91;
            --pink-dark: #c92f76;
            --ink: #17284c;
            --muted: #858da6;
            --barca-blue: #0645a0;
            --barca-red: #a50044;
            --gold: #edbb00;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 28px 18px;
            color: var(--ink);
            background:
                radial-gradient(ellipse at 10% 10%, #ffe0ef, transparent 36%),
                radial-gradient(ellipse at 90% 90%, #e4edff, transparent 35%),
                #fffafd;
            font-family: Inter, system-ui, -apple-system,
                         BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .login-shell {
            width: min(100%, 1120px);
            min-height: 650px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.9);
            border-radius: 28px;
            background: white;
            box-shadow: 0 25px 70px rgba(34, 31, 72, .13);
            animation: enter .6s ease both;
        }

        /* PANEL KIRI: TEMA BARCELONA */
        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 650px;
            overflow: hidden;
            padding: 38px;
            color: white;
            isolation: isolate;
            background:
                linear-gradient(
                    180deg,
                    rgba(9, 23, 65, .35),
                    rgba(5, 18, 53, .75)
                ),
                url('/images/barcelona-stadium.png')
                center / cover no-repeat;
                linear-gradient(
                    135deg,
                    #1749a4 0%,
                    #172b68 42%,
                    #a50044 75%,
                    #e94b91 100%
                );
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            z-index: -1;
            width: 500px;
            height: 500px;
            top: 130px;
            right: -220px;
            border: 1px solid rgba(255,255,255,.28);
            border-radius: 50%;
            box-shadow:
                0 0 0 35px rgba(255,255,255,.035),
                0 0 0 75px rgba(255,255,255,.025);
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            z-index: -1;
            width: 360px;
            height: 360px;
            left: -150px;
            bottom: -180px;
            border-radius: 50%;
            background: rgba(237,187,0,.12);
            filter: blur(8px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .9rem;
            font-weight: 850;
            letter-spacing: .13em;
        }

        .brand-mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 14px;
            background: rgba(255,255,255,.16);
            font-size: 1.3rem;
        }

        .brand-copy {
            position: relative;
            z-index: 2;
            max-width: 430px;
            margin: auto 0 25px;
        }

        .eyebrow {
            margin-bottom: 15px;
            color: #ffd6e8;
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .brand-copy h1 {
            margin: 0 0 18px;
            font-size: clamp(2.4rem, 4vw, 3.5rem);
            font-weight: 900;
            letter-spacing: -.05em;
            line-height: 1.04;
        }

        .brand-copy h1 span {
            color: #ffd5e8;
        }

        .brand-copy p {
            max-width: 330px;
            color: rgba(255,255,255,.86);
            line-height: 1.75;
        }

        .barca-art {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin: 15px 0 30px;
            text-align: center;
        }

        .barca-crest {
            display: grid;
            width: 150px;
            height: 165px;
            place-items: center;
            clip-path: polygon(
                0 0, 100% 0, 96% 68%,
                50% 100%, 4% 68%
            );
            border: 4px solid var(--gold);
            color: white;
            background:
                linear-gradient(
                    90deg,
                    #0645a0 0 25%,
                    #a50044 25% 50%,
                    #0645a0 50% 75%,
                    #a50044 75%
                );
            box-shadow: 0 12px 30px rgba(0,0,0,.22);
            text-shadow: 2px 3px 4px rgba(0,0,0,.35);
        }

        .barca-crest strong {
            font-size: 2rem;
            letter-spacing: .08em;
        }

        .barca-crest span {
            font-size: 2.4rem;
        }

        .visca {
            margin: 0;
            color: #ffe5a2;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: .1em;
        }

        .stadium-note {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 14px;
            background: rgba(255,255,255,.12);
            font-size: .78rem;
            font-weight: 700;
            backdrop-filter: blur(8px);
        }

        /* PANEL KANAN: FORM */
        .form-panel {
            display: flex;
            align-items: center;
            padding: 55px clamp(28px, 5vw, 65px);
            background:
                radial-gradient(ellipse at 100% 0%, #fff0f7, transparent 38%),
                #fff;
        }

        .form-content {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }

        .form-kicker {
            margin-bottom: 12px;
            color: var(--pink-dark);
            font-size: .73rem;
            font-weight: 850;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .form-content h2 {
            margin-bottom: 10px;
            font-size: 2.2rem;
            font-weight: 850;
            letter-spacing: -.045em;
        }

        .form-intro {
            margin-bottom: 32px;
            color: var(--muted);
            line-height: 1.65;
        }

        .form-label {
            color: #48465a;
            font-size: .85rem;
            font-weight: 750;
        }

        .form-control {
            min-height: 51px;
            border: 1px solid #e2e5ef;
            border-radius: 12px;
            background: #fcfbfe;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #ec83b1;
            background: white;
            box-shadow: 0 0 0 4px rgba(233,75,145,.11);
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 55px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 9px;
            color: #858da6;
            background: transparent;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-toggle:hover {
            color: var(--pink-dark);
            background: #fff0f7;
        }

        .btn-login {
            min-height: 53px;
            border: 0;
            border-radius: 12px;
            color: white;
            background: linear-gradient(100deg, #e94b91, #c92f76);
            box-shadow: 0 10px 22px rgba(211,54,125,.2);
            font-weight: 800;
            transition: .2s ease;
        }

        .btn-login:hover {
            color: white;
            background: linear-gradient(100deg, #d83c83, #a50044);
            transform: translateY(-2px);
        }

        .register-link {
            color: var(--pink-dark);
            font-weight: 750;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        .form-footnote {
            margin-top: 24px;
            color: #aaa5b2;
            font-size: .75rem;
            text-align: center;
        }

        @keyframes enter {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 760px) {
            body {
                padding: 14px;
            }

            .login-shell {
                max-width: 520px;
                grid-template-columns: 1fr;
                border-radius: 22px;
            }

            .brand-panel {
                min-height: 420px;
                padding: 25px;
            }

            .brand-copy {
                margin: 30px 0 10px;
            }

            .brand-copy h1 {
                font-size: 2.5rem;
            }

            .barca-art {
                margin: 20px 0;
            }

            .barca-crest {
                width: 110px;
                height: 120px;
            }

            .barca-crest strong {
                font-size: 1.5rem;
            }

            .barca-crest span {
                font-size: 1.8rem;
            }

            .form-panel {
                padding: 38px 26px;
            }

            .form-content h2 {
                font-size: 1.8rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>
<main class="login-shell">

    <section class="brand-panel" aria-label="MINIMARTGIRL Barcelona">

        <div class="brand">
            <span class="brand-mark" aria-hidden="true">🛍️</span>
            <span>MINIMARTGIRL</span>
        </div>

        <div class="brand-copy">
            <p class="eyebrow">Selamat datang kembali</p>

            <h1>
                Kasir<br>
                MINIMART<span>GIRL</span><br>
                <span>siap menemani 🫶</span>
            </h1>

            <p>
                Belanja lebih mudah, cepat, dan nyaman setiap hari.
                Semangat melayani pelanggan!
            </p>
        </div>

        <div class="barca-art" aria-label="Tema Barcelona">
            <div class="barca-crest">
                <div>
                    <strong>FCB</strong><br>
                    <span>⚽</span>
                </div>
            </div>

            <p class="visca">❤️💙 VISCA EL BARÇA! ❤️💙</p>

            <div class="stadium-note">
                ⚽ Més que un club
            </div>
        </div>

    </section>

    <section class="form-panel">
        <div class="form-content">

            <p class="form-kicker">
                {{ ($registrationMode ?? false) ? 'Buat akun kasir' : 'Selamat datang kembali' }}
            </p>

            <h2>
                {{ ($registrationMode ?? false) ? 'Daftar kasir' : 'Masuk kasir' }}
            </h2>

            <p class="form-intro">
                {{ ($registrationMode ?? false)
                    ? 'Buat akun dengan alamat surel sendiri untuk menggunakan ruang kasir.'
                    : 'Masukkan akun untuk melanjutkan ke dashboard.' }}
            </p>

            @if ($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form
                action="{{ ($registrationMode ?? false) ? route('register.store') : route('login.process') }}"
                method="POST"
            >
                @csrf

                @if ($registrationMode ?? false)
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama kasir</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Nama lengkap kasir"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                            maxlength="255"
                            autofocus
                        >
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat surel</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="nama@contoh.id"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        @if ($registrationMode ?? false)
                            maxlength="255"
                        @else
                            autofocus
                        @endif
                    >
                </div>

                <div class="{{ ($registrationMode ?? false) ? 'mb-3' : 'mb-4' }}">
                    <label for="password" class="form-label">Kata sandi</label>

                    <div class="password-field">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="{{ ($registrationMode ?? false) ? 'Buat kata sandi minimal 8 karakter' : 'Masukkan kata sandi' }}"
                            required
                            autocomplete="{{ ($registrationMode ?? false) ? 'new-password' : 'current-password' }}"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="password-toggle"
                            aria-label="Tampilkan kata sandi"
                            aria-pressed="false"
                            aria-controls="password"
                            title="Tampilkan kata sandi"
                        >
                            <span id="password-eye" aria-hidden="true">👁️</span>
                        </button>
                    </div>
                </div>

                @if ($registrationMode ?? false)
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            Konfirmasi kata sandi
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi kata sandi"
                            required
                            autocomplete="new-password"
                        >
                    </div>
                @endif

                <button type="submit" class="btn btn-login w-100">
                    {{ ($registrationMode ?? false) ? 'Buat akun' : 'Masuk ke dashboard' }}
                    <span aria-hidden="true">→</span>
                </button>
            </form>

            <p class="text-center mt-3 mb-0">
                @if ($registrationMode ?? false)
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="register-link">
                        Masuk di sini
                    </a>
                @else
                    Belum punya akun kasir?
                    <a href="{{ route('register') }}" class="register-link">
                        Daftar sekarang
                    </a>
                @endif
            </p>

            <p class="form-footnote">
                MINIMARTGIRL · Kasir dengan semangat Barcelona ❤️💙
            </p>

        </div>
    </section>

</main>

<script>
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('password-toggle');
    const passwordEye = document.getElementById('password-eye');

    if (passwordInput && passwordToggle && passwordEye) {
        passwordToggle.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';

            passwordInput.type = isHidden ? 'text' : 'password';

            passwordToggle.setAttribute(
                'aria-pressed',
                String(isHidden)
            );

            passwordToggle.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'
            );

            passwordToggle.title = isHidden
                ? 'Sembunyikan kata sandi'
                : 'Tampilkan kata sandi';

            passwordEye.textContent = isHidden ? '🙈' : '👁️';
        });
    }
</script>

</body>
</html>
