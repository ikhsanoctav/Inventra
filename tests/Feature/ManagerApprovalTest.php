<?php

namespace Tests\Feature;

use App\Models\DamagedItem;
use App\Models\DataRequest;
use App\Models\Item;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    private User $purchasing;

    private User $gudang;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->manager = User::factory()->create([
            'nip' => '10000002',
            'name' => 'Manager User',
            'email' => 'manager@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->purchasing = User::factory()->create([
            'nip' => '10000005',
            'name' => 'Purchasing User',
            'email' => 'purchasing@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'purchasing',
            'is_active' => true,
        ]);

        $this->gudang = User::factory()->create([
            'nip' => '10000004',
            'name' => 'Gudang User',
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
    }

    public function test_manager_can_access_approval_center(): void
    {
        $response = $this->actingAs($this->manager)->get('/manager/approvals');
        $response->assertStatus(200)
            ->assertSee('Approval Center', false)
            ->assertSee('Purchase Orders (PO) Menunggu Persetujuan', false);
    }

    public function test_manager_can_approve_purchase_order(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        $response = $this->actingAs($this->manager)->post("/transactions/po/{$po->id}/approve");
        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals('Approved', $po->fresh()->status);
    }

    public function test_manager_can_reject_purchase_order_with_reason(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-002',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        $response = $this->actingAs($this->manager)->post("/transactions/po/{$po->id}/reject", [
            'reason' => 'Anggaran periode ini telah habis.',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $freshPo = $po->fresh();
        $this->assertEquals('Rejected', $freshPo->status);
        $this->assertStringContainsString('Anggaran periode ini telah habis', $freshPo->notes);
    }

    public function test_reject_po_validation_requires_reason(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-003',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        $response = $this->actingAs($this->manager)->post("/transactions/po/{$po->id}/reject", [
            'reason' => '',
        ]);

        $response->assertSessionHasErrors(['reason']);
        $this->assertEquals('Pending Approval', $po->fresh()->status);
    }

    public function test_manager_can_approve_gudang_data_request(): void
    {
        $request = DataRequest::create([
            'user_id' => $this->gudang->id,
            'request_number' => 'REQ-TEST-001',
            'type' => 'Penyesuaian Stok',
            'title' => 'Stok Fisik Selisih 2 Unit',
            'description' => 'Mohon penyesuaian stok di rak B-04',
            'status' => 'Menunggu',
        ]);

        $response = $this->actingAs($this->manager)->post("/gudang/requests/{$request->id}/approve", [
            'review_note' => 'Disetujui sesuai bukti fisik.',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $request->fresh();
        $this->assertEquals('Disetujui', $fresh->status);
        $this->assertEquals('Disetujui sesuai bukti fisik.', $fresh->review_note);
        $this->assertEquals($this->manager->id, $fresh->reviewed_by);
    }

    public function test_manager_can_reject_gudang_data_request_with_reason(): void
    {
        $request = DataRequest::create([
            'user_id' => $this->gudang->id,
            'request_number' => 'REQ-TEST-002',
            'type' => 'Perubahan Master Data',
            'title' => 'Ubah Kategori Barang X',
            'description' => 'Pindah kategori',
            'status' => 'Menunggu',
        ]);

        $response = $this->actingAs($this->manager)->post("/gudang/requests/{$request->id}/reject", [
            'reason' => 'Kategori saat ini sudah tepat sesuai SOP.',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $request->fresh();
        $this->assertEquals('Ditolak', $fresh->status);
        $this->assertEquals('Kategori saat ini sudah tepat sesuai SOP.', $fresh->review_note);
        $this->assertEquals($this->manager->id, $fresh->reviewed_by);
    }

    public function test_unauthorized_user_cannot_reject_po_or_approve_requests(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->purchasing->id,
            'type' => 'PO',
            'ref_number' => 'PO-TEST-004',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        $request = DataRequest::create([
            'user_id' => $this->gudang->id,
            'request_number' => 'REQ-TEST-003',
            'type' => 'Penyesuaian Stok',
            'title' => 'Test',
            'status' => 'Menunggu',
        ]);

        $poResponse = $this->actingAs($this->kasir)->post("/transactions/po/{$po->id}/reject", [
            'reason' => 'Hacker test',
        ]);
        $poResponse->assertStatus(403);

        $reqResponse = $this->actingAs($this->kasir)->post("/gudang/requests/{$request->id}/approve");
        $reqResponse->assertStatus(403);
    }

    public function test_manager_can_view_sales_trends_page(): void
    {
        $response = $this->actingAs($this->manager)->get('/manager/trends');
        $response->assertStatus(200)
            ->assertSee('Tren Penjualan Historis', false)
            ->assertSee('Harian', false)
            ->assertSee('Mingguan', false)
            ->assertSee('Bulanan', false);
    }

    public function test_manager_can_fetch_sales_trends_json(): void
    {
        // Create an outbound sales transaction
        Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->kasir->id,
            'type' => 'Outbound',
            'ref_number' => 'OUT-TEST-100',
            'transaction_date' => now()->toDateString(),
            'status' => 'Completed',
            'total_amount' => 500000,
        ]);

        $response = $this->actingAs($this->manager)->getJson('/manager/trends?period=bulanan');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'labels',
                'data',
                'growth',
                'avgDaily',
                'topProduct',
                'title',
                'all' => [
                    'harian',
                    'mingguan',
                    'bulanan',
                ],
            ]);
    }

    public function test_manager_can_view_executive_dashboard_with_valuation_and_top_items(): void
    {
        $response = $this->actingAs($this->manager)->get('/dashboard');
        $response->assertStatus(200)
            ->assertSee('Dashboard Eksekutif & Pengendalian Bisnis', false)
            ->assertSee('Valuasi Persediaan', false)
            ->assertSee('Top 5 Fast-Moving Items', false);
    }

    public function test_manager_can_view_audit_stock_opname_page(): void
    {
        $response = $this->actingAs($this->manager)->get('/manager/audit/opname');
        $response->assertStatus(200)
            ->assertSee('Audit & Verifikasi Stock Opname', false)
            ->assertSee('Total Sesi Opname', false);
    }

    public function test_manager_can_authorize_quarantined_damaged_item(): void
    {
        $item = Item::create([
            'tenant_id' => 1,
            'name' => 'Barang Rusak Test',
            'sku' => 'SKU-DMG-001',
            'type' => 'barang',
            'stock' => 10,
            'min_stock' => 2,
            'standard_price' => 25000,
        ]);

        $damaged = DamagedItem::create([
            'tenant_id' => 1,
            'user_id' => $this->gudang->id,
            'item_id' => $item->id,
            'reference_number' => 'DMG-202610-0001',
            'quantity' => 2,
            'damage_type' => 'Rusak Fisik',
            'action_type' => 'Pemusnahan / Write-Off',
            'status' => 'Dalam Karantina',
            'notes' => 'Pecah saat bongkar muat',
            'reported_at' => now(),
        ]);

        $response = $this->actingAs($this->manager)->post("/gudang/damaged-items/{$damaged->id}/status", [
            'status' => 'Dimusnahkan',
            'notes' => 'Disetujui untuk pemusnahan sesuai berita acara.',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals('Dimusnahkan', $damaged->fresh()->status);
    }
}
