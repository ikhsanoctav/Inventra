<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionLine;
use Carbon\Carbon;
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

        $sales = Transaction::where('ref_number', 'like', 'POS-%')
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

        $revenue = Transaction::where('ref_number', 'like', 'POS-%')
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
        if ($request->wantsJson()) {
            $months = [];
            $revenues = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->translatedFormat('F');

                $revenue = Transaction::where('ref_number', 'like', 'POS-%')
                    ->where('status', 'Completed')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('total_amount');

                $revenues[] = round($revenue / 1000000, 2); // in millions
            }

            // Real growth calculation
            $currentMonthRev = end($revenues);
            $prevMonthRev = count($revenues) >= 2 ? $revenues[count($revenues) - 2] : 0;
            $growth = $prevMonthRev > 0 ? round((($currentMonthRev - $prevMonthRev) / $prevMonthRev) * 100, 1) : 0;

            // Real average daily sales in current month
            $daysInMonth = max(1, Carbon::now()->day);
            $currentMonthTotalRaw = Transaction::where('ref_number', 'like', 'POS-%')
                ->where('status', 'Completed')
                ->whereMonth('transaction_date', Carbon::now()->month)
                ->whereYear('transaction_date', Carbon::now()->year)
                ->sum('total_amount');
            $avgDaily = round($currentMonthTotalRaw / $daysInMonth);

            // Real top selling product
            $topLine = TransactionLine::whereHas('transaction', function ($q) {
                $q->where('status', 'Completed')->where(function ($sub) {
                    $sub->where('type', 'Outbound')->orWhere('ref_number', 'like', 'POS-%');
                });
            })
                ->select('item_id', DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('item_id')
                ->orderByDesc('total_qty')
                ->with('item')
                ->first();

            $topProduct = $topLine && $topLine->item ? $topLine->item->name : 'Belum Ada Penjualan';

            return response()->json([
                'labels' => $months,
                'data' => $revenues,
                'growth' => $growth,
                'avgDaily' => $avgDaily,
                'topProduct' => $topProduct,
            ]);
        }

        return view('manager.trends');
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
