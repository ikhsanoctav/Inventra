@extends('layouts.app')

@section('header_title', $title ?? 'Segera Hadir')

@section('main_content')
<div class="h-full flex flex-col items-center justify-center p-8 text-center space-y-6">
    <div class="w-24 h-24 rounded-3xl bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm">
        <span class="material-symbols-outlined text-[48px] text-indigo-400">construction</span>
    </div>
    
    <div class="max-w-md">
        <h1 class="text-3xl font-headline font-bold text-slate-800 tracking-tight">{{ $title ?? 'Halaman Sedang Dibangun' }}</h1>
        <p class="mt-3 text-slate-500 leading-relaxed">
            Fitur ini adalah bagian dari iterasi pengembangan selanjutnya. Silakan kembali lagi nanti saat fitur telah dirilis.
        </p>
    </div>

    <div class="pt-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-800 text-white font-medium hover:bg-slate-700 hover:shadow-lg hover:shadow-slate-800/20 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
