<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    private Item $itemBarang;

    private Item $itemJasa;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->kasir = User::factory()->create([
            'nip' => '10000003',
            'name' => 'Kasir Utama',
            'email' => 'kasir@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $this->itemBarang = Item::create([
            'tenant_id' => 1,
            'name' => 'Masker Medis 3-Ply',
            'sku' => 'MED-001',
            'type' => 'barang',
            'standard_price' => 25000,
            'stock' => 50,
            'min_stock' => 5,
        ]);

        $this->itemJasa = Item::create([
            'tenant_id' => 1,
            'name' => 'Jasa Cuci Kendaraan',
            'sku' => 'JAS-001',
            'type' => 'jasa',
            'standard_price' => 50000,
            'stock' => 0,
        ]);
    }

    public function test_cashier_cannot_access_pos_without_active_shift(): void
    {
        $response = $this->actingAs($this->kasir)->get('/pos');
        $response->assertRedirect('/pos/shifts')
            ->assertSessionHas('error');
    }

    public function test_cashier_can_open_shift(): void
    {
        $response = $this->actingAs($this->kasir)->post('/pos/shifts/open', [
            'starting_cash' => 150000,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('shifts', [
            'user_id' => $this->kasir->id,
            'starting_cash' => 150000,
            'status' => 'active',
        ]);
    }

    public function test_cashier_can_access_pos_with_active_shift(): void
    {
        Shift::create([
            'user_id' => $this->kasir->id,
            'start_time' => now(),
            'starting_cash' => 100000,
            'total_sales' => 0,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->kasir)->get('/pos');
        $response->assertStatus(200)
            ->assertSee('Terminal Kasir', false)
            ->assertSee('Antrian', false);
    }

    public function test_cashier_can_checkout_with_cash_and_discount(): void
    {
        Shift::create([
            'user_id' => $this->kasir->id,
            'start_time' => now(),
            'starting_cash' => 100000,
            'total_sales' => 0,
            'status' => 'active',
        ]);

        $payload = [
            'items' => [
                [
                    'id' => $this->itemBarang->id,
                    'name' => $this->itemBarang->name,
                    'quantity' => 2,
                    'price' => 25000,
                ],
            ],
            'payment_method' => 'Cash',
            'paid_amount' => 100000,
            'customer_name' => 'Bpk. Hendra',
            'discount_amount' => 5000,
        ];

        $response = $this->actingAs($this->kasir)->postJson('/pos/checkout', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Subtotal = 50.000, Diskon = 5.000 -> Taxable = 45.000 + PPN 11% (4.950) = 49.950
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->kasir->id,
            'type' => 'Outbound',
            'status' => 'Completed',
            'payment_method' => 'Cash',
            'total_amount' => 49950,
        ]);

        // Check stock reduced from 50 to 48
        $this->assertEquals(48, $this->itemBarang->fresh()->stock);
    }

    public function test_cashier_can_checkout_with_qris_and_debit(): void
    {
        Shift::create([
            'user_id' => $this->kasir->id,
            'start_time' => now(),
            'starting_cash' => 100000,
            'total_sales' => 0,
            'status' => 'active',
        ]);

        $payload = [
            'items' => [
                [
                    'id' => $this->itemJasa->id,
                    'name' => $this->itemJasa->name,
                    'quantity' => 1,
                    'price' => 50000,
                ],
            ],
            'payment_method' => 'QRIS',
            'paid_amount' => 55500, // 50.000 + 11% PPN
            'customer_name' => 'Ibu Ratna',
        ];

        $response = $this->actingAs($this->kasir)->postJson('/pos/checkout', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->kasir->id,
            'payment_method' => 'QRIS',
            'total_amount' => 55500,
        ]);
    }

    public function test_cashier_can_view_and_print_receipt(): void
    {
        $trx = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->kasir->id,
            'type' => 'Outbound',
            'ref_number' => 'POS-PRINT-TEST',
            'transaction_date' => now()->toDateString(),
            'status' => 'Completed',
            'total_amount' => 55500,
            'payment_method' => 'QRIS',
            'paid_amount' => 55500,
            'notes' => 'Pelanggan: Bpk. Bambang',
        ]);

        $response = $this->actingAs($this->kasir)->get("/pos/receipt/{$trx->ref_number}");
        $response->assertStatus(200)
            ->assertSee('POS-PRINT-TEST', false)
            ->assertSee('Bpk. Bambang', false)
            ->assertSee('QRIS', false);
    }

    public function test_cashier_can_close_shift_with_cash_reconciliation(): void
    {
        $shift = Shift::create([
            'user_id' => $this->kasir->id,
            'start_time' => now(),
            'starting_cash' => 100000,
            'total_sales' => 50000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->kasir)->post('/pos/shifts/close', [
            'ending_cash' => 150000,
            'notes' => 'Kas sesuai, tidak ada selisih.',
        ]);

        $response->assertRedirect('/pos/shifts')
            ->assertSessionHas('success')
            ->assertSessionHas('closed_shift_id', $shift->id);

        $this->assertDatabaseHas('shifts', [
            'id' => $shift->id,
            'status' => 'closed',
            'ending_cash' => 150000,
            'notes' => 'Kas sesuai, tidak ada selisih.',
        ]);
    }

    public function test_cashier_can_view_shift_receipt_and_transactions(): void
    {
        $shift = Shift::create([
            'user_id' => $this->kasir->id,
            'start_time' => now()->subHours(4),
            'end_time' => now(),
            'starting_cash' => 100000,
            'ending_cash' => 155500,
            'total_sales' => 55500,
            'status' => 'closed',
            'notes' => 'Shift siang selesai lancar.',
        ]);

        $receiptResponse = $this->actingAs($this->kasir)->get("/pos/shifts/{$shift->id}/receipt");
        $receiptResponse->assertStatus(200)
            ->assertSee('REKAPITULASI SHIFT KASIR', false)
            ->assertSee('Kasir Utama', false)
            ->assertSee('REKONSILIASI KAS LACI', false);

        $txResponse = $this->actingAs($this->kasir)->getJson("/pos/shifts/{$shift->id}/transactions");
        $txResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
}
