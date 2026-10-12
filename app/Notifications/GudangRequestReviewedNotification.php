<?php

namespace App\Notifications;

use App\Models\DataRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GudangRequestReviewedNotification extends Notification
{
    use Queueable;

    public $dataRequest;

    public string $action;

    public ?string $note;

    /**
     * Create a new notification instance.
     */
    public function __construct(DataRequest $dataRequest, string $action = 'Disetujui', ?string $note = null)
    {
        $this->dataRequest = $dataRequest;
        $this->action = $action;
        $this->note = $note;
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
        $isApproved = $this->action === 'Disetujui';
        $title = $isApproved ? 'Request Gudang Disetujui' : 'Request Gudang Ditolak';
        $msg = "Permintaan {$this->dataRequest->request_number} ({$this->dataRequest->title}) telah {$this->action} oleh Manajer.";
        if ($this->note) {
            $msg .= ' Catatan: '.$this->note;
        }

        return [
            'item_name' => $title,
            'message' => $msg,
            'type' => $isApproved ? 'info' : 'error',
            'request_id' => $this->dataRequest->id,
            'status' => $this->action,
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
