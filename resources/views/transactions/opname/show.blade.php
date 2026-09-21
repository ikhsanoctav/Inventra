@extends('layouts.app')

@section('header_title', 'Input Stock Opname')

@section('main_content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.opname.index') }}" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl font-bold text-slate-800 font-headline">Lembar Hitung Fisik ({{ $opname->ref_number }})</h2>
                    @if($opname->status === 'Draft')
                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">Proses Hitung</span>
                    @elseif($opname->status === 'Completed')
                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">Selesai & Disesuaikan</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500">Tanggal Pelaksanaan: {{ $opname->transaction_date }} • Petugas: {{ $opname->user->name }}</p>
            </div>
        </div>
        
        <div class="flex gap-2">
            @if($opname->status === 'Draft')
            <form action="{{ route('transactions.opname.complete', $opname->id) }}" method="POST" onsubmit="return confirm('Peringatan: Aksi ini akan mengubah stok sistem secara permanen berdasarkan perhitungan fisik. Lanjutkan?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span> Simpan & Sesuaikan Stok
                </button>
            </form>
            @endif
        </div>
    </div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Item List (Left, larger) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Daftar Barang Dihitung</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Barang</th>
                                <th class="px-6 py-4 text-center">Stok Sistem Saat Ini</th>
                                <th class="px-6 py-4 text-center">Hitungan Fisik</th>
                                <th class="px-6 py-4 text-center">Selisih</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($opname->lines as $line)
                            @php 
                                $physical = $line->unit_price; // We stored physical qty here
                                $variance = $line->quantity;   // Variance is physical - system
                                $system_stok = $physical - $variance;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800">{{ $line->item->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $line->item->sku }}</p>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500">{{ $system_stok }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $physical }}</td>
                                <td class="px-6 py-4 text-center font-bold {{ $variance > 0 ? 'text-emerald-600' : ($variance < 0 ? 'text-red-600' : 'text-slate-500') }}">
                                    {{ $variance > 0 ? '+'.$variance : $variance }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($opname->status === 'Draft')
                                    <form action="{{ route('transactions.opname.lines.destroy', $line->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada input hitungan fisik.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Item Form (Right side) -->
        <div class="lg:col-span-1 space-y-6">
            @if($opname->status === 'Draft')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Input Hitungan Fisik</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('transactions.opname.addLine', $opname->id) }}" method="POST" class="space-y-4 text-sm">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Scan / Pilih Barang</label>
                            <select name="item_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Pilih barang...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }} (Sistem: {{ $item->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Jumlah Hitung Fisik (Riil)</label>
                            <input type="number" name="physical_qty" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="Misal: 48" required>
                            <p class="text-xs text-slate-500">Masukkan angka pasti dari hasil hitung lapangan.</p>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">Tambah Data Hitung</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6 space-y-3 text-sm text-amber-800">
                <h3 class="font-bold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">warning</span> Petunjuk Penting</h3>
                <p>Fitur Stock Opname memungkinkan Anda melakukan validasi stok. <strong>Hanya masukkan barang yang memiliki perbedaan/selisih hitungan</strong> dengan stok di sistem.</p>
                <p>Barang yang jumlah fisik dan sistemnya sama (sinkron) tidak perlu diinput.</p>
            </div>
        </div>
    </div>
</div>
@endsection
