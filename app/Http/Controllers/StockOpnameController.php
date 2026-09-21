<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\StockOpnameCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->where('type', 'Opname');

        if ($request->filled('search')) {
            $query->where('ref_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $opnames = $query->latest()->get();

        return view('transactions.opname.index', compact('opnames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|unique:transactions',
            'transaction_date' => 'required|date',
        ]);

        $opname = Transaction::create([
            'tenant_id' => 1,
            'user_id' => auth()->id(),
            'type' => 'StockOpname',
            'ref_number' => $request->ref_number,
            'transaction_date' => $request->transaction_date,
            'status' => 'Draft', // Draft means counting is in progress
        ]);

        return redirect()->route('transactions.opname.show', $opname->id)->with('success', 'Dokumen Stock Opname dibuat. Silakan input hitungan fisik.');
    }

    public function show($id)
    {
        $opname = Transaction::with(['lines.item', 'user'])->findOrFail($id);
        $items = Item::where('type', 'barang')->get();

        return view('transactions.opname.show', compact('opname', 'items'));
    }

    public function addLine(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'physical_qty' => 'required|integer|min:0',
        ]);

        $opname = Transaction::findOrFail($id);
        if ($opname->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa mengubah opname yang sudah disetujui/selesai.');
        }

        $item = Item::findOrFail($request->item_id);

        // Calculate variance (Selisih)
        // quantity in TransactionLine will represent the variance (physical - system)
        $system_qty = $item->stock;
        $variance = $request->physical_qty - $system_qty;

        // Check if item already exists in this opname
        $existingLine = TransactionLine::where('transaction_id', $opname->id)
            ->where('item_id', $item->id)->first();

        if ($existingLine) {
            $existingLine->update([
                'quantity' => $variance,
                'unit_price' => $request->physical_qty, // We hijack unit_price to store physical_qty for display
            ]);
        } else {
            TransactionLine::create([
                'transaction_id' => $opname->id,
                'item_id' => $item->id,
                'quantity' => $variance, // Variance to adjust later
                'unit_price' => $request->physical_qty, // Hijack unit_price to store physical count temporarily
            ]);
        }

        return redirect()->back()->with('success', 'Hasil hitung fisik ditambahkan.');
    }

    public function destroyLine($id)
    {
        $line = TransactionLine::findOrFail($id);
        $opname = Transaction::findOrFail($line->transaction_id);

        if ($opname->status === 'Completed') {
            return redirect()->back()->with('error', 'Tidak bisa mengubah opname yang sudah selesai.');
        }

        $line->delete();

        return redirect()->back()->with('success', 'Item dihapus dari opname.');
    }

    public function destroy($id)
    {
        $opname = Transaction::findOrFail($id);
        if ($opname->status === 'Completed') {
            return redirect()->back()->with('error', 'Opname yang sudah selesai tidak dapat dihapus.');
        }
        $opname->delete();

        return redirect()->route('transactions.opname.index')->with('success', 'Dokumen dihapus.');
    }

    public function complete($id)
    {
        $opname = Transaction::with('lines')->findOrFail($id);

        if ($opname->status === 'Completed') {
            return redirect()->back()->with('error', 'Sudah disetujui sebelumnya.');
        }

        DB::transaction(function () use ($opname) {
            foreach ($opname->lines as $line) {
                // Here, quantity is the variance.
                // We just need to set the stock to the physical quantity (which we stored in unit_price)
                $item = Item::lockForUpdate()->find($line->item_id);
                if ($item && $item->type === 'barang') {
                    $item->update(['stock' => $line->unit_price]);
                }
            }
            $opname->update(['status' => 'Completed']);
        });

        // Notify roles
        $usersToNotify = User::whereIn('role', ['manager', 'super_admin'])->get();
        foreach ($usersToNotify as $user) {
            $user->notify(new StockOpnameCompletedNotification($opname));
        }

        return redirect()->back()->with('success', 'Stock Opname selesai! Stok sistem berhasil disesuaikan dengan fisik.');
    }
}
