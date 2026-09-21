@extends('layouts.app')

@section('header_title', 'Valuasi Persediaan')

@section('main_content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Valuasi Persediaan</h2>
        <p class="text-sm text-slate-500">Total nilai estimasi dari seluruh stok barang yang tersedia di gudang.</p>
    </div>
    
    <div class="bg-indigo-50 border border-indigo-100 text-indigo-800 px-6 py-3 rounded-xl flex flex-col justify-center shadow-sm text-right">
        <span class="text-xs uppercase font-bold text-indigo-500 mb-1">Total Nilai Persediaan</span>
        <span class="text-2xl font-black">Rp {{ number_format($totalValuation, 0, ',', '.') }}</span>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 font-semibold">SKU</th>
                <th class="px-6 py-4 font-semibold">Nama Barang</th>
                <th class="px-6 py-4 font-semibold text-center">Stok Saat Ini</th>
                <th class="px-6 py-4 font-semibold text-right">Estimasi HPP (Satuan)</th>
                <th class="px-6 py-4 font-semibold text-right">Total Valuasi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($items as $item)
                @php
                    $estimasiHpp = $item->price * 0.7; // Asumsi 30% margin sesuai controller
                    $valuasiItem = $item->stock * $estimasiHpp;
                @endphp
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-mono text-slate-500">{{ $item->sku }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold {{ $item->stock <= 10 ? 'text-red-600' : 'text-slate-700' }}">{{ $item->stock }}</span> 
                        <span class="text-xs text-slate-500">{{ $item->unit->name ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-slate-500">Rp {{ number_format($estimasiHpp, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right font-bold text-slate-800">Rp {{ number_format($valuasiItem, 0, ',', '.') }}</td>
                </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Data barang kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection
