<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class StandardizeItemSkus extends Command
{
    protected $signature = 'items:standardize-skus {--force : Force rewrite even if already adhering to standard}';

    protected $description = 'Standardize all item SKUs in the database to the KAT-KODE-YYMMDD-URUT format';

    public function handle(): int
    {
        $this->info('Starting SKU standardization...');
        $items = Item::with('category')->get();
        $updated = 0;

        foreach ($items as $item) {
            $newSku = $item->formatted_sku;
            if ($item->sku !== $newSku || $this->option('force')) {
                $oldSku = $item->sku;
                $item->sku = $newSku;
                $item->save();
                $this->line("Updated: [{$oldSku}] -> [{$newSku}] ({$item->name})");
                $updated++;
            }
        }

        $this->info("Completed! {$updated} item(s) updated to standard format (KAT-KODE-YYMMDD-URUT).");

        return Command::SUCCESS;
    }
}
