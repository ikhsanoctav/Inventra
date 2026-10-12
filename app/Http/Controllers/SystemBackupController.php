<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemBackupController extends Controller
{
    public function __construct(protected BackupService $backupService) {}

    /**
     * Display the database backup & maintenance center.
     */
    public function index()
    {
        $backups = $this->backupService->listBackups();
        $health = $this->backupService->getSystemHealth();

        return view('system.backup.index', compact('backups', 'health'));
    }

    /**
     * Generate a new database backup snapshot.
     */
    public function create()
    {
        try {
            $result = $this->backupService->createBackup();

            return redirect()->route('system.backup')->with('success', "File cadangan baru [{$result['filename']}] berhasil dibuat dan masuk ke riwayat. Silakan unduh file melalui tabel di bawah.");
        } catch (\Exception $e) {
            return redirect()->route('system.backup')->with('error', 'Gagal membuat cadangan database: '.$e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(string $filename): BinaryFileResponse
    {
        $path = 'backups/'.$filename;
        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File cadangan tidak ditemukan.');
        }

        AuditLog::record('DATABASE_BACKUP_DOWNLOADED', 'System', null, null, [
            'filename' => $filename,
            'downloaded_by' => auth()->user()->name,
        ]);

        $fullPath = Storage::disk('local')->path($path);

        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Restore database from an existing backup snapshot.
     */
    public function restore(Request $request, string $filename)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Security verification with superadmin password
        if (! Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Kata sandi konfirmasi salah! Pemulihan database dibatalkan.');
        }

        try {
            $result = $this->backupService->restoreBackup($filename);

            return redirect()->route('system.backup')->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('system.backup')->with('error', 'Gagal memulihkan database: '.$e->getMessage());
        }
    }

    /**
     * Upload an external backup file and restore it.
     */
    public function uploadRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200', // max 50MB
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Kata sandi konfirmasi salah! Pemulihan database dibatalkan.');
        }

        try {
            $file = $request->file('backup_file');
            $content = file_get_contents($file->getRealPath());

            $result = $this->backupService->restoreFromFile($content, $file->getClientOriginalName());

            return redirect()->route('system.backup')->with('success', "File eksternal berhasil dipulihkan: {$result['message']}");
        } catch (\Exception $e) {
            return redirect()->route('system.backup')->with('error', 'Gagal memulihkan file cadangan: '.$e->getMessage());
        }
    }

    /**
     * Delete a backup file.
     */
    public function destroy(string $filename)
    {
        if ($this->backupService->deleteBackup($filename)) {
            return redirect()->route('system.backup')->with('success', "File cadangan {$filename} berhasil dihapus.");
        }

        return redirect()->route('system.backup')->with('error', 'File cadangan tidak ditemukan.');
    }

    /**
     * Clear all application caches.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            AuditLog::record('SYSTEM_CACHE_CLEARED', 'System', null, null, [
                'cleared_by' => auth()->user()->name,
            ]);

            return redirect()->route('system.backup')->with('success', 'Seluruh cache sistem (Application, Config, View, Route) berhasil dibersihkan.');
        } catch (\Exception $e) {
            return redirect()->route('system.backup')->with('error', 'Gagal membersihkan cache: '.$e->getMessage());
        }
    }

    /**
     * Optimize application cache & routing.
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('view:cache');

            AuditLog::record('SYSTEM_OPTIMIZED', 'System', null, null, [
                'optimized_by' => auth()->user()->name,
            ]);

            return redirect()->route('system.backup')->with('success', 'Aplikasi berhasil dioptimasi.');
        } catch (\Exception $e) {
            return redirect()->route('system.backup')->with('error', 'Gagal mengoptimasi aplikasi: '.$e->getMessage());
        }
    }
}
