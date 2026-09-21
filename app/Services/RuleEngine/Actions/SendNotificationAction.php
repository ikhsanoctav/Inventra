<?php

namespace App\Services\RuleEngine\Actions;

use Illuminate\Support\Facades\Log;

class SendNotificationAction implements ActionInterface
{
    public function execute(?array $params, $entity): void
    {
        $email = $params['email'] ?? 'admin@default.com';
        $message = $params['message'] ?? 'RBL Rule Triggered!';

        // Simulasi pengiriman email / notifikasi dashboard
        Log::info("🔔 [RBL Action] Mengirim Notifikasi ke {$email}: {$message} | Ref: ".get_class($entity).' ID: '.($entity->id ?? 'Unknown'));
    }
}
