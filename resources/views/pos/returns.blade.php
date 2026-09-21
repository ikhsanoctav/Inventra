@extends('layouts.app')

@section('header_title', 'Retur Penjualan (POS)')

@section('main_content')
<div class="space-y-6 pb-8">
    <div class="max-w-4xl mx-auto space-y-6 pt-4">
        
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-100 shadow-sm">
                <span class="material-symbols-outlined text-3xl">undo</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Retur Penjualan</h1>
            <p class="text-slate-500 mt-2 text-lg">Proses pengembalian dana atau barang dari transaksi POS</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 rounded-2xl flex gap-3">
                <span class="material-symbols-outlined shrink-0 text-emerald-500">check_circle</span>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 text-rose-800 border border-rose-200 p-4 rounded-2xl flex gap-3">
                <span class="material-symbols-outlined shrink-0 text-rose-500">error</span>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @if(request('ref_number') && !$transaction)
            <div class="bg-amber-50 text-amber-800 border border-amber-200 p-4 rounded-2xl flex gap-3">
                <span class="material-symbols-outlined shrink-0 text-amber-500">warning</span>
                <p class="font-medium">Transaksi dengan nomor referensi <strong>{{ request('ref_number') }}</strong> tidak ditemukan atau bukan transaksi penjualan yang valid.</p>
            </div>
        @endif

        <!-- Search Box Card -->
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-8 md:p-12 relative overflow-hidden">
            <form action="{{ route('pos.returns') }}" method="GET" class="text-center space-y-6">
                <div class="text-left space-y-2">
                    <label for="ref_number" class="block text-sm font-bold text-slate-700">Nomor Referensi Transaksi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">receipt_long</span>
                        <input type="text" id="ref_number" name="ref_number" value="{{ request('ref_number') }}" placeholder="Contoh: POS-XXXXXX" 
                            class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all text-base font-medium" required>
                    </div>
                </div>
                
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-indigo-600 text-white font-semibold rounded-2xl hover:bg-indigo-700 transition-all shadow-sm w-fit mx-auto whitespace-nowrap">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                    Cari Transaksi
                </button>
            </form>
        </div>

        @if($transaction)
        <!-- Return Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-500">list_alt</span>
                        Detail Transaksi
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Ref: <span class="font-bold text-slate-700">{{ $transaction->ref_number }}</span> &bull; 
                        Tanggal: {{ $transaction->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Pembayaran</p>
                    <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>

            <form action="{{ route('pos.returns.process', $transaction->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="pb-3 font-bold px-4">Nama Barang</th>
                                <th class="pb-3 font-bold px-4 text-center">Jml Beli</th>
                                <th class="pb-3 font-bold px-4 text-right">Harga Satuan</th>
                                <th class="pb-3 font-bold px-4 text-center">Jml Retur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transaction->lines as $line)
                            <tr>
                                <td class="py-4 px-4 font-medium text-slate-800">
                                    {{ $line->item->name }}
                                </td>
                                <td class="py-4 px-4 text-center text-slate-600">
                                    {{ $line->quantity }}
                                </td>
                                <td class="py-4 px-4 text-right text-slate-600 font-medium">
                                    Rp {{ number_format($line->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <input type="number" name="items[{{ $line->id }}]" min="0" max="{{ $line->quantity }}" value="0"
                                        class="w-20 text-center py-1.5 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-bold">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <label for="reason" class="block text-sm font-bold text-slate-700">Alasan Retur</label>
                    <textarea id="reason" name="reason" rows="2" placeholder="Tuliskan alasan pengembalian barang secara singkat..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all text-sm" required></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-8 py-4 bg-rose-600 text-white font-bold rounded-2xl hover:bg-rose-700 transition-all shadow-sm" onclick="return confirm('Anda yakin ingin memproses retur ini? Stok barang akan dikembalikan ke gudang dan pendapatan shift akan dikurangi.')">
                        <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                        Proses Retur Penjualan
                    </button>
                </div>
            </form>
        </div>
        @endif
        
        <!-- Info -->
        <div class="bg-blue-50 text-blue-800 border border-blue-100 rounded-2xl p-5 flex gap-3 text-sm shadow-sm">
            <span class="material-symbols-outlined shrink-0 text-blue-500">info</span>
            <div>
                <p class="font-bold mb-0.5">Informasi Retur</p>
                <p class="opacity-90 leading-relaxed">Saat ini fitur retur masih dalam mode pratinjau (Preview). Proses retur akan mengembalikan stok barang ke dalam sistem dan otomatis mencatat pengeluaran pengembalian dana dari shift kasir Anda (jika aktif).</p>
            </div>
        </div>
    </div>
</div>
@endsection
