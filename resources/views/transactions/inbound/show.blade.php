@extends('layouts.app')

@section('header_title', 'Detail Inbound')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Detail Inbound: <span class="text-[#4F46E5]">{{ $transaction->ref_number }}</span></h2>
            <p class="text-sm text-[#6B7280]">Tanggal: {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }} | Status: 
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->status === 'Completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $transaction->status }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.inbound') }}" class="h-10 px-4 rounded-lg bg-white border border-[#D1D5DB] hover:bg-gray-50 text-[#374151] text-sm font-semibold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
            </a>
            @if($transaction->status === 'Draft')
            <form action="{{ route('transactions.inbound.complete', $transaction->id) }}" method="POST">
                @csrf
                <button type="submit" class="h-10 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all" onclick="return confirm('Selesaikan transaksi? Stok akan ditambahkan.')">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Selesai
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Item List -->
            <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E5E7EB] flex justify-between items-center bg-[#F8FAFC]">
                    <h3 class="font-semibold text-[#111827]">Daftar Item</h3>
                </div>
                <div class="overflow-x-auto w-full pb-4">
<table class="w-full text-left text-sm text-[#4B5563]">
                    <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4">Item</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($transaction->lines as $line)
                        <tr>
                            <td class="px-6 py-4 font-medium text-[#111827]">{{ $line->item->name ?? 'Unknown' }} <br><span class="text-xs text-[#6B7280]">{{ $line->item->sku ?? '' }}</span></td>
                            <td class="px-6 py-4 text-center">{{ $line->quantity }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($transaction->status === 'Draft')
                                <form action="{{ route('transactions.lines.destroy', $line->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-[#6B7280]">Belum ada item ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
</div>
            </div>
        </div>

        @if($transaction->status === 'Draft')
        <div class="space-y-6">
            <!-- Add Item Form -->
            <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden p-6">
                <h3 class="font-semibold text-[#111827] mb-4">Tambah Item</h3>
                <form action="{{ route('transactions.inbound.addLine', $transaction->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Pilih Item</label>
                        <select name="item_id" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                            <option value="">-- Pilih --</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kuantitas</label>
                        <input type="number" name="quantity" min="1" value="1" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                    </div>
                    <button type="submit" class="w-full h-10 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold transition-all">Tambahkan</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
