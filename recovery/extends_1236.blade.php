@extends('layouts.app')

@section('header_title', '{title}')

@section('main_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">{title}</h2>
            <p class="text-sm text-[#6B7280]">{desc}</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Data</span>
            </button>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden min-h-[400px] flex items-center justify-center">
        <div class="text-center">
            <span class="material-symbols-outlined text-4xl text-[#D1D5DB] mb-3">{icon}</span>
            <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada data</h3>
            <p class="text-xs text-[#6B7280]">Halaman ini telah direstorasi.</p>
        </div>
    </div>
</div>
@endsection