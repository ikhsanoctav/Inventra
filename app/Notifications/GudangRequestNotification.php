<?php

namespace App\Notifications;

use App\Models\DataRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GudangRequestNotification extends Notification
{
    use Queueable;

    public $request;

    public function __construct(DataRequest $request)
    {
        $this->request = $request;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_name' => 'Permintaan Gudang Baru',
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'message' => 'Permintaan barang baru ('.$this->request->request_number.' - '.$this->request->title.') diajukan dan memerlukan tinjauan.',
            'type' => 'gudang_request',
        ];
    }
}
