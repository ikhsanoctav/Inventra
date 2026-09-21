@extends('layouts.app')

@section('header_title', 'Kartu Stok')

@section('main_content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Kartu Stok per Item</h2>
        <p class="text-sm text-slate-500">Melihat riwayat pergerakan keluar masuk suatu barang secara spesifik.</p>
    </div>
    
    <form class="flex gap-3 w-full md:w-auto">
        <select name="item_id" class="border rounded-lg px-3 py-2 text-sm bg-white flex-1 md:w-64">
            @foreach($items as $item)
                <option value="{{ $item->id }}" {{ ($selectedItem && $selectedItem->id == $item->id) ? 'selected' : '' }}>
                    {{ $item->sku }} - {{ $item->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 whitespace-nowrap">Lihat Kartu Stok</button>
    </form>
</div>

@if($selectedItem)
<div class="mb-6 bg-indigo-50 border border-indigo-100 p-6 rounded-2xl flex items-center justify-between">
    <div>
        <h3 class="text-lg font-bold text-indigo-900">{{ $selectedItem->name }}</h3>
        <p class="text-sm text-indigo-700 font-mono">{{ $selectedItem->sku }} | Kategori: {{ $selectedItem->category->name ?? '-' }}</p>
    </div>
    <div class="text-right">
        <p class="text-xs uppercase font-bold text-indigo-500 mb-1">Stok Tersedia Saat Ini</p>
        <p class="text-3xl font-black text-indigo-700">{{ $selectedItem->stock }} <span class="text-sm font-normal">{{ $selectedItem->unit->name ?? 'Unit' }}</span></p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 font-semibold">Tanggal & Waktu</th>
                <th class="px-6 py-4 font-semibold">No Referensi</th>
                <th class="px-6 py-4 font-semibold">Tipe Transaksi</th>
                <th class="px-6 py-4 font-semibold text-center">Masuk / Keluar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($history as $log)
                @php
                    $isOut = in_array($log->transaction->type, ['Outbound', 'POS']);
                    $color = $isOut ? 'text-red-600' : 'text-emerald-600';
                    $sign = $isOut ? '-' : '+';
                    $bg = $isOut ? 'bg-red-50' : 'bg-emerald-50';
                @endphp
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-4 font-mono font-bold text-slate-600">{{ $log->transaction->ref_number }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $bg }} {{ $color }}">{{ $log->transaction->type }}</span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold {{ $color }}">
                        {{ $sign }}{{ $log->quantity }}
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada riwayat pergerakan untuk barang ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endif
@endsection
