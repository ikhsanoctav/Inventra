<?php

namespace App\Services\RuleEngine;

use App\Models\Rule;
use App\Models\RuleLog;
use Illuminate\Support\Facades\Log;

class RuleEvaluator
{
    /**
     * Mengevaluasi dan menjalankan rules yang cocok.
     */
    public function evaluateRules(string $eventTrigger, $entity, int $tenantId)
    {
        $rules = Rule::where('tenant_id', $tenantId)
            ->where('event_trigger', $eventTrigger)
            ->where('is_active', true)
            ->with(['conditions', 'actions'])
            ->get();

        foreach ($rules as $rule) {
            if ($this->checkConditions($rule->conditions, $entity)) {
                $this->executeActions($rule, $entity);
            }
        }
    }

    /**
     * Memeriksa kondisi-kondisi (IF)
     */
    private function checkConditions($conditions, $entity): bool
    {
        if ($conditions->isEmpty()) {
            return true; // Jika tidak ada kondisi, anggap selalu lolos
        }

        foreach ($conditions as $condition) {
            $field = $condition->field;
            // Baca properti secara dinamis
            $entityValue = $entity->$field ?? null;
            $ruleValue = $condition->value;

            $passed = false;
            switch ($condition->operator) {
                case '>': $passed = $entityValue > $ruleValue;
                    break;
                case '<': $passed = $entityValue < $ruleValue;
                    break;
                case '>=': $passed = $entityValue >= $ruleValue;
                    break;
                case '<=': $passed = $entityValue <= $ruleValue;
                    break;
                case '==': $passed = $entityValue == $ruleValue;
                    break;
                case '!=': $passed = $entityValue != $ruleValue;
                    break;
            }

            if (! $passed && strtoupper($condition->logic_operator) === 'AND') {
                return false;
            }
        }

        return true;
    }

    /**
     * Mengeksekusi Aksi (THEN)
     */
    private function executeActions(Rule $rule, $entity)
    {
        foreach ($rule->actions as $action) {
            try {
                // Konvensi: Class action dinamai sama dengan action_type
                $actionClass = '\\App\\Services\\RuleEngine\\Actions\\'.$action->action_type;
                if (class_exists($actionClass)) {
                    $executor = new $actionClass;
                    $executor->execute($action->action_params, $entity);

                    // Rekam jejak eksekusi yang sukses
                    RuleLog::create([
                        'tenant_id' => $rule->tenant_id,
                        'rule_id' => $rule->id,
                        'entity_type' => get_class($entity),
                        'entity_id' => $entity->id ?? null,
                        'status' => 'Success',
                    ]);
                } else {
                    throw new \Exception("Action class {$actionClass} not found.");
                }
            } catch (\Exception $e) {
                // Rekam jejak eksekusi yang gagal
                RuleLog::create([
                    'tenant_id' => $rule->tenant_id,
                    'rule_id' => $rule->id,
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id ?? null,
                    'status' => 'Failed',
                    'error_message' => $e->getMessage(),
                ]);
                Log::error('RBL Action Failed: '.$e->getMessage());
            }
        }
    }
}
