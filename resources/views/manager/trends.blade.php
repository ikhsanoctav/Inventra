@extends('layouts.app')

@section('header_title', 'Tren Penjualan')

@section('main_content')
<div class="space-y-6" x-data="trendsDashboard()">
    <!-- Header & Period Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[26px]">insights</span>
                <h2 class="font-headline text-2xl font-bold text-slate-800">Tren Penjualan Historis</h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">Analisis tren pergerakan penjualan barang dan proyeksi performa bisnis.</p>
        </div>
        
        <!-- Period Switcher Pills (Harian / Mingguan / Bulanan) -->
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200 text-xs font-bold self-start sm:self-auto shrink-0 shadow-inner">
            <button type="button" 
                    @click="setPeriod('harian')" 
                    :class="activePeriod === 'harian' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">today</span>
                <span>Harian</span>
            </button>
            <button type="button" 
                    @click="setPeriod('mingguan')" 
                    :class="activePeriod === 'mingguan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">date_range</span>
                <span>Mingguan</span>
            </button>
            <button type="button" 
                    @click="setPeriod('bulanan')" 
                    :class="activePeriod === 'bulanan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" 
                    class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                <span>Bulanan</span>
            </button>
        </div>
    </div>

    <!-- Dynamic Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Growth Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden transition-all hover:border-indigo-200 hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1" x-text="currentPeriodData.growthLabel || 'Pertumbuhan'"></p>
                <div class="flex items-baseline gap-2">
                    <h4 class="text-2xl sm:text-3xl font-black text-slate-800 font-mono" x-text="(currentPeriodData.growth >= 0 ? '+' : '') + currentPeriodData.growth + '%'"></h4>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full" 
                          :class="currentPeriodData.growth >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'" 
                          x-text="currentPeriodData.growth >= 0 ? 'Meningkat' : 'Menurun'"></span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" 
                 :class="currentPeriodData.growth >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                <span class="material-symbols-outlined text-[24px]" x-text="currentPeriodData.growth >= 0 ? 'trending_up' : 'trending_down'"></span>
            </div>
        </div>
        
        <!-- Avg Daily / Transaction Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden transition-all hover:border-indigo-200 hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1" x-text="currentPeriodData.avgLabel || 'Rata-rata Transaksi'"></p>
                <h4 class="text-2xl sm:text-3xl font-black text-slate-800 font-mono" x-text="currentPeriodData.avgDaily"></h4>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">analytics</span>
            </div>
        </div>
        
        <!-- Top Product Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden transition-all hover:border-indigo-200 hover:shadow-md">
            <div class="max-w-[70%]">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1" x-text="currentPeriodData.topProductLabel || 'Produk Terlaris'"></p>
                <h4 class="text-base sm:text-lg font-bold text-slate-800 truncate" :title="currentPeriodData.topProduct" x-text="currentPeriodData.topProduct"></h4>
                <p class="text-xs text-indigo-600 font-semibold mt-0.5">Kontribusi Penjualan Tertinggi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">star</span>
            </div>
        </div>
    </div>

    <!-- Chart Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2" x-text="currentPeriodData.title || 'Grafik Tren Penjualan'">
                    <span class="material-symbols-outlined text-indigo-600">show_chart</span>
                    Grafik Penjualan
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Nilai transaksi penjualan dalam satuan Juta Rupiah (Rp).</p>
            </div>

            <!-- Legend Badge -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100 self-start sm:self-auto">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                <span>Nilai Penjualan (Juta Rp)</span>
            </div>
        </div>

        <div class="h-80 w-full relative">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <!-- Data Breakdown Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 font-headline flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600 text-[20px]">table_chart</span>
                    Rincian Pergerakan Penjualan
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Tabel distribusi nominal transaksi berdasarkan interval waktu yang dipilih.</p>
            </div>
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase" x-text="activePeriod"></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-xs uppercase text-slate-400 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Interval Waktu</th>
                        <th class="px-6 py-3.5 text-right">Nominal Penjualan</th>
                        <th class="px-6 py-3.5 text-right">Nilai dalam Juta</th>
                        <th class="px-6 py-3.5 text-center">Status Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <template x-for="(label, idx) in currentPeriodData.labels" :key="label + '-' + idx">
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5 font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="(currentPeriodData.data[idx] > 0) ? 'bg-indigo-600' : 'bg-slate-300'"></span>
                                <span x-text="label"></span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(currentPeriodData.raw_data ? currentPeriodData.raw_data[idx] : (currentPeriodData.data[idx] * 1000000))"></td>
                            <td class="px-6 py-3.5 text-right font-mono text-indigo-600 font-bold" x-text="currentPeriodData.data[idx] + ' Jt'"></td>
                            <td class="px-6 py-3.5 text-center">
                                <template x-if="currentPeriodData.data[idx] > 0">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Ada Transaksi
                                    </span>
                                </template>
                                <template x-if="currentPeriodData.data[idx] <= 0">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                        Nihil
                                    </span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('trendsDashboard', () => ({
            activePeriod: 'bulanan',
            allData: @json($trendData ?? []),
            currentPeriodData: {},
            chartInstance: null,

            init() {
                // Ensure data is structured properly
                if (!this.allData || !this.allData.bulanan) {
                    this.allData = {
                        harian: { labels: [], data: [], raw_data: [], growth: 0, growthLabel: 'Pertumbuhan', avgDaily: 'Rp 0', avgLabel: 'Total Penjualan', topProduct: '-', topProductLabel: 'Produk Terlaris', title: 'Grafik Penjualan Hari Ini' },
                        mingguan: { labels: [], data: [], raw_data: [], growth: 0, growthLabel: 'Pertumbuhan', avgDaily: 'Rp 0', avgLabel: 'Rata-rata Penjualan', topProduct: '-', topProductLabel: 'Produk Terlaris', title: 'Grafik Penjualan 7 Hari Terakhir' },
                        bulanan: { labels: [], data: [], raw_data: [], growth: 0, growthLabel: 'Pertumbuhan', avgDaily: 'Rp 0', avgLabel: 'Rata-rata Transaksi', topProduct: '-', topProductLabel: 'Produk Terlaris', title: 'Grafik Penjualan 6 Bulan Terakhir' }
                    };
                }

                this.setPeriod('bulanan');

                // Lifecycle hooks
                document.addEventListener('htmx:afterOnLoad', () => {
                    this.renderChart();
                });
            },

            setPeriod(period) {
                this.activePeriod = period;
                this.currentPeriodData = this.allData[period] || {};
                this.$nextTick(() => {
                    this.renderChart();
                });
            },

            renderChart() {
                const canvas = document.getElementById('trendsChart');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }

                const labels = this.currentPeriodData.labels || [];
                const data = this.currentPeriodData.data || [];

                let gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.45)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0.01)');
                
                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Penjualan (Juta Rp)',
                            data: data,
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2.5,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: '#4f46e5',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 2,
                            fill: true,
                            tension: 0.35
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 500,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                cornerRadius: 10,
                                titleFont: { size: 13, weight: 'bold', family: "'Outfit', sans-serif" },
                                bodyFont: { size: 13, family: "'Outfit', sans-serif" },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        const rawVal = context.parsed.y;
                                        return 'Rp ' + rawVal + ' Juta (Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(rawVal * 1000000)) + ')';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(241, 245, 249, 0.9)',
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: { family: "'Outfit', sans-serif", size: 11 },
                                    callback: function(value) {
                                        return value + ' Jt';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: { family: "'Outfit', sans-serif", size: 11 }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                    }
                });
            }
        }));
    });
</script>
@endsection
