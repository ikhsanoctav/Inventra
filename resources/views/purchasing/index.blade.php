@extends('layouts.app')

@section('header_title', 'Portal Purchasing & Pengadaan')

@section('main_content')
<div class="space-y-8" x-data="{ showPoModal: false, selectedItem: null, qty: 1 }">
    
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Procurement & Supply Control Center</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-headline tracking-tight">Portal Pengadaan &amp; Purchase Order</h2>
            <p class="text-sm text-slate-500 mt-1">Otomasi reorder stok minimum, monitoring persetujuan PO, dan evaluasi rekanan vendor.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($lowStockCount > 0)
            <form action="{{ route('transactions.po.bulk_auto_draft') }}" method="POST" onsubmit="return confirm('Generate otomatis {{ $lowStockCount }} draft PO untuk seluruh barang stok kritis yang dikelompokkan per vendor?');">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition-all shadow-sm flex items-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                    <span>Auto-Draft Stok Kritis ({{ $lowStockCount }})</span>
                </button>
            </form>
            @endif

            <a href="{{ route('transactions.po.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Buat PO Baru</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: PO Menunggu Approval -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Menunggu Approval</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 font-headline">{{ $pendingPoCount }} <span class="text-xs font-normal text-slate-500">Dokumen</span></div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                <a href="{{ route('purchasing.pending_approval') }}" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-0.5">
                    <span>Lihat antrean</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Card 2: Stok Perlu Restock Kritis -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Restock Kritis</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">notification_important</span>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600 font-headline">{{ $lowStockCount }} <span class="text-xs font-normal text-slate-500">SKU Item</span></div>
            <div class="text-xs text-slate-500 mt-1">
                Di bawah batas minimum buffer
            </div>
        </div>

        <!-- Card 3: Anggaran Reorder Kritis -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Estimasi Kebutuhan</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">request_quote</span>
                </div>
            </div>
            <div class="text-xl font-black text-slate-900 font-headline">Rp {{ number_format($estimatedReorderBudget, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">
                Kalkulasi pemenuhan safety stock
            </div>
        </div>

        <!-- Card 4: Rekanan Supplier Aktif -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Rekanan Vendor</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 font-headline">{{ $totalSuppliers }} <span class="text-xs font-normal text-slate-500">Supplier</span></div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                <a href="{{ route('purchasing.performance') }}" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-0.5">
                    <span>Evaluasi Vendor</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Main Content Layout: Low Stock Recommendations (2/3) & Top Vendors / Quick Info (1/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Rekomendasi Restock Barang (Stok Menipis) -->
        <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-500 text-[22px]">warning</span>
                            <span>Rekomendasi Reorder Barang (Stok Kritis)</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kuantitas saran restock dihitung otomatis berdasarkan formula safety stock.</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700">
                        {{ $lowStockCount }} Item Butuh Restock
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">SKU &amp; Barang</th>
                                <th class="px-4 py-3.5">Vendor Terkait</th>
                                <th class="px-4 py-3.5 text-right">Stok Fisik</th>
                                <th class="px-4 py-3.5 text-right">Min Stock</th>
                                <th class="px-4 py-3.5 text-right text-blue-700">Saran Order</th>
                                <th class="px-4 py-3.5 text-right">Estimasi Biaya</th>
                                <th class="px-5 py-3.5 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($lowStockItems as $item)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $item->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono flex items-center gap-2 mt-0.5">
                                        <span>{{ $item->sku }}</span>
                                        @if($item->category)
                                        <span class="text-slate-300">•</span>
                                        <span>{{ $item->category->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs">
                                    @if($item->supplier)
                                        <span class="font-semibold text-slate-800">{{ $item->supplier->name }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum diset</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ $item->stock }} {{ $item->uom->code ?? 'Unit' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right text-xs font-semibold text-slate-600">
                                    {{ $item->min_stock }} {{ $item->uom->code ?? 'Unit' }}
                                </td>
                                <td class="px-4 py-4 text-right font-black text-blue-600 text-sm">
                                    +{{ $item->recommended_qty }} {{ $item->uom->code ?? 'Unit' }}
                                </td>
                                <td class="px-4 py-4 text-right text-xs font-semibold text-slate-700">
                                    Rp {{ number_format($item->estimated_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <form action="{{ route('transactions.po.quick_draft') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                                        <input type="hidden" name="quantity" value="{{ $item->recommended_qty }}">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-bold transition-colors border border-blue-200 shadow-2xs">
                                            <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
                                            <span>Buat PO</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                                            <span class="material-symbols-outlined text-2xl">check_circle</span>
                                        </div>
                                        <p class="font-bold text-slate-800">Semua Persediaan Dalam Kondisi Aman</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada item yang menyentuh batas minimum stok saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Purchase Orders Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-[22px]">history_edu</span>
                            <span>Dokumen Purchase Order Terkini</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau status persetujuan dan pengiriman pesanan pembelian.</p>
                    </div>
                    <a href="{{ route('transactions.po.index') }}" class="text-xs font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                        <span>Semua Dokumen PO</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">No. Referensi PO</th>
                                <th class="px-4 py-3.5">Tanggal</th>
                                <th class="px-4 py-3.5 text-center">Jumlah Item</th>
                                <th class="px-4 py-3.5 text-right">Nilai Total</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($recentPos as $po)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-blue-600 text-xs">{{ $po->ref_number }}</span>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Oleh: {{ $po->user->name ?? 'System' }}</div>
                                </td>
                                <td class="px-4 py-4 text-xs text-slate-700">
                                    {{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-4 text-center text-xs font-semibold text-slate-800">
                                    {{ $po->lines->count() }} Item ({{ $po->lines->sum('quantity') }} unit)
                                </td>
                                <td class="px-4 py-4 text-right text-xs font-bold text-slate-900">
                                    Rp {{ number_format($po->total_amount ?: $po->lines->sum(fn($l) => $l->quantity * $l->unit_price), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($po->status === 'Draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Draft</span>
                                    @elseif($po->status === 'Pending Approval')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending Approval</span>
                                    @elseif($po->status === 'Approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Approved</span>
                                    @elseif($po->status === 'Completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Completed</span>
                                    @elseif($po->status === 'Rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('transactions.po.show', $po->id) }}" class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                        <span>Detail</span>
                                        <span class="material-symbols-outlined text-[14px] ml-1">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                                    Belum ada transaksi Purchase Order.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right: Top Suppliers & Quick Procurement Hub (1/3) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Quick Hub Card -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-950 rounded-3xl p-6 text-white shadow-lg space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-300">Navigasi Cepat Pengadaan</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                </div>
                
                <h4 class="text-xl font-black font-headline">Alur Pengadaan Terpadu</h4>
                <p class="text-xs text-blue-100/80 leading-relaxed">
                    Setiap PO berstatus <strong>Approved</strong> otomatis terbaca oleh tim Gudang saat penerimaan barang (Inbound) untuk pencocokan fisik instan.
                </p>

                <div class="pt-2 grid grid-cols-1 gap-2.5 text-xs font-semibold">
                    <a href="{{ route('purchasing.pending_approval') }}" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors border border-white/10 text-white">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-amber-300">hourglass_top</span>
                            <span>Menunggu Persetujuan Manager</span>
                        </span>
                        <span class="font-bold px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300">{{ $pendingPoCount }}</span>
                    </a>

                    <a href="{{ route('purchasing.history') }}" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors border border-white/10 text-white">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-emerald-300">history</span>
                            <span>Riwayat Arsip PO</span>
                        </span>
                        <span class="material-symbols-outlined text-[16px] text-white/50">arrow_forward</span>
                    </a>

                    <a href="{{ route('purchasing.performance') }}" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors border border-white/10 text-white">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-cyan-300">analytics</span>
                            <span>Evaluasi Kinerja Vendor</span>
                        </span>
                        <span class="material-symbols-outlined text-[16px] text-white/50">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- Top Suppliers Widget -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="font-bold text-slate-800 text-sm font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[20px]">storefront</span>
                        <span>Daftar Supplier Utama</span>
                    </h4>
                    <a href="{{ route('master.suppliers') }}" class="text-xs font-bold text-blue-600 hover:underline">Kelola</a>
                </div>

                <div class="space-y-3">
                    @forelse($topSuppliers as $supplier)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between hover:border-blue-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                {{ substr($supplier->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-xs">{{ $supplier->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $supplier->contact_person ?? 'Kontak Vendor' }} • {{ $supplier->phone ?? '-' }}</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-700 px-2 py-0.5 rounded-md bg-white border border-slate-200">
                            {{ $supplier->items_count }} SKU
                        </span>
                    </div>
                    @empty
                    <div class="p-4 text-center text-xs text-slate-400">
                        Belum ada vendor terdaftar.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
