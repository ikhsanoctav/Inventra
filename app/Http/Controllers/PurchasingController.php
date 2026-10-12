<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionLine;
use Illuminate\Http\Request;

class PurchasingController extends Controller
{
    /**
     * Display a list of Purchase Orders waiting for approval.
     */
    public function pendingApproval(Request $request)
    {
        $query = Transaction::with(['user', 'lines.item'])
            ->where('type', 'PO')
            ->where('status', 'Pending Approval');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $pos = $query->latest()->get();

        $totalPendingAmount = $pos->sum(function ($po) {
            return $po->total_amount ?: $po->lines->sum(fn ($l) => $l->quantity * $l->unit_price);
        });

        return view('purchasing.pending_approval', compact('pos', 'totalPendingAmount'));
    }

    /**
     * Display the history of approved, completed, or rejected Purchase Orders.
     */
    public function history(Request $request)
    {
        $query = Transaction::with(['user', 'lines.item'])
            ->where('type', 'PO')
            ->whereIn('status', ['Approved', 'Completed', 'Rejected']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $pos = $query->latest()->get();

        $totalHistoricalSpend = $pos->whereIn('status', ['Approved', 'Completed'])->sum(function ($po) {
            return $po->total_amount ?: $po->lines->sum(fn ($l) => $l->quantity * $l->unit_price);
        });

        $stats = [
            'approved_count' => $pos->where('status', 'Approved')->count(),
            'completed_count' => $pos->where('status', 'Completed')->count(),
            'rejected_count' => $pos->where('status', 'Rejected')->count(),
            'total_spend' => $totalHistoricalSpend,
        ];

        return view('purchasing.history', compact('pos', 'stats'));
    }

    /**
     * Display the vendor performance dashboard.
     */
    public function performance(Request $request)
    {
        $query = Supplier::with(['items'])->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->get()->map(function ($supplier) {
            // Find POs referencing items supplied by this vendor
            $itemIds = $supplier->items->pluck('id');
            $poLines = TransactionLine::whereIn('item_id', $itemIds)
                ->whereHas('transaction', fn ($q) => $q->where('type', 'PO')->whereIn('status', ['Approved', 'Completed']))
                ->get();

            $supplier->total_po_items = $poLines->sum('quantity');
            $supplier->total_spend = $poLines->sum(fn ($l) => $l->quantity * $l->unit_price);
            $supplier->order_count = $poLines->pluck('transaction_id')->unique()->count();

            return $supplier;
        });

        $totalVendors = $suppliers->count();
        $totalSpendAll = $suppliers->sum('total_spend');
        $totalVariants = $suppliers->sum('items_count');

        return view('purchasing.performance', compact('suppliers', 'totalVendors', 'totalSpendAll', 'totalVariants'));
    }
}
