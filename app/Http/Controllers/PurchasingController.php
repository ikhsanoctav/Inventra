<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Transaction;

class PurchasingController extends Controller
{
    /**
     * Display a list of Purchase Orders waiting for approval.
     */
    public function pendingApproval()
    {
        $pos = Transaction::with(['user', 'lines.item'])
            ->where('type', 'PO')
            ->where('status', 'Pending Approval')
            ->latest()
            ->get();

        return view('purchasing.pending_approval', compact('pos'));
    }

    /**
     * Display the history of approved/completed Purchase Orders.
     */
    public function history()
    {
        $pos = Transaction::with(['user', 'lines.item'])
            ->where('type', 'PO')
            ->whereIn('status', ['Approved', 'Completed'])
            ->latest()
            ->get();

        return view('purchasing.history', compact('pos'));
    }

    /**
     * Display the vendor performance dashboard.
     */
    public function performance()
    {
        $suppliers = Supplier::withCount('items')->get();

        return view('purchasing.performance', compact('suppliers'));
    }
}
