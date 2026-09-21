@extends('layouts.app')

@section('header_title', 'Master Data Barang')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Data Barang & SKU</h2>
            <p class="text-sm text-[#6B7280]">Kelola katalog barang, pantau stok, dan batas aman persediaan.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" placeholder="Cari SKU / Barang..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all shadow-sm">
            </div>
            
            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Barang</span>
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#4B5563]">
                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                    <tr>
                        <th scope="col" class="px-6 py-4">Foto</th>
                        <th scope="col" class="px-6 py-4">SKU</th>
                        <th scope="col" class="px-6 py-4">Nama Barang</th>
                        <th scope="col" class="px-6 py-4">Kategori</th>
                        <th scope="col" class="px-6 py-4 text-right">Stok</th>
                        <th scope="col" class="px-6 py-4 text-right">Batas Aman (Buffer)</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse([] as $item)
                    <!-- Loop goes here -->
                    @empty
                    <!-- Empty State Placeholder -->
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">category</span>
                                </div>
                                <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada data barang</h3>
                                <p class="text-xs text-[#6B7280]">Data barang yang Anda tambahkan akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Placeholder -->
        <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
            <span>Total: 0 data</span>
        </div>
    </div>

</div>
@endsection