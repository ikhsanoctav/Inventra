@extends('layouts.app')

@section('header_title', 'Riwayat & Log')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Riwayat & Audit Log</h2>
            <p class="text-sm text-[#6B7280]">Jejak rekam seluruh mutasi dan aktivitas sistem inventaris.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" placeholder="Cari transaksi..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all shadow-sm">
            </div>
            <button class="h-10 px-4 rounded-lg bg-white border border-[#E5E7EB] hover:bg-gray-50 text-[#4B5563] text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> <span class="hidden sm:inline">Filter</span>
            </button>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm text-[#4B5563]">
            <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                <tr>
                    <th scope="col" class="px-6 py-4">Waktu</th>
                    <th scope="col" class="px-6 py-4">No. Transaksi</th>
                    <th scope="col" class="px-6 py-4">Tipe Mutasi</th>
                    <th scope="col" class="px-6 py-4">SKU</th>
                    <th scope="col" class="px-6 py-4 text-right">Perubahan Stok</th>
                    <th scope="col" class="px-6 py-4">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB]">
                @forelse([] as $log)
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <span class="material-symbols-outlined text-3xl text-[#D1D5DB] mb-3">history</span>
                        <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada riwayat tercatat</h3>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection