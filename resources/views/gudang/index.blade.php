@extends('layouts.app')

@section('header_title', 'Dashboard Gudang')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 font-headline">Operasional Gudang</h2>
            <p class="text-sm text-slate-500">Pantau arus barang masuk, keluar, dan jadwal stock opname.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.inbound') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2 shadow-sm shadow-indigo-500/20">
                <span class="material-symbols-outlined text-[18px]">login</span> Inbound Baru
            </a>
            <a href="{{ route('transactions.outbound') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm shadow-emerald-500/20">
                <span class="material-symbols-outlined text-[18px]">logout</span> Outbound Baru
            </a>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">Total Kapasitas Terpakai</h3>
            <p class="text-2xl font-bold text-slate-800 relative z-10">{{ \App\Models\Item::where('type', 'barang')->sum('stock') ?? 0 }} Unit</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined">login</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">Barang Masuk (Hari Ini)</h3>
            <p class="text-2xl font-bold text-slate-800 relative z-10">{{ \App\Models\Transaction::where('type', 'Inbound')->whereDate('created_at', today())->count() }} Transaksi</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined">logout</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">Barang Keluar (Hari Ini)</h3>
            <p class="text-2xl font-bold text-slate-800 relative z-10">{{ \App\Models\Transaction::where('type', 'Outbound')->whereDate('created_at', today())->count() }} Transaksi</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined">fact_check</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">Status Stock Opname</h3>
            <p class="text-xl font-bold text-amber-600 relative z-10">Belum Terjadwal</p>
        </div>
    </div>

    <!-- Latest Transactions -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-500">history</span> Riwayat Mutasi Terakhir
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">No. Referensi</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $recentTransactions = \App\Models\Transaction::orderBy('created_at', 'desc')->take(5)->get();
                    @endphp
                    
                    @forelse($recentTransactions as $trx)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono font-medium text-slate-800">{{ $trx->ref_number }}</td>
                        <td class="px-6 py-4">
                            @if($trx->type == 'Inbound')
                                <span class="px-2 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-semibold">Inbound</span>
                            @else
                                <span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-semibold">Outbound</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $trx->transaction_date }}</td>
                        <td class="px-6 py-4">
                            @if($trx->status == 'Completed')
                                <span class="text-emerald-600 font-medium flex items-center gap-1 text-xs"><span class="material-symbols-outlined text-[14px]">check_circle</span> Selesai</span>
                            @else
                                <span class="text-amber-600 font-medium flex items-center gap-1 text-xs"><span class="material-symbols-outlined text-[14px]">pending</span> Draft</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                            Belum ada riwayat transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
