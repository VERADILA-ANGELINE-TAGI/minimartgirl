<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice MINIMARTGIRL</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #29233a;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        h2 {
            color: #e94b91;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #fce4ef;
        }

        .right {
            text-align: right;
        }

        .summary {
            width: 55%;
            margin-left: auto;
        }

        .summary td {
            border: none;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>MINIMARTGIRL</h2>
        <p>INVOICE TRANSAKSI</p>
        <p>
            Kode Transaksi:
            {{ $transaction->transaction_code }}
        </p>
        <p>
            Tanggal:
            {{ $transaction->created_at->format('d/m/Y H:i') }}
        </p>
        <p>
            Kasir:
            {{ $transaction->cashier_name }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($transaction->transactionDetails as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="right">
                        Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td class="right">
                        Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Subtotal</td>
            <td class="right">
                Rp {{ number_format((float) $transaction->subtotal, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right">
                Rp {{ number_format((float) $transaction->discount, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Pajak</td>
            <td class="right">
                Rp {{ number_format((float) $transaction->tax, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td class="right">
                <strong>
                    Rp {{ number_format((float) $transaction->total, 0, ',', '.') }}
                </strong>
            </td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td class="right">{{ strtoupper($transaction->payment_method) }}</td>
        </tr>
        <tr>
            <td>Uang Dibayar</td>
            <td class="right">
                Rp {{ number_format((float) $transaction->amount_paid, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="right">
                Rp {{ number_format((float) $transaction->change_amount, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Terima kasih telah berbelanja di MINIMARTGIRL!</p>
    </div>
</body>
</html>
