@extends('layouts.app')

@section('header_title', 'Detail Purchase Order')

@section('main_content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.po.index') }}" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl font-bold text-slate-800 font-headline">{{ $po->ref_number }}</h2>
                    @if($po->status === 'Draft')
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Draft</span>
                    @elseif($po->status === 'Pending Approval')
                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">Pending Approval</span>
                    @elseif($po->status === 'Approved')
                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">Approved</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500">Dibuat oleh {{ $po->user->name }} pada {{ $po->transaction_date }}</p>
            </div>
        </div>
        
        <div class="flex gap-2">
            @if($po->status === 'Pending Approval' && auth()->user()->hasRole(['manager', 'super_admin']))
            <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui dokumen PO ini?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span> Setujui PO
                </button>
            </form>
            @endif
            
            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">print</span> Cetak PO
            </button>
        </div>
    </div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Item List (Left, larger) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Daftar Barang Pesanan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Barang</th>
                                <th class="px-6 py-4 text-right">Qty</th>
                                <th class="px-6 py-4 text-right">Harga Satuan (Rp)</th>
                                <th class="px-6 py-4 text-right">Subtotal (Rp)</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php $total = 0; @endphp
                            @forelse($po->lines as $line)
                            @php 
                                $subtotal = $line->quantity * $line->unit_price; 
                                $total += $subtotal;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800">{{ $line->item->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $line->item->sku }}</p>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-800">{{ $line->quantity }}</td>
                                <td class="px-6 py-4 text-right">{{ number_format($line->unit_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600">{{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($po->status, ['Draft', 'Pending Approval']))
                                    <form action="{{ route('transactions.po.lines.destroy', $line->id) }}" method="POST" class="inline-block">
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
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada barang di keranjang PO ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($po->lines) > 0)
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-slate-700">Total Keseluruhan</td>
                                <td class="px-6 py-4 text-right text-indigo-700">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Item Form (Right side) -->
        <div class="lg:col-span-1 space-y-6">
            @if(in_array($po->status, ['Draft', 'Pending Approval']))
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Tambah Barang ke PO</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('transactions.po.addLine', $po->id) }}" method="POST" class="space-y-4 text-sm">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Pilih Barang</label>
                            <select name="item_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Cari barang fisik...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Jumlah Pesanan (Qty)</label>
                            <input type="number" name="quantity" min="1" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Harga Beli Kesepakatan (Rp)</label>
                            <input type="number" name="unit_price" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">Tambah ke Daftar</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 space-y-3 text-sm text-slate-600">
                <h3 class="font-bold text-slate-800 mb-2">Informasi Status PO</h3>
                <p>1. <strong>Draft</strong>: Anda dapat menyusun barang dan mengubah Qty/Harga.</p>
                <p>2. <strong>Pending Approval</strong>: Dokumen diajukan ke Manager. Perubahan masih dimungkinkan sebelum disetujui.</p>
                <p>3. <strong>Approved</strong>: PO final, tidak dapat diubah lagi, dan siap dikirim ke Supplier.</p>
            </div>
        </div>
    </div>
</div>
@endsection
