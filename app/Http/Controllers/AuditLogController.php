<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $perPage = (int) $request->get('per_page', 15);
        $logs = $query->paginate($perPage)->withQueryString();

        return view('system.audit.index', compact('logs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'entity_type' => 'nullable|string|max:255',
            'entity_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        AuditLog::record(
            $validated['action'],
            $validated['entity_type'] ?? null,
            $validated['entity_id'] ?? null,
            null,
            ['notes' => $validated['notes'] ?? null]
        );

        return redirect()->back()->with('success', 'Catatan audit berhasil ditambahkan.');
    }
}
