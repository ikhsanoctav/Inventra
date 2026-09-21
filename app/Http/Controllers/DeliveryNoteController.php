<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        // Get completed outbound transactions for delivery note printing
        $outbounds = Transaction::with(['user', 'lines'])
            ->where('type', 'Outbound')
            ->where('status', 'Completed')
            ->latest()
            ->get();

        return view('gudang.delivery_note.index', compact('outbounds'));
    }

    public function print($id)
    {
        $transaction = Transaction::with(['lines.item', 'user'])->findOrFail($id);

        if ($transaction->type !== 'Outbound' || $transaction->status !== 'Completed') {
            abort(404, 'Surat Jalan hanya bisa dicetak untuk transaksi Outbound yang Selesai.');
        }

        return view('gudang.delivery_note.print', compact('transaction'));
    }
}
