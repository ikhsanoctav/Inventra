@extends('layouts.app')

@section('header_title', 'Pengaturan Sistem')

@section('main_content')
<div class="space-y-6">
    <form action="{{ route('system.settings') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="font-headline text-2xl font-bold text-[#111827]">Pengaturan Sistem</h2>
                <p class="text-sm text-[#6B7280]">Konfigurasi parameter dan opsi dasar sistem logistik.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
                </button>
            </div>
        </div>


        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">
            
            <!-- Sidebar Navigation for Settings -->
            <div class="lg:col-span-3 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 px-2">Kategori Pengaturan</div>
                
                <a href="{{ route('profile.index') }}" class="block px-4 py-3 bg-white text-indigo-700 font-bold rounded-2xl shadow-sm border border-slate-100 transition-all flex items-center gap-3 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600"></div>
                    <span class="material-symbols-outlined text-[20px] text-indigo-600">person</span>
                    Profil Pengguna
                </a>
                <a href="javascript:void(0)" class="block px-4 py-3 text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm font-medium rounded-2xl transition-all flex items-center gap-3 border border-transparent hover:border-slate-100">
                    <span class="material-symbols-outlined text-[20px]">tune</span>
                    Preferensi Sistem
                </a>
                <a href="javascript:void(0)" class="block px-4 py-3 text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm font-medium rounded-2xl transition-all flex items-center gap-3 border border-transparent hover:border-slate-100">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                    Keuangan & Pajak
                </a>
                <a href="javascript:void(0)" class="block px-4 py-3 text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm font-medium rounded-2xl transition-all flex items-center gap-3 border border-transparent hover:border-slate-100">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    Notifikasi
                </a>
            </div>
            
            <!-- Main Form Area -->
            <div class="lg:col-span-9">
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden p-6 sm:p-10">
                    <div class="mb-8 border-b border-slate-100 pb-6">
                        <h3 class="text-xl font-bold text-slate-800 font-headline flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined">apartment</span>
                            </span>
                            Profil Perusahaan
                        </h3>
                        <p class="text-sm text-slate-500 mt-2 ml-13">Informasi utama mengenai entitas bisnis Anda.</p>
                    </div>
                    
                    <div class="space-y-8">
                        <!-- Logo Upload -->
                        <div class="flex flex-col sm:flex-row gap-6 items-start">
                            <div class="w-28 h-28 rounded-[1.5rem] bg-slate-50 border-2 border-dashed border-slate-300 flex items-center justify-center text-slate-400 shrink-0 relative overflow-hidden group hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors cursor-pointer">
                                @if(isset($settings['app_logo']) && $settings['app_logo'])
                                    <img src="{{ Storage::url($settings['app_logo']) }}" alt="Logo" class="w-full h-full object-contain p-2">
                                @else
                                    <span class="material-symbols-outlined text-4xl group-hover:text-indigo-500 transition-colors">add_photo_alternate</span>
                                @endif
                                <input type="file" name="app_logo" accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                            <div class="flex-1 pt-2">
                                <h4 class="text-sm font-bold text-slate-800 mb-1">Logo Perusahaan</h4>
                                <p class="text-sm text-slate-500 mb-4 max-w-md">
                                    Ini akan ditampilkan pada sidebar, struk, invoice, dan laporan. 
                                    <br><br>
                                    <strong>Ketentuan Optimal Logo:</strong><br>
                                    • Rasio disarankan: <strong>1:1 (contoh: 512x512px)</strong> untuk ikon, atau <strong>4:1 (contoh: 800x200px)</strong> untuk logo teks (landscape).<br>
                                    • Format yang direkomendasikan adalah <strong>PNG dengan background transparan</strong>.<br>
                                    • Ukuran file maksimal <strong>2MB</strong>.
                                </p>
                                @error('app_logo')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-sm font-bold text-slate-700">Nama Perusahaan <span class="text-red-500">*</span></label>
                                <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'PT Logistik Nusantara') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                @error('company_name')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700">Nomor Telepon</label>
                                <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '+62 812-3456-7890') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                @error('company_phone')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700">Email Utama</label>
                                <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? 'admin@logistik.com') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                @error('company_email')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                            
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-sm font-bold text-slate-700">Alamat Lengkap</label>
                                <textarea name="company_address" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors resize-none">{{ old('company_address', $settings['company_address'] ?? 'Jl. Jendral Sudirman No. 45, Jakarta Pusat, DKI Jakarta 10210') }}</textarea>
                                @error('company_address')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700">NPWP Perusahaan</label>
                                <input type="text" name="company_npwp" value="{{ old('company_npwp', $settings['company_npwp'] ?? '01.234.567.8-901.000') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                @error('company_npwp')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700">Website</label>
                                <input type="url" name="company_website" value="{{ old('company_website', $settings['company_website'] ?? 'https://logistiknusantara.co.id') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-colors">
                                @error('company_website')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
