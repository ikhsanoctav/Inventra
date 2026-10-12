<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    private User $manager;

    private Item $item;

    private Shift $shift;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->kasir = User::factory()->create([
            'nip' => '10000003',
            'name' => 'Kasir User',
            'email' => 'kasir@inventra.go.id',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $this->manager = User::factory()->create([
            'nip' => '10000002',
            'name' => 'Manager User',
            'email' => 'manager@inventra.go.id',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->shift = Shift::create([
            'user_id' => $this->kasir->id,
            'start_time' => now(),
            'initial_cash' => 100000,
            'total_sales' => 0,
            'total_cash_in' => 0,
            'status' => 'active',
        ]);

        $this->item = Item::create([
            'tenant_id' => 1,
            'sku' => 'TEST-001',
            'name' => 'Barang Uji Coba',
            'type' => 'barang',
            'standard_price' => 20000,
            'selling_price' => 25000,
            'stock' => 10,
        ]);
    }

    public function test_pos_checkout_with_idempotency_key_prevents_duplicate_transactions(): void
    {
        $idempotencyKey = 'test-idemp-pos-'.uniqid();

        $payload = [
            'items' => [
                [
                    'id' => $this->item->id,
                    'quantity' => 2,
                    'price' => 25000,
                ],
            ],
            'payment_method' => 'Cash',
            'paid_amount' => 60000,
            'customer_name' => 'Budi',
        ];

        // First Request
        $firstResponse = $this->actingAs($this->kasir)
            ->withHeaders([
                'X-Idempotency-Key' => $idempotencyKey,
                'Accept' => 'application/json',
            ])
            ->postJson('/pos/checkout', $payload);

        $firstResponse->assertStatus(200);
        $firstResponse->assertHeaderMissing('X-Idempotent-Replay');
        $firstRef = $firstResponse->json('transaction_ref');

        // Verify database after first checkout
        $this->assertEquals(1, Transaction::count());
        $this->assertEquals(8, $this->item->fresh()->stock);

        // Second Request (Immediate Retry / Double Submit with identical key and payload)
        $secondResponse = $this->actingAs($this->kasir)
            ->withHeaders([
                'X-Idempotency-Key' => $idempotencyKey,
                'Accept' => 'application/json',
            ])
            ->postJson('/pos/checkout', $payload);

        $secondResponse->assertStatus(200);
        $secondResponse->assertHeader('X-Idempotent-Replay', 'true');
        $this->assertEquals($firstRef, $secondResponse->json('transaction_ref'));

        // Crucial assertions: Database still has ONLY 1 transaction and stock is STILL 8 (not 6!)
        $this->assertEquals(1, Transaction::count());
        $this->assertEquals(8, $this->item->fresh()->stock);
    }

    public function test_mismatched_payload_with_same_idempotency_key_returns_409_conflict(): void
    {
        $idempotencyKey = 'conflict-key-'.uniqid();

        $payloadA = [
            'items' => [
                ['id' => $this->item->id, 'quantity' => 1, 'price' => 25000],
            ],
            'payment_method' => 'Cash',
            'paid_amount' => 50000,
        ];

        $payloadB = [
            'items' => [
                ['id' => $this->item->id, 'quantity' => 3, 'price' => 25000],
            ],
            'payment_method' => 'Cash',
            'paid_amount' => 100000,
        ];

        // First request succeeds
        $firstResponse = $this->actingAs($this->kasir)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson('/pos/checkout', $payloadA);
        $firstResponse->assertStatus(200);

        // Second request with altered payload must be rejected
        $secondResponse = $this->actingAs($this->kasir)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson('/pos/checkout', $payloadB);

        $secondResponse->assertStatus(409);
        $secondResponse->assertJsonFragment([
            'status' => 'error',
        ]);
    }

    public function test_purchase_order_approval_is_idempotent_against_repeated_calls(): void
    {
        $po = Transaction::create([
            'tenant_id' => 1,
            'user_id' => $this->kasir->id,
            'type' => 'PO',
            'ref_number' => 'PO-IDEMP-001',
            'transaction_date' => now()->toDateString(),
            'status' => 'Pending Approval',
        ]);

        // First approval
        $response1 = $this->actingAs($this->manager)->post("/transactions/po/{$po->id}/approve");
        $response1->assertRedirect()->assertSessionHas('success');
        $this->assertEquals('Approved', $po->fresh()->status);

        // Second approval (repeated call)
        $response2 = $this->actingAs($this->manager)->post("/transactions/po/{$po->id}/approve");
        $response2->assertRedirect()->assertSessionHas('success');
        $this->assertEquals('Approved', $po->fresh()->status);
    }
}
