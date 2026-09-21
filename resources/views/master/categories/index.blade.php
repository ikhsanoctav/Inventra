@extends('layouts.app')

@section('header_title', 'Kategori Barang')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-[200px] flex-1">
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Kategori Barang</h2>
            <p class="text-sm text-[#6B7280]">Kelola dan pantau informasi kategori barang dalam sistem logistik.</p>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-600/20 transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Data</span>
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('master.categories') }}" 
          hx-get="{{ route('master.categories') }}"
          hx-target="#table-container"
          hx-select="#table-container"
          hx-swap="outerHTML"
          hx-trigger="input changed delay:500ms from:input[name='search'], change from:select"
          class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-sm flex flex-col sm:flex-row flex-wrap gap-4 items-end relative"
          x-data="{ loading: false }"
          @htmx:before-request.camel="loading = true"
          @htmx:after-request.camel="loading = false">
          
        <div x-show="loading" style="display: none;" class="absolute -top-3 right-4 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold flex items-center gap-1 shadow-sm">
            <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Loading...
        </div>

        <div class="flex-1 min-w-[200px] w-full sm:w-auto">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Kategori</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
            </div>
        </div>

        <div class="w-full sm:w-32">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilkan</label>
            <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
        </div>
    </form>

    <!-- Data Table -->
    <div id="table-container" class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#4B5563]">
                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                    <tr>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Kategori</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Deskripsi Kategori</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Jumlah Item</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-[#111827]">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-[#6B7280]">{{ $category->description ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $category->items_count }} SKU
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-[#4F46E5] hover:text-[#4338CA] mr-3">Edit</button>
                            <button class="text-red-500 hover:text-red-700">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <!-- Empty State Placeholder -->
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">inventory_2</span>
                                </div>
                                <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada kategori barang</h3>
                                <p class="text-xs text-[#6B7280]">Data yang Anda tambahkan akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-[#E5E7EB] text-xs text-[#6B7280]">
            {{ $categories->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>


    <!-- Add Data Modal Overlay -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="showModal = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB] relative z-10">
                
                <div class="bg-white px-6 pb-6 pt-6">
                    <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">
                        <h3 class="text-lg font-headline font-bold text-[#111827]" id="modal-title">Tambah Kategori Barang</h3>
                        <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <form action="{{ route('master.categories') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Kategori</label>
                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" name="name" required placeholder="Masukkan nama kategori...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Deskripsi Kategori</label>
                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" name="description" placeholder="Masukkan deskripsi kategori...">
                        </div>
                        
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-[#4F46E5] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#4338CA] sm:w-auto transition-colors">
                                Simpan Data
                            </button>
                            <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
