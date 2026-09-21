@extends('layouts.app')

@section('header_title', 'Stok Menipis')

@section('main_content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 font-headline text-red-600 flex items-center gap-2">
        <span class="material-symbols-outlined text-[28px]">warning</span> Stok Menipis
    </h2>
    <p class="text-sm text-slate-500">Daftar barang dengan kuantitas stok di bawah atau sama dengan batas aman (<= 10).</p>
</div>

<div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-red-50 text-red-700">
            <tr>
                <th class="px-6 py-4 font-semibold">SKU</th>
                <th class="px-6 py-4 font-semibold">Nama Barang</th>
                <th class="px-6 py-4 font-semibold">Kategori</th>
                <th class="px-6 py-4 font-semibold text-center">Sisa Stok</th>
                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($items as $item)
                <tr class="hover:bg-red-50/30">
                    <td class="px-6 py-4 font-mono text-slate-500">{{ $item->sku }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->category->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full font-bold text-xs">{{ $item->stock }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('transactions.po.create') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs border border-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">Buat PO</a>
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-emerald-600">
                    <span class="material-symbols-outlined text-4xl mb-3 opacity-50 block">check_circle</span>
                    Aman! Semua stok barang di atas ambang batas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection
