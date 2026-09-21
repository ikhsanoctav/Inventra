@extends('layouts.app')

@section('header_title', 'Performa Vendor')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Evaluasi Performa Vendor</h2>
            <p class="text-sm text-[#6B7280]">Analitik dan penilaian metrik kualitas pelayanan supplier.</p>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">download</span> Export Laporan
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Active Vendor</p>
                <h4 class="text-2xl font-bold text-slate-800">{{ $suppliers->count() }}</h4>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">speed</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Rata-rata Ketepatan Waktu</p>
                <h4 class="text-2xl font-bold text-slate-800">94.2%</h4>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">star</span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Rata-rata Rating Kualitas</p>
                <h4 class="text-2xl font-bold text-slate-800">4.8 / 5.0</h4>
            </div>
        </div>
    </div>

    <!-- Vendor Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($suppliers as $supplier)
        <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col">
            <div class="p-6 border-b border-slate-50 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 font-headline leading-tight">{{ $supplier->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">person</span>
                        {{ $supplier->contact_person ?? 'Tanpa Kontak' }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-500 font-bold">
                    {{ substr($supplier->name, 0, 1) }}
                </div>
            </div>
            
            <div class="p-6 flex-1 flex flex-col justify-center space-y-4">
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ketepatan Waktu</span>
                        <span class="text-sm font-bold text-emerald-600">{{ rand(85, 99) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ rand(85, 99) }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kualitas Barang</span>
                        <span class="text-sm font-bold text-blue-600">{{ rand(88, 100) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ rand(88, 100) }}%"></div>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center text-sm">
                <span class="text-slate-500"><span class="font-bold text-slate-700">{{ $supplier->items_count }}</span> Varian Item</span>
                <button class="text-indigo-600 font-semibold hover:text-indigo-700">Lihat Detail &rarr;</button>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-[2rem] border border-slate-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
            <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">storefront</span>
            <p class="text-slate-500 font-medium">Belum ada data supplier yang terdaftar.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
