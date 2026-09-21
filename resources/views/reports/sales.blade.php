@extends('layouts.app')

@section('header_title', 'Laporan Penjualan')

@section('main_content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Laporan Penjualan</h2>
        <p class="text-sm text-slate-500">Agregasi penjualan POS bulan {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>
    <form id="filterForm" hx-get="{{ route('reports.sales') }}" hx-target="body" hx-push-url="true" class="flex items-center gap-3 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm">
        <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3 text-slate-400 text-[18px] pointer-events-none">calendar_month</span>
            <select name="month" hx-get="{{ route('reports.sales') }}" hx-target="body" hx-include="#filterForm" class="appearance-none pl-9 pr-8 py-2 bg-transparent text-sm font-medium text-slate-700 focus:outline-none cursor-pointer">
                @foreach(range(1, 12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
            <span class="material-symbols-outlined absolute right-2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
        </div>
        <div class="w-px h-6 bg-slate-200"></div>
        <div class="relative flex items-center">
            <select name="year" hx-get="{{ route('reports.sales') }}" hx-target="body" hx-include="#filterForm" class="appearance-none pl-3 pr-8 py-2 bg-transparent text-sm font-medium text-slate-700 focus:outline-none cursor-pointer">
                @foreach(range(date('Y')-2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <span class="material-symbols-outlined absolute right-2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
            <span class="material-symbols-outlined text-[24px]">payments</span>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($sales->sum('total_amount'), 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
            <span class="material-symbols-outlined text-[24px]">receipt_long</span>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase">Jumlah Transaksi</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $sales->count() }} Struk</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 font-semibold">No Struk</th>
                <th class="px-6 py-4 font-semibold">Tanggal</th>
                <th class="px-6 py-4 font-semibold">Kasir</th>
                <th class="px-6 py-4 font-semibold">Metode Bayar</th>
                <th class="px-6 py-4 font-semibold text-right">Total Transaksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($sales as $trx)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $trx->ref_number }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4">{{ $trx->user->name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">{{ $trx->payment_method ?? 'Tunai' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-slate-800">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada penjualan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection
