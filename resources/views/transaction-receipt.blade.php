<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Struk - MINIMARTGIRL</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: auto;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 12px;
            background: #f2f2f2;
            font-family: "Courier New", monospace;
            color: #000;
        }

        .receipt {
            width: var(--receipt-width);
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 8px 10px 10px;
        }

        .paper-58,
        .paper-80 {
            page: receipt-{{ $paperWidth }};
        }

        .paper-58 .print-button,
        .paper-80 .print-button {
            width: var(--receipt-width);
        }

        .center {
            text-align: center;
        }

        .store-name {
            font-size: var(--receipt-store-name-size);
            font-weight: bold;
            margin-bottom: 2px;
        }

        .store-info {
            font-size: var(--receipt-info-font-size);
            line-height: var(--receipt-line-height);
        }

        .line {
            border-top: 1px dashed #000;
            margin: var(--receipt-divider-spacing) 0;
        }

        .info {
            font-size: var(--receipt-info-font-size);
            line-height: var(--receipt-line-height);
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .item {
            margin-bottom: var(--receipt-item-spacing);
            font-size: var(--receipt-font-size);
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .total {
            font-weight: bold;
            font-size: 11px;
        }

        .grand-total {
            font-size: var(--receipt-grand-total-size);
            font-weight: bold;
        }

        .footer {
            margin-top: var(--receipt-divider-spacing);
            font-size: var(--receipt-info-font-size);
            text-align: center;
            line-height: var(--receipt-line-height);
        }

        .print-button {
            display: block;
            width: var(--receipt-width);
            max-width: 100%;
            margin: 12px auto 0;
            padding: 9px 10px;
            border: none;
            background: #e83e8c;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }

        .printer-help {
            width: var(--receipt-width);
            max-width: 100%;
            margin: 12px auto 0;
            color: #555;
            font-size: 10px;
            text-align: center;
        }

        .print-confirmation {
            position: fixed;
            inset: 0;
            z-index: 10;
            display: grid;
            place-items: center;
            padding: 20px;
            background: rgb(0 0 0 / 45%);
        }

        .print-confirmation[hidden] {
            display: none;
        }

        .print-confirmation-card {
            width: min(360px, 100%);
            padding: 24px;
            background: white;
            text-align: center;
        }

        .print-confirmation-card p {
            margin: 10px 0 20px;
            font-size: 13px;
        }

        .print-confirmation-card button {
            margin: 4px;
            padding: 10px 14px;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm-print-button {
            background: #e83e8c;
            color: white;
            font-weight: bold;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .receipt {
                padding: var(--receipt-print-padding);
            }

            .print-button,
            .printer-help,
            .print-confirmation {
                display: none;
            }
        }
    </style>
    <style id="receipt-page-size"></style>
</head>

<body
    class="paper-{{ $paperWidth }}"
    data-paper-width="{{ $receiptLayout['paperWidthMm'] }}"
    data-max-page-height="{{ $receiptLayout['maxPageHeightMm'] }}"
    style="
        --receipt-width: {{ $receiptLayout['paperWidthMm'] }}mm;
        --receipt-print-padding: {{ $receiptLayout['printPadding'] }};
        --receipt-font-size: {{ $receiptLayout['fontSizePx'] }}px;
        --receipt-info-font-size: {{ $receiptLayout['infoFontSizePx'] }}px;
        --receipt-store-name-size: {{ $receiptLayout['storeNameFontSizePx'] }}px;
        --receipt-grand-total-size: {{ $receiptLayout['grandTotalFontSizePx'] }}px;
        --receipt-line-height: {{ $receiptLayout['lineHeight'] }};
        --receipt-item-spacing: {{ $receiptLayout['itemSpacingMm'] }}mm;
        --receipt-divider-spacing: {{ $receiptLayout['dividerSpacingMm'] }}mm;
    "
>

<div class="receipt">

    {{-- HEADER --}}
    <div class="center">
        <div class="store-name">
            MINIMARTGIRL
        </div>

        <div class="store-info">
            Minimarket dan Kasir<br>
            Terima kasih telah berbelanja
        </div>
    </div>

    <div class="line"></div>

    {{-- INFORMASI TRANSAKSI --}}
    <div class="info">

        <div class="row">
            <span>No. Transaksi</span>
            <span>
                {{ $transaction->transaction_code }}
            </span>
        </div>

        <div class="row">
            <span>Tanggal</span>
            <span>
                {{ $transaction->created_at?->format('d/m/Y H:i') }}
            </span>
        </div>

        <div class="row">
            <span>Kasir</span>
            <span>
                {{ $transaction->cashier_name ?? '-' }}
            </span>
        </div>

    </div>

    <div class="line"></div>

    {{-- DAFTAR BARANG --}}
    @foreach ($transaction->transactionDetails as $detail)

        <div class="item">

            <div class="item-name">
                {{ $detail->product_name }}
            </div>

            <div class="item-detail">
                <span>
                    {{ $detail->quantity }} x Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                </span>

                <span>
                    Rp {{ number_format($detail->line_total, 0, ',', '.') }}
                </span>
            </div>

        </div>

    @endforeach

    <div class="line"></div>

    {{-- PEMBAYARAN --}}
    <div class="info">

        <div class="row">
            <span>Jumlah sementara</span>
            <span>
                Rp {{ number_format($transaction->subtotal ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <div class="row">
            <span>Diskon</span>
            <span>
                Rp {{ number_format($transaction->discount, 0, ',', '.') }}
            </span>
        </div>

        <div class="row">
            <span>Pajak</span>
            <span>
                Rp {{ number_format($transaction->tax, 0, ',', '.') }}
            </span>
        </div>

    </div>

    @if ($transaction->refunds->isNotEmpty())
        <div class="line"></div>

        <div class="info">
            <strong>Catatan pengembalian</strong>

            @foreach ($transaction->refunds as $refund)
                <div class="item">
                    <div>{{ $refund->refund_code }} · {{ $refund->created_at->format('d/m/Y H:i') }}</div>
                    <div>Pengembalian Rp {{ number_format($refund->amount, 0, ',', '.') }} · {{ $refund->reason }}</div>

                    @foreach ($refund->details as $refundDetail)
                        <div>{{ $refundDetail->product_name }} × {{ $refundDetail->quantity }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    @if ($transaction->payment_method !== 'cash')
        <div class="footer">
            Cara pembayaran dicatat oleh kasir; pembayaran elektronik tidak diverifikasi oleh sistem.
        </div>
    @endif

    <div class="line"></div>

    {{-- TOTAL --}}
    <div class="row grand-total">
        <span>TOTAL</span>
        <span>
            Rp {{ number_format($transaction->total ?? 0, 0, ',', '.') }}
        </span>
    </div>

    <div class="line"></div>

    {{-- PEMBAYARAN --}}
    <div class="info">

        <div class="row">
            <span>Metode</span>
            <span>
                {{ [
                    'cash' => 'Tunai',
                    'qris' => 'QRIS',
                    'debit' => 'Kartu debit',
                    'credit' => 'Kartu kredit',
                    'ewallet' => 'Dompet digital',
                    'transfer' => 'Transfer bank',
                ][$transaction->payment_method ?? ''] ?? '-' }}
            </span>
        </div>

        <div class="row">
            <span>Uang Diterima</span>
            <span>
                Rp {{ number_format($transaction->amount_paid ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <div class="row">
            <span>Kembalian</span>
            <span>
                Rp {{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}
            </span>
        </div>

    </div>

    <div class="line"></div>

    {{-- FOOTER --}}
    <div class="footer">

        <strong>TERIMA KASIH</strong><br>
        Sudah berbelanja di MINIMARTGIRL<br>
        Barang yang sudah dibeli<br>
        tidak dapat dikembalikan.

    </div>

</div>

@if (session('print_receipt'))
    <div class="print-confirmation" role="dialog" aria-modal="true" aria-labelledby="print-confirmation-title">
        <div class="print-confirmation-card">
            <strong id="print-confirmation-title">Transaksi berhasil</strong>
            <p>Cetak struk sekarang ke printer kasir?</p>
            <button type="button" class="confirm-print-button" onclick="confirmReceiptPrint()">
                YA, CETAK STRUK
            </button>
            <button type="button" onclick="dismissReceiptPrint()">
                NANTI
            </button>
        </div>
    </div>
@endif

<p class="printer-help">
    Struk akan dicetak ke printer default Windows.
</p>

<button
    type="button"
    class="print-button"
    onclick="printReceipt()">
    CETAK ULANG STRUK
</button>

<script>
    let receiptPrintRequested = false;

    function printReceipt() {
        if (receiptPrintRequested) {
            return;
        }

        receiptPrintRequested = true;
        document.querySelectorAll('.print-button, .confirm-print-button').forEach((button) => {
            button.disabled = true;
        });

        const receipt = document.querySelector('.receipt');
        const paperWidth = Number(document.body.dataset.paperWidth);
        const rawPageHeight = Math.ceil(receipt.getBoundingClientRect().height * 25.4 / 96 + 5);
        const maxPageHeight = Number(document.body.dataset.maxPageHeight);
        const pageHeight = Math.min(rawPageHeight, maxPageHeight);

        document.getElementById('receipt-page-size').textContent =
            `@page receipt-${paperWidth} { size: ${paperWidth}mm ${pageHeight}mm; margin: 0; }`;

        window.print();
    }

    function confirmReceiptPrint() {
        document.querySelector('.print-confirmation').hidden = true;
        printReceipt();
    }

    function dismissReceiptPrint() {
        document.querySelector('.print-confirmation').hidden = true;
    }
</script>

</body>
</html>