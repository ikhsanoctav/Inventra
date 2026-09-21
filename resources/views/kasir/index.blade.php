@extends('layouts.app')

@section('header_title', 'Dashboard Kasir')

@section('main_content')
<div x-data="posDashboard()" class="space-y-8 pb-8 relative">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/60 backdrop-blur-md p-6 rounded-3xl border border-white/40 shadow-sm">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-3xl font-extrabold text-slate-900 font-headline tracking-tight">Portal Kasir</h2>
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100/80 border border-emerald-200 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Online</span>
                </div>
            </div>
            <p class="text-sm text-slate-500 font-medium">Halo <span class="font-bold text-indigo-600">{{ auth()->user()->name }}</span>, selamat bekerja! Berikut ringkasan aktivitas shift Anda saat ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.index') }}" hx-boost="false" class="group relative px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold rounded-xl hover:from-blue-500 hover:to-indigo-500 transition-all duration-300 flex items-center gap-2 shadow-[0_8px_16px_-6px_rgba(79,70,229,0.4)] hover:shadow-[0_12px_20px_-6px_rgba(79,70,229,0.5)] hover:-translate-y-0.5 border border-white/10 overflow-hidden">
                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:scale-110 transition-transform">point_of_sale</span> 
                <span class="relative z-10">Buka Aplikasi POS</span>
            </a>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total Transaksi -->
        <div class="group bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-600 rounded-3xl p-7 shadow-xl shadow-indigo-600/20 relative overflow-hidden text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-indigo-600/30">
            <!-- Decorative Orbs -->
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none transition-transform group-hover:scale-125 duration-500"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-cyan-400/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-8 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-md border border-white/10 shadow-inner">
                    <span class="material-symbols-outlined text-white text-3xl">receipt_long</span>
                </div>
                <span class="px-3 py-1.5 rounded-xl bg-black/20 backdrop-blur-md text-xs font-bold border border-white/10 shadow-sm">
                    Shift Aktif
                </span>
            </div>
            <div class="relative z-10">
                <h3 class="text-indigo-100/90 text-sm font-semibold mb-1 uppercase tracking-wider">Total Transaksi</h3>
                <div class="flex items-baseline gap-2">
                    <p class="text-5xl font-black tracking-tight">{{ $totalStruk }}</p>
                    <p class="text-lg font-medium text-indigo-100">Struk</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Pendapatan -->
        <div class="group bg-white rounded-3xl p-7 border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 relative overflow-hidden transition-all duration-300 hover:-translate-y-1">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-50 rounded-full opacity-60 pointer-events-none group-hover:scale-110 transition-transform duration-500"></div>
            
            <div class="flex items-center justify-between mb-8 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-sm">
                    <span class="material-symbols-outlined text-3xl">account_balance_wallet</span>
                </div>
                <span class="material-symbols-outlined text-slate-300 group-hover:text-emerald-300 transition-colors">trending_up</span>
            </div>
            <div class="relative z-10">
                <h3 class="text-slate-500 text-sm font-semibold mb-1 uppercase tracking-wider">Pendapatan Sementara</h3>
                <p class="text-3xl font-extrabold text-slate-800 tracking-tight">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Card 3: Waktu Transaksi -->
        <div class="group bg-white rounded-3xl p-7 border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 relative overflow-hidden transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between"
             @if(isset($activeShift) && $activeShift)
             x-data="{ 
                startTime: new Date('{{ $activeShift->start_time->toIso8601String() }}').getTime(),
                now: new Date().getTime(),
                get duration() {
                    let diff = Math.floor((this.now - this.startTime) / 1000);
                    if (diff < 0) diff = 0;
                    let h = Math.floor(diff / 3600);
                    let m = Math.floor((diff % 3600) / 60);
                    let s = diff % 60;
                    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }
             }"
             x-init="setInterval(() => { now = new Date().getTime() }, 1000)"
             @endif
        >
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-purple-50 rounded-full opacity-60 pointer-events-none group-hover:scale-110 transition-transform duration-500"></div>
            
            <div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center text-purple-600 border border-purple-100 shadow-sm">
                        <span class="material-symbols-outlined text-3xl">timer</span>
                    </div>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-500 text-sm font-semibold mb-1 uppercase tracking-wider">Durasi Shift Aktif</h3>
                    @if(isset($activeShift) && $activeShift)
                        <p class="text-3xl font-extrabold text-slate-800 tracking-tight font-mono" x-text="duration">00:00:00</p>
                        <p class="text-xs text-slate-500 mt-1">Mulai: {{ $activeShift->start_time->format('H:i') }} WIB</p>
                    @else
                        <p class="text-3xl font-extrabold text-slate-800 tracking-tight">-</p>
                        <p class="text-xs text-slate-500 mt-1">Belum ada shift aktif</p>
                    @endif
                </div>
            </div>
            
            <div class="relative z-10 mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('pos.shifts') }}" class="w-full py-2.5 rounded-xl bg-slate-50 hover:bg-purple-50 text-slate-600 hover:text-purple-700 text-sm font-bold flex items-center justify-center gap-2 transition-colors border border-slate-200 hover:border-purple-200">
                    @if($activeShift)
                        Tutup Shift Kasir <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    @else
                        Buka Shift Kasir <span class="material-symbols-outlined text-[16px]">play_circle</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Analisis Kinerja -->
    <div class="bg-white/40 backdrop-blur-md rounded-3xl p-5 border border-white/60 shadow-sm">
        <div class="flex items-center gap-2 mb-4 px-2">
            <span class="material-symbols-outlined text-indigo-500 text-[20px]">insights</span>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Analisis Kinerja Shift</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Rata-rata Transaksi -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/50 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-[24px]">calculate</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-0.5">Rata-rata Transaksi</p>
                    <p class="text-lg font-bold text-slate-800">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <!-- Barang Terjual -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/50 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-0.5">Total Barang Terjual</p>
                    <div class="flex items-baseline gap-1">
                        <p class="text-lg font-bold text-slate-800">{{ $totalItemsSold }}</p>
                        <p class="text-xs font-medium text-slate-500">item</p>
                    </div>
                </div>
            </div>
            
            <!-- Jam Tersibuk -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/50 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                    <span class="material-symbols-outlined text-[24px]">local_fire_department</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-0.5">Jam Tersibuk</p>
                    <p class="text-lg font-bold text-slate-800">{{ $peakHour }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Chart Section (Spans 2 columns on large screens) -->
        <div x-data="kasirChart()" x-init="$nextTick(() => { initChart() })" class="lg:col-span-2 bg-white rounded-3xl p-1 border border-slate-200/60 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 pb-2 flex justify-between items-center relative z-10 flex-wrap gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <span class="material-symbols-outlined text-[18px]">show_chart</span>
                        </div>
                        <span x-text="chartTitle">Grafik Penjualan Anda</span>
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 ml-10" x-text="chartSubtitle">Pantauan omzet penjualan harian</p>
                </div>
                
                <div class="bg-slate-100 p-1 rounded-xl flex items-center shrink-0">
                    <button @click="setPeriod('harian')" :class="period === 'harian' ? 'bg-white shadow-sm text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 text-xs rounded-lg transition-all">Hari Ini</button>
                    <button @click="setPeriod('mingguan')" :class="period === 'mingguan' ? 'bg-white shadow-sm text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 text-xs rounded-lg transition-all">Minggu Ini</button>
                    <button @click="setPeriod('bulanan')" :class="period === 'bulanan' ? 'bg-white shadow-sm text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 text-xs rounded-lg transition-all">Bulan Ini</button>
                    <button @click="setPeriod('tahunan')" :class="period === 'tahunan' ? 'bg-white shadow-sm text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 text-xs rounded-lg transition-all">Tahun Ini</button>
                </div>
            </div>
            <div class="w-full flex-1 relative px-4 pb-4 min-h-[320px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Recent Sales List (Spans 1 column) -->
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col h-full max-h-[420px]">
            <div class="p-6 border-b border-slate-100 bg-white relative z-10 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.02)]">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                    </div>
                    Transaksi Terakhir
                </h3>
            </div>
            
            <div class="overflow-y-auto flex-1 bg-slate-50/30 p-2">
                @if($recentSales->isEmpty())
                <div class="h-full flex flex-col items-center justify-center text-center p-8">
                    <div class="w-20 h-20 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-300 mb-4 border border-slate-100">
                        <span class="material-symbols-outlined text-4xl">point_of_sale</span>
                    </div>
                    <p class="text-slate-800 font-bold">Belum ada transaksi</p>
                    <p class="text-sm text-slate-500 mt-1">Transaksi yang Anda proses akan muncul di sini.</p>
                </div>
                @else
                <div class="space-y-2">
                    @foreach($recentSales as $sale)
                    <div x-data="{ sale: {{ Js::from($sale) }} }" @click="openModal(sale)" class="bg-white p-4 rounded-2xl border border-slate-200/50 hover:border-indigo-300 hover:shadow-md hover:shadow-indigo-500/5 transition-all duration-200 group flex justify-between items-center cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $sale->payment_method == 'Cash' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }}">
                                <span class="material-symbols-outlined text-[20px]">{{ $sale->payment_method == 'Cash' ? 'payments' : 'credit_card' }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $sale->ref_number }}</p>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $sale->created_at->format('H:i') }} • {{ $sale->payment_method }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-slate-800">Rp{{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                            <button class="text-[10px] font-bold text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity mt-1 flex items-center justify-end w-full gap-0.5">
                                Lihat Detail <span class="material-symbols-outlined text-[12px]">visibility</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            @if(!$recentSales->isEmpty())
            <div class="p-4 border-t border-slate-100 bg-white text-center">
                <a href="{{ route('pos.history.today') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center justify-center gap-1">
                    Lihat Semua Transaksi <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
            @endif
        </div>

    </div>

    <!-- Modal Transaksi -->
    <div x-show="showModal" 
         style="display: none;"
         class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden"
             @click.outside="closeModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
             <!-- Modal Header -->
             <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-500">receipt_long</span>
                        Detail Transaksi
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 font-mono" x-text="selectedSale?.ref_number"></p>
                </div>
                <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
             </div>

             <!-- Modal Body -->
             <div class="p-6">
                <!-- Info List -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-semibold mb-1">Metode Pembayaran</p>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]" :class="selectedSale?.payment_method === 'Cash' ? 'text-emerald-500' : 'text-blue-500'" x-text="selectedSale?.payment_method === 'Cash' ? 'payments' : 'credit_card'"></span>
                            <span class="font-bold text-slate-800" x-text="selectedSale?.payment_method"></span>
                        </div>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-semibold mb-1">Total Belanja</p>
                        <p class="font-black text-slate-800 text-lg" x-text="formatRupiah(selectedSale?.total_amount)"></p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Daftar Barang</span>
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full" x-text="(selectedSale?.lines?.length || 0) + ' Item'"></span>
                    </div>
                    <div class="max-h-[250px] overflow-y-auto p-2 space-y-1">
                            <template x-for="line in (selectedSale?.lines || [])" :key="line.id">
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl hover:bg-slate-50 transition-colors">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-slate-800" x-text="line.item?.name || 'Item tidak diketahui'"></p>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="formatRupiah(line.unit_price) + ' x ' + line.quantity"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-slate-800" x-text="formatRupiah(line.unit_price * line.quantity)"></p>
                                    </div>
                                </div>
                            </template>
                        <template x-if="!selectedSale?.lines || selectedSale.lines.length === 0">
                            <div class="text-center p-4">
                                <p class="text-sm text-slate-500">Tidak ada detail barang.</p>
                            </div>
                        </template>
                    </div>
                </div>
             </div>

             <!-- Modal Footer -->
             <div class="p-6 pt-0 flex gap-3">
                <button @click="printReceipt()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Cetak Struk
                </button>
             </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('posDashboard', () => ({
            showModal: false,
            selectedSale: null,
            openModal(sale) {
                this.selectedSale = sale;
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
                this.selectedSale = null;
            },
            printReceipt() {
                if(this.selectedSale) {
                    window.open(`/pos/receipt/${this.selectedSale.ref_number}`, '_blank', 'width=400,height=600');
                }
            },
            formatRupiah(number) {
                if(!number) return 'Rp0';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            }
        }));

        Alpine.data('kasirChart', () => {
            let chartInstance = null;
            return {
                period: 'harian',
                chartTitle: 'Grafik Penjualan Hari Ini',
                chartSubtitle: 'Pergerakan omzet per jam',
                
                initChart() {
                    const canvas = document.getElementById('salesChart');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    
                    // Create Gradient
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.01)');
                    
                    chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Nominal Penjualan (Rp)',
                            data: [],
                            borderColor: '#4F46E5', // Indigo 600
                            backgroundColor: gradient,
                            borderWidth: 3,
                            tension: 0.5, // Smoother curve
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4F46E5',
                            pointBorderWidth: 2,
                            pointRadius: 4, // Show points so flat lines are visible
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#4F46E5',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 2,
                            pointHitRadius: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)', // Slate 900
                                titleColor: '#F8FAFC',
                                bodyColor: '#F8FAFC',
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: { size: 13, family: "'Instrument Sans', sans-serif" },
                                bodyFont: { size: 14, weight: 'bold', family: "'Instrument Sans', sans-serif" },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        let value = context.raw;
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#F1F5F9', drawBorder: false, borderDash: [5, 5] },
                                ticks: {
                                    color: '#94A3B8',
                                    font: { family: "'Instrument Sans', sans-serif", size: 11, weight: '500' },
                                    padding: 12,
                                    callback: function(value) {
                                        if (value === 0) return '0';
                                        if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                        if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                        return value;
                                    }
                                },
                                border: { display: false }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: '#64748B', font: { family: "'Instrument Sans', sans-serif", size: 12, weight: '500' }, padding: 8 },
                                border: { display: false }
                            }
                        }
                    }
                });

                // Load initial data (harian)
                this.fetchData('harian');
            },
            
            setPeriod(newPeriod) {
                this.period = newPeriod;
                
                // Update titles
                if(newPeriod === 'harian') {
                    this.chartTitle = 'Grafik Penjualan Hari Ini';
                    this.chartSubtitle = 'Pergerakan omzet per jam';
                } else if(newPeriod === 'mingguan') {
                    this.chartTitle = 'Grafik Penjualan Minggu Ini';
                    this.chartSubtitle = 'Total penjualan harian 7 hari terakhir';
                } else if(newPeriod === 'bulanan') {
                    this.chartTitle = 'Grafik Penjualan Bulan Ini';
                    this.chartSubtitle = 'Total penjualan harian bulan ini';
                } else if(newPeriod === 'tahunan') {
                    this.chartTitle = 'Grafik Penjualan Tahun Ini';
                    this.chartSubtitle = 'Total penjualan bulanan tahun ini';
                }

                this.fetchData(newPeriod);
            },
            
            async fetchData(period) {
                try {
                    const response = await fetch(`{{ route('kasir.chart') }}?period=${period}`);
                    const json = await response.json();
                    
                    if(chartInstance) {
                        chartInstance.data.labels = json.labels;
                        chartInstance.data.datasets[0].data = json.data;
                        chartInstance.update();
                    }
                } catch(error) {
                    console.error('Error fetching chart data:', error);
                }
            }
        };
        });
    });
</script>
@endpush
