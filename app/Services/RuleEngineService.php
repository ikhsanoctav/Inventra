<?php

namespace App\Services;

use App\Events\StockUpdated;
use App\Models\AutomationRule;
use Illuminate\Support\Facades\Log;

class RuleEngineService
{
    /**
     * Evaluate rules for a specific event and payload.
     *
     * @param  string  $event  Event name (e.g. 'stock.updated')
     * @param  array  $payload  Data payload (e.g. ['item' => $item, 'tenant_id' => 1])
     */
    public function evaluate(string $event, array $payload)
    {
        $tenantId = $payload['tenant_id'] ?? null;
        if (! $tenantId) {
            return;
        }

        // Get all active rules for this event
        $rules = AutomationRule::where('tenant_id', $tenantId)
            ->where('event', $event)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            if ($this->checkConditions($rule->conditions, $payload)) {
                $this->executeActions($rule->actions, $payload);
            }
        }
    }

    /**
     * Check if all conditions match the payload.
     */
    protected function checkConditions(array $conditions, array $payload): bool
    {
        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '==';
            $expectedValue = $condition['value'] ?? null;

            // Extract field value from payload (supports dot notation like 'item.stock')
            $actualValue = data_get($payload, $field);

            $match = match ($operator) {
                '==' => $actualValue == $expectedValue,
                '!=' => $actualValue != $expectedValue,
                '>' => $actualValue > $expectedValue,
                '>=' => $actualValue >= $expectedValue,
                '<' => $actualValue < $expectedValue,
                '<=' => $actualValue <= $expectedValue,
                default => false,
            };

            if (! $match) {
                return false; // All conditions must pass (AND logic)
            }
        }

        return true;
    }

    /**
     * Execute the actions defined in the rule.
     */
    protected function executeActions(array $actions, array $payload)
    {
        foreach ($actions as $action) {
            $type = $action['type'] ?? null;

            if ($type === 'notify') {
                $targetRole = $action['target'] ?? 'manager';
                $message = $action['message'] ?? 'Notification triggered by rule.';

                // Proof of concept: Log the notification
                // In a real app, this could dispatch an email, push notification, or save to a notifications table
                Log::info("RULE ENGINE [NOTIFY {$targetRole}]: {$message}", ['payload' => $payload]);

                // We can also fire a generic event here that WebSockets can pick up
                if (class_exists(StockUpdated::class)) {
                    event(new StockUpdated("Otomatisasi: {$message}"));
                }
            }

            // Further actions could be 'auto_order', 'suspend_user', etc.
        }
    }
}
