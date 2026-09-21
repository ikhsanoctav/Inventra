@extends('layouts.app')

@section('header_title', 'Approval Menunggu')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Approval Menunggu</h2>
            <p class="text-sm text-[#6B7280]">Daftar Purchase Order yang membutuhkan persetujuan.</p>
        </div>
        <div class="flex gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" placeholder="Cari nomor referensi..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64 text-sm bg-white shadow-sm transition-all">
            </div>
        </div>
    </div>

    @if($pos->isEmpty())
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-4xl text-indigo-500">check_circle</span>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Semua Tuntas!</h3>
        <p class="text-slate-500 max-w-sm">Tidak ada dokumen Purchase Order yang sedang menunggu persetujuan Anda saat ini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-6">
        @foreach($pos as $po)
        <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row justify-between gap-6">
                    <!-- PO Info -->
                    <div class="flex-1 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg">{{ $po->status }}</span>
                            <span class="text-sm text-slate-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                {{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}
                            </span>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 font-headline">{{ $po->ref_number }}</h3>
                            <p class="text-sm text-slate-500 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">person</span>
                                Dibuat oleh: {{ $po->user->name ?? 'Sistem' }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Order Items Summary -->
                    <div class="flex-1 bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Ringkasan Item</h4>
                        <div class="space-y-2 max-h-32 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($po->lines as $line)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-700 truncate pr-4">{{ $line->item->name ?? 'Unknown Item' }}</span>
                                <span class="font-medium text-slate-800 whitespace-nowrap">{{ $line->quantity }} {{ $line->item->unit ?? 'Unit' }}</span>
                            </div>
                            @endforeach
                            @if($po->lines->isEmpty())
                            <div class="text-sm text-slate-400 italic">Belum ada item ditambahkan.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row lg:flex-col justify-end gap-3 lg:w-48">
                        <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">check</span>
                                Setujui PO
                            </button>
                        </form>
                        
                        <a href="{{ route('transactions.po.show', $po->id) }}" class="w-full py-2.5 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
