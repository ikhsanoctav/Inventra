@extends('layouts.app')

@section('header_title', 'Dashboard Purchasing')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 font-headline">Portal Pengadaan</h2>
            <p class="text-sm text-slate-500">Pantau performa supplier, Purchase Order, dan kebutuhan restock barang.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2 shadow-sm shadow-indigo-500/20">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat PO Baru
            </button>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">local_shipping</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">Supplier Aktif</h3>
            <p class="text-2xl font-bold text-slate-800 relative z-10">{{ \App\Models\Supplier::count() ?? 0 }} Vendor</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">PO Menunggu Persetujuan</h3>
            <p class="text-2xl font-bold text-slate-800 relative z-10">0 Dokumen</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full opacity-50"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
                    <span class="material-symbols-outlined">warning</span>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1 relative z-10">Butuh Restock Segera</h3>
            <p class="text-2xl font-bold text-slate-800 relative z-10">{{ \App\Models\Item::where('stock', '<=', \Illuminate\Support\Facades\DB::raw('min_stock'))->where('type', 'barang')->count() ?? 0 }} SKU</p>
        </div>
    </div>

    <!-- Need Restock List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500">notifications_active</span> Rekomendasi Pengadaan (Stok Menipis)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Nama Barang</th>
                        <th class="px-6 py-3 text-right">Stok Tersisa</th>
                        <th class="px-6 py-3 text-right">Batas Aman</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $lowStockItems = \App\Models\Item::where('stock', '<=', \Illuminate\Support\Facades\DB::raw('min_stock'))->where('type', 'barang')->take(5)->get();
                    @endphp
                    
                    @forelse($lowStockItems as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-xs">{{ $item->sku }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-right text-red-600 font-bold">{{ $item->stock }}</td>
                        <td class="px-6 py-4 text-right text-slate-500">{{ $item->min_stock }}</td>
                        <td class="px-6 py-4 text-center">
                            <button class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded hover:bg-indigo-100">Buat PO</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Semua stok barang dalam kondisi aman.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
