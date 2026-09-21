@extends('layouts.app')

@section('header_title', 'Surat Jalan')

@section('main_content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 font-headline">Cetak Surat Jalan</h2>
    <p class="text-sm text-slate-500">Daftar transaksi barang keluar (Outbound) yang siap untuk dicetak Surat Jalannya.</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">No. Referensi (Outbound)</th>
                    <th class="px-6 py-4 font-semibold">Tanggal Transaksi</th>
                    <th class="px-6 py-4 font-semibold">Total Item</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($outbounds as $trx)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono font-bold text-indigo-600">{{ $trx->ref_number }}</span>
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $trx->lines->sum('quantity') }} Unit ({{ $trx->lines->count() }} Jenis)
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Siap Kirim</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="window.open('{{ route('gudang.delivery_note.print', $trx->id) }}', '_blank', 'width=800,height=900')" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-medium rounded-lg transition-colors flex items-center gap-2 mx-auto">
                            <span class="material-symbols-outlined text-[18px]">print</span> Cetak
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <span class="material-symbols-outlined text-4xl mb-3 opacity-50">description</span>
                        <p>Belum ada transaksi Outbound yang Selesai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
