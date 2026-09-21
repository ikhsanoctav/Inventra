@extends('layouts.app')

@section('header_title', 'Purchase Order (PO)')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 font-headline">Purchase Order</h2>
            <p class="text-sm text-slate-500">Kelola pemesanan barang ke supplier.</p>
        </div>
        <button @click="showModal = true" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Buat PO Baru
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E5E7EB] shadow-sm relative">
        <form action="{{ route('transactions.po.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 relative"
              hx-get="{{ route('transactions.po.index') }}"
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
                    <option value="Pending Approval" {{ request('status') == 'Pending Approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('transactions.po.index') }}" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold flex items-center transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <div id="table-container" class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">No. PO</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Pembuat</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pos as $po)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono font-medium text-slate-800">{{ $po->ref_number }}</td>
                        <td class="px-6 py-4">{{ $po->transaction_date }}</td>
                        <td class="px-6 py-4">{{ $po->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4">
                            @if($po->status === 'Draft')
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Draft</span>
                            @elseif($po->status === 'Pending Approval')
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">Pending Approval</span>
                            @elseif($po->status === 'Approved')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">Approved</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('transactions.po.show', $po->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            Belum ada dokumen Purchase Order.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Buat PO -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm z-0" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="showModal" x-transition.scale.origin.bottom class="relative z-10 inline-block w-full max-w-md px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800">Buat Purchase Order Baru</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                </div>
                <form action="{{ route('transactions.po') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nomor PO</label>
                            <input type="text" name="ref_number" value="PO-{{ date('Ymd-His') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50" readonly required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pesanan</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status Awal</label>
                            <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                <option value="Draft">Draft (Dalam Penyusunan)</option>
                                <option value="Pending Approval">Kirim untuk Approval</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-slate-600 border border-slate-300 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Buat & Lanjut</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
