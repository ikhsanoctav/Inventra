@extends('layouts.app')

@section('header_title', 'Laporan Laba/Rugi')

@section('main_content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Laporan Laba/Rugi (Profit & Loss)</h2>
        <p class="text-sm text-slate-500">Estimasi Gross Profit periode {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>
    
    <form id="filterForm" class="flex items-center gap-3 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm">
        <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3 text-slate-400 text-[18px] pointer-events-none">calendar_month</span>
            <select name="month" onchange="document.getElementById('filterForm').submit()" class="appearance-none pl-9 pr-8 py-2 bg-transparent text-sm font-medium text-slate-700 focus:outline-none cursor-pointer">
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
            <select name="year" onchange="document.getElementById('filterForm').submit()" class="appearance-none pl-3 pr-8 py-2 bg-transparent text-sm font-medium text-slate-700 focus:outline-none cursor-pointer">
                @foreach(range(date('Y')-2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <span class="material-symbols-outlined absolute right-2 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
        </div>
    </form>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Ringkasan P&L</h3>
        </div>
        
        <div class="p-6">
            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                <span class="text-slate-600 font-medium text-lg">Total Pendapatan (Revenue)</span>
                <span class="text-emerald-600 font-bold text-xl">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                <span class="text-slate-600 font-medium text-lg">Total Beban Pokok Pembelian (COGS)</span>
                <span class="text-red-500 font-bold text-xl">- Rp {{ number_format($cogs, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between items-center pt-6 mt-2">
                <span class="text-slate-800 font-bold text-2xl">Laba Kotor (Gross Profit)</span>
                <span class="text-2xl font-bold {{ $grossProfit >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                    Rp {{ number_format($grossProfit, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="p-4 bg-amber-50 rounded-xl text-sm text-amber-800 border border-amber-200">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-600">info</span>
            <div>
                <p class="font-bold mb-1">Catatan Estimasi HPP</p>
                <p>Dalam laporan laba kotor yang disederhanakan ini, Beban Pokok / Cost of Goods Sold (COGS) dihitung berdasarkan total nominal Purchase Order (PO) yang disetujui pada bulan berjalan, bukan dari metode rata-rata (Moving Average) atau FIFO barang yang terjual sesungguhnya. Fitur akuntansi tingkat lanjut akan hadir di iterasi berikutnya.</p>
            </div>
        </div>
    </div>
</div>
@endsection
