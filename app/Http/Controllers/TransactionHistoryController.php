<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionHistoryController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'lines.item'])->latest()->get();

        return view('transactions.history.index', compact('transactions'));
    }
}
