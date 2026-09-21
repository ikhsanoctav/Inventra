<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\OutboundCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutboundController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->where('type', 'Outbound');

        if ($request->filled('search')) {
            $query->where('ref_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = request('per_page', 10);
        $transactions = $query->latest()->paginate($perPage)->appends(request()->all());
        $outlets = Outlet::all();

        return view('transactions.outbound.index', compact('transactions', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|max:255|unique:transactions,ref_number',
            'transaction_date' => 'required|date',
            'status' => 'required|in:Draft,Completed',
        ]);

        $transaction = Transaction::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'user_id' => auth()->id(),
            'type' => 'Outbound',
            'ref_number' => $request->ref_number,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
        ]);

        AuditLog::record('OUTBOUND_CREATED', 'Transaction', $transaction->id, null, [
            'ref_number' => $transaction->ref_number,
            'status' => $transaction->status,
        ]);

        return redirect()->route('transactions.outbound.show', $transaction->id)->with('success', 'Transaksi Outbound dibuat. Tambahkan barang.');
    }

    public function show($id)
    {
        $transaction = Transaction::with(['lines.item', 'user'])->findOrFail($id);
        // Only show items with stock for outbound
        $items = Item::where('type', 'barang')->where('stock', '>', 0)->get();

        return view('transactions.outbound.show', compact('transaction', 'items'));
    }

    public function addLine(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa menambah item ke transaksi yang sudah Selesai.');
        }

        // Validate stock
        $item = Item::findOrFail($request->item_id);
        if ($item->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok saat ini: '.$item->stock);
        }

        DB::transaction(function () use ($request, $transaction) {
            TransactionLine::create([
                'transaction_id' => $transaction->id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'unit_price' => 0,
            ]);
        });

        return redirect()->route('transactions.outbound.show', $transaction->id)->with('success', 'Barang ditambahkan ke daftar.');
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

        try {
            DB::transaction(function () use ($transaction) {
                foreach ($transaction->lines as $line) {
                    $item = Item::lockForUpdate()->find($line->item_id);
                    if ($item && $item->type === 'barang') {
                        if ($item->stock < $line->quantity) {
                            throw new \Exception('Stok item '.$item->name.' tidak mencukupi untuk diproses.');
                        }
                        $item->decrement('stock', $line->quantity);
                    }
                }
                $transaction->update(['status' => 'Completed']);

                AuditLog::record('OUTBOUND_COMPLETED', 'Transaction', $transaction->id, null, [
                    'ref_number' => $transaction->ref_number,
                    'lines_count' => $transaction->lines->count(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Notify roles
        $usersToNotify = User::whereIn('role', ['admin_gudang', 'manager', 'super_admin'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new OutboundCompletedNotification($transaction));
        }

        return redirect()->back()->with('success', 'Transaksi Selesai. Stok berhasil dikurangi.');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->status === 'Completed') {
            return redirect()->route('transactions.outbound.index')->with('error', 'Transaksi yang sudah selesai tidak dapat dihapus.');
        }
        $transaction->delete();

        return redirect()->route('transactions.outbound.index')->with('success', 'Transaksi berhasil dihapus!');
    }
}
