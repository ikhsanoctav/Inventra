<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Shift;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
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
            $totalInbound = Transaction::where('type', 'Inbound')->sum('total_amount') ?? 0;
            $totalOutbound = Transaction::where('type', 'Outbound')->sum('total_amount') ?? 0;
            $netValuation = Item::sum(DB::raw('stock * standard_price')) ?? 0;
            $categories = Category::withCount('items')->withSum('items', 'stock')->get();

            return view('superadmin.index', compact('totalItems', 'totalStock', 'totalSuppliers', 'criticalStock', 'totalUsers', 'activeUsers', 'totalInbound', 'totalOutbound', 'netValuation', 'categories'));
        } elseif ($role === 'manager') {
            $totalSales = Transaction::where('type', 'Outbound')->sum('total_amount');
            $transactionCount = Transaction::count();

            // Dummy chart data for PoC
            $salesData = [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data' => [12, 19, 15, 25, 22, 30, 28],
            ];

            return view('manager.index', compact(
                'totalItems', 'totalStock', 'criticalStock', 'totalSuppliers',
                'totalSales', 'transactionCount', 'salesData'
            ));
        } elseif ($role === 'purchasing') {
            return view('purchasing.index');
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
            return view('gudang.index');
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
            ->sum('total_amount') ?? 0;

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
}
