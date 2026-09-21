<?php

use App\Models\Transaction;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$transactions = Transaction::where('ref_number', 'like', 'POS-%')->get();
foreach ($transactions as $t) {
    $subtotal = 0;
    foreach ($t->lines as $l) {
        $subtotal += ($l->quantity * $l->unit_price);
    }
    $t->update(['total_amount' => $subtotal * 1.11]);
}
echo 'Updated '.count($transactions)." transactions.\n";
