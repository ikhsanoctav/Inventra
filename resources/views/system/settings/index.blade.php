@extends('layouts.app')

@section('header_title', 'Pengaturan Sistem')

@section('main_content')
<div class="space-y-6" x-data="{ 
    activeTab: 'company',
    bgPreview: '{{ isset($settings['login_bg']) && $settings['login_bg'] ? Storage::url($settings['login_bg']) : '' }}',
    removeBg: false,
    fileChosen: false,
    handleBgChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.bgPreview = e.target.result;
                this.removeBg = false;
                this.fileChosen = true;
            };
            reader.readAsDataURL(file);
        }
    },
    resetBg() {
        this.bgPreview = '';
        this.removeBg = true;
        this.fileChosen = false;
        if (this.$refs.bgInput) {
            this.$refs.bgInput.value = '';
        }
    }
}">
    <form action="{{ route('system.settings') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="font-headline text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600 text-[28px]">settings</span>
                    Pengaturan Sistem
                </h2>
                <p class="text-sm text-slate-500 mt-1">Konfigurasi profil perusahaan, background login, preferensi aplikasi, tarif pajak, dan notifikasi.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-500/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
            
            <!-- Sidebar Navigation for Settings -->
            <div class="lg:col-span-3 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 px-2">Kategori Pengaturan</div>
                
                <button type="button" @click="activeTab = 'company'" :class="activeTab === 'company' ? 'bg-indigo-50 text-indigo-700 font-bold border-indigo-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'" class="w-full text-left px-4 py-3 rounded-xl transition-all flex items-center gap-3 border shadow-sm">
                    <span class="material-symbols-outlined text-[20px]" :class="activeTab === 'company' ? 'text-indigo-600' : 'text-slate-400'">apartment</span>
                    Profil Perusahaan
                </button>

                <button type="button" @click="activeTab = 'appearance'" :class="activeTab === 'appearance' ? 'bg-indigo-50 text-indigo-700 font-bold border-indigo-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'" class="w-full text-left px-4 py-3 rounded-xl transition-all flex items-center gap-3 border shadow-sm">
                    <span class="material-symbols-outlined text-[20px]" :class="activeTab === 'appearance' ? 'text-indigo-600' : 'text-slate-400'">wallpaper</span>
                    Tampilan & Background
                </button>

                <button type="button" @click="activeTab = 'preferences'" :class="activeTab === 'preferences' ? 'bg-indigo-50 text-indigo-700 font-bold border-indigo-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'" class="w-full text-left px-4 py-3 rounded-xl transition-all flex items-center gap-3 border shadow-sm">
                    <span class="material-symbols-outlined text-[20px]" :class="activeTab === 'preferences' ? 'text-indigo-600' : 'text-slate-400'">tune</span>
                    Preferensi Sistem
                </button>

                <button type="button" @click="activeTab = 'finance'" :class="activeTab === 'finance' ? 'bg-indigo-50 text-indigo-700 font-bold border-indigo-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'" class="w-full text-left px-4 py-3 rounded-xl transition-all flex items-center gap-3 border shadow-sm">
                    <span class="material-symbols-outlined text-[20px]" :class="activeTab === 'finance' ? 'text-indigo-600' : 'text-slate-400'">payments</span>
                    Keuangan & Pajak
                </button>

                <button type="button" @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-indigo-50 text-indigo-700 font-bold border-indigo-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'" class="w-full text-left px-4 py-3 rounded-xl transition-all flex items-center gap-3 border shadow-sm">
                    <span class="material-symbols-outlined text-[20px]" :class="activeTab === 'notifications' ? 'text-indigo-600' : 'text-slate-400'">notifications</span>
                    Notifikasi Sistem
                </button>

                <div class="pt-4 border-t border-slate-200">
                    <a href="{{ route('profile.index') }}" class="w-full text-left px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-all flex items-center gap-3 border border-transparent">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">person</span>
                        Profil Saya (Akun)
                    </a>
                </div>
            </div>
            
            <!-- Main Form Area -->
            <div class="lg:col-span-9">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
                    
                    <!-- TAB 1: Profil Perusahaan -->
                    <div x-show="activeTab === 'company'" class="space-y-6">
                        <div class="border-b border-slate-100 pb-5">
                            <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-600">apartment</span>
                                Informasi Entitas & Profil Perusahaan
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Data identitas perusahaan yang akan dicantumkan pada invoice, surat jalan, dan struk POS.</p>
                        </div>
                        
                        <!-- Logo Upload -->
                        <div class="flex flex-col sm:flex-row gap-6 items-start">
                            <div class="w-48 h-24 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-300 flex items-center justify-center text-slate-400 shrink-0 relative overflow-hidden group hover:border-indigo-400 hover:bg-indigo-50/50 transition-all cursor-pointer p-2 shadow-inner">
                                @if(isset($settings['app_logo']) && $settings['app_logo'])
                                    <img src="{{ Storage::url($settings['app_logo']) }}" alt="Logo" class="w-full h-full object-contain scale-110">
                                @else
                                    <div class="flex flex-col items-center justify-center gap-1 text-slate-400 group-hover:text-indigo-600 transition-colors">
                                        <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                                        <span class="text-[11px] font-bold">Pilih File Logo</span>
                                    </div>
                                @endif
                                <input type="file" name="app_logo" accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-slate-800">Logo Resmi Perusahaan</h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    Disarankan file format <strong>PNG Transparan</strong> dengan rasio horizontal, ukuran maks. 2MB. Logo ini otomatis tampil pada sidebar aplikasi, lembar nota/struk kasir POS, dan kop laporan cetak.
                                </p>
                                @error('app_logo')<span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-bold text-slate-700">Nama Perusahaan / Usaha <span class="text-red-500">*</span></label>
                                <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'INVENTRA Logistics Corp') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('company_name')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Nomor Telepon Operasional</label>
                                <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '+62 812-3456-7890') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Email Kontak Resmi</label>
                                <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? 'admin@inventra.go.id') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-bold text-slate-700">Alamat Kantor / Gudang Utama</label>
                                <textarea name="company_address" rows="3" class="w-full p-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('company_address', $settings['company_address'] ?? 'Kawasan Industri Logistik No. 88, Jakarta Barat') }}</textarea>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">NPWP Perusahaan</label>
                                <input type="text" name="company_npwp" value="{{ old('company_npwp', $settings['company_npwp'] ?? '01.234.567.8-901.000') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Website Resmi</label>
                                <input type="url" name="company_website" value="{{ old('company_website', $settings['company_website'] ?? 'https://inventra.go.id') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Tampilan & Background -->
                    <div x-show="activeTab === 'appearance'" style="display: none;" class="space-y-6">
                        <div class="border-b border-slate-100 pb-5">
                            <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-600">wallpaper</span>
                                Kustomisasi Background Layar Login
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Ubah latar belakang (wallpaper) pada halaman login sistem agar sesuai identitas visual instansi atau perusahaan.</p>
                        </div>

                        <!-- Hidden input to track remove background -->
                        <input type="hidden" name="remove_login_bg" :value="removeBg ? 1 : 0">

                        <!-- Interactive Wallpaper Section -->
                        <div class="space-y-6">
                            <!-- Live Preview Frame Container -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pratinjau Layar Login</label>
                                    <div>
                                        <template x-if="removeBg">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Akan Direset ke Default Mesh Gradient
                                            </span>
                                        </template>
                                        <template x-if="!removeBg && fileChosen">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                Gambar Baru Dipilih (Simpan untuk Menerapkan)
                                            </span>
                                        </template>
                                        <template x-if="!removeBg && !fileChosen && bgPreview">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Background Kustom Aktif
                                            </span>
                                        </template>
                                        <template x-if="!removeBg && !fileChosen && !bgPreview">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Default Animated Mesh Gradient
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Visual Preview Mockup Screen -->
                                <div class="relative w-full h-72 sm:h-80 md:h-96 rounded-2xl overflow-hidden border border-slate-200 shadow-inner bg-slate-900 flex items-center justify-center">
                                    
                                    <!-- Background Layer -->
                                    <template x-if="bgPreview && !removeBg">
                                        <div class="absolute inset-0 w-full h-full">
                                            <img :src="bgPreview" alt="Login Background Preview" class="w-full h-full object-cover object-center filter brightness-95">
                                            <div class="absolute inset-0 bg-slate-900/35 backdrop-blur-[1.5px]"></div>
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-900/20 to-slate-950/40"></div>
                                            <div class="absolute inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] bg-[size:32px_32px] opacity-[0.06]"></div>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!bgPreview || removeBg">
                                        <div class="absolute inset-0 w-full h-full bg-slate-50 overflow-hidden">
                                            <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-indigo-200/80 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                                            <div class="absolute top-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-200/80 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                                            <div class="absolute -bottom-[10%] left-[20%] w-[50%] h-[50%] bg-sky-200/80 rounded-full mix-blend-multiply filter blur-2xl opacity-70"></div>
                                            <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] bg-[size:24px_24px] opacity-[0.2]"></div>
                                        </div>
                                    </template>

                                    <!-- Mini Mockup Card overlay to represent login card -->
                                    <div class="relative z-10 w-full max-w-xs sm:max-w-sm p-4 sm:p-5 mx-4 bg-white/85 backdrop-blur-xl rounded-2xl border border-white/60 shadow-2xl pointer-events-none scale-95 transition-transform">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                                            <span class="text-[10px] font-mono text-slate-400 ml-2">inventra.go.id/login</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-500/30">
                                                IV
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-slate-800">INVENTRA Enterprise Portal</div>
                                                <div class="text-[10px] text-slate-500">Sistem Manajemen Logistik</div>
                                            </div>
                                        </div>
                                        <div class="mt-3 space-y-1.5">
                                            <div class="h-6 w-full bg-slate-100 rounded-lg border border-slate-200 flex items-center px-2 text-[10px] text-slate-400">NIP / Email Dinas...</div>
                                            <div class="h-6 w-full bg-slate-100 rounded-lg border border-slate-200 flex items-center px-2 text-[10px] text-slate-400">••••••••</div>
                                            <div class="h-6 w-full bg-indigo-600 rounded-lg flex items-center justify-center text-[10px] text-white font-semibold shadow-sm">Masuk ke Sistem</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload & Action Controls -->
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-4">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-indigo-600 text-[18px]">add_photo_alternate</span>
                                            Unggah File Wallpaper Baru
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Pilih foto atau ilustrasi berkualitas tinggi untuk wallpaper latar login.
                                        </p>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <input 
                                            type="file" 
                                            name="login_bg" 
                                            id="loginBgInput" 
                                            x-ref="bgInput" 
                                            @change="handleBgChange($event)" 
                                            accept="image/png, image/jpeg, image/jpg, image/webp" 
                                            class="hidden"
                                        >
                                        
                                        <button 
                                            type="button" 
                                            @click="$refs.bgInput.click()" 
                                            class="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">upload</span>
                                            <span x-text="bgPreview && !removeBg ? 'Ganti Wallpaper' : 'Pilih File Gambar'">Pilih File Gambar</span>
                                        </button>

                                        <button 
                                            type="button" 
                                            x-show="bgPreview || (!removeBg && '{{ isset($settings['login_bg']) && $settings['login_bg'] ? 1 : 0 }}' == '1')" 
                                            @click="resetBg()" 
                                            class="px-3.5 py-2 bg-white text-rose-600 hover:bg-rose-50 border border-rose-200 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5 cursor-pointer"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                            Reset ke Default
                                        </button>

                                        <a 
                                            href="{{ route('login') }}" 
                                            target="_blank" 
                                            class="px-3.5 py-2 bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 text-xs font-semibold rounded-xl transition-all shadow-sm flex items-center gap-1.5"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                            Buka Halaman Login
                                        </a>
                                    </div>
                                </div>

                                @error('login_bg')
                                    <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-600 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">error</span>
                                        {{ $message }}
                                    </div>
                                @enderror

                                <!-- Recommendation Guidelines -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-200/80 text-[11px] text-slate-500">
                                    <div class="flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200/60">
                                        <span class="material-symbols-outlined text-indigo-500 text-[18px]">aspect_ratio</span>
                                        <span><strong>Rasio 16:9</strong> (1920×1080 / 2K)</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200/60">
                                        <span class="material-symbols-outlined text-indigo-500 text-[18px]">image</span>
                                        <span><strong>JPG, PNG, WebP</strong> (Maks. 5MB)</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200/60">
                                        <span class="material-symbols-outlined text-indigo-500 text-[18px]">auto_awesome</span>
                                        <span><strong>Auto Contrast & Overlay</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Preferensi Sistem -->
                    <div x-show="activeTab === 'preferences'" style="display: none;" class="space-y-6">
                        <div class="border-b border-slate-100 pb-5">
                            <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-600">tune</span>
                                Preferensi Regional & Tampilan
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Konfigurasi zona waktu, format tanggal, dan batas peringatan stok otomatis.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Zona Waktu (Timezone)</label>
                                <select name="app_timezone" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Asia/Jakarta" {{ (old('app_timezone', $settings['app_timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta') ? 'selected' : '' }}>WIB (Asia/Jakarta - UTC+7)</option>
                                    <option value="Asia/Makassar" {{ (old('app_timezone', $settings['app_timezone'] ?? '') == 'Asia/Makassar') ? 'selected' : '' }}>WITA (Asia/Makassar - UTC+8)</option>
                                    <option value="Asia/Jayapura" {{ (old('app_timezone', $settings['app_timezone'] ?? '') == 'Asia/Jayapura') ? 'selected' : '' }}>WIT (Asia/Jayapura - UTC+9)</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Format Tanggal Tampilan</label>
                                <select name="date_format" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="d M Y" {{ (old('date_format', $settings['date_format'] ?? 'd M Y') == 'd M Y') ? 'selected' : '' }}>01 Okt 2026 (d M Y)</option>
                                    <option value="d/m/Y" {{ (old('date_format', $settings['date_format'] ?? '') == 'd/m/Y') ? 'selected' : '' }}>01/10/2026 (d/m/Y)</option>
                                    <option value="Y-m-d" {{ (old('date_format', $settings['date_format'] ?? '') == 'Y-m-d') ? 'selected' : '' }}>2026-10-01 (Y-m-d)</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Ambang Batas Peringatan Stok Kritis (Default)</label>
                                <input type="number" name="min_stock_alert_threshold" value="{{ old('min_stock_alert_threshold', $settings['min_stock_alert_threshold'] ?? 10) }}" min="1" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="text-[11px] text-slate-400">Jika stok barang <= angka ini, sistem otomatis memicu notifikasi peringatan.</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Simbol Mata Uang Utama</label>
                                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'Rp') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Keuangan & Pajak -->
                    <div x-show="activeTab === 'finance'" style="display: none;" class="space-y-6">
                        <div class="border-b border-slate-100 pb-5">
                            <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-600">payments</span>
                                Parameter Perpajakan & POS
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Pengaturan tarif PPN standar dan opsi pembayaran kasir.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Tarif PPN Standar (%)</label>
                                <div class="relative">
                                    <input type="number" step="0.1" name="default_tax_rate" value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? 11) }}" class="w-full h-10 pl-3 pr-8 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">%</span>
                                </div>
                                <p class="text-[11px] text-slate-400">Tarif pajak yang dikenakan saat checkout di terminal POS.</p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Notifikasi Sistem -->
                    <div x-show="activeTab === 'notifications'" style="display: none;" class="space-y-6">
                        <div class="border-b border-slate-100 pb-5">
                            <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-600">notifications</span>
                                Notifikasi & Integrasi
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Konfigurasi email peringatan sistem dan integrasi logistik.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700">Email Penerima Laporan & Peringatan Sistem</label>
                                <input type="email" name="notification_email" value="{{ old('notification_email', $settings['notification_email'] ?? 'superadmin@inventra.go.id') }}" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
@endsection
