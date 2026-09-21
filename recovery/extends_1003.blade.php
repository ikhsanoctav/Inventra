@extends('layouts.app')
2: 
3: @section('header_title', 'Laporan Stok')
4: 
5: @section('main_content')
6: <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6">
7:     <div class="flex items-center justify-between mb-6">
8:         <div>
9:             <h2 class="font-headline text-xl font-bold text-[#111827]">Laporan Stok</h2>
10:             <p class="text-sm text-[#6B7280]">Halaman untuk mengelola laporan stok.</p>
11:         </div>
12:         <button class="h-9 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-all">
13:             <span class="material-symbols-outlined text-[16px]">add</span> Tambah Baru
14:         </button>
15:     </div>
16:     
17:     <div class="h-64 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center text-gray-400">
18:         Modul Laporan Stok belum diimplementasikan.
19:     </div>
20: </div>
21: @endsection