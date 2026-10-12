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

                if (preg_match('/-(\d+)$/', $search, $matches)) {
                    $num = intval($matches[1]);
                    $q->orWhere('id', $num)
                        ->orWhere('sku', 'like', "%-{$matches[1]}")
                        ->orWhere('sku', 'like', '%-'.str_pad($num, 4, '0', STR_PAD_LEFT));
                }
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

                if (preg_match('/-(\d+)$/', $search, $matches)) {
                    $num = intval($matches[1]);
                    $q->orWhere('id', $num)
                        ->orWhere('sku', 'like', "%-{$matches[1]}")
                        ->orWhere('sku', 'like', '%-'.str_pad($num, 4, '0', STR_PAD_LEFT));
                }
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
            'customer_name' => 'nullable|string|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_details' => 'nullable|array',
            'notes' => 'nullable|string|max:255',
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

            // Calculate Discount & PPN 11%
            $discount = min($totalAmount, (float) ($validated['discount_amount'] ?? 0));
            $taxableAmount = max(0, $totalAmount - $discount);
            $taxAmount = $taxableAmount * 0.11;
            $grandTotal = $taxableAmount + $taxAmount;

            $paidAmount = (float) $validated['paid_amount'];
            if ($paidAmount < $grandTotal && ! in_array($validated['payment_method'], ['QRIS', 'Debit', 'Transfer'])) {
                throw new \Exception('Jumlah bayar kurang dari total belanja (termasuk PPN).');
            }

            // Construct notes metadata
            $noteParts = [];
            if (! empty($validated['customer_name'])) {
                $noteParts[] = 'Pelanggan: '.$validated['customer_name'];
            }
            if ($discount > 0) {
                $noteParts[] = 'Diskon: Rp '.number_format($discount, 0, ',', '.');
            }
            if (! empty($validated['payment_details']['bank'])) {
                $noteParts[] = 'Bank: '.$validated['payment_details']['bank'];
            }
            if (! empty($validated['payment_details']['ref'])) {
                $noteParts[] = 'Ref: '.$validated['payment_details']['ref'];
            }
            if (! empty($validated['notes'])) {
                $noteParts[] = $validated['notes'];
            }
            $finalNotes = implode(' | ', $noteParts);

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
                'paid_amount' => max($paidAmount, $grandTotal),
                'notes' => $finalNotes ?: null,
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
            $activeShift->total_sales += $grandTotal;
            $activeShift->save();

            // Record audit log for POS sale
            AuditLog::record('POS_SALE', 'Transaction', $transaction->id, null, [
                'ref_number' => $transaction->ref_number,
                'total_amount' => $grandTotal,
                'paid_amount' => max($paidAmount, $grandTotal),
                'items_count' => count($validated['items']),
                'payment_method' => $validated['payment_method'],
                'customer_name' => $validated['customer_name'] ?? 'Walk-in',
            ]);

            DB::commit();

            // Broadcast that stock was updated (graceful if broadcast server is offline)
            try {
                event(new StockUpdated('Penjualan Kasir (POS) terbaru: '.$transaction->ref_number));
            } catch (\Throwable $e) {
                Log::warning('StockUpdated broadcast warning: '.$e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil',
                'transaction_ref' => $transaction->ref_number,
                'change' => max(0, $paidAmount - $grandTotal),
                'grand_total' => $grandTotal,
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

        $activeShiftStats = null;
        if ($activeShift) {
            $shiftTxs = Transaction::where('user_id', auth()->id())
                ->where('created_at', '>=', $activeShift->start_time)
                ->get();

            $salesTxs = $shiftTxs->where('type', 'Outbound')->where('status', 'Completed');
            $returnTxs = $shiftTxs->where('type', 'return');

            $cashSales = $salesTxs->filter(fn ($t) => in_array(strtolower($t->payment_method ?? 'cash'), ['cash', 'tunai']))->sum('total_amount');
            $nonCashSales = $salesTxs->filter(fn ($t) => ! in_array(strtolower($t->payment_method ?? 'cash'), ['cash', 'tunai']))->sum('total_amount');
            $totalReturns = abs($returnTxs->sum('total_amount'));
            $expectedCash = ($activeShift->starting_cash + $cashSales) - $totalReturns;

            $activeShiftStats = [
                'cash_sales' => $cashSales,
                'non_cash_sales' => $nonCashSales,
                'total_sales' => $salesTxs->sum('total_amount'),
                'total_returns' => $totalReturns,
                'expected_cash' => $expectedCash,
                'transaction_count' => $salesTxs->count(),
            ];
        }

        return view('pos.shifts', compact('shifts', 'activeShift', 'activeShiftStats'));
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

        $shift = Shift::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'total_sales' => 0,
            'status' => 'active',
        ]);

        AuditLog::record('POS_SHIFT_OPENED', 'Shift', $shift->id, null, [
            'user_id' => auth()->id(),
            'starting_cash' => $request->starting_cash,
            'start_time' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Shift baru berhasil dibuka.');
    }

    public function closeShift(Request $request)
    {
        $request->validate([
            'ending_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $activeShift) {
            return back()->with('error', 'Tidak ada shift aktif yang bisa ditutup.');
        }

        $activeStartTime = $activeShift->start_time;
        $endTime = now();

        $shiftTxs = Transaction::where('user_id', auth()->id())
            ->where('created_at', '>=', $activeStartTime)
            ->get();

        $salesTxs = $shiftTxs->where('type', 'Outbound')->where('status', 'Completed');
        $returnTxs = $shiftTxs->where('type', 'return');

        $cashSales = $salesTxs->filter(fn ($t) => in_array(strtolower($t->payment_method ?? 'cash'), ['cash', 'tunai']))->sum('total_amount');
        $nonCashSales = $salesTxs->filter(fn ($t) => ! in_array(strtolower($t->payment_method ?? 'cash'), ['cash', 'tunai']))->sum('total_amount');
        $totalReturns = abs($returnTxs->sum('total_amount'));
        $totalSales = $salesTxs->sum('total_amount');

        $expectedCash = ($activeShift->starting_cash + $cashSales) - $totalReturns;
        $endingCash = (float) $request->ending_cash;
        $discrepancy = $endingCash - $expectedCash;

        $activeShift->update([
            'end_time' => $endTime,
            'ending_cash' => $endingCash,
            'total_sales' => $totalSales,
            'status' => 'closed',
            'notes' => $request->notes,
        ]);

        AuditLog::record('POS_SHIFT_CLOSED', 'Shift', $activeShift->id, null, [
            'user_id' => auth()->id(),
            'starting_cash' => $activeShift->starting_cash,
            'cash_sales' => $cashSales,
            'non_cash_sales' => $nonCashSales,
            'total_returns' => $totalReturns,
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'ending_cash' => $endingCash,
            'discrepancy' => $discrepancy,
            'notes' => $request->notes,
        ]);

        $discText = '';
        if ($discrepancy > 0) {
            $discText = ' (Lebih Kas: Rp '.number_format($discrepancy, 0, ',', '.').')';
        } elseif ($discrepancy < 0) {
            $discText = ' (Kurang Kas: Rp '.number_format(abs($discrepancy), 0, ',', '.').')';
        } else {
            $discText = ' (Kas Sesuai / Pas)';
        }

        return redirect()->route('pos.shifts')
            ->with('success', 'Shift berhasil ditutup'.$discText.'. Rekapitulasi kas selesai.')
            ->with('closed_shift_id', $activeShift->id);
    }

    /**
     * Print POS Shift Handover Receipt
     */
    public function shiftReceipt($id)
    {
        $shift = Shift::with('user')->findOrFail($id);

        $startTime = $shift->start_time;
        $endTime = $shift->end_time ?? now();

        $transactions = Transaction::with(['lines.item', 'user'])
            ->where('user_id', $shift->user_id)
            ->where('created_at', '>=', $startTime)
            ->where('created_at', '<=', $endTime)
            ->orderBy('created_at', 'asc')
            ->get();

        $salesTransactions = $transactions->where('type', 'Outbound')->where('status', 'Completed');
        $returnTransactions = $transactions->where('type', 'return');

        $cashSales = $salesTransactions->filter(fn ($t) => in_array(strtolower($t->payment_method ?? 'cash'), ['cash', 'tunai']))->sum('total_amount');
        $qrisSales = $salesTransactions->filter(fn ($t) => strtolower($t->payment_method ?? '') === 'qris')->sum('total_amount');
        $debitSales = $salesTransactions->filter(fn ($t) => strtolower($t->payment_method ?? '') === 'debit')->sum('total_amount');
        $transferSales = $salesTransactions->filter(fn ($t) => strtolower($t->payment_method ?? '') === 'transfer')->sum('total_amount');
        $otherSales = $salesTransactions->sum('total_amount') - ($cashSales + $qrisSales + $debitSales + $transferSales);

        $totalReturns = abs($returnTransactions->sum('total_amount'));
        $totalSales = $salesTransactions->sum('total_amount');

        $totalItemsSold = 0;
        foreach ($salesTransactions as $stx) {
            $totalItemsSold += $stx->lines->sum('quantity');
        }

        $expectedCash = ($shift->starting_cash + $cashSales) - $totalReturns;
        $endingCash = $shift->ending_cash ?? 0;
        $discrepancy = $shift->ending_cash !== null ? ($endingCash - $expectedCash) : 0;

        $stats = [
            'tx_count' => $salesTransactions->count(),
            'return_count' => $returnTransactions->count(),
            'items_sold' => $totalItemsSold,
            'cash_sales' => $cashSales,
            'qris_sales' => $qrisSales,
            'debit_sales' => $debitSales,
            'transfer_sales' => $transferSales,
            'other_sales' => max(0, $otherSales),
            'total_returns' => $totalReturns,
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'ending_cash' => $endingCash,
            'discrepancy' => $discrepancy,
        ];

        return view('pos.shift_receipt', compact('shift', 'transactions', 'stats'));
    }

    /**
     * Get Shift itemized transactions list (AJAX)
     */
    public function shiftTransactions($id)
    {
        $shift = Shift::with('user')->findOrFail($id);
        $startTime = $shift->start_time;
        $endTime = $shift->end_time ?? now();

        $transactions = Transaction::with(['lines.item', 'user'])
            ->where('user_id', $shift->user_id)
            ->where('created_at', '>=', $startTime)
            ->where('created_at', '<=', $endTime)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'shift' => $shift,
            'transactions' => $transactions,
        ]);
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
