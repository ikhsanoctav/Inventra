@extends('layouts.app')

@section('header_title', 'Transaksi Barang Keluar')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Barang Keluar (Outbound)</h2>
            <p class="text-sm text-[#6B7280]">Catat pengeluaran barang dari gudang untuk keperluan logistik.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Buat Outbound</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E5E7EB] shadow-sm mb-6 relative">
        <form action="{{ route('transactions.outbound') }}" method="GET" class="flex flex-col sm:flex-row gap-4 relative"
              hx-get="{{ route('transactions.outbound') }}"
              hx-target="#table-container"
              hx-select="#table-container"
              hx-swap="outerHTML"
              hx-trigger="input changed delay:500ms from:input[name='search'], change from:select"
              x-data="{ loading: false }"
              @htmx:before-request.camel="loading = true"
              @htmx:after-request.camel="loading = false">
            <div x-show="loading" style="display: none;" class="absolute -top-3 right-4 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Loading...
            </div>
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Referensi..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
            <div class="sm:w-48">
                <select name="status" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">Semua Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="sm:w-32">
                <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('transactions.outbound') }}" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold flex items-center transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <div id="table-container" class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-left text-sm text-[#4B5563]">
            <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                <tr>
                    <th scope="col" class="px-6 py-4">No. Referensi</th>
                    <th scope="col" class="px-6 py-4">Tanggal</th>
                    <th scope="col" class="px-6 py-4">Pembuat</th>
                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB]">
                @forelse($transactions as $outbound)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $outbound->ref_number }}</td>
                    <td class="px-6 py-4 text-[#6B7280]">{{ \Carbon\Carbon::parse($outbound->transaction_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-[#6B7280]">{{ $outbound->user->name ?? 'Sistem' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $outbound->status === 'Completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $outbound->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('transactions.outbound.show', $outbound->id) }}" class="text-[#4F46E5] hover:text-[#4338CA] font-medium text-xs">Detail</a>
                        <form action="{{ route('transactions.outbound.destroy', $outbound->id) }}" method="POST" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-5xl text-slate-300 mb-4">outbox</span>
                            <p class="text-slate-500 font-medium">Tidak ada transaksi outbound yang ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Modal Buat Outbound -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showModal" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB] z-10">
                <div class="bg-white px-6 pb-6 pt-6">
                    <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">
                        <h3 class="text-lg font-headline font-bold text-[#111827]">Buat Transaksi Outbound Baru</h3>
                        <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <form action="{{ route('transactions.outbound') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">No. Referensi <span class="text-red-500">*</span></label>
                            <input type="text" name="ref_number" value="OUT-{{ time() }}" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Status Awal <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                                <option value="Draft">Draft</option>
                                <option value="Completed">Selesai (Completed)</option>
                            </select>
                        </div>
                        
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-[#4F46E5] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#4338CA] sm:w-auto transition-colors">Buat Transaksi</button>
                            <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
