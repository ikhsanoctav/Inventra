<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * Display the POS interface.
     */
    public function index(Request $request)
    {
        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $activeShift) {
            return redirect()->route('pos.shifts')->with('error', 'Harap buka shift kasir terlebih dahulu sebelum mengakses Terminal POS.');
        }

        $type = $request->query('type', 'barang'); // 'barang' or 'jasa'
        $search = $request->query('search', '');

        $itemsQuery = Item::query();

        if ($type === 'jasa') {
            $itemsQuery->where('type', 'jasa');
        } else {
            $itemsQuery->where('type', 'barang')
                ->where('stock', '>', 0);
        }

        if (! empty($search)) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $initialItems = $itemsQuery->paginate(20);

        return view('pos.index', compact('type', 'initialItems'));
    }

    /**
     * Fetch items for POS (AJAX with Pagination & Search)
     */
    public function items(Request $request)
    {
        $type = $request->query('type', 'barang');
        $search = $request->query('search', '');

        $itemsQuery = Item::query();

        if ($type === 'jasa') {
            $itemsQuery->where('type', 'jasa');
        } else {
            $itemsQuery->where('type', 'barang')
                ->where('stock', '>', 0);
        }

        if (! empty($search)) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Return paginated JSON response (20 items per page)
        $items = $itemsQuery->paginate(20);

        return response()->json($items);
    }

    /**
     * Handle POS Checkout.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $activeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki shift yang aktif. Harap buka shift terlebih dahulu.',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;

            // Calculate total and validate stock
            foreach ($validated['items'] as $cartItem) {
                $item = Item::lockForUpdate()->find($cartItem['id']);

                if ($item->type === 'barang' && $item->stock < $cartItem['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk item: {$item->name}");
                }

                $totalAmount += ($cartItem['quantity'] * $cartItem['price']);
            }

            // Calculate PPN 11%
            $taxAmount = $totalAmount * 0.11;
            $grandTotal = $totalAmount + $taxAmount;

            if ($validated['paid_amount'] < $grandTotal) {
                throw new \Exception('Jumlah bayar kurang dari total belanja (termasuk PPN).');
            }

            // Create Transaction
            $transaction = Transaction::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'user_id' => auth()->id(),
                'type' => 'Outbound', // Treating POS sales as outbound
                'ref_number' => 'POS-'.strtoupper(Str::random(8)),
                'transaction_date' => now()->toDateString(),
                'status' => 'Completed', // POS transactions are instantly completed
                'total_amount' => $grandTotal,
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $validated['paid_amount'],
            ]);

            // Create Lines and Deduct Stock
            foreach ($validated['items'] as $cartItem) {
                $item = Item::lockForUpdate()->find($cartItem['id']);

                if (! $item) {
                    throw new \Exception("Item dengan ID {$cartItem['id']} tidak ditemukan.");
                }

                if ($item->type === 'barang' && $item->stock < $cartItem['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk item {$item->name}. Tersedia: {$item->stock}, diminta: {$cartItem['quantity']}.");
                }

                TransactionLine::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                ]);

                // Deduct stock if it's a physical good
                if ($item->type === 'barang') {
                    $item->decrement('stock', $cartItem['quantity']);

                    // Trigger low stock notification if below threshold
                    $minStock = $item->min_stock ?? 5;
                    if ($item->stock <= $minStock) {
                        $alertUsers = User::whereIn('role', ['super_admin', 'manager', 'admin_gudang', 'purchasing'])->get();
                        foreach ($alertUsers as $alertUser) {
                            $alertUser->notify(new LowStockNotification($item, $item->stock));
                        }
                    }
                }
            }

            // Update shift total sales
            $activeShift->total_sales += $totalAmount;
            $activeShift->save();

            // Record audit log for POS sale
            AuditLog::record('POS_SALE', 'Transaction', $transaction->id, null, [
                'ref_number' => $transaction->ref_number,
                'total_amount' => $grandTotal,
                'paid_amount' => $validated['paid_amount'],
                'items_count' => count($validated['items']),
                'payment_method' => $validated['payment_method'],
            ]);

            DB::commit();

            // Broadcast that stock was updated
            event(new StockUpdated('Penjualan Kasir (POS) terbaru: '.$transaction->ref_number));

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil',
                'transaction_ref' => $transaction->ref_number,
                'change' => max(0, $validated['paid_amount'] - $grandTotal),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Print POS Receipt
     */
    public function receipt($ref_number)
    {
        $transaction = Transaction::with(['lines.item', 'user'])
            ->where('ref_number', $ref_number)
            ->where('type', 'Outbound')
            ->firstOrFail();

        return view('pos.receipt', compact('transaction'));
    }

    public function historyToday()
    {
        $query = Transaction::with('lines.item', 'user')
            ->where('ref_number', 'like', 'POS-%')
            ->where('user_id', auth()->id());

        // Calculate overall stats for all time
        $totalRevenue = $query->sum('total_amount');
        $totalTransactions = $query->count();

        $totalItems = TransactionLine::whereIn('transaction_id', $query->pluck('id'))
            ->sum('quantity');

        if (request()->filled('search')) {
            $search = request('search');
            $query->where('ref_number', 'like', "%{$search}%");
        }

        $perPage = request('per_page', 15);
        $transactions = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(request()->all());
        $groupedTransactions = $transactions->groupBy(function ($item) {
            return Carbon::parse($item->transaction_date)->format('d F Y');
        });

        return view('pos.history', compact('transactions', 'groupedTransactions', 'totalRevenue', 'totalTransactions', 'totalItems'));
    }

    public function shifts()
    {
        $perPage = request('per_page', 15);
        $shifts = Shift::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)->appends(request()->all());

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        return view('pos.shifts', compact('shifts', 'activeShift'));
    }

    public function openShift(Request $request)
    {
        Log::info('openShift method called!', $request->all());

        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            return back()->with('error', 'Anda masih memiliki shift yang aktif.');
        }

        Shift::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'total_sales' => 0,
            'status' => 'active',
        ]);

        return back()->with('success', 'Shift baru berhasil dibuka.');
    }

    public function closeShift(Request $request)
    {
        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $activeShift) {
            return back()->with('error', 'Tidak ada shift aktif yang bisa ditutup.');
        }

        $activeShift->update([
            'end_time' => now(),
            'status' => 'closed',
            // closing_cash could be added if needed, but not in current model schema
        ]);

        return back()->with('success', 'Shift berhasil ditutup.');
    }

    public function returns(Request $request)
    {
        $transaction = null;
        if ($request->has('ref_number')) {
            $transaction = Transaction::with(['lines.item', 'user'])
                ->where('ref_number', $request->ref_number)
                ->where('type', 'Outbound')
                ->first();
        }

        return view('pos.returns', compact('transaction'));
    }

    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $transaction = Transaction::with('lines.item')->findOrFail($id);

        if ($transaction->type !== 'Outbound') {
            return back()->with('error', 'Hanya transaksi penjualan (Outbound) yang dapat diretur.');
        }

        try {
            DB::beginTransaction();

            $totalReturnedAmount = 0;
            $itemsReturnedCount = 0;

            foreach ($request->items as $lineId => $qtyToReturn) {
                if ($qtyToReturn <= 0) {
                    continue;
                }

                $line = $transaction->lines->where('id', $lineId)->first();
                if (! $line) {
                    continue;
                }

                if ($qtyToReturn > $line->quantity) {
                    throw new \Exception("Jumlah retur untuk item {$line->item->name} melebihi jumlah pembelian.");
                }

                $amount = $qtyToReturn * $line->unit_price;
                $totalReturnedAmount += $amount;
                $itemsReturnedCount++;

                // Return stock to Item inventory
                $item = Item::find($line->item_id);
                if ($item && $item->type === 'barang') {
                    $item->increment('stock', $qtyToReturn);
                }
            }

            if ($itemsReturnedCount === 0) {
                throw new \Exception('Pilih minimal satu item untuk diretur.');
            }

            // Create return transaction record
            $returnTransaction = Transaction::create([
                'tenant_id' => $transaction->tenant_id ?? null,
                'branch_id' => $transaction->branch_id ?? null,
                'user_id' => auth()->id(),
                'type' => 'return',
                'ref_number' => 'RET-'.strtoupper(Str::random(6)),
                'transaction_date' => now(),
                'status' => 'completed',
                'total_amount' => -$totalReturnedAmount, // Negative to denote refund
                'payment_method' => $transaction->payment_method,
                'paid_amount' => -$totalReturnedAmount,
                'notes' => 'Retur dari ref: '.$transaction->ref_number.' - Alasan: '.$request->reason,
            ]);

            // Deduct from active shift
            $activeShift = Shift::where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();

            if ($activeShift) {
                $activeShift->total_sales -= $totalReturnedAmount;
                $activeShift->save();
            }

            // Record audit log for POS return
            AuditLog::record('POS_RETURN', 'Transaction', $returnTransaction->id, null, [
                'original_ref' => $transaction->ref_number,
                'return_ref' => $returnTransaction->ref_number,
                'total_refund' => $totalReturnedAmount,
                'reason' => $request->reason,
                'items_count' => $itemsReturnedCount,
            ]);

            DB::commit();

            return redirect()->route('pos.returns')->with('success', 'Retur penjualan sebesar Rp '.number_format($totalReturnedAmount, 0, ',', '.').' berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses retur: '.$e->getMessage());
        }
    }
}
