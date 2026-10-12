@extends('layouts.app')

@section('header_title', 'Performa & Evaluasi Vendor')

@section('main_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>Vendor Quality &amp; SLA Analytics</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-headline tracking-tight">Evaluasi Performa Rekanan Vendor</h2>
            <p class="text-sm text-slate-500 mt-1">Analitik keterandalan, riwayat pemesanan, dan penilaian kualitas pelayanan supplier.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('master.suppliers') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_business</span>
                <span>Kelola Master Vendor</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Total Vendor Rekanan</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 font-headline">{{ $totalVendors ?? count($suppliers) }} <span class="text-xs font-normal text-slate-500">Perusahaan</span></div>
            <p class="text-xs text-slate-400 mt-1">Terdaftar dalam database e-Procurement</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Total Akumulasi Pembelian</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 font-headline">Rp {{ number_format($totalSpendAll ?? 0, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-400 mt-1">Realisasi PO terselesaikan</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Katalog Item Terpasok</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">category</span>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 font-headline">{{ $totalVariants ?? 0 }} <span class="text-xs font-normal text-slate-500">Varian SKU</span></div>
            <p class="text-xs text-slate-400 mt-1">Terasosiasi dengan supplier</p>
        </div>
    </div>

    <!-- Search Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form action="{{ route('purchasing.performance') }}" method="GET" class="flex gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vendor, kontak person, email, atau no telepon..." 
                       class="w-full h-11 pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
            </div>
            <button type="submit" class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                <span>Cari</span>
            </button>
            @if(request('search'))
            <a href="{{ route('purchasing.performance') }}" class="h-11 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold flex items-center transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Vendor Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($suppliers as $supplier)
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden hover:border-blue-300 transition-all flex flex-col justify-between">
            <div>
                <!-- Top Header Box -->
                <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-4 bg-slate-50/40">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold font-headline text-base shrink-0 shadow-sm shadow-blue-500/20">
                            {{ strtoupper(substr($supplier->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 font-headline leading-snug">{{ $supplier->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1 font-medium">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">person</span>
                                <span>{{ $supplier->contact_person ?? 'Kontak Person Tidak Ada' }}</span>
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $supplier->status === 'inactive' ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                        {{ $supplier->status === 'inactive' ? 'Nonaktif' : 'Vendor Aktif' }}
                    </span>
                </div>

                <!-- Metrics Body -->
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60">
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block mb-0.5">Total Belanja</span>
                            <span class="font-black text-slate-900 text-sm">Rp {{ number_format($supplier->total_spend ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60">
                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block mb-0.5">Riwayat PO</span>
                            <span class="font-black text-blue-700 text-sm">{{ $supplier->order_count ?? 0 }} Transaksi</span>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">mail</span>
                            <span class="truncate">{{ $supplier->email ?? 'Email belum diatur' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                            <span>{{ $supplier->phone ?? 'Telepon belum diatur' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400 mt-0.5">location_on</span>
                            <span class="line-clamp-2 text-slate-500">{{ $supplier->address ?? 'Alamat belum diatur' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Bar -->
            <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex justify-between items-center text-xs">
                <span class="font-bold text-slate-700">{{ $supplier->items_count ?? 0 }} Varian SKU Terhubung</span>
                <a href="{{ route('transactions.po.create') }}" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                    <span>Terbitkan PO</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-3xl border border-slate-200 shadow-xs p-12 flex flex-col items-center justify-center text-center">
            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">storefront</span>
            <p class="font-bold text-slate-700">Belum ada vendor terdaftar</p>
            <p class="text-xs text-slate-400 mt-0.5">Silakan tambahkan data rekanan di Master Supplier.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
