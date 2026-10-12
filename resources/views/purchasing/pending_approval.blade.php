@extends('layouts.app')

@section('header_title', 'PO Menunggu Persetujuan')

@section('main_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Antrean Otorisasi Dokumen</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-headline tracking-tight">Purchase Order Menunggu Persetujuan</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar permohonan PO yang diajukan tim Purchasing dan membutuhkan otorisasi Manager.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-right">
                <div class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Total Nilai Tertunda</div>
                <div class="text-lg font-black text-amber-900 font-headline">Rp {{ number_format($totalPendingAmount ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form action="{{ route('purchasing.pending_approval') }}" method="GET" class="flex gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. PO, nama pembuat, atau catatan..." 
                       class="w-full h-11 pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
            </div>
            <button type="submit" class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                <span>Cari</span>
            </button>
            @if(request('search'))
            <a href="{{ route('purchasing.pending_approval') }}" class="h-11 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold flex items-center transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Pending PO List -->
    @if($pos->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-12 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mb-4 text-emerald-600">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>
        <h3 class="text-lg font-extrabold text-slate-900 font-headline">Semua Antrean Selesai!</h3>
        <p class="text-xs text-slate-500 max-w-md mt-1">Tidak ada dokumen Purchase Order yang sedang menunggu persetujuan saat ini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-4">
        @foreach($pos as $po)
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden hover:border-blue-300 transition-all p-6">
            <div class="flex flex-col lg:flex-row justify-between gap-6 items-start lg:items-center">
                
                <!-- Info Section -->
                <div class="flex-1 space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-full">
                            Pending Approval
                        </span>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">calendar_today</span>
                            <span>{{ \Carbon\Carbon::parse($po->transaction_date)->format('d F Y') }}</span>
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-black text-slate-900 font-headline font-mono">{{ $po->ref_number }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Diajukan oleh <strong class="text-slate-800">{{ $po->user->name ?? 'Staff Purchasing' }}</strong>
                            @if($po->notes) • <span class="italic text-slate-600">{{ $po->notes }}</span>@endif
                        </p>
                    </div>

                    <div class="flex items-center gap-6 pt-1 text-xs">
                        <div>
                            <span class="text-slate-400">Jumlah Item:</span>
                            <span class="font-bold text-slate-800 ml-1">{{ $po->lines->count() }} Item ({{ $po->lines->sum('quantity') }} unit)</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Total Anggaran:</span>
                            <span class="font-black text-blue-700 text-sm ml-1">Rp {{ number_format($po->total_amount ?: $po->lines->sum(fn($l) => $l->quantity * $l->unit_price), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Section -->
                <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto lg:w-auto justify-end">
                    @if(auth()->user()->hasRole(['manager', 'super_admin']))
                    <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST" onsubmit="return confirm('Setujui Purchase Order {{ $po->ref_number }}?');">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5 active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            <span>Setujui</span>
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('transactions.po.show', $po->id) }}" class="px-4 py-2.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition-colors flex items-center gap-1.5 shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Periksa Rincian</span>
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
