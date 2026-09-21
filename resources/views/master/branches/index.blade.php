@extends('layouts.app')

@section('header_title', 'Data Cabang / Outlet')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-[200px] flex-1">
            <h2 class="font-headline text-2xl font-bold text-[#111827] leading-tight">Data Cabang / Outlet</h2>
            <p class="text-sm text-[#6B7280] mt-1">Kelola dan pantau informasi cabang toko dalam sistem.</p>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Cabang</span>
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('master.branches') }}" 
          hx-get="{{ route('master.branches') }}"
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
            <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Cabang</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kontak..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
            </div>
        </div>

        <div class="w-full sm:w-48">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Cabang</label>
            <select name="status" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
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
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Kode</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Cabang</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Manager</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Kontak</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($branches as $branch)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-[#6B7280] font-bold">{{ $branch->code ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-[#111827]">{{ $branch->name }}</td>
                        <td class="px-6 py-4 text-[#6B7280]">{{ $branch->manager_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-[#6B7280]">
                            {{ $branch->phone ?? '-' }}<br>
                            <span class="text-xs">{{ $branch->email ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $branch->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $branch->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('master.branches.destroy', $branch->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus cabang ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">store</span>
                                </div>
                                <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada data cabang</h3>
                                <p class="text-xs text-[#6B7280]">Cabang yang Anda tambahkan akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-[#E5E7EB] text-xs text-[#6B7280]">
            {{ $branches->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    </div>
</div>
@endsection
