<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    protected string $backupDir = 'backups';

    public function __construct()
    {
        if (! Storage::disk('local')->exists($this->backupDir)) {
            Storage::disk('local')->makeDirectory($this->backupDir);
        }
    }

    /**
     * Create a complete database backup snapshot.
     */
    public function createBackup(): array
    {
        $tableNames = $this->getAllTableNames();
        $backupData = [
            'app' => config('app.name', 'INVENTRA'),
            'version' => '1.0',
            'created_at' => now()->toIso8601String(),
            'created_by' => auth()->user()->name ?? 'System',
            'driver' => DB::getDriverName(),
            'tables' => [],
            'stats' => [
                'total_tables' => 0,
                'total_rows' => 0,
            ],
        ];

        $totalRows = 0;
        $excludedTables = ['migrations', 'sqlite_sequence', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'personal_access_tokens'];

        foreach ($tableNames as $table) {
            if (in_array($table, $excludedTables)) {
                continue;
            }

            $rows = DB::table($table)->get()->map(function ($row) {
                return (array) $row;
            })->toArray();

            $rowCount = count($rows);
            $totalRows += $rowCount;

            $backupData['tables'][$table] = [
                'count' => $rowCount,
                'data' => $rows,
            ];
        }

        $backupData['stats']['total_tables'] = count($backupData['tables']);
        $backupData['stats']['total_rows'] = $totalRows;

        $filename = 'backup_inventra_'.now()->format('Ymd_His').'.sql';
        $jsonFilename = 'backup_inventra_'.now()->format('Ymd_His').'.json';
        $path = $this->backupDir.'/'.$filename;
        $jsonPath = $this->backupDir.'/'.$jsonFilename;

        // Save JSON structured snapshot
        $jsonContent = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::disk('local')->put($jsonPath, $jsonContent);

        // Save SQL Dump file
        $sqlContent = "-- ==========================================================\n";
        $sqlContent .= "-- INVENTRA Enterprise Database Backup\n";
        $sqlContent .= '-- Generated at: '.now()->format('Y-m-d H:i:s')."\n";
        $sqlContent .= '-- Driver: '.DB::getDriverName()."\n";
        $sqlContent .= '-- Total Tables: '.count($backupData['tables'])."\n";
        $sqlContent .= '-- Total Rows: '.$totalRows."\n";
        $sqlContent .= "-- ==========================================================\n\n";

        foreach ($backupData['tables'] as $tableName => $tableInfo) {
            $sqlContent .= "-- Table: {$tableName} (".count($tableInfo['data'])." rows)\n";
            if (! empty($tableInfo['data'])) {
                foreach ($tableInfo['data'] as $row) {
                    $columns = array_map(fn ($col) => '`'.addslashes($col).'`', array_keys($row));
                    $values = array_map(function ($val) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        if (is_numeric($val) && ! is_string($val)) {
                            return $val;
                        }

                        return "'".addslashes((string) $val)."'";
                    }, array_values($row));
                    $sqlContent .= "INSERT INTO `{$tableName}` (".implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n";
                }
            }
            $sqlContent .= "\n";
        }

        Storage::disk('local')->put($path, $sqlContent);

        $size = Storage::disk('local')->size($path);

        AuditLog::record('DATABASE_BACKUP_CREATED', 'System', null, null, [
            'filename' => $filename,
            'tables_count' => $backupData['stats']['total_tables'],
            'rows_count' => $totalRows,
            'size_kb' => round($size / 1024, 2),
        ]);

        return [
            'filename' => $filename,
            'path' => $path,
            'size_bytes' => $size,
            'size_formatted' => $this->formatBytes($size),
            'tables_count' => $backupData['stats']['total_tables'],
            'rows_count' => $totalRows,
            'created_at' => now()->format('d M Y H:i:s'),
        ];
    }

    /**
     * List all available backup files.
     */
    public function listBackups(): array
    {
        $files = Storage::disk('local')->files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if (! str_ends_with($file, '.json') && ! str_ends_with($file, '.sql')) {
                continue;
            }

            // If .sql companion exists, avoid showing separate duplicate row for .json
            if (str_ends_with($file, '.json')) {
                $sqlCompanion = str_replace('.json', '.sql', $file);
                if (Storage::disk('local')->exists($sqlCompanion)) {
                    continue;
                }
            }

            $filename = basename($file);
            $size = Storage::disk('local')->size($file);
            $lastModified = Storage::disk('local')->lastModified($file);

            $meta = null;
            $jsonFile = str_ends_with($file, '.sql') ? str_replace('.sql', '.json', $file) : $file;
            if (Storage::disk('local')->exists($jsonFile)) {
                try {
                    $content = Storage::disk('local')->get($jsonFile);
                    $decoded = json_decode($content, true);
                    if ($decoded && isset($decoded['stats'])) {
                        $meta = $decoded['stats'];
                    }
                } catch (\Throwable) {
                    // Ignore decoding error for corrupt file
                }
            }

            $backups[] = [
                'filename' => $filename,
                'path' => $file,
                'size_bytes' => $size,
                'size_formatted' => $this->formatBytes($size),
                'total_tables' => $meta['total_tables'] ?? '-',
                'total_rows' => $meta['total_rows'] ?? '-',
                'created_at' => date('d M Y H:i:s', $lastModified),
                'timestamp' => $lastModified,
            ];
        }

        // Sort latest first
        usort($backups, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Restore database from an existing backup file.
     */
    public function restoreBackup(string $filename): array
    {
        $path = $this->backupDir.'/'.$filename;
        if (str_ends_with($filename, '.sql')) {
            $jsonFilename = str_replace('.sql', '.json', $filename);
            if (Storage::disk('local')->exists($this->backupDir.'/'.$jsonFilename)) {
                $path = $this->backupDir.'/'.$jsonFilename;
                $filename = $jsonFilename;
            }
        }

        if (! Storage::disk('local')->exists($path)) {
            throw new \Exception("File backup {$filename} tidak ditemukan.");
        }

        $content = Storage::disk('local')->get($path);

        return $this->restoreFromFile($content, $filename);
    }

    /**
     * Restore database from file content (.sql or .json).
     */
    public function restoreFromFile(string $content, string $filename): array
    {
        $trimmed = trim($content);
        if (str_starts_with($trimmed, '{') || str_ends_with(strtolower($filename), '.json')) {
            return $this->restoreFromJson($content, $filename);
        }

        return $this->restoreFromSql($content, $filename);
    }

    /**
     * Restore database from raw SQL queries.
     */
    public function restoreFromSql(string $sqlContent, string $filename): array
    {
        $driver = DB::getDriverName();
        $executed = 0;

        DB::transaction(function () use ($driver, $sqlContent, &$executed) {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } elseif ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            }

            try {
                $lines = explode("\n", $sqlContent);
                $query = '';
                foreach ($lines as $line) {
                    $trimLine = trim($line);
                    if (empty($trimLine) || str_starts_with($trimLine, '--')) {
                        continue;
                    }
                    $query .= $line."\n";
                    if (str_ends_with($trimLine, ';')) {
                        DB::unprepared($query);
                        $query = '';
                        $executed++;
                    }
                }
                if (! empty(trim($query))) {
                    DB::unprepared($query);
                    $executed++;
                }
            } finally {
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } elseif ($driver === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                }
            }
        });

        AuditLog::record('DATABASE_RESTORED_SQL', 'System', null, null, [
            'filename' => $filename,
            'statements_executed' => $executed,
        ]);

        return [
            'status' => 'success',
            'tables_count' => 0,
            'rows_count' => $executed,
            'message' => "Berhasil memulihkan database dari file SQL ({$executed} query dieksekusi).",
        ];
    }

    /**
     * Restore database from JSON content string.
     */
    public function restoreFromJson(string $jsonContent, ?string $filename = null): array
    {
        $backupData = json_decode($jsonContent, true);
        if (! $backupData || ! isset($backupData['tables'])) {
            throw new \Exception('Format file cadangan tidak valid atau rusak.');
        }

        $restoredTables = 0;
        $restoredRows = 0;
        $driver = DB::getDriverName();

        DB::transaction(function () use ($backupData, $driver, &$restoredTables, &$restoredRows) {
            // Disable foreign key constraints
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } elseif ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            }

            $excludedTables = ['migrations', 'sqlite_sequence', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'personal_access_tokens'];

            try {
                foreach ($backupData['tables'] as $table => $tableInfo) {
                    if (in_array($table, $excludedTables) || ! Schema::hasTable($table)) {
                        continue;
                    }

                    // Truncate / delete table contents
                    DB::table($table)->delete();

                    if (! empty($tableInfo['data'])) {
                        // Chunk insert to avoid memory/parameter limits
                        $chunks = array_chunk($tableInfo['data'], 200);
                        foreach ($chunks as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }

                    $restoredTables++;
                    $restoredRows += $tableInfo['count'] ?? count($tableInfo['data']);
                }
            } finally {
                // Re-enable foreign key constraints
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } elseif ($driver === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                }
            }
        });

        AuditLog::record('DATABASE_RESTORED', 'System', null, null, [
            'filename' => $filename ?? 'Uploaded File',
            'restored_tables' => $restoredTables,
            'restored_rows' => $restoredRows,
        ]);

        return [
            'status' => 'success',
            'tables_count' => $restoredTables,
            'rows_count' => $restoredRows,
            'message' => "Berhasil memulihkan database ({$restoredTables} tabel, {$restoredRows} baris data).",
        ];
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $path = $this->backupDir.'/'.$filename;
        $deleted = false;

        if (Storage::disk('local')->exists($path)) {
            $deleted = Storage::disk('local')->delete($path);
        }

        // Clean up companion file if exists
        if (str_ends_with($filename, '.sql')) {
            $jsonPath = $this->backupDir.'/'.str_replace('.sql', '.json', $filename);
            if (Storage::disk('local')->exists($jsonPath)) {
                Storage::disk('local')->delete($jsonPath);
            }
        } elseif (str_ends_with($filename, '.json')) {
            $sqlPath = $this->backupDir.'/'.str_replace('.json', '.sql', $filename);
            if (Storage::disk('local')->exists($sqlPath)) {
                Storage::disk('local')->delete($sqlPath);
            }
        }

        if ($deleted) {
            AuditLog::record('DATABASE_BACKUP_DELETED', 'System', null, null, [
                'filename' => $filename,
            ]);
        }

        return $deleted;
    }

    /**
     * Get system health and database statistics.
     */
    public function getSystemHealth(): array
    {
        $driver = DB::getDriverName();
        $dbName = config("database.connections.{$driver}.database");

        $stats = [
            'driver' => strtoupper($driver),
            'database_name' => basename((string) $dbName),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS_FAMILY.' ('.php_uname('m').')',
            'server_time' => now()->format('d M Y H:i:s T'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'storage_size' => $this->formatBytes($this->getDirectorySize(storage_path('app'))),
            'counts' => [
                'users' => DB::table('users')->count(),
                'items' => Schema::hasTable('items') ? DB::table('items')->count() : 0,
                'transactions' => Schema::hasTable('transactions') ? DB::table('transactions')->count() : 0,
                'suppliers' => Schema::hasTable('suppliers') ? DB::table('suppliers')->count() : 0,
                'audit_logs' => Schema::hasTable('audit_logs') ? DB::table('audit_logs')->count() : 0,
                'rules' => Schema::hasTable('rules') ? DB::table('rules')->count() : 0,
                'branches' => Schema::hasTable('branches') ? DB::table('branches')->count() : 0,
            ],
        ];

        return $stats;
    }

    /**
     * Helper to get all table names safely.
     */
    protected function getAllTableNames(): array
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

            return array_map(fn ($t) => $t->name, $tables);
        }

        if ($driver === 'mysql') {
            $tables = DB::select('SHOW TABLES');

            return array_map(fn ($t) => array_values((array) $t)[0], $tables);
        }

        if ($driver === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'");

            return array_map(fn ($t) => $t->tablename, $tables);
        }

        return ['users', 'items', 'categories', 'units', 'suppliers', 'transactions', 'transaction_lines', 'settings', 'audit_logs', 'rules', 'rule_conditions', 'rule_actions', 'schedules', 'shifts', 'branches', 'outlets'];
    }

    /**
     * Calculate directory size.
     */
    protected function getDirectorySize(string $path): int
    {
        $size = 0;
        if (is_dir($path)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * Format bytes into human readable string.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
