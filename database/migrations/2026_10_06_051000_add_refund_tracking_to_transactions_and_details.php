<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('refunded_amount', 12, 2)->default(0)->after('total');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('net_total', 12, 2)->default(0)->after('line_total');
            $table->unsignedInteger('refunded_quantity')->default(0)->after('net_total');
            $table->decimal('refunded_amount', 12, 2)->default(0)->after('refunded_quantity');
        });

        DB::table('transactions')->orderBy('id')->chunkById(100, function ($transactions): void {
            foreach ($transactions as $transaction) {
                $details = DB::table('transaction_details')
                    ->where('transaction_id', $transaction->id)
                    ->orderBy('id')
                    ->get(['id', 'line_total']);

                if ($details->isEmpty()) {
                    continue;
                }

                $toCents = static fn (string|int|float $amount): int => (int) round((float) $amount * 100);
                $subtotalCents = $toCents($transaction->subtotal);
                $totalCents = $toCents($transaction->total);
                $allocatedCents = 0;
                $lastIndex = $details->count() - 1;

                foreach ($details as $index => $detail) {
                    $lineCents = $toCents($detail->line_total);
                    $netCents = $subtotalCents > 0
                        ? (int) floor(($lineCents / $subtotalCents) * $totalCents)
                        : ($index === 0 ? $totalCents : 0);
                    $allocatedCents += $netCents;

                    if ($index === $lastIndex) {
                        $netCents += $totalCents - $allocatedCents;
                    }

                    DB::table('transaction_details')
                        ->where('id', $detail->id)
                        ->update(['net_total' => number_format($netCents / 100, 2, '.', '')]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['net_total', 'refunded_quantity', 'refunded_amount']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('refunded_amount');
        });
    }
};
