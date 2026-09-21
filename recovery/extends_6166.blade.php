@extends('layouts.app')

@section('header_title', 'Pengaturan Sistem')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Pengaturan Sistem</h2>
            <p class="text-sm text-[#6B7280]">Kelola preferensi utama dan konfigurasi aplikasi Anda.</p>
        </div>
        <button type="submit" form="settings-form" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all">
            <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
        </button>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden p-2">
                <nav class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 bg-indigo-50 text-[#4F46E5] rounded-xl font-semibold text-sm transition-colors">
                        <span class="material-symbols-outlined text-[20px]">domain</span> Identitas Perusahaan
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-[#4B5563] hover:bg-gray-50 rounded-xl font-semibold text-sm transition-colors">
                        <span class="material-symbols-outlined text-[20px]">notifications</span> Notifikasi
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-[#4B5563] hover:bg-gray-50 rounded-xl font-semibold text-sm transition-colors">
                        <span class="material-symbols-outlined text-[20px]">api</span> API & Integrasi
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Form Settings -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
                <div class="p-5 border-b border-[#E5E7EB]">
                    <h3 class="font-semibold text-[#111827]">Identitas Perusahaan</h3>
                    <p class="text-xs text-gray-500 mt-1">Informasi ini akan ditampilkan pada laporan dan dokumen transaksi cetak.</p>
                </div>
                <div class="p-6">
                    <form id="settings-form" action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Perusahaan</label>
                                <input type="text" value="PT Logistik Nusantara" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Email Kontak</label>
                                <input type="email" value="admin@logistik.com" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Telepon</label>
                                <input type="text" value="021-12345678" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
                            </div>
                            
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Alamat Lengkap</label>
                                <textarea rows="3" class="w-full px-3 py-2 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">Jl. Merdeka No.45, Jakarta Pusat</textarea>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Mata Uang Default</label>
                                <select class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
                                    <option value="IDR">Rupiah (IDR)</option>
                                    <option value="USD">US Dollar (USD)</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Timezone</label>
                                <select class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
                                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection