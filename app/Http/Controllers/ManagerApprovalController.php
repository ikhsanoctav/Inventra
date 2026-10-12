<?php

namespace App\Http\Controllers;

use App\Models\DamagedItem;
use App\Models\DataRequest;
use App\Models\Transaction;

class ManagerApprovalController extends Controller
{
    public function index()
    {
        // 1. Purchase Orders pending approval
        $pendingPOs = Transaction::with(['user', 'lines.item'])
            ->where('type', 'PO')
            ->whereIn('status', ['Pending Approval', 'Draft'])
            ->latest()
            ->get();

        // 2. Stock Opnames
        $pendingOpnames = Transaction::with(['user', 'lines.item'])
            ->where('type', 'Opname')
            ->latest()
            ->take(15)
            ->get();

        // 3. Warehouse Data Requests pending approval
        $pendingRequests = DataRequest::with(['user', 'reviewer'])
            ->where('status', 'Menunggu')
            ->latest()
            ->get();

        // 4. Damaged Items pending manager review / action
        $pendingDamagedItems = DamagedItem::with(['user', 'item.uom', 'branch'])
            ->where('status', 'Dalam Karantina')
            ->latest()
            ->get();

        // 5. Recent Processed Decisions (Approved/Rejected)
        $recentPOs = Transaction::with(['user', 'lines.item'])
            ->where('type', 'PO')
            ->whereIn('status', ['Approved', 'Rejected', 'Completed'])
            ->latest()
            ->take(10)
            ->get();

        $recentRequests = DataRequest::with(['user', 'reviewer'])
            ->whereIn('status', ['Disetujui', 'Ditolak'])
            ->latest()
            ->take(10)
            ->get();

        $recentDamagedItems = DamagedItem::with(['user', 'item.uom', 'branch'])
            ->whereIn('status', ['Dimusnahkan', 'Retur Supplier', 'Selesai', 'Dibatalkan'])
            ->latest()
            ->take(10)
            ->get();

        return view('manager.approvals.index', compact(
            'pendingPOs',
            'pendingOpnames',
            'pendingRequests',
            'pendingDamagedItems',
            'recentPOs',
            'recentRequests',
            'recentDamagedItems'
        ));
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
