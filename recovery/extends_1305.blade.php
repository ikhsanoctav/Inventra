@extends('layouts.app')

@section('header_title', 'Dashboard Super Admin')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Dashboard Super Admin</h2>
            <p class="text-sm text-[#6B7280]">Selamat datang di panel kontrol utama sistem logistik INVENTRA.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm">
            <h3 class="text-sm font-semibold text-[#6B7280] mb-2">Total Barang</h3>
            <p class="text-3xl font-bold text-[#111827]">0</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm">
            <h3 class="text-sm font-semibold text-[#6B7280] mb-2">Total Transaksi</h3>
            <p class="text-3xl font-bold text-[#111827]">0</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-[#E5E7EB] shadow-sm">
            <h3 class="text-sm font-semibold text-[#6B7280] mb-2">Total Pengguna</h3>
            <p class="text-3xl font-bold text-[#111827]">0</p>
        </div>
    </div>
</div>
@endsection