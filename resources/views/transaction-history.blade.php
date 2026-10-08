<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f3f6fc">
    <title>Riwayat Transaksi - MINIMARTGIRL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --blue: #2864ed; --ink: #17243b; --muted: #748199; --line: #e5ebf4; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: var(--ink); background: #f2f5fb; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .topbar { height: 70px; display: flex; align-items: center; justify-content: space-between; padding: 0 max(22px, calc((100vw - 1120px) / 2)); background: white; border-bottom: 1px solid var(--line); }
        .brand { color: var(--ink); font-size: .9rem; font-weight: 850; text-decoration: none; letter-spacing: -.02em; }
        .topbar nav { display: flex; gap: 18px; }
        .topbar nav a { color: #68768c; font-size: .78rem; font-weight: 700; text-decoration: none; }
        .topbar nav a:hover { color: var(--blue); }
        .page-shell { width: min(1120px, calc(100% - 36px)); margin: 0 auto; padding: 34px 0 54px; animation: enter .5s ease both; }
        .page-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 22px; }
        .eyebrow { margin: 0 0 7px; color: var(--blue); font-size: .68rem; font-weight: 850; letter-spacing: .15em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 850; letter-spacing: -.05em; }
        .lead { margin: 7px 0 0; color: var(--muted); font-size: .84rem; }
        .btn-blue { border: 0; border-radius: 9px; color: #fff; background: var(--blue); font-size: .78rem; font-weight: 750; }
        .btn-blue:hover { color: white; background: #194fc9; }
        .panel { overflow: hidden; border: 1px solid #e7ecf4; border-radius: 15px; background: white; box-shadow: 0 8px 25px rgba(29,48,82,.045); }
        .filter-panel { margin-bottom: 16px; padding: 15px; }
        .filters { display: grid; grid-template-columns: minmax(200px,1fr) 180px 155px 155px auto; align-items: end; gap: 10px; }
        .filters label { display: block; margin-bottom: 6px; color: #66748b; font-size: .7rem; font-weight: 700; }
        .form-control, .form-select { min-height: 39px; border-color: #dfe6f0; border-radius: 8px; font-size: .77rem; box-shadow: none!important; }
        .table-responsive { overflow-x: auto; }
        .table { min-width: 760px; margin: 0; vertical-align: middle; }
        .table thead th { padding: 13px 15px; border-bottom: 1px solid var(--line); color: #8490a4; background: #fafbfd; font-size: .66rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
        .table tbody td { padding: 14px 15px; border-color: #eef1f6; color: #52617a; font-size: .75rem; }
        .code { color: #24344f; font-weight: 800; text-decoration: none; }
        .code:hover { color: var(--blue); }
        .amount { color: #263653!important; font-weight: 800; white-space: nowrap; }
        .status { display: inline-block; padding: 5px 9px; border-radius: 999px; font-size: .64rem; font-weight: 800; white-space: nowrap; }
        .status-paid { color: #21814e; background: #ebf9f0; }
        .status-partial { color: #9a6818; background: #fff6df; }
        .status-refunded { color: #a64252; background: #fff0f1; }
        .status-held { color: #9a6818; background: #fff6df; }
        .status-cancelled { color: #707887; background: #f0f2f5; }
        .actions { display: flex; gap: 7px; }
        .action-link { padding: 6px 8px; border: 1px solid #e2e8f2; border-radius: 7px; color: #4b5d79; background: white; font-size: .67rem; font-weight: 750; text-decoration: none; white-space: nowrap; }
        .action-link:hover { border-color: #b6cbff; color: var(--blue); }
        .action-link.disabled { color: #a4adbb; background: #f6f7f9; pointer-events: none; }
        .empty { padding: 45px 18px; color: var(--muted); text-align: center; font-size: .82rem; }
        .pagination { margin: 0; padding: 14px; }
        .pagination .page-link { font-size: .75rem; }
        .page-footer { margin-top: 20px; color: #98a3b5; font-size: .7rem; }
        @keyframes enter { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 820px) { .filters { grid-template-columns: repeat(2,minmax(0,1fr)); } .filters .filter-search { grid-column: 1/-1; } }
        @media (max-width: 520px) { .topbar { padding: 0 14px; } .topbar nav { gap: 11px; } .page-shell { width: calc(100% - 24px); padding-top: 24px; } .page-heading { align-items: start; } .page-heading .btn { padding: 8px 9px; font-size: .68rem; white-space: nowrap; } .filters { grid-template-columns: 1fr 1fr; } }
        @media (prefers-reduced-motion: reduce) { *,*::before,*::after { animation-duration: .01ms!important; animation-iteration-count: 1!important; scroll-behavior: auto!important; transition-duration: .01ms!important; } }
    </style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('dashboard') }}">MINIMARTGIRL <span class="text-secondary fw-normal">/ Riwayat</span></a>
    <nav>
        <a href="{{ route('transaction.create') }}">Kasir</a>
        <a href="{{ route('dashboard') }}">Dasbor</a>
    </nav>
</header>
<main class="page-shell">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Catatan penjualan</p>
            <h1>Riwayat Transaksi</h1>
            <p class="lead">Cari transaksi, cetak ulang struk, atau proses pengembalian barang.</p>
        </div>
        <a href="{{ route('transaction.create') }}" class="btn btn-blue px-3 py-2">+ Transaksi Baru</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 small">{{ session('success') }}</div>
    @endif

    <form class="panel filter-panel" method="GET" action="{{ route('transaction.history') }}">
        <div class="filters">
            <div class="filter-search">
                <label for="search">Kode transaksi / nama kasir</label>
                <input id="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari transaksi...">
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua status</option>
                    <option value="held" @selected(($filters['status'] ?? '') === 'held')>Tertahan</option>
                    <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Dibatalkan</option>
                    <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Lunas</option>
                    <option value="partially_refunded" @selected(($filters['status'] ?? '') === 'partially_refunded')>Dikembalikan sebagian</option>
                    <option value="refunded" @selected(($filters['status'] ?? '') === 'refunded')>Dikembalikan seluruhnya</option>
                </select>
            </div>
            <div>
                <label for="from">Dari tanggal</label>
                <input id="from" type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
            </div>
            <div>
                <label for="to">Sampai tanggal</label>
                <input id="to" type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
            </div>
            <button class="btn btn-blue px-3" type="submit">Terapkan</button>
        </div>
    </form>

    <section class="panel">
        @if ($transactions->isEmpty())
            <div class="empty">Belum ada transaksi yang cocok dengan pencarian ini.</div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Kode transaksi</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Barang</th>
                        <th>Total</th>
                        <th>Pengembalian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td><a class="code" href="{{ route('transaction.receipt', $transaction) }}">{{ $transaction->transaction_code }}</a></td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $transaction->cashier_name ?? 'Kasir' }}</td>
                            <td>{{ $transaction->transaction_details_count }}</td>
                            <td class="amount">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($transaction->refunded_amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusLabel = match ($transaction->payment_status) {
                                        'held' => ['Tertahan', 'status-held'],
                                        'cancelled' => ['Dibatalkan', 'status-cancelled'],
                                        'refunded' => ['Dikembalikan seluruhnya', 'status-refunded'],
                                        'partially_refunded' => ['Dikembalikan sebagian', 'status-partial'],
                                        default => ['Lunas', 'status-paid'],
                                    };
                                @endphp
                                <span class="status {{ $statusLabel[1] }}">{{ $statusLabel[0] }}</span>
                            </td>
                            <td>
                                <div class="actions">
                                    @if ($transaction->payment_status === 'held')
                                        <a class="action-link" href="{{ route('transaction.create', ['resume' => $transaction->id]) }}">Lanjutkan</a>
                                    @elseif ($transaction->payment_status !== 'cancelled')
                                        <a class="action-link" href="{{ route('transaction.receipt', $transaction) }}">Struk</a>
                                    @endif
                                    @if (in_array($transaction->payment_status, ['paid', 'partially_refunded'], true))
                                        <a class="action-link" href="{{ route('transaction.refund.create', $transaction) }}">Kembalikan</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        @endif
    </section>
    <footer class="page-footer">MINIMARTGIRL · Sistem kasir</footer>
</main>
</body>
</html>
