@extends('layouts.app')

@section('header_title', 'Rule Base Logic')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Rule Base Logic (RBL)</h2>
            <p class="text-sm text-[#6B7280]">Atur logika otomatisasi sistem untuk event logistik tertentu.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Rule</span>
            </button>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm text-[#4B5563]">
            <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                <tr>
                    <th scope="col" class="px-6 py-4">Nama Rule</th>
                    <th scope="col" class="px-6 py-4">Trigger Event</th>
                    <th scope="col" class="px-6 py-4">Kondisi Logika</th>
                    <th scope="col" class="px-6 py-4">Aksi (Action)</th>
                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB]">
                @forelse([] as $rule)
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <span class="material-symbols-outlined text-3xl text-[#D1D5DB] mb-3">rule</span>
                        <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada rule logic yang diatur</h3>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection