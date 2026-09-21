<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\PoApprovedNotification;
use App\Notifications\PoPendingNotification;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'lines'])->where('type', 'PO');

        if ($request->filled('search')) {
            $query->where('ref_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pos = $query->latest()->get();

        return view('transactions.po.index', compact('pos'));
    }

    public function create()
    {
        return view('transactions.po.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|unique:transactions',
            'transaction_date' => 'required|date',
            'status' => 'required|in:Draft,Pending Approval,Approved',
        ]);

        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => auth()->id(),
            'type' => 'PO',
            'ref_number' => $request->ref_number,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
        ]);

        if ($po->status === 'Pending Approval') {
            $managers = User::whereIn('role', ['manager', 'super_admin'])->get();
            foreach ($managers as $manager) {
                $manager->notify(new PoPendingNotification($po));
            }
        }

        return redirect()->route('transactions.po.show', $po->id)->with('success', 'PO dibuat. Tambahkan barang.');
    }

    public function show($id)
    {
        $po = Transaction::with(['lines.item', 'user'])->findOrFail($id);
        $items = Item::where('type', 'barang')->get();

        return view('transactions.po.show', compact('po', 'items'));
    }

    public function addLine(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $po = Transaction::findOrFail($id);
        if (in_array($po->status, ['Approved', 'Completed'])) {
            return redirect()->back()->with('error', 'Tidak bisa mengubah item PO yang sudah disetujui.');
        }

        TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
        ]);

        return redirect()->back()->with('success', 'Item ditambahkan ke PO.');
    }

    public function destroyLine($id)
    {
        $line = TransactionLine::findOrFail($id);
        $po = Transaction::findOrFail($line->transaction_id);

        if (in_array($po->status, ['Approved', 'Completed'])) {
            return redirect()->back()->with('error', 'Tidak bisa mengubah item PO yang sudah disetujui.');
        }

        $line->delete();

        return redirect()->back()->with('success', 'Item dihapus dari PO.');
    }

    public function destroy($id)
    {
        $po = Transaction::findOrFail($id);
        if ($po->status === 'Approved') {
            return redirect()->back()->with('error', 'PO yang sudah disetujui tidak dapat dihapus.');
        }
        $po->delete();

        return redirect()->route('transactions.po.index')->with('success', 'PO dihapus.');
    }

    public function approve($id)
    {
        $po = Transaction::findOrFail($id);
        // Only manager/super_admin can approve
        if (! auth()->user()->hasRole(['manager', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui PO.');
        }

        $po->update(['status' => 'Approved']);

        if ($po->user) {
            $po->user->notify(new PoApprovedNotification($po));
        }
        $purchasings = User::whereIn('role', ['purchasing', 'super_admin'])
            ->where('id', '!=', $po->user_id)
            ->get();
        foreach ($purchasings as $purchasing) {
            $purchasing->notify(new PoApprovedNotification($po));
        }

        return redirect()->back()->with('success', 'Purchase Order berhasil disetujui.');
    }
}
