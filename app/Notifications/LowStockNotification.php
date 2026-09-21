<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public $item;

    public $currentStock;

    /**
     * Create a new notification instance.
     */
    public function __construct(Item $item, $currentStock)
    {
        $this->item = $item;
        $this->currentStock = $currentStock;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = 'Stok '.$this->item->name.' ('.$this->item->sku.') telah menipis. Sisa: '.$this->currentStock;
        if ($notifiable->role === 'super_admin') {
            $message = 'Laporan Sistem: Stok barang '.$this->item->name.' mencapai batas minimum ('.$this->currentStock.').';
        }

        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'sku' => $this->item->sku,
            'current_stock' => $this->currentStock,
            'reorder_point' => $this->item->reorder_level ?? 10,
            'message' => $message,
            'type' => 'low_stock',
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
