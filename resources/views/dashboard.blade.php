<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fff8fb">
    <title>Dasbor - MINIMARTGIRL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
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
            overflow-x: hidden;
            color: var(--ink);
            background:
                radial-gradient(ellipse at 88% 12%, rgba(255, 211, 230, .55), transparent 28%),
                radial-gradient(ellipse at 5% 90%, rgba(255, 229, 204, .5), transparent 28%),
                #fff9fc;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-shell {
            width: min(100% - 48px, 1120px);
            margin: 0 auto;
            padding: 32px 0 56px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 38px;
            animation: fade-down .55s ease both;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: inherit;
            font-size: .9rem;
            font-weight: 850;
            letter-spacing: .1em;
            text-decoration: none;
        }

        .brand:focus-visible {
            border-radius: 18px;
            outline: 3px solid var(--pink-dark);
            outline-offset: 6px;
        }

        .brand-mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border: 1px solid #f7d9e7;
            border-radius: 15px;
            background: white;
            box-shadow: 0 8px 24px rgba(99, 54, 79, .08);
            font-size: 1.25rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #5a5366;
            font-size: .85rem;
            font-weight: 650;
        }

        .user-avatar {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 13px;
            color: var(--pink-dark);
            background: #fde7f1;
            font-weight: 800;
        }

        .btn-logout {
            padding: 10px 16px;
            border: 1px solid #f0dce6;
            border-radius: 11px;
            color: #8f3d68;
            background: rgba(255, 255, 255, .72);
            font-size: .82rem;
            font-weight: 700;
            transition: background .2s ease, transform .2s ease;
        }

        .btn-logout:hover {
            color: var(--pink-dark);
            background: white;
            transform: translateY(-1px);
        }

        .welcome-card {
            position: relative;
            display: grid;
            min-height: 430px;
            grid-template-columns: 1.05fr .95fr;
            align-items: center;
            overflow: hidden;
            padding: 58px 64px;
            border: 1px solid rgba(255, 255, 255, .75);
            border-radius: 30px;
            color: white;
            background: linear-gradient(116deg, #ed65a0, #d63b82 57%, #b92d75);
            box-shadow: 0 28px 70px rgba(177, 57, 112, .2);
            isolation: isolate;
            animation: rise-in .75s .08s cubic-bezier(.2, .8, .2, 1) both;
        }

        .welcome-card::before,
        .welcome-card::after {
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, .17);
            border-radius: 50%;
            content: "";
        }

        .welcome-card::before {
            width: 520px;
            height: 520px;
            top: -290px;
            right: -60px;
        }

        .welcome-card::after {
            width: 670px;
            height: 670px;
            top: -365px;
            right: -130px;
        }

        .welcome-copy {
            position: relative;
            z-index: 2;
            max-width: 490px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 22px;
            color: rgba(255, 255, 255, .78);
            font-size: .73rem;
            font-weight: 800;
            letter-spacing: .17em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            width: 24px;
            height: 1px;
            background: currentColor;
            content: "";
        }

        .welcome-copy h1 {
            margin: 0 0 16px;
            font-size: clamp(2.5rem, 5vw, 4.1rem);
            font-weight: 850;
            letter-spacing: -.065em;
            line-height: .99;
        }

        .welcome-copy p {
            max-width: 390px;
            margin: 0 0 28px;
            color: rgba(255, 255, 255, .82);
            line-height: 1.75;
        }

        .welcome-pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, .27);
            border-radius: 999px;
            background: rgba(255, 255, 255, .13);
            font-size: .78rem;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }

        .welcome-pill span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #bbf7d0;
            box-shadow: 0 0 12px rgba(187, 247, 208, .7);
        }

        /* TOMBOL MULAI TRANSAKSI */

        .transaction-button-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn-start-transaction {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 20px;
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 13px;
            color: #c92f76;
            background: white;
            text-decoration: none;
            font-size: .85rem;
            font-weight: 800;
            box-shadow: 0 10px 25px rgba(103, 18, 62, .15);
            transition: .25s ease;
        }

        .btn-start-transaction:hover {
            color: #c92f76;
            background: #fff8fb;
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(103, 18, 62, .22);
        }

        .art-stage {
            position: relative;
            display: grid;
            min-height: 315px;
            place-items: center;
            perspective: 1000px;
        }

        .halo {
            position: absolute;
            width: 290px;
            height: 290px;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 50%;
            transform: rotateX(65deg) rotateZ(-20deg);
        }

        .halo::before,
        .halo::after {
            position: absolute;
            inset: 21px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: inherit;
            content: "";
        }

        .halo::after {
            inset: 43px;
        }

        .glass-card {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 13px;
            border: 1px solid rgba(255, 255, 255, .4);
            background: rgba(255, 255, 255, .17);
            box-shadow: 0 25px 50px rgba(103, 18, 62, .2);
            backdrop-filter: blur(14px);
            transform-style: preserve-3d;
        }

        .main-glass-card {
            z-index: 1;
            width: 220px;
            padding: 20px;
            border-radius: 23px;
            color: inherit;
            cursor: pointer;
            text-decoration: none;
            transform: rotateY(-13deg) rotateX(9deg) rotateZ(-5deg);
            animation: float-main 6s ease-in-out infinite;
            transition: background .2s ease, box-shadow .2s ease;
        }

        .main-glass-card:hover {
            color: inherit;
            background: rgba(255, 255, 255, .25);
            box-shadow: 0 30px 58px rgba(103, 18, 62, .28);
        }

        .main-glass-card:focus-visible {
            border-radius: 23px;
            outline: 3px solid white;
            outline-offset: 5px;
        }

        .mini-glass-card {
            right: 0;
            bottom: 39px;
            padding: 13px 16px;
            border-radius: 17px;
            font-size: .76rem;
            font-weight: 750;
            transform: rotateY(12deg) rotateX(-4deg) rotateZ(4deg);
            animation: float-mini 5s ease-in-out infinite;
        }

        .cart-icon {
            display: grid;
            width: 52px;
            height: 52px;
            place-items: center;
            border-radius: 17px;
            color: #d83d83;
            background: rgba(255, 255, 255, .93);
            font-size: 1.55rem;
        }

        .glass-caption small {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .71);
            font-size: .68rem;
        }

        .glass-caption strong {
            font-size: .98rem;
        }

        .mini-icon {
            display: grid;
            width: 31px;
            height: 31px;
            place-items: center;
            border-radius: 11px;
            background: rgba(255, 255, 255, .2);
        }

        .sparkle {
            position: absolute;
            color: #fff4c6;
            font-size: 1.35rem;
            animation: sparkle 3s ease-in-out infinite;
        }

        .sparkle-one {
            top: 29px;
            right: 18%;
        }

        .sparkle-two {
            bottom: 45px;
            left: 10%;
            font-size: .9rem;
            animation-delay: 1s;
        }

        .dashboard-footer {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 25px 4px 0;
            color: #a09aa8;
            font-size: .75rem;
            animation: rise-in .65s .25s ease both;
        }

        /* ANIMASI */

        @keyframes fade-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float-main {
            0%, 100% {
                transform: translateY(0) rotateY(-13deg) rotateX(9deg) rotateZ(-5deg);
            }

            50% {
                transform: translateY(-13px) rotateY(-5deg) rotateX(14deg) rotateZ(-2deg);
            }
        }

        @keyframes float-mini {
            0%, 100% {
                transform: translateY(0) rotateY(12deg) rotateX(-4deg) rotateZ(4deg);
            }

            50% {
                transform: translateY(-9px) rotateY(4deg) rotateX(2deg) rotateZ(1deg);
            }
        }

        @keyframes sparkle {
            0%, 100% {
                opacity: .55;
                transform: scale(.8) rotate(0);
            }

            50% {
                opacity: 1;
                transform: scale(1.12) rotate(20deg);
            }
        }

        @media (max-width: 800px) {

            .page-shell {
                width: min(100% - 32px, 600px);
                padding-top: 22px;
            }

            .topbar {
                margin-bottom: 24px;
            }

            .welcome-card {
                min-height: 0;
                grid-template-columns: 1fr;
                padding: 42px 34px 30px;
            }

            .welcome-copy {
                max-width: 540px;
            }

            .art-stage {
                min-height: 260px;
                margin-top: 14px;
            }

            .halo {
                width: 230px;
                height: 230px;
            }

            .mini-glass-card {
                right: 8%;
                bottom: 20px;
            }
        }

        @media (max-width: 520px) {

            .page-shell {
                width: min(100% - 24px, 440px);
                padding-top: 16px;
            }

            .topbar {
                align-items: flex-start;
                gap: 12px;
            }

            .brand {
                gap: 8px;
                font-size: .72rem;
                letter-spacing: .07em;
            }

            .brand-mark {
                width: 38px;
                height: 38px;
                border-radius: 13px;
            }

            .topbar-right {
                gap: 8px;
            }

            .user-chip {
                gap: 7px;
                font-size: .72rem;
            }

            .user-avatar {
                width: 34px;
                height: 34px;
            }

            .btn-logout {
                padding: 8px 10px;
                font-size: .72rem;
            }

            .welcome-card {
                padding: 34px 25px 20px;
                border-radius: 24px;
            }

            .welcome-copy h1 {
                font-size: 2.7rem;
            }

            .welcome-copy p {
                font-size: .9rem;
            }

            .art-stage {
                min-height: 220px;
            }

            .main-glass-card {
                width: 192px;
                padding: 16px;
            }

            .halo {
                width: 205px;
                height: 205px;
            }

            .mini-glass-card {
                right: 0;
                bottom: 10px;
                padding: 10px 12px;
                font-size: .68rem;
            }

            .dashboard-footer {
                flex-direction: column;
                gap: 6px;
                padding-top: 18px;
            }

            .btn-start-transaction {
                width: 100%;
            }

            .transaction-button-wrapper {
                align-items: stretch;
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

<main class="page-shell">

    <!-- NOTIFIKASI TRANSAKSI REAL-TIME -->
    <div id="transaction-notification"
         class="alert alert-success d-none"
         role="alert"
         style="border-radius: 14px; margin-bottom: 20px;">

        <strong>Transaksi baru!</strong>
        <span id="transaction-message"></span>

    </div>

    <header class="topbar">

        <a class="brand" href="{{ route('dashboard') }}" aria-label="Kembali ke dasbor MINIMARTGIRL">

            <span class="brand-mark" aria-hidden="true">
                🛍️
            </span>

            <span>
                MINIMARTGIRL
            </span>

        </a>


        <div class="topbar-right">

            <div class="user-chip">

                <span class="user-avatar" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </span>

                <span>
                    {{ auth()->user()->name }}
                </span>

            </div>


            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button type="submit" class="btn btn-logout">
                    Keluar
                </button>

            </form>

        </div>

    </header>


    <section class="welcome-card">

        <div class="welcome-copy">

            <span class="eyebrow">
                Dasbor kasir
            </span>


            <h1>
                Hai, {{ auth()->user()->name }}!
            </h1>


            <p>
                Selamat datang di ruang kerja MINIMARTGIRL.
                Semoga harimu menyenangkan dan lancar melayani pelanggan.
            </p>


            <!-- TOMBOL TRANSAKSI -->
            <div class="transaction-button-wrapper">

                <a href="{{ route('transaction.create') }}"
                   class="btn-start-transaction">

                    <span aria-hidden="true">🛒</span>
                    Mulai Transaksi

                </a>


                <span class="welcome-pill">

                    <span aria-hidden="true"></span>

                    Ruang kasir siap digunakan

                </span>

            </div>

        </div>


        <div class="art-stage">

            <div class="halo" aria-hidden="true"></div>

            <span class="sparkle sparkle-one" aria-hidden="true">
                ✦
            </span>

            <span class="sparkle sparkle-two" aria-hidden="true">
                ✧
            </span>


            <a href="{{ route('transaction.create') }}"
               class="glass-card main-glass-card"
               aria-label="Mulai transaksi baru">

                <span class="cart-icon" aria-hidden="true">
                    🛒
                </span>

                <span class="glass-caption">

                    <small>
                        MINIMARTGIRL
                    </small>

                    <strong>
                        Belanja jadi mudah
                    </strong>

                </span>

            </a>


            <div class="glass-card mini-glass-card" aria-hidden="true">

                <span class="mini-icon">
                    ✦
                </span>

                <span>
                    Siap melayani
                </span>

            </div>

        </div>

    </section>


    <footer class="dashboard-footer">

        <span>
            MINIMARTGIRL · Ruang kerja kasir
        </span>

        <span>
            Semua kebutuhan toko, dalam satu tempat.
        </span>

    </footer>

</main>

<!-- MEMUAT LARAVEL ECHO DAN REVERB -->
@vite(['resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function () {

        if (!window.Echo) {
            console.error('Laravel Echo belum tersedia.');
            return;
        }

        window.Echo.private('transactions')
            .listen('.order.placed', function (event) {

                const notification = document.getElementById('transaction-notification');
                const message = document.getElementById('transaction-message');

                message.textContent = event.message || 'Transaksi baru berhasil dibuat!';

                notification.classList.remove('d-none');

                console.log('Transaksi baru:', event.transaction);

            });

    });
</script>

</body>
</html>
