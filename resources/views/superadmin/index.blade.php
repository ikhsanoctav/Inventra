@extends('layouts.app')

@section('header_title', 'Overview')

@section('main_content')
<!-- CONTENT CANVAS -->
<div class="space-y-7" 
     x-data="superAdminDashboard()" 
     x-init="initDashboard()">
    
    <!-- DASHBOARD HEADER & QUICK ACTIONS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-1">
        <div>
            <h1 class="font-headline text-2xl font-bold text-[#111827] tracking-tight">Dashboard Super Admin</h1>
            <p class="font-body-md text-sm text-[#6B7280] mt-1">Pantau kondisi inventaris, alur pergerakan stok, dan aktivitas sistem secara menyeluruh di seluruh unit.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('master.items') }}" class="h-9 px-3 rounded-lg bg-white border border-[#E5E7EB] hover:bg-[#F8FAFC] hover:border-[#D1D5DB] text-[#1F2937] font-medium text-xs flex items-center gap-2 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[#6B7280] text-[18px]">add_box</span>
                <span>Tambah Barang Baru</span>
            </a>
            <a href="{{ route('master.suppliers') }}" class="h-9 px-3 rounded-lg bg-white border border-[#E5E7EB] hover:bg-[#F8FAFC] hover:border-[#D1D5DB] text-[#1F2937] font-medium text-xs flex items-center gap-2 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[#6B7280] text-[18px]">domain_add</span>
                <span>Tambah Supplier</span>
            </a>
            <a href="{{ route('system.users') }}" class="h-9 px-3 rounded-lg bg-white border border-[#E5E7EB] hover:bg-[#F8FAFC] hover:border-[#D1D5DB] text-[#1F2937] font-medium text-xs flex items-center gap-2 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[#6B7280] text-[18px]">person_add</span>
                <span>Tambah Pengguna</span>
            </a>
            <button class="h-9 px-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-semibold text-xs flex items-center gap-2 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[18px]">file_download</span>
                <span>Export Laporan Konsolidasi</span>
            </button>
        </div>
    </div>

    <!-- SYSTEM OVERVIEW STATISTIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 relative">
        <!-- Optional global loading indicator for first fetch -->
        <div x-show="loading" class="absolute right-0 -top-8 flex items-center gap-1.5 text-xs font-semibold text-indigo-600">
            <span class="material-symbols-outlined text-[16px] animate-spin">refresh</span>
            <span>Memperbarui Data...</span>
        </div>

        <!-- Metric 1: Total Barang -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm relative overflow-hidden group hover:border-[#D1D5DB] transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Total SKU Barang</span>
                <div class="w-8 h-8 rounded-lg bg-[#EFF6FF] text-[#1D4ED8] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">category</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] leading-8 font-bold text-[#111827] font-mono tracking-tight" x-text="stats.totalItemsFormatted">{{ number_format($totalItems, 0, ',', '.') }}</span>
                <span class="text-xs text-[#6B7280]">Item</span>
            </div>
            <div class="mt-3.5 flex items-center justify-between text-xs pt-3 border-t border-[#F1F5F9]">
                <span class="inline-flex items-center gap-1 font-semibold text-[#166534]">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    +5.2% MoM
                </span>
                <span class="text-[#9CA3AF]">vs bulan sebelumnya</span>
            </div>
        </div>
        <!-- Metric 2: Total Stok Terdistribusi -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm relative overflow-hidden group hover:border-[#D1D5DB] transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Stok Terdistribusi</span>
                <div class="w-8 h-8 rounded-lg bg-[#FFF7ED] text-orange-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">warehouse</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] leading-8 font-bold text-[#111827] font-mono tracking-tight" x-text="stats.totalStockFormatted">{{ number_format($totalStock, 0, ',', '.') }}</span>
                <span class="text-xs text-[#6B7280]">Unit</span>
            </div>
            <div class="mt-3.5 flex items-center justify-between text-xs pt-3 border-t border-[#F1F5F9]">
                <div class="flex items-center gap-1.5 w-full">
                    <div class="flex-1 bg-[#F1F5F9] h-2 rounded-full overflow-hidden">
                        <div class="bg-orange-500 h-full rounded-full transition-all duration-1000 ease-in-out" :style="`width: ${stats.totalStock > 0 ? '92' : '0'}%`"></div>
                    </div>
                    <span class="font-medium text-[#374151] text-[11px] whitespace-nowrap">Kapasitas Dinamis</span>
                </div>
            </div>
        </div>
        <!-- Metric 3: Rekanan Supplier -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm relative overflow-hidden group hover:border-[#D1D5DB] transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Rekanan Supplier</span>
                <div class="w-8 h-8 rounded-lg bg-[#F0FDF4] text-[#15803D] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] leading-8 font-bold text-[#111827] font-mono tracking-tight" x-text="stats.totalSuppliers">{{ $totalSuppliers }}</span>
                <span class="text-xs text-[#6B7280]">Vendor Aktif</span>
            </div>
            <div class="mt-3.5 flex items-center justify-between text-xs pt-3 border-t border-[#F1F5F9]">
                <span class="inline-flex items-center gap-1 font-semibold text-[#166534]">
                    <span class="material-symbols-outlined text-[16px]">verified</span>
                    98% SLA On-Time
                </span>
                <span class="text-[#9CA3AF]">Semua kontrak valid</span>
            </div>
        </div>
        <!-- Metric 4: Pengguna Sistem -->
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-sm relative overflow-hidden group hover:border-[#D1D5DB] transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Pengguna Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-[#FAF5FF] text-[#7E22CE] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] leading-8 font-bold text-[#111827] font-mono tracking-tight" x-text="stats.totalUsers">{{ $totalUsers }}</span>
                <span class="text-xs text-[#6B7280]">User Terdaftar</span>
            </div>
            <div class="mt-3.5 flex items-center justify-between text-xs pt-3 border-t border-[#F1F5F9]">
                <span class="inline-flex items-center gap-1 font-semibold text-[#0369A1]">
                    <span class="w-2 h-2 rounded-full bg-[#0EA5E9] inline-block relative">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-[#0EA5E9] opacity-75 animate-ping"></span>
                    </span>
                    <span x-text="stats.activeUsers">{{ $activeUsers }}</span>&nbsp;Sedang Aktif
                </span>
                <span class="text-[#9CA3AF]">Akses terpantau aman</span>
            </div>
        </div>
    </div>

    <!-- INVENTORY ANALYTICS & CATEGORY DISTRIBUTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Main Chart (65% width = 8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-xl border border-[#E5E7EB] p-6 shadow-sm flex flex-col justify-between">
            <div>
                <!-- Header Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#F1F5F9] pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-[#111827]">Pergerakan Inventaris &amp; Valuasi</h3>
                        <p class="text-sm text-[#6B7280] mt-0.5">Analisis arus masuk, arus keluar, dan valuasi aset berjalan</p>
                    </div>
                    <!-- Time Filter Buttons -->
                    <div class="inline-flex p-1 bg-[#F1F5F9] rounded-lg border border-[#E2E8F0]">
                        <button @click="period = 7; fetchStats()" :class="period === 7 ? 'bg-white text-[#111827] shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors">7 Hari</button>
                        <button @click="period = 30; fetchStats()" :class="period === 30 ? 'bg-white text-[#111827] shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors">30 Hari</button>
                        <button @click="period = 90; fetchStats()" :class="period === 90 ? 'bg-white text-[#111827] shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors">3 Bulan</button>
                        <button @click="period = 365; fetchStats()" :class="period === 365 ? 'bg-white text-[#111827] shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors">1 Tahun</button>
                    </div>
                </div>
                <!-- Metric Summary Pill Row -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 my-5 bg-[#F8FAFC] p-3.5 rounded-lg border border-[#E5E7EB]">
                    <div>
                        <p class="text-[11px] uppercase font-semibold text-[#6B7280]">Total Barang Masuk</p>
                        <p class="text-lg font-bold text-[#166534] font-mono mt-0.5" x-text="stats.totalInboundFormatted">Rp {{ number_format($totalInbound, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-[#16A34A] flex items-center gap-0.5 font-medium">
                            <span class="material-symbols-outlined text-[14px]">arrow_upward</span> +14.2% vs target
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase font-semibold text-[#6B7280]">Total Barang Keluar</p>
                        <p class="text-lg font-bold text-orange-600 font-mono mt-0.5" x-text="stats.totalOutboundFormatted">Rp {{ number_format($totalOutbound, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-[#6B7280] flex items-center gap-0.5">
                            Terpenuhi 96.4% requisition
                        </span>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-[11px] uppercase font-semibold text-[#6B7280]">Nilai Net Aset Gudang</p>
                        <p class="text-lg font-bold text-[#111827] font-mono mt-0.5" x-text="stats.netValuationFormatted">Rp {{ number_format($netValuation, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-[#3B82F6] font-medium">Audit Valuasi Q3 Valid</span>
                    </div>
                </div>
                <!-- Simulated High-Precision Modern SVG ERP Chart -->
                <div class="relative h-64 w-full pt-2">
                    <canvas id="inventoryChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Bottom Legend -->
            <div class="flex items-center justify-end gap-6 pt-3 mt-2 border-t border-[#F1F5F9] text-xs font-semibold">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-sm bg-indigo-600 inline-block"></span>
                    <span class="text-[#374151]">Barang Masuk (Inbound Procurement)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span>
                    <span class="text-[#374151]">Barang Keluar (Dispatched Orders)</span>
                </div>
            </div>
        </div>

        <!-- Category Distribution (35% width = 4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-xl border border-[#E5E7EB] p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-[#111827]">Distribusi Kategori</h3>
                        <p class="text-sm text-[#6B7280] mt-0.5">Proporsi volume fisik barang terdaftar</p>
                    </div>
                    <button class="p-1 rounded-md text-[#9CA3AF] hover:text-[#111827] hover:bg-[#F1F5F9]">
                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                    </button>
                </div>
                <!-- Distribution Horizontal Progress Metrics -->
                <div class="space-y-4 my-5">
                    @php $colors = ['indigo', 'blue', 'green', 'purple', 'rose', 'amber']; @endphp
                    @foreach($categories as $index => $category)
                        @php
                            $stock = $category->items_sum_stock ?? 0;
                            $percentage = $totalStock > 0 ? round(($stock / $totalStock) * 100) : 0;
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1.5">
                                <span class="text-[#1F2937] flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-{{$color}}-600"></span>{{ $category->name }}</span>
                                <span class="font-mono font-bold text-[#111827]">{{ $percentage }}% ({{ number_format($stock, 0, ',', '.') }} Unit)</span>
                            </div>
                            <div class="w-full bg-[#F1F5F9] h-2 rounded-full overflow-hidden">
                                <div class="bg-{{$color}}-600 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Micro Insight Strip -->
            <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100 text-xs text-indigo-800 flex items-start gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[18px] shrink-0 mt-0.5">lightbulb</span>
                <p>Perputaran kategori <strong>Elektronik &amp; IT</strong> mengalami peningkatan permintaan 12% menjelang siklus pembaruan akhir tahun anggaran.</p>
            </div>
        </div>
    </div>

    <!-- STOCK HEALTH OVERVIEW CARDS -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#6B7280]">Status Kesehatan Stok Fisik</h2>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                <span class="text-xs text-[#6B7280]">Sinkronisasi Realtime Aktif</span>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Health 1: Stok Aman -->
            <div class="bg-white p-4 rounded-xl border border-[#BBF7D0] bg-gradient-to-br from-white to-[#F0FDF4] shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-[#166534] flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>Stok Aman</span>
                    <p class="text-2xl font-bold text-[#111827] mt-1 font-mono" x-text="stats.safeStock">{{ $totalItems - $criticalStock }}</p>
                    <p class="text-[11px] text-[#6B7280] mt-0.5">Item berada di batas buffer</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#DCFCE7] text-[#15803D] flex items-center justify-center">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
            </div>
            <!-- Health 2: Stok Menipis -->
            <div class="bg-white p-4 rounded-xl border border-[#FDE68A] bg-gradient-to-br from-white to-[#FFFBEB] shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-[#92400E] flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>Stok Kritis</span>
                    <p class="text-2xl font-bold text-[#111827] mt-1 font-mono" x-text="stats.criticalStock">{{ $criticalStock }}</p>
                    <p class="text-[11px] text-[#6B7280] mt-0.5">Memerlukan PO Pengadaan</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#FEF3C7] text-[#B45309] flex items-center justify-center">
                    <span class="material-symbols-outlined">warning</span>
                </div>
            </div>
            <!-- Health 3: Stok Habis -->
            <div class="bg-white p-4 rounded-xl border border-[#FECACA] bg-gradient-to-br from-white to-[#FEF2F2] shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-[#991B1B] flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>Stok Habis</span>
                    <p class="text-2xl font-bold text-[#111827] mt-1 font-mono">0</p>
                    <p class="text-[11px] text-[#6B7280] mt-0.5">Kritis untuk operasional</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#FEE2E2] text-[#B91C1C] flex items-center justify-center">
                    <span class="material-symbols-outlined">error</span>
                </div>
            </div>
            <!-- Health 4: Mendekati Kadaluarsa / EOL -->
            <div class="bg-white p-4 rounded-xl border border-[#FFEDD5] bg-gradient-to-br from-white to-[#FFF7ED] shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-orange-700 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-500"></span>Kadaluarsa / EOL</span>
                    <p class="text-2xl font-bold text-[#111827] mt-1 font-mono">34</p>
                    <p class="text-[11px] text-[#6B7280] mt-0.5">Siklus &lt; 45 hari kedepan</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#FFEDD5] text-orange-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">event_busy</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY AUDIT FEED & TOP INVENTORY TABLE -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Left: Aktivitas Audit Terkini (40% = 5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-xl border border-[#E5E7EB] p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3.5">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-600 text-[20px]">assignment</span>
                        <h3 class="text-lg font-bold text-[#111827]">Aktivitas Audit Terkini</h3>
                    </div>
                    <a class="text-xs font-semibold text-indigo-600 hover:underline" href="javascript:void(0)">Lihat Semua</a>
                </div>
                <!-- Timeline items -->
                <div class="mt-4 space-y-4">
                    <!-- Audit Event 1 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#EFF6FF] border border-[#BFDBFE] text-[#1E40AF] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[16px]">edit_note</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-[#111827] truncate">Penyesuaian Stok SKU-IT-0082</p>
                                <span class="text-[11px] font-mono text-[#9CA3AF]">Baru saja</span>
                            </div>
                            <p class="text-xs text-[#4B5563] mt-0.5">Penambahan +20 Unit (Laptop ThinkPad L14) via Audit Fisik Triwulan.</p>
                            <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#6B7280]">
                                <span class="px-1.5 py-0.2 bg-[#F1F5F9] rounded font-medium">Budi Santoso</span>
                                <span>•</span>
                                <span>Supervisor Gudang 1</span>
                            </div>
                        </div>
                    </div>
                    <!-- Audit Event 2 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#F0FDF4] border border-[#BBF7D0] text-[#166534] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-[#111827] truncate">Approval PO Pengadaan #PO-2024-991</p>
                                <span class="text-[11px] font-mono text-[#9CA3AF]">12m lalu</span>
                            </div>
                            <p class="text-xs text-[#4B5563] mt-0.5">Disetujui untuk vendor <strong>PT Sentra Medika Tama</strong> (Rp 84.500.000).</p>
                            <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#6B7280]">
                                <span class="px-1.5 py-0.2 bg-[#F1F5F9] rounded font-medium">Dr. Hendra Wijaya</span>
                                <span>•</span>
                                <span class="text-indigo-600 font-semibold">Super Admin</span>
                            </div>
                        </div>
                    </div>
                    <!-- Audit Event 3 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[16px]">security</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-[#111827] truncate">Pembaruan Hak Akses User</p>
                                <span class="text-[11px] font-mono text-[#9CA3AF]">45m lalu</span>
                            </div>
                            <p class="text-xs text-[#4B5563] mt-0.5">Promosi akun <strong>Rina Kartika</strong> dari Petugas Lapangan ke Admin Regional.</p>
                            <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#6B7280]">
                                <span class="px-1.5 py-0.2 bg-[#F1F5F9] rounded font-medium">System Security Bot</span>
                                <span>•</span>
                                <span>Automasi RBAC</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[#F1F5F9] flex items-center justify-between text-xs text-[#6B7280]">
                <span>Integritas Log: Terenkripsi SHA-256</span>
                <span class="font-mono text-[#166534] font-semibold">ISO 27001 Terverifikasi</span>
            </div>
        </div>

        <!-- Right: Top Inventory Table (60% = 7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between overflow-hidden">
            <div>
                <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-lg font-bold text-[#111827]">Inventaris dengan Perputaran Tertinggi</h3>
                        <p class="text-sm text-[#6B7280] mt-0.5">SKU paling dinamis dalam alur distribusi 30 hari terakhir</p>
                    </div>
                    <button class="h-8 px-2.5 text-xs font-medium text-[#1F2937] bg-[#F8FAFC] border border-[#E5E7EB] rounded-lg hover:bg-[#F1F5F9] flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">filter_list</span>
                        <span>Filter</span>
                    </button>
                </div>
                <!-- Pristine Linear/ERP Style Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E5E7EB] h-10">
                                <th class="pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-[#6B7280]">Nama Barang / SKU</th>
                                <th class="px-3 text-[11px] font-semibold uppercase tracking-wider text-[#6B7280]">Kategori</th>
                                <th class="px-3 text-[11px] font-semibold uppercase tracking-wider text-[#6B7280] text-right">Stok Terkini</th>
                                <th class="px-3 text-[11px] font-semibold uppercase tracking-wider text-[#6B7280] text-center">Status</th>
                                <th class="pr-5 pl-3 text-[11px] font-semibold uppercase tracking-wider text-[#6B7280] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-xs">
                            <tr class="hover:bg-[#F8FAFC] transition-colors h-12">
                                <td class="pl-5 pr-3">
                                    <div>
                                        <span class="font-semibold text-[#111827]">Laptop ThinkPad L14 Gen 4</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded font-mono text-[10px] bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">SKU-IT-0082</span>
                                            <span class="text-[10px] text-[#9CA3AF]">Gudang A</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 text-[#4B5563]">Elektronik &amp; IT</td>
                                <td class="px-3 text-right font-bold text-[#111827] font-mono">48 <span class="text-[11px] font-normal text-[#6B7280]">Unit</span></td>
                                <td class="px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-[#F0FDF4] border border-[#BBF7D0] text-[#166534]">Stok Aman</span>
                                </td>
                                <td class="pr-5 pl-3 text-center">
                                    <button class="p-1 hover:bg-[#F1F5F9] rounded text-[#6B7280] hover:text-[#111827]">
                                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-[#F8FAFC] transition-colors h-12">
                                <td class="pl-5 pr-3">
                                    <div>
                                        <span class="font-semibold text-[#111827]">Kertas HVS A4 80gr PaperOne</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded font-mono text-[10px] bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">SKU-ATK-0194</span>
                                            <span class="text-[10px] text-[#9CA3AF]">Gudang B</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 text-[#4B5563]">Alat Tulis Kantor</td>
                                <td class="px-3 text-right font-bold text-[#92400E] font-mono">12 <span class="text-[11px] font-normal text-[#6B7280]">Rim</span></td>
                                <td class="px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E]">Stok Menipis</span>
                                </td>
                                <td class="pr-5 pl-3 text-center">
                                    <button class="p-1 hover:bg-[#F1F5F9] rounded text-[#6B7280] hover:text-[#111827]">
                                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-[#F8FAFC] transition-colors h-12">
                                <td class="pl-5 pr-3">
                                    <div>
                                        <span class="font-semibold text-[#111827]">Masker Medis 3-Ply Earloop</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded font-mono text-[10px] bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">SKU-MED-0031</span>
                                            <span class="text-[10px] text-[#9CA3AF]">Gudang Sentral</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 text-[#4B5563]">Medis &amp; Safety</td>
                                <td class="px-3 text-right font-bold text-[#111827] font-mono">320 <span class="text-[11px] font-normal text-[#6B7280]">Box</span></td>
                                <td class="px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-[#F0FDF4] border border-[#BBF7D0] text-[#166534]">Stok Aman</span>
                                </td>
                                <td class="pr-5 pl-3 text-center">
                                    <button class="p-1 hover:bg-[#F1F5F9] rounded text-[#6B7280] hover:text-[#111827]">
                                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Table Footer Pagination -->
            <div class="px-5 py-3 border-t border-[#E5E7EB] bg-[#F8FAFC] flex items-center justify-between text-xs text-[#6B7280]">
                <span>Menampilkan <strong>3</strong> dari <strong x-text="stats.totalItemsFormatted">{{ number_format($totalItems, 0, ',', '.') }}</strong> item terdaftar</span>
                <div class="flex items-center gap-2">
                    <button class="px-2.5 py-1 bg-white border border-[#E5E7EB] rounded font-medium text-[#6B7280] hover:bg-[#F1F5F9] disabled:opacity-50" disabled="">Sebelumnya</button>
                    <button class="px-2.5 py-1 bg-white border border-[#E5E7EB] rounded font-medium text-[#111827] hover:bg-[#F1F5F9]">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('superAdminDashboard', () => ({
            loading: false,
            period: 30,
            stats: {
                totalItems: {{ $totalItems }},
                totalItemsFormatted: '{{ number_format($totalItems, 0, ',', '.') }}',
                totalStock: {{ $totalStock }},
                totalStockFormatted: '{{ number_format($totalStock, 0, ',', '.') }}',
                totalSuppliers: {{ $totalSuppliers }},
                criticalStock: {{ $criticalStock }},
                totalUsers: {{ $totalUsers }},
                activeUsers: {{ $activeUsers }},
                safeStock: {{ $totalItems - $criticalStock }},
                totalInbound: {{ $totalInbound }},
                totalInboundFormatted: 'Rp {{ number_format($totalInbound, 0, ',', '.') }}',
                totalOutbound: {{ $totalOutbound }},
                totalOutboundFormatted: 'Rp {{ number_format($totalOutbound, 0, ',', '.') }}',
                netValuation: {{ $netValuation }},
                netValuationFormatted: 'Rp {{ number_format($netValuation, 0, ',', '.') }}'
            },
            initDashboard() {
                // Listen to Reverb
                if (window.Echo) {
                    window.Echo.channel('inventory')
                        .listen('StockUpdated', (e) => {
                            this.fetchStats();
                            console.log('Realtime event received:', e);
                        });
                }
                
                // Fetch initial chart data
                this.fetchStats();
                
                // Init Chart if data exists
                this.$nextTick(() => {
                    this.initChart();
                });
            },
            
            initChart() {
                const ctx = document.getElementById('inventoryChart');
                if (!ctx) return;
                
                let chart = Chart.getChart(ctx);
                if (chart) {
                    chart.destroy();
                }
                
                const data = this.stats.chart || { labels: [], inbound: [], outbound: [] };
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Barang Masuk',
                                data: data.inbound,
                                borderColor: '#4F46E5',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Barang Keluar',
                                data: data.outbound,
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#111827',
                                bodyColor: '#4B5563',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                padding: 12,
                                boxPadding: 6,
                                usePointStyle: true,
                                titleFont: { size: 13, family: 'Inter', weight: 'bold' },
                                bodyFont: { size: 12, family: 'Inter' },
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        },
                        scales: {
                            y: { display: false, beginAtZero: true },
                            x: { 
                                grid: { display: false },
                                ticks: { font: { size: 10, family: 'monospace' }, color: '#94A3B8' }
                            }
                        }
                    }
                });
            },
            
            async fetchStats() {
                this.loading = true;
                try {
                    const response = await fetch(`{{ route('dashboard.stats') }}?period=${this.period}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.stats = { ...this.stats, ...data };
                        
                        const ctx = document.getElementById('inventoryChart');
                        let chart = Chart.getChart(ctx);
                        
                        if (chart && data.chart) {
                            chart.data.labels = data.chart.labels;
                            chart.data.datasets[0].data = data.chart.inbound;
                            chart.data.datasets[1].data = data.chart.outbound;
                            chart.update();
                        } else if (data.chart) {
                            this.initChart();
                        }
                    }
                } catch (error) {
                    console.error('Failed to fetch realtime stats:', error);
                } finally {
                    setTimeout(() => { this.loading = false; }, 500);
                }
            }
        }));
    });
</script>
@endpush
@endsection
