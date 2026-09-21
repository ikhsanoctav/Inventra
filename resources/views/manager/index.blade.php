@extends('layouts.app')

@section('header_title', 'Manager Dashboard')

@section('main_content')
<div class="space-y-6">
    <!-- Header Greeting -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold font-headline">Advanced Analytics</h2>
            <p class="text-indigo-100 mt-1">Ringkasan performa inventaris dan transaksi periode ini.</p>
        </div>
        <!-- Decorative bg -->
        <div class="absolute right-0 top-0 w-64 h-full bg-white opacity-10 skew-x-12 translate-x-16"></div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Sales Volume -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-[#6B7280]">Total Transaksi POS</p>
                    <h3 class="text-2xl font-bold text-[#111827] mt-1 font-mono">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-emerald-600">
                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span>
                <span>+12.5% vs bulan lalu</span>
            </div>
        </div>

        <!-- Inventory Value -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-[#6B7280]">Total Stok Fisik</p>
                    <h3 class="text-2xl font-bold text-[#111827] mt-1 font-mono">{{ number_format($totalStock, 0, ',', '.') }} <span class="text-sm text-gray-500 font-sans">unit</span></h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-[20px]">inventory</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-blue-600">
                <span class="material-symbols-outlined text-[16px] mr-1">inventory_2</span>
                <span>Tersebar di {{ $totalItems }} SKU</span>
            </div>
        </div>

        <!-- Transaction Count -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-[#6B7280]">Total Order</p>
                    <h3 class="text-2xl font-bold text-[#111827] mt-1">{{ number_format($transactionCount, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-gray-500">
                <span>Inbound & Outbound</span>
            </div>
        </div>

        <!-- Alerts -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-[#6B7280]">Peringatan Stok</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $criticalStock }} <span class="text-sm font-sans font-medium text-gray-500">SKU kritis</span></h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-red-600">
                <span>Butuh restock segera!</span>
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-[#E5E7EB] p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Tren Transaksi (Mingguan)</h3>
                <button class="text-sm text-indigo-600 font-semibold hover:text-indigo-700">Lihat Detail</button>
            </div>
            <div class="relative h-64 w-full bg-gray-50 rounded-lg border border-dashed border-gray-200 flex items-end px-4 pt-4 pb-0 gap-2 items-center justify-center">
                <!-- Using Chart.js via CDN would be better, but building a mock visual for now -->
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- System Activity or RBL Triggers -->
        <div class="bg-white rounded-xl shadow-sm border border-[#E5E7EB] p-5">
            <h3 class="font-bold text-gray-900 mb-4">Log Sistem & RBL Terakhir</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Rule "Peringatan Stok Rendah" dipicu</p>
                        <p class="text-xs text-gray-500">2 menit yang lalu</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">POS Transaksi POS-X9V2 berhasil</p>
                        <p class="text-xs text-gray-500">15 menit yang lalu</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Barang Masuk (Inbound) PO-102 selesai</p>
                        <p class="text-xs text-gray-500">1 jam yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($salesData['labels']) !!},
                    datasets: [{
                        label: 'Transaksi',
                        data: {!! json_encode($salesData['data']) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: true, color: '#f3f4f6' }, border: { dash: [4, 4] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
