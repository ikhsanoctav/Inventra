@extends('layouts.app')

@section('header_title', 'Master Data Kategori')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Data Kategori</h2>
            <p class="text-sm text-[#6B7280]">Kelola pengelompokkan jenis barang untuk mempermudah pencarian.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" placeholder="Cari kategori..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all shadow-sm">
            </div>
            
            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Kategori</span>
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#4B5563]">
                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama Kategori</th>
                        <th scope="col" class="px-6 py-4">Deskripsi Kategori</th>
                        <th scope="col" class="px-6 py-4 text-center">Jumlah Item</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse([] as $category)
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">category</span>
                                </div>
                                <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada kategori</h3>
                                <p class="text-xs text-[#6B7280]">Kategori yang Anda tambahkan akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection