<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\InboundCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('transactions.inbound.index', compact('transactions', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|max:255|unique:transactions,ref_number',
            'transaction_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|in:Draft,Completed',
        ]);

        $transaction = Transaction::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'user_id' => auth()->id(),
            'type' => 'Inbound',
            'ref_number' => $request->ref_number,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
        ]);

        AuditLog::record('INBOUND_CREATED', 'Transaction', $transaction->id, null, [
            'ref_number' => $transaction->ref_number,
            'status' => $transaction->status,
        ]);

        return redirect()->route('transactions.inbound.show', $transaction->id)->with('success', 'Transaksi Inbound dibuat. Tambahkan barang.');
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

            // Jika menambah saat Completed, kita tambah stok. Tapi kita block di atas jika completed, so ini save for Draft.
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

        $line->delete();

        return redirect()->back()->with('success', 'Item dihapus.');
    }

    public function complete($id)
    {
        $transaction = Transaction::with('lines')->findOrFail($id);

        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Transaksi sudah berstatus Selesai.');
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update(['status' => 'Completed']);
            foreach ($transaction->lines as $line) {
                $item = Item::lockForUpdate()->find($line->item_id);
                if ($item && $item->type === 'barang') {
                    $item->increment('stock', $line->quantity);
                }
            }

            AuditLog::record('INBOUND_COMPLETED', 'Transaction', $transaction->id, null, [
                'ref_number' => $transaction->ref_number,
                'lines_count' => $transaction->lines->count(),
            ]);
        });

        // Notify roles about completed inbound
        $usersToNotify = User::whereIn('role', ['admin_gudang', 'manager', 'super_admin'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new InboundCompletedNotification($transaction));
        }

        return redirect()->route('transactions.inbound.index')->with('success', 'Transaksi Selesai. Stok berhasil ditambahkan.');
    }
}
