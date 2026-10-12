@extends('layouts.app')

@section('header_title', 'Riwayat Purchase Order')

@section('main_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>Arsip &amp; Riwayat Pengadaan</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-headline tracking-tight">Riwayat Pemesanan (PO)</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar arsip seluruh Purchase Order yang telah disetujui, selesai diterima, atau ditolak.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="p-3 bg-blue-50 rounded-2xl border border-blue-200 text-right">
                <div class="text-[11px] font-bold uppercase tracking-wider text-blue-800">Total Realisasi Belanja</div>
                <div class="text-lg font-black text-blue-900 font-headline">Rp {{ number_format($stats['total_spend'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form action="{{ route('purchasing.history') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. PO, nama pembuat, atau catatan..." 
                       class="w-full h-11 pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
            </div>
            
            <div class="sm:w-44">
                <select name="status" class="w-full h-11 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
                    <option value="">Semua Status Arsip</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved (Disetujui)</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed (Selesai Inbound)</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 transition-colors shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                <a href="{{ route('purchasing.history') }}" class="h-11 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold flex items-center transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">No. PO &amp; Catatan</th>
                        <th class="px-5 py-4">Tanggal Pesanan</th>
                        <th class="px-5 py-4">Staff Pembuat</th>
                        <th class="px-5 py-4 text-center">Jumlah Item</th>
                        <th class="px-5 py-4 text-right">Nilai Total</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pos as $po)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-blue-600 text-sm">{{ $po->ref_number }}</div>
                            @if($po->notes)
                            <p class="text-[11px] text-slate-500 mt-0.5 max-w-xs truncate">{{ $po->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-700">
                            {{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-800">
                            <div class="font-semibold">{{ $po->user->name ?? 'System' }}</div>
                        </td>
                        <td class="px-5 py-4 text-center text-xs">
                            <span class="font-bold text-slate-800">{{ $po->lines->count() }} Item</span>
                            <div class="text-[10px] text-slate-400">({{ $po->lines->sum('quantity') }} unit)</div>
                        </td>
                        <td class="px-5 py-4 text-right text-xs font-bold text-slate-900">
                            Rp {{ number_format($po->total_amount ?: $po->lines->sum(fn($l) => $l->quantity * $l->unit_price), 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($po->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200">Approved</span>
                            @elseif($po->status === 'Completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">Completed</span>
                            @elseif($po->status === 'Rejected')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('transactions.po.show', $po->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white transition-colors border border-blue-200 shadow-2xs">
                                <span>Detail</span>
                                <span class="material-symbols-outlined text-[16px] ml-1">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">history</span>
                                <p class="font-bold text-slate-700">Belum ada riwayat arsip Purchase Order</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
