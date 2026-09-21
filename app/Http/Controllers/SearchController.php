<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (! $query) {
            return response()->json([]);
        }

        $items = Item::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Item',
                    'title' => $item->name,
                    'subtitle' => $item->sku,
                    'url' => route('master.items', ['search' => $item->sku]),
                ];
            });

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'User',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'url' => route('system.users', ['search' => $user->email]),
                ];
            });

        $transactions = Transaction::where('ref_number', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($tx) {
                $url = '#';
                $routeMap = [
                    'PO' => 'transactions.po.show',
                    'Inbound' => 'transactions.inbound.show',
                    'Outbound' => 'transactions.outbound.show',
                    'Transfer' => 'transactions.transfer.show',
                    'StockOpname' => 'transactions.opname.show',
                ];
                if (isset($routeMap[$tx->type])) {
                    $url = route($routeMap[$tx->type], $tx->id);
                }

                return [
                    'type' => 'Transaction',
                    'title' => $tx->ref_number,
                    'subtitle' => $tx->type,
                    'url' => $url,
                ];
            });

        $results = $items->concat($users)->concat($transactions);

        return response()->json($results);
    }
}
