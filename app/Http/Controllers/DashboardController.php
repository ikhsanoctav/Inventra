<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DamagedItem;
use App\Models\DataRequest;
use App\Models\Item;
use App\Models\Shift;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        $totalItems = Item::count();
        $totalStock = Item::sum('stock');
        $totalSuppliers = Supplier::count();
        $criticalStock = Item::where('stock', '<', 10)->count();
        $totalUsers = User::count();
        $activeUsers = User::whereDate('updated_at', '>=', now()->subDays(7))->count();

        if ($role === 'super_admin') {
            $totalInbound = Transaction::where('type', 'Inbound')->sum('total_amount');
            if ($totalInbound <= 0) {
                $totalInbound = TransactionLine::whereHas('transaction', fn ($q) => $q->where('type', 'Inbound'))->sum(DB::raw('quantity * unit_price')) ?? 0;
            }
            $totalOutbound = Transaction::where('type', 'Outbound')->sum('total_amount') ?? 0;
            $netValuation = Item::sum(DB::raw('stock * standard_price')) ?? 0;
            $categories = Category::withCount('items')->withSum('items', 'stock')->get();

            return view('superadmin.index', compact('totalItems', 'totalStock', 'totalSuppliers', 'criticalStock', 'totalUsers', 'activeUsers', 'totalInbound', 'totalOutbound', 'netValuation', 'categories'));
        } elseif ($role === 'manager') {
            $totalSales = Transaction::whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->where('status', 'Completed')
                ->sum('total_amount');
            if ($totalSales <= 0) {
                $totalSales = TransactionLine::whereHas('transaction', fn ($q) => $q->whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])->where('status', 'Completed'))
                    ->sum(DB::raw('quantity * unit_price')) ?? 0;
            }

            $netValuation = Item::where('type', 'barang')->sum(DB::raw('stock * standard_price')) ?? 0;

            // Monthly Sales vs Inbound
            $monthlySales = Transaction::whereIn('type', ['Outbound', 'pos', 'POS', 'Sales'])
                ->where('status', 'Completed')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('total_amount') ?? 0;

            $monthlyInbound = TransactionLine::whereHas('transaction', function ($q) {
                $q->whereIn('type', ['Inbound', 'PO'])
                    ->whereIn('status', ['Approved', 'Completed'])
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
            })->sum(DB::raw('quantity * unit_price')) ?? 0;

            $transactionCount = Transaction::count();

            // Approvals pending
            $pendingPoCount = Transaction::where('type', 'PO')->where('status', 'Pending Approval')->count();
            $pendingRequestsCount = DataRequest::where('status', 'Menunggu')->count();
            $pendingDamagedCount = DamagedItem::where('status', 'Dalam Karantina')->count();
            $totalPendingApprovals = $pendingPoCount + $pendingRequestsCount + $pendingDamagedCount;

            // Top moving items
            $topMovingItems = TransactionLine::with('item.uom')
                ->whereHas('transaction', fn ($q) => $q->whereIn('type', ['Outbound', 'pos', 'POS'])->where('status', 'Completed'))
                ->select('item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * unit_price) as total_val'))
                ->groupBy('item_id')
                ->orderByDesc('total_qty')
                ->take(5)
                ->get();

            // All 3 periods: harian, mingguan, bulanan
            $trendData = $this->getManagerTrendsData();
            $salesData = [
                'labels' => $trendData['mingguan']['labels'],
                'data' => $trendData['mingguan']['outbound'],
                'inbound' => $trendData['mingguan']['inbound'],
            ];

            $recentLogs = AuditLog::with('user')->latest()->take(6)->get();

            $pendingPOs = Transaction::with(['user', 'lines.item'])
                ->where('type', 'PO')
                ->where('status', 'Pending Approval')
                ->latest()
                ->take(5)
                ->get();

            return view('manager.index', compact(
                'totalItems',
                'totalStock',
                'criticalStock',
                'totalSuppliers',
                'totalSales',
                'netValuation',
                'monthlySales',
                'monthlyInbound',
                'transactionCount',
                'pendingPoCount',
                'pendingRequestsCount',
                'pendingDamagedCount',
                'totalPendingApprovals',
                'topMovingItems',
                'salesData',
                'trendData',
                'recentLogs',
                'pendingPOs'
            ));
        } elseif ($role === 'purchasing') {
            $pendingPoCount = Transaction::where('type', 'PO')->where('status', 'Pending Approval')->count();
            $draftPoCount = Transaction::where('type', 'PO')->where('status', 'Draft')->count();
            $approvedPoCount = Transaction::where('type', 'PO')->where('status', 'Approved')->count();
            $completedPoCount = Transaction::where('type', 'PO')->where('status', 'Completed')->count();

            // Total PO spend this month
            $totalPoSpendMonth = TransactionLine::whereHas('transaction', function ($q) {
                $q->where('type', 'PO')
                    ->whereIn('status', ['Approved', 'Completed'])
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
            })->sum(DB::raw('quantity * unit_price')) ?? 0;

            // Low stock items with recommendation
            $lowStockItems = Item::with(['supplier', 'category', 'uom'])
                ->where('type', 'barang')
                ->whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock', 'asc')
                ->get()
                ->map(function ($item) {
                    $targetStock = max((int) ($item->min_stock * 2), (int) $item->min_stock + 10);
                    $recommendedQty = max(1, $targetStock - (int) $item->stock);
                    $item->recommended_qty = $recommendedQty;
                    $item->estimated_cost = $recommendedQty * ($item->standard_price ?? 0);

                    return $item;
                });

            $lowStockCount = $lowStockItems->count();
            $estimatedReorderBudget = $lowStockItems->sum('estimated_cost');

            $recentPos = Transaction::with(['user', 'lines.item'])
                ->where('type', 'PO')
                ->latest()
                ->take(6)
                ->get();

            $topSuppliers = Supplier::withCount('items')
                ->take(5)
                ->get();

            return view('purchasing.index', compact(
                'totalSuppliers',
                'totalItems',
                'totalStock',
                'pendingPoCount',
                'draftPoCount',
                'approvedPoCount',
                'completedPoCount',
                'totalPoSpendMonth',
                'lowStockItems',
                'lowStockCount',
                'estimatedReorderBudget',
                'recentPos',
                'topSuppliers'
            ));
        } elseif ($role === 'kasir') {
            $activeShift = Shift::where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();

            $todayTransactions = Transaction::with('lines.item')
                ->where('user_id', auth()->id())
                ->whereDate('transaction_date', now()->toDateString())
                ->latest()
                ->get();

            $totalStruk = $todayTransactions->count();
            $totalPendapatan = $todayTransactions->sum('total_amount');
            $recentSales = $todayTransactions->take(5);
            $firstSale = $todayTransactions->last(); // because we ordered by latest()
            $waktuMulai = $activeShift ? $activeShift->start_time->format('H:i').' WIB' : '-';

            // Generate chart data for the shift
            $salesChartLabels = [];
            $salesChartData = [];

            $startHour = 8;
            if ($firstSale) {
                $startHour = (int) $firstSale->created_at->format('H');
            }
            $endHour = max((int) now()->format('H'), $startHour + 5);

            for ($i = $startHour; $i <= $endHour; $i++) {
                $salesChartLabels[] = sprintf('%02d:00', $i);
                $salesChartData[] = 0;
            }

            foreach ($todayTransactions as $tx) {
                $hourStr = $tx->created_at->format('H:00');
                $index = array_search($hourStr, $salesChartLabels);
                if ($index !== false) {
                    $salesChartData[$index] += $tx->total_amount;
                }
            }

            // Performance Analysis Metrics
            $averageTransaction = $totalStruk > 0 ? $totalPendapatan / $totalStruk : 0;

            $totalItemsSold = 0;
            foreach ($todayTransactions as $tx) {
                $totalItemsSold += $tx->lines->sum('quantity');
            }

            $peakHour = '-';
            if (count($salesChartData) > 0 && max($salesChartData) > 0) {
                $maxSales = max($salesChartData);
                $peakIndex = array_search($maxSales, $salesChartData);
                if ($peakIndex !== false) {
                    $peakHour = $salesChartLabels[$peakIndex];
                }
            }

            return view('kasir.index', compact(
                'totalStruk', 'totalPendapatan', 'recentSales', 'waktuMulai',
                'salesChartLabels', 'salesChartData',
                'averageTransaction', 'totalItemsSold', 'peakHour', 'activeShift'
            ));
        } elseif ($role === 'admin_gudang') {
            $totalItems = Item::where('type', 'barang')->count();
            $totalStock = Item::where('type', 'barang')->sum('stock') ?? 0;
            $inboundTodayCount = Transaction::where('type', 'Inbound')->whereDate('transaction_date', today())->count();
            $outboundTodayCount = Transaction::where('type', 'Outbound')->whereDate('transaction_date', today())->count();
            $criticalStockCount = Item::where('type', 'barang')->where('stock', '<', 10)->count();
            $damagedItemsCount = DamagedItem::where('status', 'Dalam Karantina')->count();
            $totalDamagedUnits = DamagedItem::where('status', 'Dalam Karantina')->sum('quantity') ?? 0;
            $pendingRequestsCount = DataRequest::where('status', 'Menunggu')->count();
            $approvedPOsCount = Transaction::where('type', 'PO')->where('status', 'Approved')->count();
            $recentTransactions = Transaction::with(['user', 'lines.item'])
                ->whereIn('type', ['Inbound', 'Outbound', 'StockOpname', 'Transfer'])
                ->latest()
                ->take(6)
                ->get();
            $lowStockItems = Item::with('category')
                ->where('type', 'barang')
                ->where('stock', '<=', 10)
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get();
            $recentDamaged = DamagedItem::with('item')
                ->latest()
                ->take(5)
                ->get();

            return view('gudang.index', compact(
                'totalItems',
                'totalStock',
                'inboundTodayCount',
                'outboundTodayCount',
                'criticalStockCount',
                'damagedItemsCount',
                'totalDamagedUnits',
                'pendingRequestsCount',
                'approvedPOsCount',
                'recentTransactions',
                'lowStockItems',
                'recentDamaged'
            ));
        }

        return view('dashboard', compact('totalItems', 'totalStock', 'totalSuppliers', 'criticalStock'));
    }

    public function kasirChartData(Request $request)
    {
        $period = $request->query('period', 'harian');
        $userId = auth()->id();

        $labels = [];
        $data = [];

        if ($period === 'harian') {
            // Data per jam untuk hari ini
            $transactions = Transaction::where('user_id', $userId)
                ->where('type', 'Outbound')
                ->whereDate('transaction_date', now()->toDateString())
                ->get();

            $startHour = 8;
            $firstSale = $transactions->first();
            if ($firstSale) {
                $startHour = (int) $firstSale->created_at->format('H');
            }
            $endHour = max((int) now()->format('H'), $startHour + 5);

            for ($i = $startHour; $i <= $endHour; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $data[] = 0;
            }

            foreach ($transactions as $tx) {
                $hourStr = $tx->created_at->format('H:00');
                $index = array_search($hourStr, $labels);
                if ($index !== false) {
                    $data[$index] += $tx->total_amount;
                }
            }
        } elseif ($period === 'mingguan') {
            // Data per hari selama 7 hari terakhir
            $transactions = Transaction::selectRaw('DATE(transaction_date) as date, SUM(total_amount) as total')
                ->where('user_id', $userId)
                ->where('type', 'Outbound')
                ->whereDate('transaction_date', '>=', now()->subDays(6))
                ->groupBy('date')
                ->pluck('total', 'date');

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->translatedFormat('D'); // Sen, Sel, dll
                $data[] = $transactions->get($date->toDateString()) ?? 0;
            }
        } elseif ($period === 'bulanan') {
            // Data per hari selama bulan ini
            $daysInMonth = now()->daysInMonth;
            $transactions = Transaction::selectRaw('DAY(transaction_date) as day, SUM(total_amount) as total')
                ->where('user_id', $userId)
                ->where('type', 'Outbound')
                ->whereYear('transaction_date', now()->year)
                ->whereMonth('transaction_date', now()->month)
                ->groupBy('day')
                ->pluck('total', 'day');

            for ($i = 1; $i <= $daysInMonth; $i++) {
                // only add labels for every 3 days or so to avoid clutter, or all days
                $labels[] = $i;
                $data[] = $transactions->get($i) ?? 0;
            }
        } elseif ($period === 'tahunan') {
            // Data per bulan selama tahun ini
            $transactions = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(total_amount) as total')
                ->where('user_id', $userId)
                ->where('type', 'Outbound')
                ->whereYear('transaction_date', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month');

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            foreach ($months as $index => $month) {
                $labels[] = $month;
                $data[] = $transactions->get($index + 1) ?? 0;
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function stats()
    {
        $totalItems = Item::count();
        $totalStock = Item::sum('stock');
        $totalSuppliers = Supplier::count();
        $criticalStock = Item::where('stock', '<', 10)->count();
        $totalUsers = User::count();
        $activeUsers = User::whereDate('updated_at', '>=', now()->subDays(7))->count();
        $period = request('period', 30);
        $dateStart = now()->subDays($period);

        $totalInbound = Transaction::where('type', 'Inbound')
            ->whereDate('transaction_date', '>=', $dateStart)
            ->sum('total_amount');

        if ($totalInbound <= 0) {
            $totalInbound = TransactionLine::whereHas('transaction', function ($q) use ($dateStart) {
                $q->where('type', 'Inbound')->whereDate('transaction_date', '>=', $dateStart);
            })->sum(DB::raw('quantity * unit_price')) ?? 0;
        }

        $totalOutbound = Transaction::where('type', 'Outbound')
            ->whereDate('transaction_date', '>=', $dateStart)
            ->sum('total_amount') ?? 0;

        $netValuation = Item::sum(DB::raw('stock * standard_price')) ?? 0;

        // Generate chart data based on period
        $labels = [];
        $inboundData = [];
        $outboundData = [];

        $interval = $period > 60 ? 'month' : 'day';

        $inboundRaw = Transaction::selectRaw('DATE(transaction_date) as date, SUM(total_amount) as total')
            ->where('type', 'Inbound')
            ->whereDate('transaction_date', '>=', $dateStart)
            ->groupBy('date')
            ->pluck('total', 'date');

        $outboundRaw = Transaction::selectRaw('DATE(transaction_date) as date, SUM(total_amount) as total')
            ->where('type', 'Outbound')
            ->whereDate('transaction_date', '>=', $dateStart)
            ->groupBy('date')
            ->pluck('total', 'date');

        for ($i = $period; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $inboundData[] = $inboundRaw->get($date) ?? 0;
            $outboundData[] = $outboundRaw->get($date) ?? 0;
        }

        return response()->json([
            'totalItems' => $totalItems,
            'totalItemsFormatted' => number_format($totalItems, 0, ',', '.'),
            'totalStock' => $totalStock,
            'totalStockFormatted' => number_format($totalStock, 0, ',', '.'),
            'totalSuppliers' => $totalSuppliers,
            'criticalStock' => $criticalStock,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'safeStock' => $totalItems - $criticalStock,
            'totalInbound' => $totalInbound,
            'totalInboundFormatted' => 'Rp '.number_format($totalInbound, 0, ',', '.'),
            'totalOutbound' => $totalOutbound,
            'totalOutboundFormatted' => 'Rp '.number_format($totalOutbound, 0, ',', '.'),
            'netValuation' => $netValuation,
            'netValuationFormatted' => 'Rp '.number_format($netValuation, 0, ',', '.'),
            'chart' => [
                'labels' => $labels,
                'inbound' => $inboundData,
                'outbound' => $outboundData,
            ],
        ]);
    }

    /**
     * Compute multi-period trend data (Harian, Mingguan, Bulanan) for Manager Dashboard.
     */
    protected function getManagerTrendsData(): array
    {
        // 1. HARIAN (Hari ini per 2 jam)
        $harianLabels = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];
        $harianOutbound = [0, 0, 0, 0, 0, 0, 0, 0];
        $harianInbound = [0, 0, 0, 0, 0, 0, 0, 0];

        $todayTxs = Transaction::whereDate('transaction_date', now()->toDateString())->get();
        foreach ($todayTxs as $tx) {
            $hour = (int) $tx->created_at->format('H');
            $idx = min(7, max(0, (int) floor(($hour - 8) / 2)));
            if ($hour < 8) {
                $idx = 0;
            }

            if (in_array($tx->type, ['Outbound', 'pos', 'POS'])) {
                $harianOutbound[$idx] += (float) $tx->total_amount;
            } elseif ($tx->type === 'Inbound') {
                $harianInbound[$idx] += (float) $tx->total_amount;
            }
        }

        // 2. MINGGUAN (7 Hari Terakhir)
        $mingguanLabels = [];
        $mingguanOutbound = [];
        $mingguanInbound = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayStr = $day->toDateString();
            $mingguanLabels[] = $day->translatedFormat('D (d M)');

            $outVal = Transaction::whereIn('type', ['Outbound', 'pos', 'POS'])
                ->whereDate('transaction_date', $dayStr)
                ->sum('total_amount');
            if ($outVal <= 0) {
                $outVal = TransactionLine::whereHas('transaction', fn ($q) => $q->whereIn('type', ['Outbound', 'pos', 'POS'])->whereDate('transaction_date', $dayStr))
                    ->sum(DB::raw('quantity * unit_price')) ?? 0;
            }

            $inVal = Transaction::where('type', 'Inbound')
                ->whereDate('transaction_date', $dayStr)
                ->sum('total_amount');
            if ($inVal <= 0) {
                $inVal = TransactionLine::whereHas('transaction', fn ($q) => $q->where('type', 'Inbound')->whereDate('transaction_date', $dayStr))
                    ->sum(DB::raw('quantity * unit_price')) ?? 0;
            }

            $mingguanOutbound[] = (float) $outVal;
            $mingguanInbound[] = (float) $inVal;
        }

        // 3. BULANAN (12 Bulan Tahun Ini)
        $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $bulananOutbound = array_fill(0, 12, 0);
        $bulananInbound = array_fill(0, 12, 0);

        $yearlyTxs = Transaction::whereYear('transaction_date', now()->year)->get();
        foreach ($yearlyTxs as $tx) {
            $mIdx = (int) Carbon::parse($tx->transaction_date)->format('n') - 1;
            if ($mIdx >= 0 && $mIdx < 12) {
                if (in_array($tx->type, ['Outbound', 'pos', 'POS'])) {
                    $bulananOutbound[$mIdx] += (float) $tx->total_amount;
                } elseif ($tx->type === 'Inbound') {
                    $bulananInbound[$mIdx] += (float) $tx->total_amount;
                }
            }
        }

        return [
            'harian' => [
                'labels' => $harianLabels,
                'outbound' => $harianOutbound,
                'inbound' => $harianInbound,
                'title' => 'Tren Transaksi Hari Ini (Per Jam)',
            ],
            'mingguan' => [
                'labels' => $mingguanLabels,
                'outbound' => $mingguanOutbound,
                'inbound' => $mingguanInbound,
                'title' => 'Tren Transaksi Mingguan (7 Hari Terakhir)',
            ],
            'bulanan' => [
                'labels' => $bulanNames,
                'outbound' => $bulananOutbound,
                'inbound' => $bulananInbound,
                'title' => 'Tren Transaksi Bulanan (Tahun '.now()->year.')',
            ],
        ];
    }
}
