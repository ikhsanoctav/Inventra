<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;

class TestNotify extends Command
{
    protected $signature = 'test:notify';

    protected $description = 'Trigger a test low stock notification';

    public function handle()
    {
        $users = User::where('is_active', true)->get();
        $item = Item::first() ?? Item::create(['sku' => 'TEST-001', 'name' => 'Kertas A4', 'category_id' => 1, 'unit_id' => 1]);

        foreach ($users as $user) {
            $user->notify(new LowStockNotification($item, 3));
            $this->info("Notifikasi Low Stock berhasil dikirim ke {$user->name} ({$user->role})");
        }

        $this->info('Semua notifikasi uji coba berhasil dikirim ke seluruh peran!');
    }
}
