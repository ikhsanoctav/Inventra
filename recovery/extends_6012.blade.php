@extends('layouts.app')

@section('header_title', 'Detail Barang Masuk')

@section('main_content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('transactions.inbound') }}" class="text-[#6B7280] hover:text-[#111827] transition-colors"><span class="material-symbols-outlined text-[20px]">arrow_back</span></a>
                <h2 class="font-headline text-2xl font-bold text-[#111827]">Detail Transaksi: {{ $transaction->ref_number }}</h2>
            </div>
            <p class="text-sm text-[#6B7280]">Tanggal: {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }} | Status: <span class="font-semibold {{ $transaction->status == 'Completed' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $transaction->status }}</span></p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-lg text-sm font-medium border border-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lines Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <h3 class="font-semibold text-[#111827]">Daftar Item</h3>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-[#4B5563]">
                    <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4">Item (SKU)</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($transaction->lines as $line)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-[#111827]">
                                {{ $line->item->name ?? 'Unknown Item' }} <br>
                                <span class="text-xs text-[#6B7280]">{{ $line->item->sku ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-[#111827]">{{ $line->quantity }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('transactions.lines.destroy', $line->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-[#6B7280] text-sm">Belum ada item ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[#E5E7EB] bg-[#F8FAFC] flex justify-between font-bold text-[#111827] text-sm">
                <span>Total Item</span>
                <span>{{ $transaction->lines->sum('quantity') }}</span>
            </div>
        </div>

        <!-- Add Item Form -->
        <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-[#E5E7EB]">
                <h3 class="font-semibold text-[#111827]">Tambah Item</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('transactions.inbound.addLine', $transaction->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Pilih Barang</label>
                        <select name="item_id" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
                            <option value="">Cari barang...</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kuantitas</label>
                        <input type="number" name="quantity" required min="1" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="0">
                    </div>
                    <button type="submit" class="w-full h-10 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-sm transition-colors mt-2">
                        <span class="material-symbols-outlined text-[18px]">add</span> Tambahkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection