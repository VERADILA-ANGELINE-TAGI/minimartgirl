<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\WindowsEscPosService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class StrukController extends Controller
{
    public function show(
        Request $request,
        Transaction $transaction,
        WindowsEscPosService $windowsEscPosService
    ): View {
        abort_unless(
            in_array(
                $transaction->payment_status,
                ['paid', 'partially_refunded', 'refunded'],
                true
            ),
            404
        );

        $validated = $request->validate([
            'paper' => ['nullable', Rule::in(['58', '80'])],
        ]);

        $paperWidth = $validated['paper'] ?? '80';
        $receiptLayout = $windowsEscPosService->layoutFor($paperWidth);

        $transaction->load([
            'transactionDetails.product',
            'refunds.details',
        ]);

        return view(
            'transaction-receipt',
            compact('transaction', 'paperWidth', 'receiptLayout')
        );
    }

    /**
     * Download invoice transaksi dalam format PDF.
     */
    public function downloadPdf(
        Request $request,
        Transaction $transaction
    ): Response {
        abort_unless(
            in_array(
                $transaction->payment_status,
                ['paid', 'partially_refunded', 'refunded'],
                true
            ),
            404
        );

        $transaction->load([
            'transactionDetails.product',
            'refunds.details',
        ]);

        $pdf = Pdf::loadView('invoices.invoice', [
            'transaction' => $transaction,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'invoice-' . $transaction->transaction_code . '.pdf'
        );
    }
}
