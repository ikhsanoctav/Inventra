@extends('layouts.app')

@section('header_title', 'Detail Mutasi')

@section('main_content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.transfer') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ $transaction->ref_number }}</h2>
                <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</p>
            </div>
        </div>
        <span class="px-3 py-1.5 rounded-full text-sm font-bold {{ $transaction->status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
            {{ $transaction->status }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="md:col-span-1 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Informasi Mutasi</h3>
            
            <div class="mb-4">
                <span class="block text-xs font-semibold text-slate-500 mb-1">Gudang Asal</span>
                <span class="text-slate-800 font-medium">{{ $fromWarehouse->name ?? 'Tidak Ada' }}</span>
            </div>
            <div class="mb-4">
                <span class="block text-xs font-semibold text-slate-500 mb-1">Gudang Tujuan</span>
                <span class="text-slate-800 font-medium">{{ $toWarehouse->name ?? 'Tidak Ada' }}</span>
            </div>
            <div class="mb-4">
                <span class="block text-xs font-semibold text-slate-500 mb-1">Catatan</span>
                <span class="text-slate-800 font-medium">{{ $transferData['notes'] ?? '-' }}</span>
            </div>
            
            @if($transaction->status === 'Draft')
                <div class="mt-6 pt-4 border-t">
                    <form action="{{ route('transactions.transfer.complete', $transaction->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span> Selesaikan Mutasi
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Barang Mutasi</h3>
                </div>
                
                @if($transaction->status === 'Draft')
                    <div class="p-4 bg-slate-50 border-b border-slate-100">
                        <form action="{{ route('transactions.transfer.addLine', $transaction->id) }}" method="POST" class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Barang</label>
                                <select name="item_id" required class="w-full px-4 py-2 border rounded-lg bg-white">
                                    <option value="">Pilih Barang...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} (Stok: {{ $item->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Qty</label>
                                <input type="number" name="quantity" min="1" required class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                Tambah
                            </button>
                        </form>
                    </div>
                @endif

                <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">SKU</th>
                            <th class="px-6 py-4 font-semibold">Nama Barang</th>
                            <th class="px-6 py-4 font-semibold text-center">Qty Mutasi</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transaction->lines as $line)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-mono text-slate-500">{{ $line->item->sku }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $line->item->name }}</td>
                            <td class="px-6 py-4 text-center font-bold">{{ $line->quantity }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->status === 'Draft')
                                    <form action="{{ route('transactions.transfer.lines.destroy', $line->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50" onclick="return confirm('Hapus item?')">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if($transaction->lines->isEmpty())
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada barang yang ditambahkan.</td></tr>
                        @endif
                    </tbody>
                </table>
</div>
            </div>
        </div>
    </div>
</div>
@endsection
