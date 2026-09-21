<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndFunctionalFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'nip' => '12345678',
            'email' => 'active@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'active@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Verify audit log record
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'LOGIN_SUCCESS',
        ]);
    }

    public function test_inactive_user_is_blocked_from_logging_in(): void
    {
        $user = User::factory()->create([
            'nip' => '87654321',
            'email' => 'inactive@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'kasir',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Verify audit log record for blocked attempt
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'LOGIN_BLOCKED_INACTIVE',
        ]);
    }

    public function test_transfer_route_is_protected_by_role_middleware(): void
    {
        // Kasir is not permitted to access transactions/transfer
        $kasir = User::factory()->create([
            'nip' => '11223344',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->actingAs($kasir)->get('/transactions/transfer');
        $response->assertStatus(403);

        // Admin gudang is permitted
        $gudang = User::factory()->create([
            'nip' => '55667788',
            'role' => 'admin_gudang',
            'is_active' => true,
        ]);

        $responseGudang = $this->actingAs($gudang)->get('/transactions/transfer');
        $responseGudang->assertStatus(200);
    }

    public function test_schedule_controller_and_routes_work_without_reflection_error(): void
    {
        $superAdmin = User::factory()->create([
            'nip' => '99887766',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get('/system/schedules');
        $response->assertStatus(200);

        $responseShifts = $this->actingAs($superAdmin)->get('/system/schedules/shifts');
        $responseShifts->assertStatus(200);
    }

    public function test_audit_log_record_helper_stores_data(): void
    {
        $user = User::factory()->create([
            'nip' => '44332211',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        AuditLog::record('TEST_ACTION', 'User', $user->id, ['old' => 1], ['new' => 2]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'TEST_ACTION',
            'entity_type' => 'User',
            'entity_id' => $user->id,
        ]);
    }

    public function test_notifications_unread_endpoint_returns_json(): void
    {
        $user = User::factory()->create([
            'nip' => '33221100',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $tenant = Tenant::create(['name' => 'Main Tenant']);

        $item = Item::create([
            'tenant_id' => $tenant->id,
            'sku' => 'TEST-NOTIF',
            'name' => 'Barang Uji',
            'stock' => 1,
            'min_stock' => 5,
        ]);

        $user->notify(new LowStockNotification($item, 1));

        $response = $this->actingAs($user)->getJson('/notifications/unread');

        $response->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonFragment(['item_name' => 'Barang Uji']);
    }

    public function test_notifications_can_be_marked_as_read(): void
    {
        $user = User::factory()->create([
            'nip' => '33221199',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $tenant = Tenant::create(['name' => 'Main Tenant 2']);

        $item = Item::create([
            'tenant_id' => $tenant->id,
            'sku' => 'TEST-NOTIF2',
            'name' => 'Barang Uji 2',
            'stock' => 2,
            'min_stock' => 5,
        ]);

        $user->notify(new LowStockNotification($item, 2));
        $this->assertEquals(1, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post('/notifications/read');
        $response->assertRedirect();

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_notifications_all_endpoint_returns_json(): void
    {
        $user = User::factory()->create([
            'nip' => '33221188',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $tenant = Tenant::create(['name' => 'Main Tenant 3']);

        $item = Item::create([
            'tenant_id' => $tenant->id,
            'sku' => 'TEST-NOTIF3',
            'name' => 'Barang Uji 3',
            'stock' => 3,
            'min_stock' => 5,
        ]);

        $user->notify(new LowStockNotification($item, 3));

        $response = $this->actingAs($user)->getJson('/notifications/all');

        $response->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('unread_count', 1)
            ->assertJsonFragment(['item_name' => 'Barang Uji 3']);
    }
}
