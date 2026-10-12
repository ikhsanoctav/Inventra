<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PoRejectedNotification extends Notification
{
    use Queueable;

    public $po;

    public string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $po, string $reason = '')
    {
        $this->po = $po;
        $this->reason = $reason;
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
            ->subject('Purchase Order Ditolak: '.$this->po->ref_number)
            ->line('Purchase Order ('.$this->po->ref_number.') telah ditolak oleh Manager.')
            ->line('Alasan Penolakan: '.$this->reason)
            ->action('Lihat Detail PO', url('/transactions/po/'.$this->po->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_name' => 'PO Ditolak',
            'message' => 'PO ('.$this->po->ref_number.') ditolak oleh Manager. Alasan: '.$this->reason,
            'type' => 'error',
            'po_id' => $this->po->id,
            'reason' => $this->reason,
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
