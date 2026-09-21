@extends('layouts.app')

@section('header_title', 'Laporan Transaksi')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Laporan Transaksi</h2>
            <p class="text-sm text-[#6B7280]">Laporan histori mutasi (Barang Masuk & Keluar).</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="h-10 px-4 rounded-lg bg-white border border-[#D1D5DB] hover:bg-gray-50 text-[#374151] text-sm font-semibold flex items-center gap-2 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#4B5563] print:text-black">
                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB] print:bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">No. Ref</th>
                        <th scope="col" class="px-6 py-4">Tipe</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Item Transaksi</th>
                        <th scope="col" class="px-6 py-4 text-center">Total Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-[#6B7280] whitespace-nowrap">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-bold text-[#111827]">{{ $trx->ref_number }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $trx->type == 'Inbound' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $trx->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold {{ $trx->status == 'Completed' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $trx->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="list-disc list-inside text-xs">
                                @foreach($trx->lines as $line)
                                    <li>{{ $line->item->name ?? 'Unknown' }} ({{ $line->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 text-center font-bold">{{ $trx->lines->sum('quantity') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
            <span>Total Transaksi: {{ $transactions->count() }}</span>
        </div>
    </div>
</div>
@endsection
