@extends('layouts.app')

@section('header_title', 'Riwayat Pemesanan')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Riwayat Pemesanan (PO)</h2>
            <p class="text-sm text-[#6B7280]">Daftar seluruh Purchase Order yang telah disetujui atau selesai.</p>
        </div>
        <div class="flex gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" placeholder="Cari nomor referensi..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64 text-sm bg-white shadow-sm transition-all">
            </div>
            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Referensi PO</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Dibuat Oleh</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Total Item</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pos as $po)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-800">{{ $po->ref_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $po->user->name ?? 'Sistem' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($po->status === 'Approved')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg">{{ $po->status }}</span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">{{ $po->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-slate-700">
                            {{ $po->lines->sum('quantity') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('transactions.po.show', $po->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">history</span>
                                <p class="text-slate-500 font-medium">Belum ada riwayat pemesanan.</p>
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
