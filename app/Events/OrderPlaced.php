<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('transactions'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'transaction' => [
                'id' => $this->transaction->id,
                'transaction_code' => $this->transaction->transaction_code,
                'cashier_name' => $this->transaction->cashier_name,
                'total' => $this->transaction->total,
                'payment_method' => $this->transaction->payment_method,
                'payment_status' => $this->transaction->payment_status,
                'created_at' => $this->transaction->created_at?->toDateTimeString(),
            ],
            'message' => 'Transaksi baru berhasil dibuat!',
        ];
    }
}
