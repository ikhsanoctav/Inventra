@extends('layouts.app')

@section('header_title', 'Manager Dashboard')

@section('main_content')
<div class="space-y-6" x-data="{
    activePeriod: 'mingguan',
    allTrendData: {{ json_encode($trendData) }},
    get currentTitle() {
        return this.allTrendData[this.activePeriod] ? this.allTrendData[this.activePeriod].title : 'Tren Transaksi Logistik';
    },
    setPeriod(p) {
        this.activePeriod = p;
        if (window.updateManagerChartPeriod) {
            window.updateManagerChartPeriod(p);
        }
    }
}">
    <!-- Header Greeting & Executive Action Shortcuts -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-600 rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-bold backdrop-blur-sm mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Dashboard Eksekutif & Pengendalian Bisnis
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold font-headline tracking-tight">Executive Analytics & Pusat Otorisasi</h2>
                <p class="text-indigo-100 text-xs sm:text-sm mt-1.5 leading-relaxed">Pantau arus transaksi logistik, valuasi persediaan, dan otorisasi dokumen operasional secara real-time.</p>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('manager.approvals') }}" class="px-4 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 font-bold text-xs sm:text-sm rounded-xl transition-all shadow-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span>Approval Center</span>
                    @if($totalPendingApprovals > 0)
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-600 text-white animate-pulse">{{ $totalPendingApprovals }}</span>
                    @endif
                </a>
                <a href="{{ route('manager.audit.opname') }}" class="px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white backdrop-blur-sm font-bold text-xs sm:text-sm rounded-xl transition-all border border-white/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span>
                    <span>Audit Opname</span>
                </a>
                <a href="{{ route('manager.trends') }}" class="px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white backdrop-blur-sm font-bold text-xs sm:text-sm rounded-xl transition-all border border-white/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">insights</span>
                    <span>Tren Multi-Periode</span>
                </a>
            </div>
        </div>
        <!-- Decorative bg shapes -->
        <div class="absolute right-0 top-0 w-80 h-full bg-white/10 skew-x-12 translate-x-20 pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-purple-400/20 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- PENDING APPROVALS ALERT BANNER -->
    @if($totalPendingApprovals > 0)
    <div class="bg-gradient-to-r from-amber-500/10 via-indigo-500/10 to-rose-500/10 border border-amber-300/80 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/20">
                <span class="material-symbols-outlined text-[24px]">pending_actions</span>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-800 font-headline flex items-center gap-2">
                    <span>Perhatian: Ada {{ $totalPendingApprovals }} Dokumen Memerlukan Keputusan Otorisasi</span>
                </h4>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600 mt-1">
                    @if($pendingPoCount > 0)
                        <span class="inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            {{ $pendingPoCount }} Purchase Orders
                        </span>
                    @endif
                    @if($pendingRequestsCount > 0)
                        <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            {{ $pendingRequestsCount }} Request Gudang
                        </span>
                    @endif
                    @if($pendingDamagedCount > 0)
                        <span class="inline-flex items-center gap-1 font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                            {{ $pendingDamagedCount }} Barang Karantina
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('manager.approvals') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1.5 self-start sm:self-auto shrink-0">
            <span>Buka Approval Center</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
    @endif

    <!-- Key Metrics Grid (5 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Net Valuation -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Valuasi Persediaan</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 mt-1 font-mono">Rp {{ number_format($netValuation, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                </div>
            </div>
            <div class="mt-3 flex items-center text-[11px] font-bold text-indigo-600">
                <span class="material-symbols-outlined text-[14px] mr-1">inventory</span>
                <span>{{ number_format($totalStock, 0, ',', '.') }} Unit Fisik</span>
            </div>
        </div>

        <!-- 2. Sales Volume -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 mt-1 font-mono">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </div>
            </div>
            <div class="mt-3 flex items-center text-[11px] font-bold text-emerald-600">
                <span class="material-symbols-outlined text-[14px] mr-1">calendar_today</span>
                <span>Bulan Ini: Rp {{ number_format($monthlySales, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 3. Monthly Inbound -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inbound Bulan Ini</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 mt-1 font-mono">Rp {{ number_format($monthlyInbound, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                </div>
            </div>
            <div class="mt-3 flex items-center text-[11px] font-bold text-sky-600">
                <span class="material-symbols-outlined text-[14px] mr-1">local_shipping</span>
                <span>PO & Inbound Masuk</span>
            </div>
        </div>

        <!-- 4. Transaction Count -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 mt-1 font-mono">{{ number_format($transactionCount, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Dokumen</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                </div>
            </div>
            <div class="mt-3 flex items-center text-[11px] font-semibold text-slate-500">
                <span>Inbound, Outbound, Opname</span>
            </div>
        </div>

        <!-- 5. Alerts -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Kritis</p>
                    <h3 class="text-lg sm:text-xl font-black text-rose-600 mt-1 font-mono">{{ $criticalStock }} <span class="text-xs font-medium text-slate-400">SKU</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                </div>
            </div>
            <div class="mt-3 flex items-center text-[11px] font-bold text-rose-600">
                <a href="{{ route('reports.low_stock') }}" class="hover:underline flex items-center gap-0.5">
                    <span>Lihat Rincian</span>
                    <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts & Intelligence Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sales & Inbound Trend Chart (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800 font-headline flex items-center gap-2" x-text="currentTitle">
                        <span class="material-symbols-outlined text-indigo-600 text-[20px]">show_chart</span>
                        Tren Transaksi Logistik
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbandingan nominal pergerakan Barang Masuk (Inbound) vs Barang Keluar/Penjualan (Outbound).</p>
                </div>
                
                <!-- Period Selector (Harian / Mingguan / Bulanan) -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold self-start sm:self-auto shrink-0 shadow-inner">
                    <button type="button" @click="setPeriod('harian')" :class="activePeriod === 'harian' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                        Harian
                    </button>
                    <button type="button" @click="setPeriod('mingguan')" :class="activePeriod === 'mingguan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                        Mingguan
                    </button>
                    <button type="button" @click="setPeriod('bulanan')" :class="activePeriod === 'bulanan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                        Bulanan
                    </button>
                </div>
            </div>

            <div class="relative h-72 sm:h-80 w-full">
                <canvas id="managerSalesChart"></canvas>
            </div>
        </div>

        <!-- Right Side: Top Moving Items & Recent Audit (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Top Moving Items Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 font-headline flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-amber-500 text-[18px]">star</span>
                        Top 5 Fast-Moving Items
                    </h3>
                    <a href="{{ route('reports.sales') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800">Laporan</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($topMovingItems ?? [] as $topItem)
                    <div class="p-2.5 rounded-xl bg-slate-50/80 hover:bg-slate-100/80 transition-colors flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $topItem->item->name ?? 'SKU #'.$topItem->item_id }}</p>
                            <p class="text-[11px] text-slate-500 font-mono">{{ $topItem->item->sku ?? '-' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-black text-indigo-600 font-mono block">{{ number_format($topItem->total_qty) }} {{ $topItem->item->uom->symbol ?? 'Unit' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">Rp {{ number_format($topItem->total_val, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        <p>Belum ada data penjualan tercatat.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Audit Log -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 font-headline flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-indigo-600 text-[18px]">history</span>
                        Aktivitas Sistem Terkini
                    </h3>
                    <a href="{{ route('system.audit') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800">Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentLogs ?? [] as $log)
                    <div class="flex gap-2.5 items-start">
                        <div class="w-2 h-2 rounded-full bg-indigo-600 mt-1.5 shrink-0"></div>
                        <div class="flex-1 min-w-0 text-xs">
                            <p class="font-bold text-slate-800 truncate">{{ str_replace('_', ' ', $log->action) }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                Oleh: <span class="font-semibold text-slate-700">{{ $log->user->name ?? 'System' }}</span>
                            </p>
                            <span class="text-[10px] text-slate-400 block">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        <p>Belum ada catatan aktivitas.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- PENDING PURCHASE ORDERS QUICK REVIEW TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800 font-headline">Purchase Orders (PO) Mendesak Menunggu Keputusan</h3>
                    <p class="text-xs text-slate-500">Tinjau dan proses pengajuan pengadaan barang secara instan.</p>
                </div>
            </div>
            <a href="{{ route('manager.approvals') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <span>Buka Seluruh Approval</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">No. PO & Tanggal</th>
                        <th class="py-3 px-4">Diajukan Oleh</th>
                        <th class="py-3 px-4">Rincian Item</th>
                        <th class="py-3 px-4">Total Estimasi</th>
                        <th class="py-3 px-4 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingPOs ?? [] as $po)
                    @php
                        $totalPoAmount = $po->lines->sum(fn($l) => $l->quantity * $l->unit_price);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4">
                            <span class="font-mono font-bold text-slate-800 block">{{ $po->ref_number }}</span>
                            <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-700">
                            {{ $po->user->name ?? 'Staff Purchasing' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800">{{ $po->lines->count() }} Jenis Item</span>
                            <p class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">
                                {{ $po->lines->map(fn($l) => ($l->item->name ?? 'Item') . ' (' . $l->quantity . ')')->take(2)->implode(', ') }}
                            </p>
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-800 font-mono">
                            Rp {{ number_format($totalPoAmount, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('transactions.po.show', $po->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                                    Review Detail
                                </a>
                                <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST" onsubmit="return confirm('Setujui Purchase Order {{ $po->ref_number }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        <span>Setujui</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl mb-1 text-emerald-500">task_alt</span>
                            <p class="font-bold text-slate-700 text-xs">Semua Purchase Order Telah Diproses</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada pengajuan PO yang menunggu otorisasi pimpinan saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const trendData = @json($trendData);
        let currentPeriod = 'mingguan';

        function renderManagerChart(period) {
            const canvas = document.getElementById('managerSalesChart');
            if (!canvas) return;

            period = period || currentPeriod;
            currentPeriod = period;

            const selectedData = trendData[period] || trendData['mingguan'];

            // Destroy previous instance to avoid canvas overlay bugs
            if (window._managerChartInstance) {
                window._managerChartInstance.destroy();
                window._managerChartInstance = null;
            }

            const ctx = canvas.getContext('2d');

            const gradientSales = ctx.createLinearGradient(0, 0, 0, 300);
            gradientSales.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
            gradientSales.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

            const gradientInbound = ctx.createLinearGradient(0, 0, 0, 300);
            gradientInbound.addColorStop(0, 'rgba(16, 185, 129, 0.20)');
            gradientInbound.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

            window._managerChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: selectedData.labels || [],
                    datasets: [
                        {
                            label: 'Penjualan / Outbound (Rp)',
                            data: selectedData.outbound || [],
                            borderColor: '#4F46E5',
                            backgroundColor: gradientSales,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#4F46E5',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Barang Masuk / Inbound (Rp)',
                            data: selectedData.inbound || [],
                            borderColor: '#10B981',
                            backgroundColor: gradientInbound,
                            borderWidth: 2,
                            borderDash: [4, 4],
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 500,
                        easing: 'easeOutQuart'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                font: { size: 11, family: 'Inter', weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(241, 245, 249, 0.8)', borderDash: [3, 3] },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + ' rb';
                                    return 'Rp ' + value;
                                },
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }

        // Global period switcher hook called by Alpine buttons
        window.updateManagerChartPeriod = function(period) {
            renderManagerChart(period);
        };

        // Initialize safely on all lifecycle events
        if (typeof Chart !== 'undefined') {
            renderManagerChart(currentPeriod);
        }
        document.addEventListener('DOMContentLoaded', () => renderManagerChart(currentPeriod));
        document.addEventListener('htmx:afterOnLoad', () => renderManagerChart(currentPeriod));
        setTimeout(() => renderManagerChart(currentPeriod), 150);
        setTimeout(() => renderManagerChart(currentPeriod), 600);
    })();
</script>
@endpush
@endsection
