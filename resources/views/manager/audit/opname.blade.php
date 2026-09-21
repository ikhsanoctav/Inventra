@extends('layouts.app')

@section('header_title', 'Audit Stock Opname')

@section('main_content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-headline">Audit Stock Opname</h2>
        <p class="text-sm text-slate-500">Pemantauan hasil perhitungan fisik barang di gudang (Stock Opname).</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 font-semibold">No Referensi</th>
                <th class="px-6 py-4 font-semibold">Tanggal</th>
                <th class="px-6 py-4 font-semibold">PIC Gudang</th>
                <th class="px-6 py-4 font-semibold text-center">Status</th>
                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($opnames as $opname)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $opname->ref_number }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($opname->transaction_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4">{{ $opname->user->name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-bold 
                            {{ $opname->status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $opname->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('transactions.opname.show', $opname->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold border border-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-50">
                            Lihat Detail Selisih
                        </a>
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data Stock Opname.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection
