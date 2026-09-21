@extends('layouts.app')

@section('header_title', 'Ringkasan Shift Kasir')

@section('main_content')
<div class="space-y-6 pb-8">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                    <span class="material-symbols-outlined text-[18px]">manage_history</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Shift</h1>
            </div>
            <p class="text-sm text-slate-500 ml-10">Kelola pembukaan, penutupan, dan pantau pendapatan shift kasir.</p>
        </div>
        
        <div class="flex items-center gap-3">
            @if(!$activeShift)
            <button onclick="document.getElementById('openShiftModal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px]">play_circle</span>
                Buka Shift Baru
            </button>
            @else
            <button type="button" onclick="document.getElementById('closeShiftModal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2.5 bg-rose-600 text-white text-sm font-bold rounded-xl hover:bg-rose-700 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px]">stop_circle</span>
                Tutup Shift Saat Ini
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 rounded-2xl flex gap-3 shadow-sm">
            <span class="material-symbols-outlined shrink-0 text-emerald-500">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 border border-rose-200 p-4 rounded-2xl flex gap-3 shadow-sm">
            <span class="material-symbols-outlined shrink-0 text-rose-500">error</span>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Active Shift Status -->
    @if($activeShift)
    <div class="bg-indigo-600 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] text-white rounded-3xl shadow-sm p-6 sm:p-8 border border-indigo-500 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold tracking-wider uppercase mb-3 border border-white/20 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Shift Aktif
                </div>
                <p class="text-indigo-200 text-sm mb-1 font-medium">Total Pendapatan Sementara</p>
                <h2 class="text-4xl font-bold">Rp {{ number_format($activeShift->total_sales, 0, ',', '.') }}</h2>
            </div>
            <div class="flex gap-6 sm:gap-10 p-4 sm:p-5 rounded-2xl bg-white/10 border border-white/10 w-full md:w-auto">
                <div>
                    <div class="flex items-center gap-1 text-indigo-200 mb-1">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        <p class="text-[11px] font-bold uppercase tracking-wider">Mulai Shift</p>
                    </div>
                    <p class="font-bold text-lg">{{ $activeShift->start_time->format('H:i') }} <span class="text-xs font-normal opacity-70">WIB</span></p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <div class="flex items-center gap-1 text-indigo-200 mb-1">
                        <span class="material-symbols-outlined text-[14px]">payments</span>
                        <p class="text-[11px] font-bold uppercase tracking-wider">Modal Kas</p>
                    </div>
                    <p class="font-bold text-lg">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-white">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100">
                    <span class="material-symbols-outlined text-[18px]">history</span>
                </div>
                Riwayat Shift
            </h2>
        </div>
        
        @if($shifts->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Belum ada riwayat shift</h3>
                <p class="text-slate-500 text-sm mt-1">Buka shift kasir pertama Anda untuk mulai mencatat riwayat.</p>
            </div>
        @else
            <div class="overflow-x-auto w-full pb-2">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-bold">Status</th>
                            <th class="px-6 py-4 font-bold">Kasir</th>
                            <th class="px-6 py-4 font-bold">Waktu Mulai</th>
                            <th class="px-6 py-4 font-bold">Waktu Selesai</th>
                            <th class="px-6 py-4 font-bold text-right">Modal Awal</th>
                            <th class="px-6 py-4 font-bold text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($shifts as $shift)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                @if($shift->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-800 font-medium">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-[10px]">
                                        {{ substr($shift->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    {{ $shift->user->name ?? 'Sistem' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $shift->start_time->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $shift->end_time ? $shift->end_time->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-right">
                                Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600 text-right">
                                Rp {{ number_format($shift->total_sales, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Buka Shift -->
<div id="openShiftModal" class="fixed inset-0 z-50 {{ $errors->any() ? '' : 'hidden' }} bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-800">Buka Shift Kasir</h3>
                <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('pos.shifts.open') }}" method="POST" class="p-6 space-y-6" hx-boost="false">
                @csrf
                <div>
                    <label for="starting_cash" class="block text-sm font-bold text-slate-700 mb-2">Modal Kas Awal (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">Rp</span>
                        <input type="number" id="starting_cash" name="starting_cash" min="0" value="{{ old('starting_cash', 0) }}"
                            class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-bold" required>
                    </div>
                    @error('starting_cash')
                        <p class="text-xs text-rose-500 mt-2 font-bold">{{ $message }}</p>
                    @else
                        <p class="text-xs text-slate-500 mt-2">Masukkan nominal uang tunai yang ada di laci kasir saat ini.</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">play_circle</span>
                        Mulai Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Tutup Shift -->
<div id="closeShiftModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="p-6 border-b border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-rose-500 text-2xl">warning</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Tutup Shift Kasir?</h3>
                    <p class="text-sm text-slate-500 mt-1">Anda yakin ingin menutup shift ini?</p>
                </div>
            </div>
            <div class="p-6 bg-slate-50">
                <p class="text-sm text-slate-600 mb-6">Pendapatan shift akan dihitung dan direkapitulasi secara otomatis. Anda tidak dapat menambahkan transaksi pada shift ini setelah ditutup.</p>
                <form action="{{ route('pos.shifts.close') }}" method="POST" class="flex items-center justify-end gap-3" hx-boost="false">
                    @csrf
                    <button type="button" onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-xl transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">stop_circle</span>
                        Ya, Tutup Shift
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
