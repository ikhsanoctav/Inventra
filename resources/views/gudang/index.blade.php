@extends('layouts.app')

@section('header_title', 'Dashboard Gudang')

@section('main_content')
<div class="space-y-6" x-data="warehouseDashboard()">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h2 class="text-2xl font-bold text-slate-800 font-headline">Operasional Gudang & Logistik</h2>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Stream
                </span>
            </div>
            <p class="text-sm text-slate-500">Pantau pergerakan arus barang masuk, keluar, mutasi, dan stok fisik secara real-time.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('transactions.inbound') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors flex items-center gap-2 shadow-sm shadow-indigo-500/20">
                <span class="material-symbols-outlined text-[18px]">login</span> Inbound Baru
            </a>
            <a href="{{ route('transactions.outbound') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm shadow-emerald-500/20">
                <span class="material-symbols-outlined text-[18px]">logout</span> Outbound Baru
            </a>
            <a href="{{ route('transactions.opname.index') }}" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px] text-amber-500">fact_check</span> Opname
            </a>
            <a href="{{ route('transactions.transfer') }}" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px] text-blue-500">sync_alt</span> Mutasi
            </a>
        </div>
    </div>

    <!-- Live Event Toast Banner (Alpine.js) -->
    <div x-show="lastEventMessage" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         style="display: none;"
         class="bg-indigo-600 text-white px-4 py-3 rounded-2xl shadow-lg flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[18px]">notifications_active</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-200">Aktivitas Gudang Terkini:</span>
                <p class="text-sm font-medium" x-text="lastEventMessage"></p>
            </div>
        </div>
        <button @click="lastEventMessage = ''" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    <!-- 6 Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Fisik Unit -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-indigo-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Stok Fisik</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-800 font-headline">{{ number_format($totalStock, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="font-semibold text-slate-700">{{ $totalItems }}</span> SKU Terdaftar
            </p>
        </div>

        <!-- Inbound Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-indigo-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inbound Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-indigo-600 font-headline">{{ $inboundTodayCount }}</p>
            <p class="text-xs text-slate-500 mt-1">Transaksi Penerimaan</p>
        </div>

        <!-- Outbound Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Outbound Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 font-headline">{{ $outboundTodayCount }}</p>
            <p class="text-xs text-slate-500 mt-1">Transaksi Pengeluaran</p>
        </div>

        <!-- Stok Kritis / Menipis -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-red-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Kritis</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-rose-600 font-headline">{{ $criticalStockCount }}</p>
            <p class="text-xs text-slate-500 mt-1">Item Perlu Re-order</p>
        </div>

        <!-- Karantina / Rusak -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-amber-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Karantina / Rusak</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">healing</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 font-headline">{{ $damagedItemsCount }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $totalDamagedUnits }} Unit Terisolasi</p>
        </div>

        <!-- PO Disetujui Siap Terima -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-purple-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">PO Siap Terima</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">shopping_cart_checkout</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-purple-600 font-headline">{{ $approvedPOsCount }}</p>
            <p class="text-xs text-slate-500 mt-1">Menunggu Inbound</p>
        </div>
    </div>

    <!-- Operational Shortcuts Hub -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
                <div>
                    <h3 class="text-lg font-bold font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-400">tune</span> Pusat Aksi Operasional Cepat
                    </h3>
                    <p class="text-xs text-slate-300">Akses langsung ke alur kerja utama gudang tanpa repot.</p>
                </div>
                <div class="text-xs bg-white/10 px-3 py-1.5 rounded-xl text-slate-200 border border-white/10 font-medium">
                    Shift Logistik: <span class="font-bold text-white">{{ now()->format('d M Y') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Inbound -->
                <a href="{{ route('transactions.inbound') }}" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-4 flex flex-col items-center text-center transition-all group hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/30 text-indigo-300 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">input</span>
                    </div>
                    <span class="text-xs font-bold">Penerimaan (Inbound)</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">Tarik PO / Manual</span>
                </a>

                <!-- Outbound -->
                <a href="{{ route('transactions.outbound') }}" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-4 flex flex-col items-center text-center transition-all group hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/30 text-emerald-300 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">output</span>
                    </div>
                    <span class="text-xs font-bold">Pengeluaran (Outbound)</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">Kirim ke Cabang/Outlet</span>
                </a>

                <!-- Surat Jalan -->
                <a href="{{ route('gudang.delivery_note') }}" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-4 flex flex-col items-center text-center transition-all group hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/30 text-blue-300 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">local_shipping</span>
                    </div>
                    <span class="text-xs font-bold">Surat Jalan</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">Cetak Dokumen Kirim</span>
                </a>

                <!-- Stock Opname -->
                <a href="{{ route('transactions.opname.index') }}" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-4 flex flex-col items-center text-center transition-all group hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/30 text-amber-300 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">checklist</span>
                    </div>
                    <span class="text-xs font-bold">Stock Opname</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">Hitung Fisik Stok</span>
                </a>

                <!-- Cetak Barcode -->
                <a href="{{ route('gudang.barcode.index') }}" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-4 flex flex-col items-center text-center transition-all group hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/30 text-purple-300 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">qr_code_scanner</span>
                    </div>
                    <span class="text-xs font-bold">Cetak Barcode</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">Label Rak & Produk</span>
                </a>

                <!-- Barang Rusak -->
                <a href="{{ route('gudang.damaged_items.index') }}" class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl p-4 flex flex-col items-center text-center transition-all group hover:-translate-y-0.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/30 text-rose-300 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">report_problem</span>
                    </div>
                    <span class="text-xs font-bold">Barang Rusak</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">Karantina & Retur</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Split Grid: Transactions Feed (Left) & Alerts / Damaged (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Recent Transactions (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Riwayat Aktivitas & Mutasi Terakhir</h3>
                        <p class="text-xs text-slate-500">Daftar pergerakan barang terkini di gudang.</p>
                    </div>
                </div>
                <a href="{{ route('transactions.history') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                    Semua Riwayat <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">No. Referensi</th>
                            <th class="px-6 py-3">Tipe Mutasi</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Item / Baris</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-indigo-600">{{ $trx->ref_number }}</span>
                                @if($trx->notes)
                                <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ $trx->notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->type == 'Inbound')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                                        <span class="material-symbols-outlined text-[14px]">login</span> Inbound
                                    </span>
                                @elseif($trx->type == 'Outbound')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">
                                        <span class="material-symbols-outlined text-[14px]">logout</span> Outbound
                                    </span>
                                @elseif($trx->type == 'StockOpname')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">
                                        <span class="material-symbols-outlined text-[14px]">fact_check</span> Opname
                                    </span>
                                @elseif($trx->type == 'Transfer')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                                        <span class="material-symbols-outlined text-[14px]">sync_alt</span> Mutasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        {{ $trx->type }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                {{ $trx->lines->count() }} Jenis Item
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($trx->status == 'Completed')
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-xs bg-emerald-50 px-2.5 py-1 rounded-full">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-amber-600 font-semibold text-xs bg-amber-50 px-2.5 py-1 rounded-full">
                                        <span class="material-symbols-outlined text-[14px]">pending</span> Draft
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada riwayat transaksi mutasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Low Stock Alerts & Damaged Items (1 col) -->
        <div class="space-y-6">
            <!-- Critical / Low Stock Alert Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm">Peringatan Stok Menipis</h3>
                    </div>
                    <a href="{{ route('reports.low_stock') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($lowStockItems as $item)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-rose-200 transition-colors">
                        <div class="min-w-0 flex-1 pr-2">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $item->name }}</p>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $item->sku }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-extrabold {{ $item->stock <= 3 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                Sisa {{ $item->stock }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-xs">
                        <span class="material-symbols-outlined text-3xl text-emerald-400 mb-1">verified</span>
                        <p class="font-medium text-emerald-700">Semua stok barang dalam kondisi aman.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Damaged / Quarantine Items Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">shield_with_heart</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm">Barang Dalam Karantina</h3>
                    </div>
                    <a href="{{ route('gudang.damaged_items.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Kelola</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentDamaged as $dmg)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="min-w-0 flex-1 pr-2">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $dmg->item->name ?? 'Barang' }}</p>
                            <span class="text-[10px] text-amber-700 font-medium">{{ $dmg->damage_type }} • {{ $dmg->reference_number }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-bold text-slate-700">{{ $dmg->quantity }} Unit</span>
                            <span class="block text-[10px] text-amber-600 font-medium">{{ $dmg->status }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-xs">
                        Tidak ada barang yang sedang dalam status karantina.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function warehouseDashboard() {
    return {
        lastEventMessage: '',
        playAudioBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(650, ctx.currentTime);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            } catch(e) {}
        },
        init() {
            if (window.Echo) {
                window.Echo.channel('inventory')
                    .listen('StockUpdated', (e) => {
                        this.lastEventMessage = e.message || 'Perubahan stok terdeteksi secara real-time.';
                        this.playAudioBeep();
                    })
                    .listen('.StockUpdated', (e) => {
                        this.lastEventMessage = e.message || 'Perubahan stok terdeteksi secara real-time.';
                        this.playAudioBeep();
                    });
            }
        }
    }
}
</script>
@endsection
