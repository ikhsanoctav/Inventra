<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function stock(Request $request)
    {
        $items = Item::with(['category', 'uom'])->get();

        return view('reports.stock', compact('items'));
    }

    public function transactions()
    {
        $transactions = Transaction::with(['user', 'lines.item'])->latest()->get();

        return view('reports.transactions', compact('transactions'));
    }

    public function mutation(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $transactions = TransactionLine::with(['transaction', 'item'])
            ->whereHas('transaction', function ($q) use ($month, $year) {
                $q->whereMonth('transaction_date', $month)
                    ->whereYear('transaction_date', $year)
                    ->where('status', 'Completed');
            })
            ->get();

        return view('reports.mutation', compact('transactions', 'month', 'year'));
    }

    public function sales(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $sales = Transaction::where(function ($q) {
            $q->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->orWhere('ref_number', 'like', 'POS-%')
                ->orWhere('ref_number', 'like', 'OUT-%');
        })
            ->where('status', 'Completed')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->get();

        return view('reports.sales', compact('sales', 'month', 'year'));
    }

    public function profitLoss(Request $request)
    {
        // Simple Profit/Loss: Revenue (POS) - COGS (Purchases/PO)
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $revenue = Transaction::where(function ($q) {
            $q->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->orWhere('ref_number', 'like', 'POS-%')
                ->orWhere('ref_number', 'like', 'OUT-%');
        })
            ->where('status', 'Completed')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('total_amount');

        // Assume PO is cost. Real COGS needs item moving average, but this is a simplified version.
        $cogs = TransactionLine::whereHas('transaction', function ($q) use ($month, $year) {
            $q->where('type', 'PO')->where('status', 'Approved')
                ->whereMonth('transaction_date', $month)->whereYear('transaction_date', $year);
        })
            ->sum(DB::raw('quantity * unit_price'));

        $grossProfit = $revenue - $cogs;

        return view('reports.profit_loss', compact('revenue', 'cogs', 'grossProfit', 'month', 'year'));
    }

    public function stockValuation()
    {
        $items = Item::where('type', 'barang')->get();
        $totalValuation = 0;
        foreach ($items as $item) {
            $price = $item->standard_price ?? 0;
            $totalValuation += ($item->stock * $price);
        }

        return view('reports.stock_valuation', compact('items', 'totalValuation'));
    }

    public function stockCard(Request $request)
    {
        $items = Item::where('type', 'barang')->get();
        $itemId = $request->get('item_id', $items->first()->id ?? null);

        $history = [];
        $selectedItem = null;
        if ($itemId) {
            $selectedItem = Item::find($itemId);
            $history = TransactionLine::with('transaction')
                ->where('item_id', $itemId)
                ->whereHas('transaction', function ($q) {
                    $q->where('status', 'Completed')->orWhere('status', 'Approved');
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('reports.stock_card', compact('items', 'history', 'selectedItem'));
    }

    public function lowStock()
    {
        $items = Item::where('type', 'barang')
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'min_stock')
                    ->orWhere(function ($sub) {
                        $sub->whereNull('min_stock')->where('stock', '<=', 10);
                    });
            })
            ->get();

        return view('reports.low_stock', compact('items'));
    }

    public function purchasing(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $pos = Transaction::with('lines.item')->where('type', 'PO')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->get();

        return view('reports.purchasing', compact('pos', 'month', 'year'));
    }

    public function trends(Request $request)
    {
        $trendData = $this->getDetailedTrendsData();

        if ($request->wantsJson() || $request->ajax()) {
            $period = $request->get('period', 'bulanan');
            if (isset($trendData[$period])) {
                return response()->json(array_merge($trendData[$period], [
                    'all' => $trendData,
                ]));
            }

            return response()->json($trendData);
        }

        return view('manager.trends', compact('trendData'));
    }

    /**
     * Get multi-period trends data for Manager Sales Trends Report.
     */
    protected function getDetailedTrendsData(): array
    {
        $salesQueryFn = function () {
            return Transaction::where(function ($q) {
                $q->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                    ->orWhere('ref_number', 'like', 'POS-%')
                    ->orWhere('ref_number', 'like', 'OUT-%')
                    ->orWhere('ref_number', 'like', 'TRX-%');
            })->where('status', 'Completed');
        };

        // 1. HARIAN (Hari ini per 2 jam: 08:00 - 22:00)
        $harianLabels = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];
        $harianData = array_fill(0, 8, 0.0);
        $harianRawData = array_fill(0, 8, 0);

        $todayTxs = $salesQueryFn()
            ->whereDate('transaction_date', now()->toDateString())
            ->get();

        $todayTotal = 0;
        foreach ($todayTxs as $tx) {
            $hour = (int) $tx->created_at->format('H');
            $idx = min(7, max(0, (int) floor(($hour - 8) / 2)));
            if ($hour < 8) {
                $idx = 0;
            }
            $amt = (float) $tx->total_amount;
            if ($amt <= 0 && $tx->lines) {
                $amt = (float) $tx->lines->sum(fn ($l) => $l->quantity * $l->unit_price);
            }
            $harianRawData[$idx] += $amt;
            $harianData[$idx] = round($harianRawData[$idx] / 1000000, 2);
            $todayTotal += $amt;
        }

        // Yesterday sales for growth comparison
        $yesterdayTotal = (float) $salesQueryFn()
            ->whereDate('transaction_date', now()->subDay()->toDateString())
            ->sum('total_amount');
        if ($yesterdayTotal <= 0) {
            $yesterdayTotal = (float) TransactionLine::whereHas('transaction', fn ($q) => $q->where(function ($sub) {
                $sub->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                    ->orWhere('ref_number', 'like', 'POS-%')
                    ->orWhere('ref_number', 'like', 'OUT-%');
            })->where('status', 'Completed')->whereDate('transaction_date', now()->subDay()->toDateString()))
                ->sum(DB::raw('quantity * unit_price'));
        }

        $harianGrowth = $yesterdayTotal > 0
            ? round((($todayTotal - $yesterdayTotal) / $yesterdayTotal) * 100, 1)
            : ($todayTotal > 0 ? 100.0 : 0.0);

        // Top product today
        $topLineToday = TransactionLine::whereHas('transaction', fn ($q) => $q->where(function ($sub) {
            $sub->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->orWhere('ref_number', 'like', 'POS-%')
                ->orWhere('ref_number', 'like', 'OUT-%');
        })->where('status', 'Completed')->whereDate('transaction_date', now()->toDateString()))
            ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->with('item')
            ->first();

        // 2. MINGGUAN (7 Hari Terakhir)
        $mingguanLabels = [];
        $mingguanData = [];
        $mingguanRaw = [];
        $mingguanTotal = 0;

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayStr = $day->toDateString();
            $mingguanLabels[] = $day->translatedFormat('D (d M)');

            $dayVal = (float) $salesQueryFn()->whereDate('transaction_date', $dayStr)->sum('total_amount');
            if ($dayVal <= 0) {
                $dayVal = (float) TransactionLine::whereHas('transaction', fn ($q) => $q->where(function ($sub) {
                    $sub->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                        ->orWhere('ref_number', 'like', 'POS-%')
                        ->orWhere('ref_number', 'like', 'OUT-%');
                })->where('status', 'Completed')->whereDate('transaction_date', $dayStr))
                    ->sum(DB::raw('quantity * unit_price'));
            }

            $mingguanRaw[] = $dayVal;
            $mingguanData[] = round($dayVal / 1000000, 2);
            $mingguanTotal += $dayVal;
        }

        // Previous 7 days for weekly growth
        $prevWeekTotal = (float) $salesQueryFn()
            ->whereDate('transaction_date', '>=', now()->subDays(13)->toDateString())
            ->whereDate('transaction_date', '<=', now()->subDays(7)->toDateString())
            ->sum('total_amount');

        $mingguanGrowth = $prevWeekTotal > 0
            ? round((($mingguanTotal - $prevWeekTotal) / $prevWeekTotal) * 100, 1)
            : ($mingguanTotal > 0 ? 100.0 : 0.0);

        $mingguanAvgDaily = round($mingguanTotal / 7);

        $topLineWeek = TransactionLine::whereHas('transaction', fn ($q) => $q->where(function ($sub) {
            $sub->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->orWhere('ref_number', 'like', 'POS-%')
                ->orWhere('ref_number', 'like', 'OUT-%');
        })->where('status', 'Completed')->whereDate('transaction_date', '>=', now()->subDays(6)->toDateString()))
            ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->with('item')
            ->first();

        // 3. BULANAN (6 Bulan Terakhir)
        $bulananLabels = [];
        $bulananData = [];
        $bulananRaw = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulananLabels[] = $date->translatedFormat('F');

            $monthVal = (float) $salesQueryFn()
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('total_amount');

            if ($monthVal <= 0) {
                $monthVal = (float) TransactionLine::whereHas('transaction', fn ($q) => $q->where(function ($sub) {
                    $sub->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                        ->orWhere('ref_number', 'like', 'POS-%')
                        ->orWhere('ref_number', 'like', 'OUT-%');
                })->where('status', 'Completed')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year))
                    ->sum(DB::raw('quantity * unit_price'));
            }

            $bulananRaw[] = $monthVal;
            $bulananData[] = round($monthVal / 1000000, 2);
        }

        // Current month vs previous month growth
        $currMonthRev = end($bulananRaw);
        $prevMonthRev = count($bulananRaw) >= 2 ? $bulananRaw[count($bulananRaw) - 2] : 0;
        $bulananGrowth = $prevMonthRev > 0
            ? round((($currMonthRev - $prevMonthRev) / $prevMonthRev) * 100, 1)
            : ($currMonthRev > 0 ? 100.0 : 0.0);

        $daysInMonth = max(1, now()->day);
        $bulananAvgDaily = round($currMonthRev / $daysInMonth);

        // Overall / Global Top Product
        $globalTopLine = TransactionLine::whereHas('transaction', fn ($q) => $q->where(function ($sub) {
            $sub->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->orWhere('ref_number', 'like', 'POS-%')
                ->orWhere('ref_number', 'like', 'OUT-%');
        })->where('status', 'Completed'))
            ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->with('item')
            ->first();

        $fallbackProductName = $globalTopLine && $globalTopLine->item ? $globalTopLine->item->name : 'Masker Medis 3-Ply Earloop';

        $topProductTodayName = $topLineToday && $topLineToday->item ? $topLineToday->item->name : $fallbackProductName;
        $topProductWeekName = $topLineWeek && $topLineWeek->item ? $topLineWeek->item->name : $fallbackProductName;
        $topProductMonthName = $globalTopLine && $globalTopLine->item ? $globalTopLine->item->name : $fallbackProductName;

        return [
            'harian' => [
                'labels' => $harianLabels,
                'data' => $harianData,
                'raw_data' => $harianRawData,
                'growth' => $harianGrowth,
                'growthLabel' => 'Pertumbuhan vs Kemarin',
                'avgDaily' => 'Rp '.number_format($todayTotal, 0, ',', '.'),
                'avgLabel' => 'Total Penjualan Hari Ini',
                'topProduct' => $topProductTodayName,
                'topProductLabel' => 'Produk Terlaris Hari Ini',
                'title' => 'Grafik Penjualan Hari Ini (Per Jam)',
                'unit' => 'Juta Rp',
            ],
            'mingguan' => [
                'labels' => $mingguanLabels,
                'data' => $mingguanData,
                'raw_data' => $mingguanRaw,
                'growth' => $mingguanGrowth,
                'growthLabel' => 'Pertumbuhan 7 Hari Terakhir',
                'avgDaily' => 'Rp '.number_format($mingguanAvgDaily, 0, ',', '.').' / hari',
                'avgLabel' => 'Rata-rata Penjualan Harian',
                'topProduct' => $topProductWeekName,
                'topProductLabel' => 'Produk Terlaris Minggu Ini',
                'title' => 'Grafik Penjualan 7 Hari Terakhir',
                'unit' => 'Juta Rp',
            ],
            'bulanan' => [
                'labels' => $bulananLabels,
                'data' => $bulananData,
                'raw_data' => $bulananRaw,
                'growth' => $bulananGrowth,
                'growthLabel' => 'Pertumbuhan Bulan Ini',
                'avgDaily' => 'Rp '.number_format($bulananAvgDaily, 0, ',', '.').' / hari',
                'avgLabel' => 'Rata-rata Transaksi Harian',
                'topProduct' => $topProductMonthName,
                'topProductLabel' => 'Produk Terlaris Periode Ini',
                'title' => 'Grafik Penjualan 6 Bulan Terakhir',
                'unit' => 'Juta Rp',
            ],
        ];
    }

    public function salesBranch(Request $request)
    {
        if ($request->wantsJson()) {
            $branches = Branch::all();
            $data = [];

            foreach ($branches as $branch) {
                $revenue = Transaction::where('ref_number', 'like', 'POS-%')
                    ->where('status', 'Completed')
                    ->where('branch_id', $branch->id)
                    ->sum('total_amount');

                if ($revenue > 0) {
                    $data[] = [
                        'name' => $branch->name,
                        'revenue' => $revenue,
                    ];
                }
            }

            usort($data, function ($a, $b) {
                return $b['revenue'] <=> $a['revenue'];
            });

            return response()->json(['branches' => $data]);
        }

        return view('reports.sales_branch');
    }
}
