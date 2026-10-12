@extends('layouts.app')

@section('header_title', 'Cadangan & Pemulihan Database')

@section('main_content')
<div class="space-y-6" x-data="{ 
    showUploadModal: false,
    showRestoreModal: false,
    restoreFilename: '',
    selectedFileText: '',
    handleFileSelected(event) {
        const file = event.target.files[0];
        if (file) {
            this.selectedFileText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        } else {
            this.selectedFileText = '';
        }
    },
    openRestoreModal(filename) {
        this.restoreFilename = filename;
        this.showRestoreModal = true;
    }
}">
    <!-- Top Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[28px]">database</span>
                Cadangan & Pemulihan Database
            </h2>
            <p class="text-sm text-slate-500 mt-1">Unduh salinan data sistem, unggah & pulihkan (restore) backup, atau bersihkan cache aplikasi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Generate Backup Button -->
            <form action="{{ route('system.backup.create') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm shadow-indigo-500/20 flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    Buat Cadangan Baru (.sql)
                </button>
            </form>

            <!-- Upload & Restore Button -->
            <button type="button" @click="showUploadModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm shadow-emerald-500/20 flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                Unggah & Pulihkan
            </button>

            <!-- Clear Cache Button -->
            <form action="{{ route('system.backup.clear_cache') }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-2.5 bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5 cursor-pointer" title="Bersihkan cache aplikasi">
                    <span class="material-symbols-outlined text-[18px]">cached</span>
                    Bersihkan Cache
                </button>
            </form>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">folder_zip</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total File Cadangan</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ count($backups) }} File</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">storage</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Penyimpanan Storage</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $health['storage_size'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">dns</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Basis Data (Driver)</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $health['driver'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">table_rows</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Master Barang</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ number_format($health['counts']['items'] ?? 0) }} Item</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: List of Backups Table (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-600 text-[20px]">history</span>
                            Riwayat File Cadangan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar file cadangan database yang pernah dibuat di server.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Nama File</th>
                                <th class="py-3 px-4">Waktu Dibuat</th>
                                <th class="py-3 px-4">Ukuran</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($backups as $b)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-slate-800 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-indigo-500 text-[18px]">description</span>
                                    <span>{{ $b['filename'] }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $b['created_at'] }}</td>
                                <td class="py-3 px-4 text-slate-700 font-semibold">{{ $b['size_formatted'] }}</td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Download Button -->
                                        <a href="{{ route('system.backup.download', $b['filename']) }}" download="{{ $b['filename'] }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold text-xs rounded-xl transition-all shadow-sm border border-indigo-100 cursor-pointer" title="Unduh File Ini">
                                            <span class="material-symbols-outlined text-[15px]">download</span>
                                            <span>Unduh</span>
                                        </a>

                                        <!-- Restore Button -->
                                        <button type="button" @click="openRestoreModal('{{ $b['filename'] }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 font-bold text-xs rounded-xl transition-all shadow-sm border border-emerald-100 cursor-pointer" title="Pulihkan (Restore) dari File Ini">
                                            <span class="material-symbols-outlined text-[15px]">settings_backup_restore</span>
                                            <span>Pulihkan</span>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('system.backup.destroy', $b['filename']) }}" method="POST" onsubmit="return confirm('Hapus file cadangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer" title="Hapus File Cadangan">
                                                <span class="material-symbols-outlined text-[17px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <span class="material-symbols-outlined text-[24px]">folder_off</span>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum Ada File Cadangan</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Unduh Cadangan Database (.sql)" di atas untuk membuat dan mengunduh salinan database.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: System Diagnostics & Health (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Server Diagnostics Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 font-headline flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-indigo-600 text-[18px]">info</span>
                    Informasi Sistem
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-500">Framework</span>
                        <span class="font-bold text-slate-800">Laravel v{{ $health['laravel_version'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-500">PHP Version</span>
                        <span class="font-bold text-slate-800">v{{ $health['php_version'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-500">Sistem Operasi</span>
                        <span class="font-semibold text-slate-800">{{ $health['server_os'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-500">Waktu Server</span>
                        <span class="font-mono text-slate-700">{{ $health['server_time'] }}</span>
                    </div>
                </div>

                <!-- Optimization Action -->
                <div class="pt-3 border-t border-slate-100">
                    <form action="{{ route('system.backup.optimize') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-200 hover:border-indigo-200 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">bolt</span>
                            Jalankan Optimasi Cache
                        </button>
                    </form>
                </div>
            </div>

            <!-- Database Records Summary Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
                <h3 class="text-sm font-bold text-slate-800 font-headline flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="material-symbols-outlined text-indigo-600 text-[18px]">query_stats</span>
                    Total Data Tersimpan
                </h3>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Pengguna</span>
                        <span class="text-base font-extrabold text-slate-800">{{ number_format($health['counts']['users'] ?? 0) }}</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Transaksi</span>
                        <span class="text-base font-extrabold text-slate-800">{{ number_format($health['counts']['transactions'] ?? 0) }}</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Pemasok</span>
                        <span class="text-base font-extrabold text-slate-800">{{ number_format($health['counts']['suppliers'] ?? 0) }}</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Log Audit</span>
                        <span class="text-base font-extrabold text-slate-800">{{ number_format($health['counts']['audit_logs'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL 1: Unggah & Pulihkan File Eksternal -->
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showUploadModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">upload_file</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 font-headline">Unggah & Pulihkan Cadangan</h3>
                            <p class="text-xs text-slate-500">Restore database dari file .sql atau .json eksternal</p>
                        </div>
                    </div>
                    <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Warning Notice -->
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-[20px] shrink-0 mt-0.5">warning</span>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        <strong class="font-bold">Peringatan:</strong> Memulihkan database akan menimpa seluruh data tabel yang ada. Pastikan Anda telah mengunduh backup terbaru sebelum melanjutkan.
                    </p>
                </div>

                <form action="{{ route('system.backup.upload_restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- File Upload Input Area -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Pilih File Cadangan (.sql / .json)</label>
                        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-5 text-center transition-colors bg-slate-50 hover:bg-emerald-50/40 cursor-pointer">
                            <input type="file" name="backup_file" accept=".sql,.json" required @change="handleFileSelected" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="space-y-1">
                                <span class="material-symbols-outlined text-3xl text-emerald-600">cloud_upload</span>
                                <p class="text-xs font-bold text-slate-700" x-text="selectedFileText ? selectedFileText : 'Klik atau seret file cadangan ke sini'"></p>
                                <p class="text-[11px] text-slate-400">Mendukung file dump .sql dan snapshot .json (Maks. 50MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Superadmin Password Confirmation -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Kata Sandi Akun Super Admin <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Masukkan kata sandi untuk verifikasi keamanan" class="w-full h-10 px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-[11px] text-slate-400">Verifikasi kata sandi diperlukan demi keamanan integritas sistem.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showUploadModal = false" class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 text-xs font-bold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-emerald-500/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">settings_backup_restore</span>
                            Mulai Pemulihan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Konfirmasi Pulihkan dari Riwayat Server -->
    <div x-show="showRestoreModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showRestoreModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">settings_backup_restore</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 font-headline">Pulihkan Database</h3>
                            <p class="text-xs text-slate-500">Pulihkan sistem dari file riwayat server</p>
                        </div>
                    </div>
                    <button type="button" @click="showRestoreModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">File Cadangan Dipilih:</span>
                    <p class="font-mono text-xs font-bold text-indigo-700 break-all" x-text="restoreFilename"></p>
                </div>

                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-[20px] shrink-0 mt-0.5">warning</span>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Seluruh data database saat ini akan digantikan oleh data dari file cadangan ini.
                    </p>
                </div>

                <form :action="'/system/backup/restore/' + restoreFilename" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Kata Sandi Super Admin <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Masukkan kata sandi untuk konfirmasi" class="w-full h-10 px-3.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showRestoreModal = false" class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 text-xs font-bold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-amber-500/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">settings_backup_restore</span>
                            Konfirmasi & Pulihkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
