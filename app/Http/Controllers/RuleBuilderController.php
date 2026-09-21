<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use Illuminate\Http\Request;

class RuleBuilderController extends Controller
{
    public function index()
    {
        $rules = AutomationRule::latest()->get();

        return view('system.rbl.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'event' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Default empty condition and action for new rule
        AutomationRule::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'name' => $request->name,
            'description' => $request->description,
            'event' => $request->event,
            'conditions' => [],
            'actions' => [],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('system.rbl')->with('success', 'Rule berhasil dibuat.');
    }

    // In a real application, you'd add update() and destroy() methods
    // as well as methods to add/remove conditions and actions to the JSON arrays.
}
