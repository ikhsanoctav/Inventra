<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class ManagerApprovalController extends Controller
{
    public function index()
    {
        // Get all POs that need approval
        $pendingPOs = Transaction::with(['user', 'lines'])
            ->where('type', 'PO')
            ->where('status', 'Pending Approval')
            ->orWhere(function ($q) {
                $q->where('type', 'PO')->where('status', 'Draft'); // Often they just submit draft, we can show drafts too or just pending
            })
            ->latest()
            ->get();

        // Get Stock Opname that need approval (if we have a status for it)
        $pendingOpnames = Transaction::with(['user', 'lines'])
            ->where('type', 'Opname')
            ->where('status', 'Draft')
            ->latest()
            ->get();

        return view('manager.approvals.index', compact('pendingPOs', 'pendingOpnames'));
    }

    public function auditOpname()
    {
        $opnames = Transaction::with(['user', 'lines.item'])
            ->where('type', 'Opname')
            ->latest()
            ->get();

        return view('manager.audit.opname', compact('opnames'));
    }
}
