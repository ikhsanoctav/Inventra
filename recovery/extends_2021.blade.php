@extends('layouts.app')

@section('header_title', 'Dashboard')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Dashboard Logistik</h2>
            <p class="text-sm text-[#6B7280]">Ringkasan aktivitas dan status pergerakan barang hari ini.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm">
            <h3 class="text-sm font-semibold text-[#6B7280] mb-2">Total SKU Aktif</h3>
            <p class="text-3xl font-bold text-[#111827]">0</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm">
            <h3 class="text-sm font-semibold text-[#6B7280] mb-2">Inbound Hari Ini</h3>
            <p class="text-3xl font-bold text-[#111827]">0</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm">
            <h3 class="text-sm font-semibold text-[#6B7280] mb-2">Outbound Hari Ini</h3>
            <p class="text-3xl font-bold text-[#111827]">0</p>
        </div>
    </div>

    <!-- Additional Dashboard Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm min-h-[300px]">
            <h3 class="text-sm font-semibold text-[#111827] mb-4">Aktivitas Terbaru</h3>
            <div class="text-center text-[#6B7280] mt-20 text-sm">Belum ada aktivitas tercatat.</div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm min-h-[300px]">
            <h3 class="text-sm font-semibold text-[#111827] mb-4">Peringatan Stok Menipis</h3>
            <div class="text-center text-[#6B7280] mt-20 text-sm">Semua stok barang dalam batas aman.</div>
        </div>
    </div>
</div>
@endsection