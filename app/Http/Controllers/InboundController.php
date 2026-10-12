<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\InboundCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InboundController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->where('type', 'Inbound');

        if ($request->filled('search')) {
            $query->where('ref_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate($request->get('per_page', 10))->withQueryString();
        $suppliers = Supplier::all();
        $approvedPOs = Transaction::with(['lines.item', 'user'])
            ->where('type', 'PO')
            ->where('status', 'Approved')
            ->latest()
            ->get();

        return view('transactions.inbound.index', compact('transactions', 'suppliers', 'approvedPOs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|max:255|unique:transactions,ref_number',
            'transaction_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'po_id' => 'nullable|exists:transactions,id',
            'status' => 'required|in:Draft,Completed',
            'notes' => 'nullable|string|max:500',
        ]);

        $po = null;
        $notes = $request->notes;

        if ($request->filled('po_id')) {
            $po = Transaction::with('lines.item')->where('type', 'PO')->findOrFail($request->po_id);
            $poNote = "Tarik dari Purchase Order: {$po->ref_number}";
            $notes = $notes ? ($poNote.' | '.$notes) : $poNote;
        }

        $transaction = DB::transaction(function () use ($request, $po, $notes) {
            $trx = Transaction::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'user_id' => auth()->id(),
                'type' => 'Inbound',
                'ref_number' => $request->ref_number,
                'transaction_date' => $request->transaction_date,
                'status' => $request->status,
                'notes' => $notes,
            ]);

            // If created from PO, copy items from PO
            if ($po && $po->lines->isNotEmpty()) {
                foreach ($po->lines as $line) {
                    TransactionLine::create([
                        'transaction_id' => $trx->id,
                        'item_id' => $line->item_id,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->unit_price,
                    ]);
                }

                $total = $trx->lines()->sum(DB::raw('quantity * unit_price'));
                $trx->update(['total_amount' => $total]);
            }

            // If immediately marked completed
            if ($request->status === 'Completed') {
                $lines = $trx->lines;
                foreach ($lines as $line) {
                    $item = Item::lockForUpdate()->find($line->item_id);
                    if ($item && $item->type === 'barang') {
                        $item->increment('stock', $line->quantity);
                    }
                }

                if ($po) {
                    $po->update(['status' => 'Completed']);
                }
            }

            AuditLog::record('INBOUND_CREATED', 'Transaction', $trx->id, null, [
                'ref_number' => $trx->ref_number,
                'status' => $trx->status,
                'po_ref' => $po ? $po->ref_number : null,
            ]);

            return $trx;
        });

        if ($transaction->status === 'Completed') {
            try {
                event(new StockUpdated("Penerimaan barang (Inbound) {$transaction->ref_number} selesai."));
            } catch (\Throwable $e) {
                Log::warning('StockUpdated event broadcast error: '.$e->getMessage());
            }

            $usersToNotify = User::whereIn('role', ['admin_gudang', 'manager', 'super_admin'])->get();
            foreach ($usersToNotify as $user) {
                $user->notify(new InboundCompletedNotification($transaction));
            }
        }

        return redirect()->route('transactions.inbound.show', $transaction->id)->with('success', 'Transaksi Inbound berhasil dibuat.'.($po ? ' Item dari PO '.$po->ref_number.' telah disalin otomatis.' : ''));
    }

    public function show($id)
    {
        $transaction = Transaction::with(['lines.item', 'user'])->findOrFail($id);
        $items = Item::where('type', 'barang')->get();

        return view('transactions.inbound.show', compact('transaction', 'items'));
    }

    public function addLine(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa menambah item ke transaksi yang sudah Selesai.');
        }

        DB::transaction(function () use ($request, $transaction) {
            TransactionLine::create([
                'transaction_id' => $transaction->id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price ?? 0,
            ]);

            $total = $transaction->lines()->sum(DB::raw('quantity * unit_price'));
            $transaction->update(['total_amount' => $total]);
        });

        return redirect()->route('transactions.inbound.show', $transaction->id)->with('success', 'Barang ditambahkan.');
    }

    public function destroyLine($id)
    {
        $line = TransactionLine::findOrFail($id);
        $transaction = Transaction::findOrFail($line->transaction_id);

        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa menghapus item dari transaksi yang sudah Selesai.');
        }

        DB::transaction(function () use ($line, $transaction) {
            $line->delete();
            $total = $transaction->lines()->sum(DB::raw('quantity * unit_price'));
            $transaction->update(['total_amount' => $total]);
        });

        return redirect()->back()->with('success', 'Item dihapus.');
    }

    public function complete($id)
    {
        $transaction = Transaction::with('lines')->findOrFail($id);

        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Transaksi sudah berstatus Selesai.');
        }

        $completed = false;
        try {
            DB::transaction(function () use ($transaction, &$completed) {
                $lockedTrx = Transaction::lockForUpdate()->findOrFail($transaction->id);
                if ($lockedTrx->status === 'Completed') {
                    return;
                }

                $total = $lockedTrx->lines()->sum(DB::raw('quantity * unit_price'));
                $lockedTrx->update([
                    'status' => 'Completed',
                    'total_amount' => $total,
                ]);

                foreach ($lockedTrx->lines as $line) {
                    $item = Item::lockForUpdate()->find($line->item_id);
                    if ($item && $item->type === 'barang') {
                        $item->increment('stock', $line->quantity);
                    }
                }
                $completed = true;

                // Check if there is a linked PO in notes
                if ($transaction->notes && preg_match('/Purchase Order:\s*([A-Za-z0-9\-_]+)/', $transaction->notes, $matches)) {
                    $poRef = $matches[1];
                    Transaction::where('type', 'PO')
                        ->where('ref_number', $poRef)
                        ->where('status', 'Approved')
                        ->update(['status' => 'Completed']);
                }

                AuditLog::record('INBOUND_COMPLETED', 'Transaction', $transaction->id, null, [
                    'ref_number' => $transaction->ref_number,
                    'total_amount' => $total,
                    'lines_count' => $transaction->lines->count(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (! $completed) {
            return redirect()->back()->with('error', 'Transaksi sudah berstatus Selesai sebelumnya.');
        }

        try {
            event(new StockUpdated("Penerimaan barang (Inbound) {$transaction->ref_number} selesai. Stok telah ditambahkan."));
        } catch (\Throwable $e) {
            Log::warning('StockUpdated event broadcast error: '.$e->getMessage());
        }

        // Notify roles about completed inbound
        $usersToNotify = User::whereIn('role', ['admin_gudang', 'manager', 'super_admin'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new InboundCompletedNotification($transaction));
        }

        return redirect()->route('transactions.inbound')->with('success', 'Transaksi Selesai. Stok berhasil ditambahkan.');
    }
}
