@extends('layouts.app')

@section('header_title', 'Mutasi Antar Gudang')

@section('main_content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Mutasi Barang</h2>
        <p class="text-sm text-slate-500">Daftar transaksi transfer stok antar gudang.</p>
    </div>
    <a href="{{ route('transactions.transfer.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30 flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px]">add</span> Buat Mutasi Baru
    </a>
</div>

<!-- Filter Bar -->
<div class="bg-white p-4 rounded-2xl border border-[#E5E7EB] shadow-sm mb-6 relative">
    <form action="{{ route('transactions.transfer') }}" method="GET" class="flex flex-col sm:flex-row gap-4 relative"
          hx-get="{{ route('transactions.transfer') }}"
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
                <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
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
            <a href="{{ route('transactions.transfer') }}" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold flex items-center transition-colors">
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

<div id="table-container" class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 font-semibold">No. Referensi</th>
                <th class="px-6 py-4 font-semibold">Tanggal</th>
                <th class="px-6 py-4 font-semibold">Total Item</th>
                <th class="px-6 py-4 font-semibold">Status</th>
                <th class="px-6 py-4 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($transfers as $trx)
            <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $trx->ref_number }}</td>
                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                <td class="px-6 py-4">{{ $trx->lines->count() ?? 0 }} Jenis</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $trx->status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $trx->status }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('transactions.transfer.show', $trx->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-5xl text-slate-300 mb-4">local_shipping</span>
                        <p class="text-slate-500 font-medium">Belum ada transaksi mutasi.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
    <div class="p-4 border-t border-slate-100">
        {{ $transfers->links() }}
    </div>
</div>
@endsection
