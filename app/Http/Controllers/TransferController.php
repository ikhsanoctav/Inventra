<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\TransferCompletedNotification;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->where('type', 'Transfer');

        if ($request->filled('search')) {
            $query->where('ref_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = request('per_page', 10);
        $transfers = $query->latest()->paginate($perPage)->appends(request()->all());

        return view('transactions.transfer.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();

        return view('transactions.transfer.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|unique:transactions',
            'transaction_date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'notes' => 'nullable|string',
        ]);

        $notesData = json_encode([
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id' => $request->to_warehouse_id,
            'notes' => $request->notes,
        ]);

        $transfer = Transaction::create([
            'tenant_id' => 1,
            'user_id' => auth()->id(),
            'type' => 'Transfer',
            'ref_number' => $request->ref_number,
            'transaction_date' => $request->transaction_date,
            'status' => 'Draft',
            'notes' => $notesData,
        ]);

        return redirect()->route('transactions.transfer.show', $transfer->id)->with('success', 'Transaksi Mutasi dibuat. Tambahkan barang.');
    }

    public function show($id)
    {
        $transaction = Transaction::with(['lines.item', 'user'])->findOrFail($id);
        $items = Item::where('type', 'barang')->get();

        $transferData = $transaction->notes ? json_decode($transaction->notes, true) : null;
        $fromWarehouse = $transferData ? Warehouse::find($transferData['from_warehouse_id']) : null;
        $toWarehouse = $transferData ? Warehouse::find($transferData['to_warehouse_id']) : null;

        return view('transactions.transfer.show', compact('transaction', 'items', 'fromWarehouse', 'toWarehouse', 'transferData'));
    }

    public function addLine(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $transaction = Transaction::findOrFail($id);
        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa mengubah mutasi yang sudah selesai.');
        }

        TransactionLine::create([
            'transaction_id' => $transaction->id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'unit_price' => 0,
        ]);

        return redirect()->back()->with('success', 'Barang ditambahkan ke mutasi.');
    }

    public function destroyLine($id)
    {
        $line = TransactionLine::findOrFail($id);
        $transaction = Transaction::findOrFail($line->transaction_id);

        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa mengubah mutasi yang sudah selesai.');
        }

        $line->delete();

        return redirect()->back()->with('success', 'Item dihapus dari mutasi.');
    }

    public function complete($id)
    {
        $transaction = Transaction::with('lines')->findOrFail($id);
        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Mutasi sudah Selesai.');
        }
        $transaction->update(['status' => 'Completed']);

        // Notify roles
        $usersToNotify = User::whereIn('role', ['admin_gudang', 'manager', 'super_admin'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new TransferCompletedNotification($transaction));
        }

        return redirect()->back()->with('success', 'Mutasi Antar Gudang diselesaikan.');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->status === 'Completed') {
            return redirect()->back()->with('error', 'Mutasi yang sudah selesai tidak dapat dihapus.');
        }
        $transaction->delete();

        return redirect()->route('transactions.transfer')->with('success', 'Transaksi mutasi dihapus.');
    }
}
