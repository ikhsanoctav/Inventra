<?php

namespace Tests\Feature;

use App\Events\StockUpdated;
use App\Models\Category;
use App\Models\DamagedItem;
use App\Models\Item;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Uom;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AdminGudangTest extends TestCase
{
    use RefreshDatabase;

    private User $gudang;

    private User $kasir;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->gudang = User::factory()->create([
            'nip' => '10000004',
            'name' => 'Admin Gudang',
            'email' => 'gudang@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'admin_gudang',
            'is_active' => true,
        ]);

        $this->kasir = User::factory()->create([
            'nip' => '10000003',
            'name' => 'Kasir User',
            'email' => 'kasir@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $uom = Uom::firstOrCreate(['symbol' => 'PCS', 'tenant_id' => 1], ['name' => 'Pieces']);
        $cat = Category::firstOrCreate(['name' => 'Medis', 'tenant_id' => 1], ['slug' => 'medis']);

        $this->item = Item::create([
            'tenant_id' => 1,
            'name' => 'Masker Medis 3-Ply',
            'sku' => 'MED-001',
            'category_id' => $cat->id,
            'uom_id' => $uom->id,
            'type' => 'barang',
            'standard_price' => 25000,
            'stock' => 100,
            'min_stock' => 10,
        ]);
    }

    public function test_admin_gudang_can_view_damaged_items_list(): void
    {
        $response = $this->actingAs($this->gudang)->get('/gudang/damaged-items');
        $response->assertStatus(200)
            ->assertSee('Barang Rusak & Karantina', false)
            ->assertSee('Catat Barang Rusak', false);
    }

    public function test_admin_gudang_can_record_damaged_item_and_deduct_stock(): void
    {
        $payload = [
            'item_id' => $this->item->id,
            'quantity' => 15,
            'damage_type' => 'Rusak Fisik',
            'action_type' => 'Karantina',
            'notes' => 'Penyok saat pengiriman kontainer',
        ];

        $response = $this->actingAs($this->gudang)->post('/gudang/damaged-items', $payload);

        $response->assertRedirect('/gudang/damaged-items')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('damaged_items', [
            'item_id' => $this->item->id,
            'quantity' => 15,
            'damage_type' => 'Rusak Fisik',
            'action_type' => 'Karantina',
            'status' => 'Dalam Karantina',
        ]);

        // Assert that stock reduced from 100 to 85
        $this->assertEquals(85, $this->item->fresh()->stock);

        // Assert audit transaction was created
        $this->assertDatabaseHas('transactions', [
            'type' => 'Adjustment',
            'status' => 'Completed',
        ]);
    }

    public function test_admin_gudang_cannot_record_damage_exceeding_available_stock(): void
    {
        $payload = [
            'item_id' => $this->item->id,
            'quantity' => 200, // Stock is only 100
            'damage_type' => 'Kadaluarsa / Expired',
            'action_type' => 'Pemusnahan / Write-Off',
        ];

        $response = $this->actingAs($this->gudang)->post('/gudang/damaged-items', $payload);

        $response->assertRedirect()
            ->assertSessionHas('error');

        // Stock remains 100
        $this->assertEquals(100, $this->item->fresh()->stock);
    }

    public function test_admin_gudang_can_update_damaged_item_status(): void
    {
        $damaged = DamagedItem::create([
            'tenant_id' => 1,
            'user_id' => $this->gudang->id,
            'item_id' => $this->item->id,
            'reference_number' => 'DMG-TEST-001',
            'quantity' => 5,
            'damage_type' => 'Cacat Pabrik',
            'action_type' => 'Karantina',
            'status' => 'Dalam Karantina',
            'reported_at' => now(),
        ]);

        $response = $this->actingAs($this->gudang)->post("/gudang/damaged-items/{$damaged->id}/status", [
            'status' => 'Diretur',
            'notes' => 'Dikembalikan ke supplier via ekspedisi',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals('Diretur', $damaged->fresh()->status);
    }

    public function test_admin_gudang_can_view_barcode_generator(): void
    {
        $response = $this->actingAs($this->gudang)->get('/gudang/barcode');
        $response->assertStatus(200)
            ->assertSee('Generator & Cetak Label Barcode', false)
            ->assertSee('Label Rak Gudang', false);
    }

    public function test_admin_gudang_can_render_printable_barcodes(): void
    {
        $payload = [
            'items' => [
                $this->item->id => 3,
            ],
            'template' => 'rack_label',
        ];

        $response = $this->actingAs($this->gudang)->post('/gudang/barcode/print', $payload);
        $response->assertStatus(200)
            ->assertSee('INVENTRA Barcode Printing', false)
            ->assertSee($this->item->formatted_sku, false);
    }

    public function test_unauthorized_user_cannot_access_damaged_items(): void
    {
        $response = $this->actingAs($this->kasir)->get('/gudang/damaged-items');
        $response->assertStatus(403);
    }

    public function test_admin_gudang_dashboard_renders_metrics(): void
    {
        $response = $this->actingAs($this->gudang)->get('/dashboard');
        $response->assertStatus(200)
            ->assertSee('Operasional Gudang')
            ->assertSee('Total Stok Fisik')
            ->assertSee('Pusat Aksi Operasional Cepat');
    }

    public function test_admin_gudang_can_create_inbound_from_approved_po(): void
    {
        Event::fake([StockUpdated::class]);

        // Create an Approved PO
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->gudang->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Approved',
            'total_amount' => 250000,
        ]);

        TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 25000,
        ]);

        $payload = [
            'ref_number' => 'INB-PO-TEST-001',
            'transaction_date' => now()->toDateString(),
            'po_id' => $po->id,
            'status' => 'Draft',
        ];

        $response = $this->actingAs($this->gudang)->post('/transactions/inbound', $payload);
        $response->assertRedirect();

        // Verify Inbound transaction was created and replicated PO items
        $inbound = Transaction::where('ref_number', 'INB-PO-TEST-001')->first();
        $this->assertNotNull($inbound);
        $this->assertEquals('Inbound', $inbound->type);
        $this->assertEquals(1, $inbound->lines()->count());
        $this->assertEquals(10, $inbound->lines()->first()->quantity);

        // Now complete the Inbound
        $completeResponse = $this->actingAs($this->gudang)->post("/transactions/inbound/{$inbound->id}/complete");
        $completeResponse->assertRedirect();

        $this->assertEquals(110, $this->item->fresh()->stock);
        $this->assertEquals('Completed', $po->fresh()->status);
        Event::assertDispatched(StockUpdated::class);
    }

    public function test_admin_gudang_can_complete_outbound_and_dispatch_stock_updated(): void
    {
        Event::fake([StockUpdated::class]);

        $outbound = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->gudang->id,
            'type' => 'Outbound',
            'ref_number' => 'OUT-TEST-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
        ]);

        TransactionLine::create([
            'transaction_id' => $outbound->id,
            'item_id' => $this->item->id,
            'quantity' => 20,
            'unit_price' => 25000,
        ]);

        $response = $this->actingAs($this->gudang)->post("/transactions/outbound/{$outbound->id}/complete");
        $response->assertRedirect();

        $this->assertEquals(80, $this->item->fresh()->stock);
        $this->assertEquals('Completed', $outbound->fresh()->status);
        Event::assertDispatched(StockUpdated::class);
    }

    public function test_admin_gudang_can_complete_stock_opname_and_dispatch_stock_updated(): void
    {
        Event::fake([StockUpdated::class]);

        $opname = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->gudang->id,
            'type' => 'StockOpname',
            'ref_number' => 'OPN-TEST-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
        ]);

        TransactionLine::create([
            'transaction_id' => $opname->id,
            'item_id' => $this->item->id,
            'quantity' => 15, // Variance
            'unit_price' => 115, // Physical count
        ]);

        $response = $this->actingAs($this->gudang)->post("/transactions/opname/{$opname->id}/complete");
        $response->assertRedirect();

        $this->assertEquals(115, $this->item->fresh()->stock);
        $this->assertEquals('Completed', $opname->fresh()->status);
        Event::assertDispatched(StockUpdated::class);
    }

    public function test_admin_gudang_can_complete_transfer_and_dispatch_stock_updated(): void
    {
        Event::fake([StockUpdated::class]);

        $fromWh = Warehouse::firstOrCreate(['name' => 'Gudang Pusat', 'tenant_id' => 1]);
        $toWh = Warehouse::firstOrCreate(['name' => 'Gudang Cabang', 'tenant_id' => 1]);

        $transfer = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->gudang->id,
            'type' => 'Transfer',
            'ref_number' => 'TRF-TEST-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
            'notes' => json_encode([
                'from_warehouse_id' => $fromWh->id,
                'to_warehouse_id' => $toWh->id,
            ]),
        ]);

        TransactionLine::create([
            'transaction_id' => $transfer->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 0,
        ]);

        $response = $this->actingAs($this->gudang)->post("/transactions/transfer/{$transfer->id}/complete");
        $response->assertRedirect();

        $this->assertEquals('Completed', $transfer->fresh()->status);
        Event::assertDispatched(StockUpdated::class);
    }
}
