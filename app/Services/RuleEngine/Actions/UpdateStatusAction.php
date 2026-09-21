<?php

namespace App\Services\RuleEngine\Actions;

class UpdateStatusAction implements ActionInterface
{
    /**
     * Mengeksekusi aksi Update Status.
     *
     * @param  array|null  $params  Parameter dari JSON (contoh: ['status' => 'Pending Approval'])
     * @param  mixed  $entity  Model Eloquent yang memicu event (misal: Transaction)
     */
    public function execute(?array $params, $entity): void
    {
        if (isset($params['status']) && method_exists($entity, 'update')) {
            $entity->update([
                'status' => $params['status'],
            ]);
        }
    }
}
