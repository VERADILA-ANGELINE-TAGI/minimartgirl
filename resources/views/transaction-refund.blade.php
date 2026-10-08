<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f3f6fc">
    <title>Pengembalian Barang {{ $transaction->transaction_code }} - MINIMARTGIRL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --blue: #2864ed; --ink: #17243b; --muted: #748199; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: var(--ink); background: #f2f5fb; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .topbar { height: 68px; display: flex; align-items: center; justify-content: space-between; padding: 0 max(18px, calc((100vw - 900px) / 2)); background: #fff; border-bottom: 1px solid #e5ebf4; }
        .topbar a { color: #52617a; font-size: .78rem; font-weight: 750; text-decoration: none; }
        .page-shell { width: min(900px, calc(100% - 30px)); margin: 0 auto; padding: 30px 0 48px; animation: enter .5s ease both; }
        .eyebrow { margin: 0 0 7px; color: var(--blue); font-size: .68rem; font-weight: 850; letter-spacing: .15em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(1.55rem,3vw,2rem); font-weight: 850; letter-spacing: -.05em; }
        .lead { margin: 7px 0 20px; color: var(--muted); font-size: .83rem; }
        .panel { overflow: hidden; margin-bottom: 15px; border: 1px solid #e7ecf4; border-radius: 14px; background: white; box-shadow: 0 8px 25px rgba(29,48,82,.045); }
        .panel-head { display: flex; justify-content: space-between; gap: 12px; padding: 14px 17px; border-bottom: 1px solid #e9edf4; font-size: .8rem; font-weight: 800; }
        .panel-body { padding: 16px; }
        .refund-row { display: grid; grid-template-columns: minmax(0,1fr) 100px minmax(100px,auto); align-items: center; gap: 15px; padding: 12px 0; border-bottom: 1px solid #eef1f6; }
        .refund-row:last-child { border-bottom: 0; }
        .product-name { margin: 0 0 4px; font-size: .8rem; font-weight: 800; }
        .product-meta { color: #7d899e; font-size: .7rem; }
        .form-control { min-height: 39px; border-color: #dfe6f0; border-radius: 8px; font-size: .78rem; box-shadow: none!important; }
        .form-control:focus { border-color: #80a7ff; }
        .refund-value { color: #364760; font-size: .75rem; font-weight: 800; text-align: right; }
        .form-label { color: #66748b; font-size: .73rem; font-weight: 750; }
        .btn-blue { min-height: 42px; border: 0; border-radius: 9px; color: white; background: var(--blue); font-size: .8rem; font-weight: 800; }
        .btn-blue:hover { color: white; background: #194fc9; }
        .refund-total { display: flex; justify-content: space-between; gap: 12px; padding: 14px 0; border-top: 1px solid #e9edf4; font-size: .82rem; font-weight: 800; }
        .refund-total strong { color: #b33b54; }
        @keyframes enter { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @media(max-width:560px) { .refund-row { grid-template-columns: minmax(0,1fr) 75px; gap: 8px; } .refund-value { grid-column: 1/-1; text-align: left; } }
        @media(prefers-reduced-motion:reduce) { *,*::before,*::after { animation-duration:.01ms!important; animation-iteration-count:1!important; transition-duration:.01ms!important; } }
    </style>
</head>
<body>
<header class="topbar">
    <a href="{{ route('transaction.receipt', $transaction) }}">← Kembali ke struk</a>
    <a href="{{ route('transaction.history') }}">Riwayat transaksi</a>
</header>
<main class="page-shell">
    <p class="eyebrow">Pengembalian barang</p>
    <h1>Pengembalian Barang</h1>
    <p class="lead">{{ $transaction->transaction_code }} · Total transaksi Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small" role="alert">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('transaction.refund.store', $transaction) }}" method="POST" id="refund-form">
        @csrf
        <section class="panel">
            <div class="panel-head"><span>Pilih barang dan jumlah pengembalian</span><span>Stok dikembalikan ke persediaan</span></div>
            <div class="panel-body">
                @foreach ($transaction->transactionDetails as $index => $detail)
                    @php
                        $remainingQuantity = $detail->quantity - $detail->refunded_quantity;
                    @endphp
                    @if ($remainingQuantity > 0)
                        <div class="refund-row" data-max="{{ $remainingQuantity }}" data-net-cents="{{ (int) round((float) $detail->net_total * 100) }}" data-refunded-cents="{{ (int) round((float) $detail->refunded_amount * 100) }}" data-previous-quantity="{{ $detail->refunded_quantity }}" data-total-quantity="{{ $detail->quantity }}">
                            <div>
                                <p class="product-name">{{ $detail->product_name }}</p>
                                <span class="product-meta">Terjual {{ $detail->quantity }} · Sudah dikembalikan {{ $detail->refunded_quantity }} · Harga Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                                <input type="hidden" name="items[{{ $index }}][transaction_detail_id]" value="{{ $detail->id }}">
                            </div>
                            <input class="form-control quantity-input" name="items[{{ $index }}][quantity]" type="number" min="0" max="{{ $remainingQuantity }}" value="0" aria-label="Jumlah pengembalian {{ $detail->product_name }}">
                            <span class="refund-value">Nilai pengembalian: <span class="line-refund">Rp 0</span></span>
                        </div>
                    @endif
                @endforeach

                @if ($transaction->payment_status === 'refunded' || $transaction->transactionDetails->every(fn ($detail) => $detail->refunded_quantity >= $detail->quantity))
                    <p class="text-secondary small mb-0">Semua barang pada transaksi ini sudah dikembalikan.</p>
                @endif

                <div class="refund-total"><span>Perkiraan pengembalian</span><strong id="refund-total">Rp 0</strong></div>
                <label for="reason" class="form-label">Alasan pengembalian</label>
                <textarea id="reason" name="reason" class="form-control mb-3" rows="3" maxlength="500" required placeholder="Tuliskan alasan pengembalian...">{{ old('reason') }}</textarea>
                <button type="submit" class="btn btn-blue w-100" id="refund-submit">Simpan Pengembalian</button>
            </div>
        </section>
    </form>
</main>
<script>
    const formatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
    const rows = [...document.querySelectorAll('.refund-row')];
    const totalElement = document.getElementById('refund-total');
    const submitButton = document.getElementById('refund-submit');

    function updateRefundEstimate() {
        let totalCents = 0;
        rows.forEach((row) => {
            const quantity = Math.min(Number(row.querySelector('.quantity-input').value) || 0, Number(row.dataset.max));
            const netCents = Number(row.dataset.netCents);
            const refundedCents = Number(row.dataset.refundedCents);
            const previousRefundedQty = Number(row.dataset.previousQuantity);
            const fullQty = Number(row.dataset.totalQuantity);
            const newRefundedQty = previousRefundedQty + quantity;
            const cumulativeRefundCents = newRefundedQty === fullQty
                ? netCents
                : Math.floor(netCents * newRefundedQty / fullQty);
            const estimate = cumulativeRefundCents - refundedCents;
            row.querySelector('.line-refund').textContent = `Rp ${formatter.format(Math.max(0, estimate))}`;
            totalCents += Math.max(0, estimate);
        });
        totalElement.textContent = `Rp ${formatter.format(Math.round(totalCents / 100))}`;
        submitButton.disabled = !rows.some((row) => Number(row.querySelector('.quantity-input').value) > 0);
    }

    document.querySelectorAll('.quantity-input').forEach((input) => {
        input.addEventListener('input', updateRefundEstimate);
    });
    updateRefundEstimate();
</script>
</body>
</html>
