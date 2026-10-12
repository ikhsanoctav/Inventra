<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Rule;
use App\Models\RuleAction;
use App\Models\RuleCondition;
use Illuminate\Http\Request;

class RuleBuilderController extends Controller
{
    /**
     * Display a listing of the automation rules.
     */
    public function index()
    {
        $rules = Rule::with(['conditions', 'actions'])->latest()->get();

        return view('system.rbl.index', compact('rules'));
    }

    /**
     * Store a newly created automation rule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_trigger' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $rule = Rule::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'name' => $validated['name'],
            'event_trigger' => $validated['event_trigger'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::record('RBL_RULE_CREATED', 'Rule', $rule->id, null, [
            'name' => $rule->name,
            'event_trigger' => $rule->event_trigger,
            'is_active' => $rule->is_active,
        ]);

        return redirect()->route('system.rbl.show', $rule->id)->with('success', 'Rule otomatisasi berhasil dibuat. Silakan tambahkan kondisi dan aksi.');
    }

    /**
     * Display the specified automation rule details.
     */
    public function show($id)
    {
        $rule = Rule::with(['conditions', 'actions'])->findOrFail($id);

        return view('system.rbl.show', compact('rule'));
    }

    /**
     * Remove the specified automation rule from storage.
     */
    public function destroy($id)
    {
        $rule = Rule::findOrFail($id);

        AuditLog::record('RBL_RULE_DELETED', 'Rule', $rule->id, [
            'name' => $rule->name,
            'event_trigger' => $rule->event_trigger,
        ]);

        // Delete associated conditions and actions
        $rule->conditions()->delete();
        $rule->actions()->delete();
        $rule->delete();

        return redirect()->route('system.rbl')->with('success', 'Rule otomatisasi berhasil dihapus.');
    }

    /**
     * Store a new condition for the rule.
     */
    public function storeCondition(Request $request, $id)
    {
        $rule = Rule::findOrFail($id);

        $validated = $request->validate([
            'field' => 'required|string|max:255',
            'operator' => 'required|string|in:==,!=,>,>=,<,<=',
            'value' => 'required|string|max:255',
            'logic_operator' => 'nullable|string|in:AND,OR',
        ]);

        $condition = RuleCondition::create([
            'rule_id' => $rule->id,
            'field' => $validated['field'],
            'operator' => $validated['operator'],
            'value' => $validated['value'],
            'logic_operator' => $validated['logic_operator'] ?? 'AND',
        ]);

        AuditLog::record('RBL_CONDITION_ADDED', 'RuleCondition', $condition->id, null, [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'condition' => "{$condition->field} {$condition->operator} {$condition->value}",
        ]);

        return redirect()->back()->with('success', 'Kondisi logika (IF) berhasil ditambahkan.');
    }

    /**
     * Delete a specific condition.
     */
    public function destroyCondition($id)
    {
        $condition = RuleCondition::findOrFail($id);
        $ruleId = $condition->rule_id;

        $condition->delete();

        return redirect()->back()->with('success', 'Kondisi berhasil dihapus.');
    }

    /**
     * Store a new action for the rule.
     */
    public function storeAction(Request $request, $id)
    {
        $rule = Rule::findOrFail($id);

        $validated = $request->validate([
            'action_type' => 'required|string|max:255',
            'action_params' => 'nullable|string',
        ]);

        $params = null;
        if ($request->filled('action_params')) {
            $decoded = json_decode($request->action_params, true);
            $params = is_array($decoded) ? $decoded : ['raw' => $request->action_params];
        }

        $action = RuleAction::create([
            'rule_id' => $rule->id,
            'action_type' => $validated['action_type'],
            'action_params' => $params,
        ]);

        AuditLog::record('RBL_ACTION_ADDED', 'RuleAction', $action->id, null, [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'action_type' => $action->action_type,
        ]);

        return redirect()->back()->with('success', 'Aksi eksekusi (THEN) berhasil ditambahkan.');
    }

    /**
     * Delete a specific action.
     */
    public function destroyAction($id)
    {
        $action = RuleAction::findOrFail($id);
        $action->delete();

        return redirect()->back()->with('success', 'Aksi berhasil dihapus.');
    }
}
