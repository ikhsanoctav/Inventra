<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InboundCompletedNotification extends Notification
{
    use Queueable;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = 'Barang Masuk (Inbound '.$this->transaction->ref_number.') telah selesai diproses. Stok barang telah ditambahkan ke sistem.';
        if ($notifiable->role === 'super_admin') {
            $message = 'Laporan Sistem: Transaksi Inbound '.$this->transaction->ref_number.' telah diselesaikan.';
        }

        return [
            'item_name' => 'Barang Masuk Selesai',
            'transaction_id' => $this->transaction->id,
            'ref_number' => $this->transaction->ref_number,
            'message' => $message,
            'type' => 'inbound_completed',
        ];
    }
}
