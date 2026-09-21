<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$user = User::first();
auth()->login($user);

$request = Request::create('/transactions/po', 'POST', [
    'ref_number' => 'PO-TEST-'.time(),
    'transaction_date' => '2026-09-15',
    'status' => 'Draft',
]);

$response = $kernel->handle($request);
var_dump($response->getStatusCode());
if ($response->getStatusCode() === 302) {
    var_dump($response->headers->get('Location'));
    var_dump(session()->all());
}
