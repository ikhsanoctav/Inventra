@extends('layouts.app')

@section('header_title', 'Tren Penjualan')

@section('main_content')
<div class="space-y-6" x-data="trendsDashboard()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Tren Penjualan Historis</h2>
            <p class="text-sm text-[#6B7280]">Analitik tren pergerakan penjualan dan proyeksi masa depan.</p>
        </div>
        <div class="flex gap-3">
            <select class="pl-4 pr-10 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white shadow-sm disabled:opacity-50" :disabled="isLoading">
                <option>6 Bulan Terakhir</option>
            </select>
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Growth Metric -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden">
            <!-- Skeleton -->
            <div x-show="isLoading" class="absolute inset-0 bg-white z-10 p-6 flex items-center justify-between">
                <div class="w-full">
                    <div class="h-4 bg-slate-200 rounded w-1/2 mb-3 animate-pulse"></div>
                    <div class="h-8 bg-slate-200 rounded w-3/4 animate-pulse"></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-slate-100 animate-pulse shrink-0"></div>
            </div>
            <!-- Content -->
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Pertumbuhan Kuartal Ini</p>
                <h4 class="text-2xl font-bold text-slate-800" x-text="'+' + growth + '%'"></h4>
            </div>
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">trending_up</span>
            </div>
        </div>
        
        <!-- Avg Daily Metric -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden">
            <!-- Skeleton -->
            <div x-show="isLoading" class="absolute inset-0 bg-white z-10 p-6 flex items-center justify-between">
                <div class="w-full">
                    <div class="h-4 bg-slate-200 rounded w-1/2 mb-3 animate-pulse"></div>
                    <div class="h-8 bg-slate-200 rounded w-3/4 animate-pulse"></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-slate-100 animate-pulse shrink-0"></div>
            </div>
            <!-- Content -->
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Rata-rata Transaksi Harian</p>
                <h4 class="text-2xl font-bold text-slate-800" x-text="avgDaily"></h4>
            </div>
            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">analytics</span>
            </div>
        </div>
        
        <!-- Top Product Metric -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden">
            <!-- Skeleton -->
            <div x-show="isLoading" class="absolute inset-0 bg-white z-10 p-6 flex items-center justify-between">
                <div class="w-full">
                    <div class="h-4 bg-slate-200 rounded w-1/2 mb-3 animate-pulse"></div>
                    <div class="h-8 bg-slate-200 rounded w-3/4 animate-pulse"></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-slate-100 animate-pulse shrink-0"></div>
            </div>
            <!-- Content -->
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Produk Terlaris</p>
                <h4 class="text-2xl font-bold text-slate-800" x-text="topProduct"></h4>
            </div>
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">star</span>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8 relative overflow-hidden">
        
        <!-- Skeleton -->
        <div x-show="isLoading" class="absolute inset-0 bg-white z-10 p-8 flex flex-col">
            <div class="h-6 bg-slate-200 rounded w-1/3 mb-8 animate-pulse"></div>
            <!-- Mock Chart Bars for skeleton -->
            <div class="flex-1 flex items-end justify-between gap-4 pt-4">
                <div class="w-full bg-slate-100 h-1/3 rounded-t-lg animate-pulse"></div>
                <div class="w-full bg-slate-100 h-1/2 rounded-t-lg animate-pulse" style="animation-delay: 100ms"></div>
                <div class="w-full bg-slate-100 h-2/5 rounded-t-lg animate-pulse" style="animation-delay: 200ms"></div>
                <div class="w-full bg-slate-100 h-3/5 rounded-t-lg animate-pulse" style="animation-delay: 300ms"></div>
                <div class="w-full bg-slate-100 h-4/5 rounded-t-lg animate-pulse" style="animation-delay: 400ms"></div>
                <div class="w-full bg-slate-100 h-full rounded-t-lg animate-pulse" style="animation-delay: 500ms"></div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800 font-headline">Grafik Penjualan 6 Bulan Terakhir</h3>
        </div>
        <div class="h-80 w-full relative">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('trendsDashboard', () => ({
            isLoading: true,
            growth: 0,
            avgDaily: 0,
            topProduct: '-',
            chartInstance: null,

            async init() {
                // Simulate a slight delay so user can see the skeleton loader (optional, for effect)
                await new Promise(r => setTimeout(r, 800));
                await this.fetchData();
            },

            async fetchData() {
                try {
                    const response = await fetch("{{ route('manager.trends') }}", {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.growth = data.growth;
                        this.avgDaily = data.avgDaily;
                        this.topProduct = data.topProduct;
                        
                        this.renderChart(data.labels, data.data);
                    }
                } catch (error) {
                    console.error('Failed to fetch trend data:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            renderChart(labels, data) {
                const ctx = document.getElementById('trendsChart').getContext('2d');
                
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.5)'); // Indigo 600
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
                
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
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                titleFont: { size: 14, family: "'Outfit', sans-serif" },
                                bodyFont: { size: 13, family: "'Outfit', sans-serif" },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y + ' Juta';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: { family: "'Outfit', sans-serif" }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: { family: "'Outfit', sans-serif" }
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
