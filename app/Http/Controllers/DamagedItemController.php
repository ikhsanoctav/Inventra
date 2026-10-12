<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Models\AuditLog;
use App\Models\DamagedItem;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DamagedItemController extends Controller
{
    /**
     * Display a listing of damaged & quarantined items.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $damageType = $request->query('damage_type');
        $search = $request->query('search');

        $query = DamagedItem::with(['item.category', 'item.uom', 'user'])->latest();

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($damageType)) {
            $query->where('damage_type', $damageType);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        $damagedItems = $query->paginate(15)->appends($request->all());

        // Calculate summary metrics
        $totalItemsCount = DamagedItem::count();
        $totalDamagedUnits = DamagedItem::sum('quantity');
        $quarantineUnits = DamagedItem::where('status', 'Dalam Karantina')->sum('quantity');
        $writtenOffUnits = DamagedItem::where('status', 'Dimusnahkan')->sum('quantity');

        // Total loss valuation
        $totalLossValuation = 0;
        $allDamaged = DamagedItem::with('item')->get();
        foreach ($allDamaged as $dmg) {
            $price = $dmg->item->standard_price ?? 0;
            $totalLossValuation += ($dmg->quantity * $price);
        }

        $items = Item::where('type', 'barang')->where('stock', '>', 0)->orderBy('name')->get();

        return view('gudang.damaged_items.index', compact(
            'damagedItems',
            'totalItemsCount',
            'totalDamagedUnits',
            'quarantineUnits',
            'writtenOffUnits',
            'totalLossValuation',
            'items'
        ));
    }

    /**
     * Store a new damaged goods / write-off / quarantine record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'damage_type' => 'required|string|in:Rusak Fisik,Kadaluarsa / Expired,Cacat Pabrik,Hilang / Selisih,Lainnya',
            'action_type' => 'required|string|in:Karantina,Pemusnahan / Write-Off,Retur Supplier',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($item->stock < $validated['quantity']) {
            return back()->with('error', "Kuantitas melebihi stok yang tersedia saat ini (Stok: {$item->stock}).");
        }

        try {
            DB::beginTransaction();

            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            // Determine status
            $status = 'Tercatat';
            if ($validated['action_type'] === 'Karantina') {
                $status = 'Dalam Karantina';
            } elseif ($validated['action_type'] === 'Pemusnahan / Write-Off') {
                $status = 'Dimusnahkan';
            } elseif ($validated['action_type'] === 'Retur Supplier') {
                $status = 'Diretur';
            }

            $refNumber = 'DMG-'.date('Ym').'-'.strtoupper(Str::random(5));

            $damagedRecord = DamagedItem::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'branch_id' => auth()->user()->branch_id ?? null,
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'reference_number' => $refNumber,
                'quantity' => $validated['quantity'],
                'damage_type' => $validated['damage_type'],
                'action_type' => $validated['action_type'],
                'status' => $status,
                'notes' => $validated['notes'],
                'reported_at' => now(),
            ]);

            // Deduct from physical item stock
            $item->decrement('stock', $validated['quantity']);

            // Create Transaction record for stock ledger audit trace
            $trx = Transaction::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'branch_id' => auth()->user()->branch_id ?? null,
                'user_id' => auth()->id(),
                'type' => 'Adjustment',
                'ref_number' => $refNumber,
                'transaction_date' => now()->toDateString(),
                'status' => 'Completed',
                'total_amount' => $validated['quantity'] * ($item->standard_price ?? 0),
                'notes' => "Pencatatan Barang Rusak/Karantina ({$validated['damage_type']} - {$validated['action_type']}). Catatan: {$validated['notes']}",
            ]);

            TransactionLine::create([
                'transaction_id' => $trx->id,
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'unit_price' => $item->standard_price ?? 0,
            ]);

            // Record Audit Log
            AuditLog::record('DAMAGED_GOODS_RECORDED', 'DamagedItem', $damagedRecord->id, null, [
                'ref_number' => $refNumber,
                'item_name' => $item->name,
                'sku' => $item->sku,
                'quantity' => $validated['quantity'],
                'damage_type' => $validated['damage_type'],
                'action_type' => $validated['action_type'],
                'deducted_stock' => true,
            ]);

            DB::commit();

            try {
                event(new StockUpdated("Pencatatan barang rusak/karantina {$refNumber} ({$item->name}: {$validated['quantity']} unit). Stok telah dikurangi."));
            } catch (\Throwable $e) {
                Log::warning('StockUpdated event broadcast error: '.$e->getMessage());
            }

            return redirect()->route('gudang.damaged_items.index')->with('success', "Barang rusak {$item->name} ({$validated['quantity']} Unit) berhasil dicatat dan stok telah disesuaikan.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal mencatat barang rusak: '.$e->getMessage());
        }
    }

    /**
     * Update status of damaged/quarantined item.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Dalam Karantina,Dimusnahkan,Diretur,Selesai Diproses',
            'notes' => 'nullable|string|max:500',
        ]);

        $damaged = DamagedItem::with('item')->findOrFail($id);
        $oldStatus = $damaged->status;

        $damaged->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ? ($damaged->notes."\n[Update: ".$validated['notes'].']') : $damaged->notes,
        ]);

        AuditLog::record('DAMAGED_GOODS_STATUS_UPDATED', 'DamagedItem', $damaged->id, [
            'status' => $oldStatus,
        ], [
            'status' => $validated['status'],
            'ref_number' => $damaged->reference_number,
        ]);

        try {
            event(new StockUpdated("Status barang karantina {$damaged->reference_number} diubah menjadi {$validated['status']}."));
        } catch (\Throwable $e) {
            Log::warning('StockUpdated event broadcast error: '.$e->getMessage());
        }

        return back()->with('success', "Status barang karantina {$damaged->reference_number} berhasil diubah menjadi {$validated['status']}.");
    }
}
