<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PoPendingNotification extends Notification
{
    use Queueable;

    public $po;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $po)
    {
        $this->po = $po;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = 'Purchase Order baru ('.$this->po->ref_number.') menunggu persetujuan Anda.';
        if ($notifiable->role === 'super_admin') {
            $message = 'Laporan: Purchase Order ('.$this->po->ref_number.') telah dibuat dan menunggu persetujuan Manager.';
        }

        return [
            'item_name' => 'PO Menunggu Persetujuan',
            'message' => $message,
            'type' => 'po_pending',
            'po_id' => $this->po->id,
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
