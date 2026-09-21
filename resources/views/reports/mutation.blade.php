@extends('layouts.app')

@section('header_title', 'Laporan Mutasi Barang')

@section('main_content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Laporan Mutasi Barang</h2>
        <p class="text-sm text-slate-500">Pergerakan keluar-masuk barang pada bulan {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
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

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 font-semibold">Tipe & No Ref</th>
                <th class="px-6 py-4 font-semibold">Tanggal</th>
                <th class="px-6 py-4 font-semibold">Barang</th>
                <th class="px-6 py-4 font-semibold text-center">Qty Mutasi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($transactions as $line)
                @php
                    $isOut = in_array($line->transaction->type, ['Outbound', 'POS']);
                    $color = $isOut ? 'text-red-600' : 'text-emerald-600';
                    $sign = $isOut ? '-' : '+';
                @endphp
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $line->transaction->type }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $line->transaction->ref_number }}</div>
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($line->transaction->transaction_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $line->item->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $line->item->sku }}</div>
                    </td>
                    <td class="px-6 py-4 text-center font-bold {{ $color }}">
                        {{ $sign }}{{ $line->quantity }}
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-slate-500">Tidak ada pergerakan barang pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection
