@extends('layouts.app')

@section('header_title', 'Approval Center')

@section('main_content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 font-headline">Approval Center</h2>
    <p class="text-sm text-slate-500">Pusat persetujuan dokumen (PO, Mutasi, Opname) yang memerlukan otorisasi Manajer.</p>
</div>

<div class="space-y-8">
    <!-- Pending Purchase Orders -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-indigo-50/50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-indigo-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">receipt_long</span>
                Purchase Orders Menunggu Persetujuan
            </h3>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">{{ $pendingPOs->count() }} Dokumen</span>
        </div>
        <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">No. PO</th>
                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                    <th class="px-6 py-4 font-semibold">Dibuat Oleh</th>
                    <th class="px-6 py-4 font-semibold">Total Item</th>
                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pendingPOs as $po)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-mono font-bold text-slate-700">{{ $po->ref_number }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ $po->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4">{{ $po->lines->count() }} Jenis</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('transactions.po.show', $po->id) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 text-xs font-semibold">Review</a>
                                <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 text-xs font-semibold">Approve</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Tidak ada PO yang menunggu persetujuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
</div>
    </div>

    <!-- Pending Stock Opname -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-amber-50/50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-amber-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600">fact_check</span>
                Stock Opname Menunggu Konfirmasi
            </h3>
            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">{{ $pendingOpnames->count() }} Dokumen</span>
        </div>
        <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">No. Referensi</th>
                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                    <th class="px-6 py-4 font-semibold">Dibuat Oleh</th>
                    <th class="px-6 py-4 font-semibold">Item Dihitung</th>
                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pendingOpnames as $opname)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-mono font-bold text-slate-700">{{ $opname->ref_number }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($opname->transaction_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ $opname->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4">{{ $opname->lines->count() }} Jenis</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('transactions.opname.show', $opname->id) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 text-xs font-semibold">Review & Konfirmasi</a>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Tidak ada hasil Stock Opname yang menunggu konfirmasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
</div>
    </div>
</div>
@endsection
