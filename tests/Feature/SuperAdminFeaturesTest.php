<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Rule;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->superAdmin = User::factory()->create([
            'nip' => '10000001',
            'name' => 'Super Admin',
            'email' => 'superadmin@inventra.go.id',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_access_dashboard_and_stats(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/dashboard');
        $response->assertStatus(200);

        $statsResponse = $this->actingAs($this->superAdmin)->getJson('/dashboard/stats');
        $statsResponse->assertStatus(200)
            ->assertJsonStructure([
                'totalItems',
                'totalStock',
                'totalInbound',
                'totalOutbound',
                'netValuation',
                'chart',
            ]);
    }

    public function test_superadmin_can_manage_rules_and_conditions_actions(): void
    {
        // 1. Create Rule
        $storeResponse = $this->actingAs($this->superAdmin)->post('/system/rbl', [
            'name' => 'Peringatan Stok Rendah',
            'event_trigger' => 'stock.updated',
            'is_active' => '1',
        ]);
        $storeResponse->assertRedirect();

        $rule = Rule::where('name', 'Peringatan Stok Rendah')->first();
        $this->assertNotNull($rule);

        // 2. View Rule Details
        $showResponse = $this->actingAs($this->superAdmin)->get("/system/rbl/{$rule->id}");
        $showResponse->assertStatus(200)
            ->assertSee('Peringatan Stok Rendah');

        // 3. Add Condition
        $condResponse = $this->actingAs($this->superAdmin)->post("/system/rbl/{$rule->id}/conditions", [
            'field' => 'stock',
            'operator' => '<=',
            'value' => '10',
            'logic_operator' => 'AND',
        ]);
        $condResponse->assertRedirect();
        $this->assertEquals(1, $rule->fresh()->conditions()->count());

        $condition = $rule->conditions()->first();

        // 4. Add Action
        $actResponse = $this->actingAs($this->superAdmin)->post("/system/rbl/{$rule->id}/actions", [
            'action_type' => 'App\Services\RuleEngine\Actions\SendNotificationAction',
            'action_params' => json_encode(['role' => 'manager', 'message' => 'Stok kritis']),
        ]);
        $actResponse->assertRedirect();
        $this->assertEquals(1, $rule->fresh()->actions()->count());

        $action = $rule->actions()->first();

        // 5. Delete Condition & Action
        $delCondResponse = $this->actingAs($this->superAdmin)->delete("/system/rbl/conditions/{$condition->id}");
        $delCondResponse->assertRedirect();
        $this->assertEquals(0, $rule->fresh()->conditions()->count());

        $delActResponse = $this->actingAs($this->superAdmin)->delete("/system/rbl/actions/{$action->id}");
        $delActResponse->assertRedirect();
        $this->assertEquals(0, $rule->fresh()->actions()->count());

        // 6. Delete Rule
        $delRuleResponse = $this->actingAs($this->superAdmin)->delete("/system/rbl/{$rule->id}");
        $delRuleResponse->assertRedirect('/system/rbl');
        $this->assertNull(Rule::find($rule->id));
    }

    public function test_superadmin_can_save_system_settings(): void
    {
        Storage::fake('public');

        $bgFile = UploadedFile::fake()->image('custom_bg.jpg', 1920, 1080);

        $response = $this->actingAs($this->superAdmin)->post('/system/settings', [
            'company_name' => 'PT Inventra Jaya Logistik',
            'company_phone' => '081122334455',
            'company_email' => 'contact@inventra.go.id',
            'company_address' => 'Gedung Cyber 2, Jakarta',
            'company_npwp' => '01.999.888.7-000.000',
            'company_website' => 'https://inventra.go.id',
            'app_timezone' => 'Asia/Jakarta',
            'default_tax_rate' => 11,
            'min_stock_alert_threshold' => 15,
            'login_bg' => $bgFile,
        ]);

        $response->assertRedirect('/system/settings');

        $this->assertEquals('PT Inventra Jaya Logistik', Setting::get('company_name'));
        $this->assertEquals('15', Setting::get('min_stock_alert_threshold'));
        $this->assertNotNull(Setting::get('login_bg'));
        Storage::disk('public')->assertExists(Setting::get('login_bg'));

        // Test login page as guest shows the custom background
        auth()->logout();
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200)
            ->assertSee(Setting::get('login_bg'));

        // Test resetting login background
        $resetResponse = $this->actingAs($this->superAdmin)->post('/system/settings', [
            'company_name' => 'PT Inventra Jaya Logistik',
            'remove_login_bg' => 1,
        ]);
        $resetResponse->assertRedirect('/system/settings');
        $this->assertNull(Setting::get('login_bg'));
    }

    public function test_superadmin_users_page_returns_correct_role_counts(): void
    {
        // Create diverse users
        User::factory()->create(['role' => 'kasir', 'is_active' => true]);
        User::factory()->create(['role' => 'manager', 'is_active' => true]);
        User::factory()->create(['role' => 'admin_gudang', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin)->get('/system/users');
        $response->assertStatus(200)
            ->assertViewHas('counts', function ($counts) {
                return $counts['total'] >= 4 &&
                    $counts['super_admin'] >= 1 &&
                    $counts['kasir'] >= 1 &&
                    $counts['manager'] >= 1 &&
                    $counts['admin_gudang'] >= 1;
            });
    }

    public function test_superadmin_can_toggle_user_active_status(): void
    {
        $user = User::factory()->create([
            'name' => 'Target User',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        // Toggle to inactive (blocked)
        $toggleResponse = $this->actingAs($this->superAdmin)->post("/system/users/{$user->id}/toggle-active");
        $toggleResponse->assertRedirect();
        $this->assertFalse($user->fresh()->is_active);

        // Toggle back to active
        $toggleAgain = $this->actingAs($this->superAdmin)->post("/system/users/{$user->id}/toggle-active");
        $toggleAgain->assertRedirect();
        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_superadmin_can_soft_delete_and_restore_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Delete Target',
            'role' => 'admin_gudang',
        ]);

        // Soft delete
        $delResponse = $this->actingAs($this->superAdmin)->delete("/system/users/{$user->id}");
        $delResponse->assertRedirect();
        $this->assertTrue($user->fresh()->trashed());

        // Restore
        $restoreResponse = $this->actingAs($this->superAdmin)->post("/system/users/{$user->id}/restore");
        $restoreResponse->assertRedirect();
        $this->assertFalse($user->fresh()->trashed());
    }

    public function test_superadmin_can_manage_work_shifts_and_assign_schedules(): void
    {
        // 1. Create Work Shift
        $shiftResponse = $this->actingAs($this->superAdmin)->post('/system/schedules/shifts', [
            'name' => 'Shift Pagi Khusus',
            'start_time' => '07:00',
            'end_time' => '15:00',
            'color_hex' => '#4F46E5',
        ]);
        $shiftResponse->assertRedirect();

        $shift = WorkShift::where('name', 'Shift Pagi Khusus')->first();
        $this->assertNotNull($shift);

        // 2. Assign Schedule to User
        $user = User::factory()->create(['role' => 'kasir']);
        $assignResponse = $this->actingAs($this->superAdmin)->post('/system/schedules', [
            'user_id' => $user->id,
            'work_shift_id' => $shift->id,
            'date' => now()->toDateString(),
        ]);
        $assignResponse->assertRedirect();

        $this->assertDatabaseHas('user_schedules', [
            'user_id' => $user->id,
            'work_shift_id' => $shift->id,
        ]);
    }

    public function test_superadmin_can_filter_audit_logs(): void
    {
        AuditLog::record('POS_SALE', 'Transaction', 101, null, ['amount' => 150000]);
        AuditLog::record('SYSTEM_SETTINGS_UPDATED', 'Setting', null, null, ['company' => 'Inventra']);

        $response = $this->actingAs($this->superAdmin)->get('/system/audit?action=POS_SALE');
        $response->assertStatus(200)
            ->assertSee('POS_SALE', false);
    }
}
