<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fff7fb">
    <title>{{ ($registrationMode ?? false) ? 'Daftar akun' : 'Masuk' }} - MINIMARTGIRL</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            color-scheme: light;
            --pink: #e94b91;
            --pink-dark: #c92f76;
            --ink: #29233a;
            --muted: #858094;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            overflow-x: hidden;
            padding: 32px 20px;
            color: var(--ink);
            background:
                radial-gradient(ellipse at 12% 12%, rgba(255, 203, 226, .62), transparent 32%),
                radial-gradient(ellipse at 88% 86%, rgba(255, 226, 196, .52), transparent 30%),
                #fff9fc;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .login-shell {
            width: min(100%, 1040px);
            min-height: 620px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .9);
            border-radius: 30px;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 28px 80px rgba(99, 54, 79, .13);
            animation: enter .75s cubic-bezier(.2, .8, .2, 1) both;
        }

        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 620px;
            overflow: hidden;
            padding: 48px;
            color: white;
            background: linear-gradient(145deg, #ef6ca5 0%, #d83f83 58%, #b92a70 100%);
            isolation: isolate;
        }

        .brand-panel::before,
        .brand-panel::after {
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 50%;
            content: "";
        }

        .brand-panel::before {
            width: 390px;
            height: 390px;
            right: -155px;
            top: 75px;
        }

        .brand-panel::after {
            width: 500px;
            height: 500px;
            right: -210px;
            top: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .88rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .brand-mark {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .38);
            border-radius: 14px;
            background: rgba(255, 255, 255, .18);
            font-size: 1.25rem;
            backdrop-filter: blur(12px);
        }

        .brand-copy {
            position: relative;
            z-index: 1;
            max-width: 360px;
            margin-bottom: 28px;
        }

        .brand-copy .eyebrow {
            margin-bottom: 14px;
            color: rgba(255, 255, 255, .76);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .brand-copy h1 {
            margin: 0 0 16px;
            font-size: clamp(2.15rem, 4vw, 3.25rem);
            font-weight: 800;
            letter-spacing: -.055em;
            line-height: 1.04;
        }

        .brand-copy p {
            max-width: 300px;
            margin: 0;
            color: rgba(255, 255, 255, .82);
            line-height: 1.7;
        }

        .art-stage {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            pointer-events: none;
            perspective: 900px;
        }

        .orb {
            position: absolute;
            width: 225px;
            height: 225px;
            margin: -55px 0 0 60px;
            border: 1px solid rgba(255, 255, 255, .45);
            border-radius: 50%;
            background: radial-gradient(circle at 32% 26%, rgba(255,255,255,.75), rgba(255,255,255,.13) 22%, rgba(255,255,255,.04) 62%);
            box-shadow: inset -18px -18px 50px rgba(124, 17, 74, .15), 0 32px 70px rgba(105, 14, 63, .12);
            transform-style: preserve-3d;
            animation: orb-float 7s ease-in-out infinite;
        }

        .orbit {
            position: absolute;
            width: 290px;
            height: 110px;
            margin: -55px 0 0 60px;
            border: 1px solid rgba(255, 255, 255, .58);
            border-radius: 50%;
            transform: rotate(-26deg) rotateX(64deg);
        }

        .orbit::after {
            position: absolute;
            top: 10px;
            right: 23px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff3bc;
            box-shadow: 0 0 24px rgba(255, 243, 188, .8);
            content: "";
        }

        .floating-note {
            position: absolute;
            right: 11%;
            bottom: 30%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 16px;
            background: rgba(255, 255, 255, .16);
            box-shadow: 0 18px 35px rgba(99, 19, 62, .16);
            backdrop-filter: blur(12px);
            font-size: .78rem;
            font-weight: 700;
            animation: note-float 5s ease-in-out infinite;
        }

        .floating-note span {
            display: grid;
            width: 28px;
            height: 28px;
            place-items: center;
            border-radius: 9px;
            background: rgba(255, 255, 255, .23);
        }

        .form-panel {
            display: flex;
            align-items: center;
            padding: 64px clamp(32px, 6vw, 76px);
        }

        .form-content {
            width: 100%;
            max-width: 390px;
            margin: auto;
        }

        .form-kicker {
            margin-bottom: 12px;
            color: var(--pink-dark);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .form-content h2 {
            margin-bottom: 10px;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -.045em;
        }

        .form-intro {
            margin-bottom: 32px;
            color: var(--muted);
        }

        .form-label {
            color: #504a5f;
            font-size: .82rem;
            font-weight: 700;
        }

        .form-control {
            min-height: 50px;
            border: 1px solid #ece8f0;
            border-radius: 12px;
            background: #fcfbfd;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #ec83b1;
            background: white;
            box-shadow: 0 0 0 4px rgba(233, 75, 145, .11);
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 54px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 0;
            border-radius: 9px;
            color: #858094;
            background: transparent;
            transform: translateY(-50%);
            transition: color .18s ease, background .18s ease;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            color: var(--pink-dark);
            background: #fff1f7;
        }

        .password-toggle:focus-visible {
            outline: 2px solid var(--pink);
            outline-offset: 1px;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
        }

        .password-toggle .icon-eye-off {
            display: none;
        }

        .btn-login {
            min-height: 52px;
            border: 0;
            border-radius: 12px;
            color: white;
            background: linear-gradient(100deg, #e94b91, #ce367d);
            box-shadow: 0 10px 22px rgba(211, 54, 125, .22);
            font-weight: 750;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn-login:hover {
            color: white;
            box-shadow: 0 14px 26px rgba(211, 54, 125, .3);
            transform: translateY(-2px);
        }

        .form-footnote {
            margin: 22px 0 0;
            color: #aaa5b2;
            font-size: .76rem;
            text-align: center;
        }

        @keyframes enter {
            from { opacity: 0; transform: translateY(18px) scale(.985); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes orb-float {
            0%, 100% { transform: translate3d(0, 0, 0) rotateY(-18deg) rotateX(8deg); }
            50% { transform: translate3d(0, -18px, 24px) rotateY(12deg) rotateX(-6deg); }
        }

        @keyframes note-float {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }

        @media (max-width: 760px) {
            body {
                padding: 18px 14px;
            }

            .login-shell {
                max-width: 500px;
                min-height: 0;
                grid-template-columns: 1fr;
                border-radius: 24px;
            }

            .brand-panel {
                min-height: 230px;
                padding: 26px 28px;
            }

            .brand-copy {
                margin: 36px 0 0;
            }

            .brand-copy h1 {
                max-width: 260px;
                font-size: 2rem;
            }

            .brand-copy p,
            .brand-copy .eyebrow {
                display: none;
            }

            .orb {
                width: 150px;
                height: 150px;
                margin: 35px -170px 0 0;
            }

            .orbit {
                width: 195px;
                height: 78px;
                margin: 35px -170px 0 0;
            }

            .floating-note {
                right: 8%;
                bottom: 16%;
                padding: 9px 12px;
                font-size: .7rem;
            }

            .form-panel {
                padding: 38px 28px 32px;
            }

            .form-content h2 {
                font-size: 1.75rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
<main class="login-shell">
    <section class="brand-panel" aria-label="MINIMARTGIRL">
        <div class="brand">
            <span class="brand-mark" aria-hidden="true">🛍️</span>
            <span>MINIMARTGIRL</span>
        </div>

        <div class="art-stage" aria-hidden="true">
            <div class="orb"></div>
            <div class="orbit"></div>
            <div class="floating-note"><span>✦</span> Kasir lebih praktis</div>
        </div>

        <div class="brand-copy">
            <p class="eyebrow">Teman terbaik tokomu</p>
            <h1>Semua urusan toko, jadi mudah.</h1>
            <p>Masuk ke ruang kasir untuk mulai melayani pelanggan dengan nyaman.</p>
        </div>
    </section>

    <section class="form-panel">
        <div class="form-content">
            <p class="form-kicker">{{ ($registrationMode ?? false) ? 'Buat akun kasir' : 'Selamat datang kembali' }}</p>
            <h2>{{ ($registrationMode ?? false) ? 'Daftar kasir' : 'Masuk kasir' }}</h2>
            <p class="form-intro">
                {{ ($registrationMode ?? false) ? 'Buat akun dengan alamat surel sendiri untuk menggunakan ruang kasir.' : 'Masukkan akun untuk melanjutkan ke dasbor.' }}
            </p>

            @if ($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ ($registrationMode ?? false) ? route('register.store') : route('login.process') }}" method="POST">
                @csrf

                @if ($registrationMode ?? false)
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama kasir</label>
                        <input id="name" type="text" name="name" class="form-control"
                               placeholder="Nama lengkap kasir" value="{{ old('name') }}" required
                               autocomplete="name" maxlength="255" autofocus>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat surel</label>
                    <input id="email" type="email" name="email" class="form-control"
                           placeholder="nama@contoh.id" value="{{ old('email') }}" required
                           autocomplete="username" @if ($registrationMode ?? false) maxlength="255" @else autofocus @endif>
                </div>

                <div class="{{ ($registrationMode ?? false) ? 'mb-3' : 'mb-4' }}">
                    <label for="password" class="form-label">Kata sandi</label>
                    <div class="password-field">
                        <input id="password" type="password" name="password" class="form-control"
                               placeholder="{{ ($registrationMode ?? false) ? 'Buat kata sandi minimal 8 karakter' : 'Masukkan kata sandi' }}"
                               required autocomplete="{{ ($registrationMode ?? false) ? 'new-password' : 'current-password' }}">
                        <button type="button" class="password-toggle" id="password-toggle"
                                aria-label="Tampilkan kata sandi" aria-pressed="false"
                                aria-controls="password" title="Tampilkan kata sandi">
                            <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m3 3 18 18"></path>
                                <path d="M10.6 5.1A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a14 14 0 0 1-3.1 3.8"></path>
                                <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path>
                                <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                @if ($registrationMode ?? false)
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control" placeholder="Ulangi kata sandi" required
                               autocomplete="new-password">
                    </div>
                @endif

                <button type="submit" class="btn btn-login w-100">
                    {{ ($registrationMode ?? false) ? 'Buat akun' : 'Masuk ke dasbor' }}
                    <span aria-hidden="true">→</span>
                </button>
            </form>

            <p class="text-center mt-3 mb-0">
                @if ($registrationMode ?? false)
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
                @else
                    Belum punya akun kasir? <a href="{{ route('register') }}">Daftar sekarang</a>
                @endif
            </p>

            <p class="form-footnote">MINIMARTGIRL · Ruang kerja kasir</p>
        </div>
    </section>
</main>
<script>
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('password-toggle');
    const eyeIcon = passwordToggle.querySelector('.icon-eye');
    const eyeOffIcon = passwordToggle.querySelector('.icon-eye-off');

    passwordToggle.addEventListener('click', () => {
        const isVisible = passwordInput.type === 'text';

        passwordInput.type = isVisible ? 'password' : 'text';
        passwordToggle.setAttribute('aria-pressed', String(!isVisible));
        passwordToggle.setAttribute('aria-label', isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
        passwordToggle.title = isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi';
        eyeIcon.style.display = isVisible ? '' : 'none';
        eyeOffIcon.style.display = isVisible ? 'none' : '';
    });
</script>
</body>
</html>
