<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Uom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasingTest extends TestCase
{
    use RefreshDatabase;

    private User $purchasing;

    private User $manager;

    private User $gudang;

    private Supplier $supplier;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->purchasing = User::factory()->create([
            'nip' => '10000005',
            'name' => 'Staff Purchasing',
            'email' => 'purchasing@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'purchasing',
            'is_active' => true,
        ]);

        $this->manager = User::factory()->create([
            'nip' => '10000002',
            'name' => 'Manager Operasional',
            'email' => 'manager@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->gudang = User::factory()->create([
            'nip' => '10000004',
            'name' => 'Admin Gudang',
            'email' => 'gudang@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'admin_gudang',
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Sumber Medika Utama',
            'contact_person' => 'Budi Santoso',
            'email' => 'budi@sumbermedika.co.id',
            'phone' => '081234567890',
            'address' => 'Kawasan Industri Pulogadung No. 45',
            'status' => 'active',
        ]);

        $uom = Uom::firstOrCreate(['symbol' => 'BOX', 'tenant_id' => 1], ['name' => 'Box']);
        $cat = Category::firstOrCreate(['name' => 'Medis', 'tenant_id' => 1], ['slug' => 'medis']);

        $this->item = Item::create([
            'tenant_id' => 1,
            'name' => 'Sarung Tangan Lateks Medis',
            'sku' => 'MED-GLV-01',
            'category_id' => $cat->id,
            'uom_id' => $uom->id,
            'supplier_id' => $this->supplier->id,
            'standard_price' => 50000,
            'stock' => 5,
            'min_stock' => 20, // Low stock condition
            'type' => 'barang',
        ]);
    }

    public function test_purchasing_can_access_dashboard_with_all_metrics(): void
    {
        $response = $this->actingAs($this->purchasing)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Pengadaan');
        $response->assertSee('Rekomendasi Reorder Barang');
        $response->assertSee('Sarung Tangan Lateks Medis');
        $response->assertSee('PT Sumber Medika Utama');
    }

    public function test_purchasing_can_create_manual_po_draft(): void
    {
        $response = $this->actingAs($this->purchasing)->post(route('transactions.po'), [
            'ref_number' => 'PO-20261003-9999',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
            'notes' => 'Pengadaan reguler obat dan alat medis',
        ]);

        $po = Transaction::where('ref_number', 'PO-20261003-9999')->first();
        $this->assertNotNull($po);
        $this->assertEquals('Draft', $po->status);
        $this->assertEquals('PO', $po->type);

        $response->assertRedirect(route('transactions.po.show', $po->id));
    }

    public function test_purchasing_can_add_item_to_po(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($this->purchasing)->post(route('transactions.po.addLine', $po->id), [
            'item_id' => $this->item->id,
            'quantity' => 25,
            'unit_price' => 48000,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('transaction_lines', [
            'transaction_id' => $po->id,
            'item_id' => $this->item->id,
            'quantity' => 25,
            'unit_price' => 48000,
        ]);

        $po->refresh();
        $this->assertEquals(1200000, $po->total_amount);
    }

    public function test_purchasing_can_update_and_delete_item_in_po(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-002',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
        ]);

        $line = TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 50000,
        ]);

        // Update line
        $response = $this->actingAs($this->purchasing)->put(route('transactions.po.update_line', $line->id), [
            'quantity' => 15,
            'unit_price' => 49000,
        ]);

        $response->assertSessionHas('success');
        $line->refresh();
        $this->assertEquals(15, $line->quantity);
        $this->assertEquals(49000, $line->unit_price);

        // Delete line
        $deleteResponse = $this->actingAs($this->purchasing)->delete(route('transactions.po.lines.destroy', $line->id));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('transaction_lines', ['id' => $line->id]);
    }

    public function test_purchasing_can_create_quick_draft_from_low_stock_item(): void
    {
        $response = $this->actingAs($this->purchasing)->post(route('transactions.po.quick_draft'), [
            'item_id' => $this->item->id,
            'quantity' => 35,
        ]);

        $po = Transaction::where('notes', 'like', '%PT Sumber Medika Utama%')->latest()->first();
        $this->assertNotNull($po);
        $this->assertEquals('Draft', $po->status);
        $this->assertCount(1, $po->lines);
        $this->assertEquals(35, $po->lines->first()->quantity);

        $response->assertRedirect(route('transactions.po.show', $po->id));
    }

    public function test_purchasing_can_bulk_auto_draft_from_all_low_stock_items(): void
    {
        $response = $this->actingAs($this->purchasing)->post(route('transactions.po.bulk_auto_draft'));

        $response->assertRedirect(route('transactions.po.index'));
        $response->assertSessionHas('success');

        $this->assertTrue(Transaction::where('type', 'PO')->where('notes', 'like', '%Auto-draft%')->exists());
    }

    public function test_purchasing_can_submit_po_for_approval(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-SUBMIT-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
        ]);

        TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $this->item->id,
            'quantity' => 20,
            'unit_price' => 50000,
        ]);

        $response = $this->actingAs($this->purchasing)->post(route('transactions.po.submit', $po->id));

        $response->assertSessionHas('success');
        $po->refresh();
        $this->assertEquals('Pending Approval', $po->status);
    }

    public function test_cannot_submit_empty_po_for_approval(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-EMPTY-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($this->purchasing)->post(route('transactions.po.submit', $po->id));

        $response->assertSessionHas('error');
        $po->refresh();
        $this->assertEquals('Draft', $po->status);
    }

    public function test_manager_can_approve_po(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-APPROVE-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $this->item->id,
            'quantity' => 20,
            'unit_price' => 50000,
        ]);

        $response = $this->actingAs($this->manager)->post(route('transactions.po.approve', $po->id));

        $response->assertSessionHas('success');
        $po->refresh();
        $this->assertEquals('Approved', $po->status);
    }

    public function test_manager_can_reject_po_with_reason(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-REJECT-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        TransactionLine::create([
            'transaction_id' => $po->id,
            'item_id' => $this->item->id,
            'quantity' => 20,
            'unit_price' => 50000,
        ]);

        $response = $this->actingAs($this->manager)->post(route('transactions.po.reject', $po->id), [
            'reason' => 'Kuantitas terlalu banyak, mohon revisi menjadi 10 box.',
        ]);

        $response->assertSessionHas('success');
        $po->refresh();
        $this->assertEquals('Rejected', $po->status);
        $this->assertStringContainsString('Kuantitas terlalu banyak', $po->notes);
    }

    public function test_purchasing_cannot_modify_approved_po(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-LOCKED-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Approved',
        ]);

        $response = $this->actingAs($this->purchasing)->post(route('transactions.po.addLine', $po->id), [
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 50000,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_purchasing_can_view_pending_approval_list(): void
    {
        Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-PENDING-LIST-01',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        $response = $this->actingAs($this->purchasing)->get(route('purchasing.pending_approval'));

        $response->assertStatus(200);
        $response->assertSee('PO-PENDING-LIST-01');
    }

    public function test_purchasing_can_view_po_history(): void
    {
        Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-HIST-01',
            'transaction_date' => now()->toDateString(),
            'status' => 'Approved',
        ]);

        $response = $this->actingAs($this->purchasing)->get(route('purchasing.history'));

        $response->assertStatus(200);
        $response->assertSee('PO-HIST-01');
    }

    public function test_purchasing_can_view_vendor_performance(): void
    {
        $response = $this->actingAs($this->purchasing)->get(route('purchasing.performance'));

        $response->assertStatus(200);
        $response->assertSee('PT Sumber Medika Utama');
        $response->assertSee('Evaluasi Performa Rekanan Vendor');
    }
}
