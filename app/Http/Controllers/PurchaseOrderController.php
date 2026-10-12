<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\PoApprovedNotification;
use App\Notifications\PoPendingNotification;
use App\Notifications\PoRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'lines.item'])->where('type', 'PO');

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

        $pos = $query->latest()->get();

        $stats = [
            'total' => Transaction::where('type', 'PO')->count(),
            'draft' => Transaction::where('type', 'PO')->where('status', 'Draft')->count(),
            'pending' => Transaction::where('type', 'PO')->where('status', 'Pending Approval')->count(),
            'approved' => Transaction::where('type', 'PO')->where('status', 'Approved')->count(),
            'completed' => Transaction::where('type', 'PO')->where('status', 'Completed')->count(),
        ];

        return view('transactions.po.index', compact('pos', 'stats'));
    }

    public function create()
    {
        $autoRef = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
        while (Transaction::where('ref_number', $autoRef)->exists()) {
            $autoRef = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
        }

        $items = Item::where('type', 'barang')->orderBy('name')->get();

        return view('transactions.po.create', compact('autoRef', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_number' => 'required|string|unique:transactions,ref_number',
            'transaction_date' => 'required|date',
            'status' => 'required|in:Draft,Pending Approval,Approved',
            'notes' => 'nullable|string|max:500',
        ]);

        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => auth()->id(),
            'type' => 'PO',
            'ref_number' => $request->ref_number,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
            'notes' => $request->notes,
            'total_amount' => 0,
        ]);

        AuditLog::record('PO_CREATED', 'Transaction', $po->id, null, [
            'ref_number' => $po->ref_number,
            'status' => $po->status,
            'created_by' => auth()->user()->name,
        ]);

        if ($po->status === 'Pending Approval') {
            $managers = User::whereIn('role', ['manager', 'super_admin'])->get();
            foreach ($managers as $manager) {
                $manager->notify(new PoPendingNotification($po));
            }
        }

        return redirect()->route('transactions.po.show', $po->id)
            ->with('success', "Purchase Order {$po->ref_number} berhasil dibuat. Silakan tambahkan barang pesanan.");
    }

    public function show($id)
    {
        $po = Transaction::with(['lines.item.category', 'lines.item.uom', 'lines.item.supplier', 'user'])->findOrFail($id);
        $items = Item::where('type', 'barang')->orderBy('name')->get();

        $this->updatePoTotal($po);

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

        $this->updatePoTotal($po);

        return redirect()->back()->with('success', 'Item berhasil ditambahkan ke Purchase Order.');
    }

    public function updateLine(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $line = TransactionLine::findOrFail($id);
        $po = Transaction::findOrFail($line->transaction_id);

        if (in_array($po->status, ['Approved', 'Completed'])) {
            return redirect()->back()->with('error', 'Tidak bisa mengubah item PO yang sudah disetujui.');
        }

        $line->update([
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
        ]);

        $this->updatePoTotal($po);

        return redirect()->back()->with('success', 'Item PO berhasil diperbarui.');
    }

    public function destroyLine($id)
    {
        $line = TransactionLine::findOrFail($id);
        $po = Transaction::findOrFail($line->transaction_id);

        if (in_array($po->status, ['Approved', 'Completed'])) {
            return redirect()->back()->with('error', 'Tidak bisa mengubah item PO yang sudah disetujui.');
        }

        $line->delete();
        $this->updatePoTotal($po);

        return redirect()->back()->with('success', 'Item dihapus dari Purchase Order.');
    }

    public function submitForApproval($id)
    {
        $po = Transaction::with('lines')->findOrFail($id);

        if ($po->lines->isEmpty()) {
            return redirect()->back()->with('error', 'Purchase Order harus memiliki minimal 1 item sebelum diajukan untuk approval.');
        }

        if (! in_array($po->status, ['Draft', 'Rejected'])) {
            return redirect()->back()->with('error', 'Hanya PO berstatus Draft atau Ditolak yang dapat diajukan untuk approval.');
        }

        $this->updatePoTotal($po);
        $po->update(['status' => 'Pending Approval']);

        $managers = User::whereIn('role', ['manager', 'super_admin'])->get();
        foreach ($managers as $manager) {
            $manager->notify(new PoPendingNotification($po));
        }

        AuditLog::record('PO_SUBMITTED', 'Transaction', $po->id, null, [
            'ref_number' => $po->ref_number,
            'submitted_by' => auth()->user()->name,
            'total_amount' => $po->total_amount,
        ]);

        return redirect()->back()->with('success', "Purchase Order {$po->ref_number} berhasil diajukan untuk persetujuan Manager.");
    }

    public function quickDraft(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $item = Item::with('supplier')->findOrFail($request->item_id);
        $targetStock = max((int) ($item->min_stock * 2), (int) $item->min_stock + 10);
        $recommendedQty = $request->filled('quantity')
            ? (int) $request->quantity
            : max(1, $targetStock - (int) $item->stock);

        $refNumber = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
        while (Transaction::where('ref_number', $refNumber)->exists()) {
            $refNumber = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
        }

        $notes = $item->supplier ? "Supplier Rekomendasi: {$item->supplier->name}" : 'Restock Otomatis';

        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => auth()->id(),
            'type' => 'PO',
            'ref_number' => $refNumber,
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
            'notes' => $notes,
            'total_amount' => 0,
        ]);

        TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $item->id,
            'quantity' => $recommendedQty,
            'unit_price' => $item->standard_price ?? 0,
        ]);

        $this->updatePoTotal($po);

        return redirect()->route('transactions.po.show', $po->id)
            ->with('success', "Draft PO {$refNumber} berhasil dibuat untuk item {$item->name} dengan kuantitas {$recommendedQty}.");
    }

    public function bulkAutoDraft(Request $request)
    {
        $lowStockItems = Item::with('supplier')
            ->where('type', 'barang')
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();

        if ($lowStockItems->isEmpty()) {
            return redirect()->back()->with('info', 'Semua stok dalam batas aman. Tidak ada item kritis yang memerlukan PO darurat.');
        }

        $grouped = $lowStockItems->groupBy('supplier_id');
        $createdCount = 0;

        foreach ($grouped as $supplierId => $items) {
            $supplierName = $supplierId && $items->first()->supplier ? $items->first()->supplier->name : 'Umum';
            $refNumber = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
            while (Transaction::where('ref_number', $refNumber)->exists()) {
                $refNumber = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
            }

            $po = Transaction::create([
                'tenant_id' => 1,
                'user_id' => auth()->id(),
                'type' => 'PO',
                'ref_number' => $refNumber,
                'transaction_date' => now()->toDateString(),
                'status' => 'Draft',
                'notes' => "Auto-draft Restock Stok Kritis [Supplier: {$supplierName}]",
                'total_amount' => 0,
            ]);

            foreach ($items as $item) {
                $targetStock = max((int) ($item->min_stock * 2), (int) $item->min_stock + 10);
                $qty = max(1, $targetStock - (int) $item->stock);

                TransactionLine::create([
                    'transaction_id' => $po->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->standard_price ?? 0,
                ]);
            }

            $this->updatePoTotal($po);
            $createdCount++;
        }

        return redirect()->route('transactions.po.index')
            ->with('success', "{$createdCount} Dokumen Draft Purchase Order berhasil di-generate secara otomatis.");
    }

    public function destroy($id)
    {
        $po = Transaction::findOrFail($id);
        if ($po->status === 'Approved') {
            return redirect()->back()->with('error', 'PO yang sudah disetujui tidak dapat dihapus.');
        }
        $po->delete();

        return redirect()->route('transactions.po.index')->with('success', 'Purchase Order berhasil dihapus.');
    }

    public function approve($id)
    {
        $po = Transaction::findOrFail($id);
        if (! auth()->user()->hasRole(['manager', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui PO.');
        }

        if ($po->status === 'Approved') {
            return redirect()->back()->with('success', "Purchase Order {$po->ref_number} sudah disetujui sebelumnya.");
        }

        $affected = Transaction::where('id', $po->id)
            ->where('status', '!=', 'Approved')
            ->update(['status' => 'Approved']);

        if ($affected === 0) {
            return redirect()->back()->with('success', "Purchase Order {$po->ref_number} sudah disetujui sebelumnya.");
        }

        $po->refresh();
        $this->updatePoTotal($po);

        AuditLog::record('PO_APPROVED', 'Transaction', $po->id, null, [
            'ref_number' => $po->ref_number,
            'approved_by' => auth()->user()->name,
            'total_amount' => $po->total_amount,
        ]);

        if ($po->user) {
            $po->user->notify(new PoApprovedNotification($po));
        }
        $purchasings = User::whereIn('role', ['purchasing', 'super_admin'])
            ->where('id', '!=', $po->user_id)
            ->get();
        foreach ($purchasings as $purchasing) {
            $purchasing->notify(new PoApprovedNotification($po));
        }

        return redirect()->back()->with('success', "Purchase Order {$po->ref_number} berhasil disetujui.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $po = Transaction::findOrFail($id);
        if (! auth()->user()->hasRole(['manager', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menolak PO.');
        }

        if (in_array($po->status, ['Approved', 'Completed'])) {
            return redirect()->back()->with('error', 'PO yang sudah disetujui/selesai tidak dapat ditolak.');
        }

        $reason = $request->reason;
        $po->update([
            'status' => 'Rejected',
            'notes' => ($po->notes ? $po->notes."\n" : '')."[Ditolak: {$reason}]",
        ]);

        AuditLog::record('PO_REJECTED', 'Transaction', $po->id, null, [
            'ref_number' => $po->ref_number,
            'rejected_by' => auth()->user()->name,
            'reason' => $reason,
        ]);

        if ($po->user) {
            $po->user->notify(new PoRejectedNotification($po, $reason));
        }

        $purchasings = User::whereIn('role', ['purchasing', 'super_admin'])
            ->where('id', '!=', $po->user_id)
            ->get();
        foreach ($purchasings as $purchasing) {
            $purchasing->notify(new PoRejectedNotification($po, $reason));
        }

        return redirect()->back()->with('success', "Purchase Order {$po->ref_number} berhasil ditolak.");
    }

    private function updatePoTotal(Transaction $po): void
    {
        $total = $po->lines()->sum(DB::raw('quantity * unit_price'));
        $po->update(['total_amount' => $total]);
    }
}
