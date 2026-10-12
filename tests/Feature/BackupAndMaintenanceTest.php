<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupAndMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::firstOrCreate(['id' => 1], ['name' => 'Main Tenant']);

        $this->superAdmin = User::factory()->create([
            'nip' => '10000001',
            'name' => 'Super Admin',
            'email' => 'superadmin@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->manager = User::factory()->create([
            'nip' => '10000002',
            'name' => 'Manager User',
            'email' => 'manager@inventra.go.id',
            'password' => bcrypt('password123'),
            'role' => 'manager',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Storage::disk('local')->deleteDirectory('backups');
        parent::tearDown();
    }

    public function test_superadmin_can_access_backup_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/system/backup');
        $response->assertStatus(200)
            ->assertSee('Cadangan & Pemulihan Database', false)
            ->assertSee('Riwayat File Cadangan', false);
    }

    public function test_non_superadmin_cannot_access_backup_page(): void
    {
        $response = $this->actingAs($this->manager)->get('/system/backup');
        $response->assertStatus(403);
    }

    public function test_superadmin_can_create_backup_and_download(): void
    {
        $createResponse = $this->actingAs($this->superAdmin)->post('/system/backup/create');
        $createResponse->assertRedirect('/system/backup')
            ->assertSessionHas('success');

        $backupFiles = Storage::disk('local')->files('backups');
        $this->assertNotEmpty($backupFiles);

        $filename = basename($backupFiles[0]);

        $downloadResponse = $this->actingAs($this->superAdmin)->get("/system/backup/download/{$filename}");
        $downloadResponse->assertStatus(200);
    }

    public function test_superadmin_can_restore_backup_with_valid_password(): void
    {
        // 1. Create a backup
        $this->actingAs($this->superAdmin)->post('/system/backup/create');
        $backupFiles = Storage::disk('local')->files('backups');
        $filename = basename($backupFiles[0]);

        // 2. Restore with valid password
        $restoreResponse = $this->actingAs($this->superAdmin)->post("/system/backup/restore/{$filename}", [
            'password' => 'password123',
        ]);
        $restoreResponse->assertRedirect('/system/backup')
            ->assertSessionHas('success');
    }

    public function test_superadmin_cannot_restore_with_invalid_password(): void
    {
        $this->actingAs($this->superAdmin)->post('/system/backup/create');
        $backupFiles = Storage::disk('local')->files('backups');
        $filename = basename($backupFiles[0]);

        $restoreResponse = $this->actingAs($this->superAdmin)->post("/system/backup/restore/{$filename}", [
            'password' => 'wrongpassword',
        ]);
        $restoreResponse->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_superadmin_can_delete_backup(): void
    {
        $this->actingAs($this->superAdmin)->post('/system/backup/create');
        $backupFiles = Storage::disk('local')->files('backups');
        $filename = basename($backupFiles[0]);

        $deleteResponse = $this->actingAs($this->superAdmin)->delete("/system/backup/{$filename}");
        $deleteResponse->assertRedirect('/system/backup')
            ->assertSessionHas('success');

        $this->assertFalse(Storage::disk('local')->exists("backups/{$filename}"));
    }

    public function test_superadmin_can_clear_cache_and_optimize(): void
    {
        $cacheResponse = $this->actingAs($this->superAdmin)->post('/system/backup/clear-cache');
        $cacheResponse->assertRedirect('/system/backup')
            ->assertSessionHas('success');

        $optimizeResponse = $this->actingAs($this->superAdmin)->post('/system/backup/optimize');
        $optimizeResponse->assertRedirect('/system/backup')
            ->assertSessionHas('success');
    }

    public function test_superadmin_can_upload_and_restore_backup(): void
    {
        $this->actingAs($this->superAdmin)->post('/system/backup/create');
        $backupFiles = Storage::disk('local')->files('backups');
        $filename = basename($backupFiles[0]);
        $content = Storage::disk('local')->get($backupFiles[0]);

        $uploadedFile = UploadedFile::fake()->createWithContent($filename, $content);

        $response = $this->actingAs($this->superAdmin)->post('/system/backup/upload-restore', [
            'backup_file' => $uploadedFile,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/system/backup')
            ->assertSessionHas('success');
    }
}
