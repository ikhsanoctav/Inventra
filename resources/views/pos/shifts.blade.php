@extends('layouts.app')

@section('header_title', 'Ringkasan Shift Kasir')

@section('main_content')
<div x-data="shiftManagement()" class="space-y-6 pb-8 font-sans">

    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">manage_history</span>
                </div>
                <h1 class="text-2xl font-headline font-bold text-slate-800 tracking-tight">Manajemen Shift & Kas</h1>
            </div>
            <p class="text-sm text-slate-500 ml-11">Kelola pembukaan, rekonsiliasi kas tutup shift, dan cetak serah terima kasir.</p>
        </div>
        
        <div class="flex items-center gap-3">
            @if(!$activeShift)
            <button type="button" @click="openModal('openShiftModal')" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-emerald-600/20 active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">play_circle</span>
                Buka Shift Baru
            </button>
            @else
            <button type="button" @click="openModal('closeShiftModal')" class="flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-rose-600/20 active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">stop_circle</span>
                Tutup Shift & Rekonsiliasi
            </button>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined shrink-0 text-emerald-500 text-[24px]">check_circle</span>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            @if(session('closed_shift_id'))
            <button type="button" @click="printShiftReceipt({{ session('closed_shift_id') }})" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all">
                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                Cetak Struk Rekap Shift Ini
            </button>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 border border-rose-200 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined shrink-0 text-rose-500 text-[24px]">error</span>
            <p class="font-medium text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Active Shift Status Banner -->
    @if($activeShift)
    <div class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-700 text-white rounded-3xl shadow-md shadow-indigo-600/10 p-6 sm:p-8 border border-indigo-500 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold tracking-wider uppercase mb-3 border border-white/20 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 absolute"></span>
                    Shift Kasir Aktif (#SHF-{{ str_pad($activeShift->id, 5, '0', STR_PAD_LEFT) }})
                </div>
                <p class="text-indigo-200 text-xs sm:text-sm font-medium mb-1">Total Omzet Penjualan Berjalan</p>
                <h2 class="text-3xl sm:text-4xl font-headline font-bold">Rp {{ number_format($activeShiftStats['total_sales'] ?? $activeShift->total_sales, 0, ',', '.') }}</h2>
                <p class="text-indigo-200 text-xs mt-2 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">receipt</span>
                    <span>{{ $activeShiftStats['transaction_count'] ?? 0 }} transaksi berhasil diproses pada shift ini</span>
                </p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 w-full lg:w-auto">
                <div class="p-2">
                    <div class="flex items-center gap-1 text-indigo-200 mb-1">
                        <span class="material-symbols-outlined text-[15px]">schedule</span>
                        <p class="text-[11px] font-bold uppercase tracking-wider">Mulai Shift</p>
                    </div>
                    <p class="font-headline font-bold text-base sm:text-lg">{{ $activeShift->start_time->format('H:i') }} <span class="text-xs font-normal opacity-80">WIB</span></p>
                </div>
                
                <div class="p-2 border-l border-white/15">
                    <div class="flex items-center gap-1 text-indigo-200 mb-1">
                        <span class="material-symbols-outlined text-[15px]">payments</span>
                        <p class="text-[11px] font-bold uppercase tracking-wider">Modal Awal</p>
                    </div>
                    <p class="font-headline font-bold text-base sm:text-lg">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</p>
                </div>

                <div class="p-2 border-l border-white/15">
                    <div class="flex items-center gap-1 text-indigo-200 mb-1">
                        <span class="material-symbols-outlined text-[15px]">account_balance_wallet</span>
                        <p class="text-[11px] font-bold uppercase tracking-wider">Tunai Masuk</p>
                    </div>
                    <p class="font-headline font-bold text-base sm:text-lg text-emerald-300">Rp {{ number_format($activeShiftStats['cash_sales'] ?? 0, 0, ',', '.') }}</p>
                </div>

                <div class="p-2 border-l border-white/15 bg-white/10 rounded-xl">
                    <div class="flex items-center gap-1 text-indigo-200 mb-1">
                        <span class="material-symbols-outlined text-[15px]">point_of_sale</span>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-200">Kas di Laci</p>
                    </div>
                    <p class="font-headline font-bold text-base sm:text-lg text-amber-300">Rp {{ number_format($activeShiftStats['expected_cash'] ?? $activeShift->starting_cash, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table Riwayat Shift -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
            <h2 class="text-lg font-headline font-bold text-slate-800 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">history</span>
                </div>
                Riwayat & Rekonsiliasi Shift
            </h2>
            <div class="text-xs text-slate-400 font-medium">
                Menampilkan total {{ $shifts->total() }} riwayat shift tercatat
            </div>
        </div>
        
        @if($shifts->isEmpty())
            <div class="p-16 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4 shadow-inner">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <h3 class="text-lg font-headline font-bold text-slate-800">Belum ada riwayat shift</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-sm">Buka shift kasir pertama Anda untuk mulai mencatat dan memantau rekonsiliasi kas.</p>
            </div>
        @else
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Status & ID</th>
                            <th class="px-6 py-4">Kasir</th>
                            <th class="px-6 py-4">Waktu Mulai</th>
                            <th class="px-6 py-4">Waktu Selesai</th>
                            <th class="px-6 py-4 text-right">Modal Awal</th>
                            <th class="px-6 py-4 text-right">Total Omzet</th>
                            <th class="px-6 py-4 text-right">Kas Fisik Akhir</th>
                            <th class="px-6 py-4 text-center">Selisih Kas</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($shifts as $shift)
                        @php
                            // Calculate simple stats for closed shift
                            $isClosed = $shift->status === 'closed';
                            $endingCash = $shift->ending_cash;
                            $diff = null;
                            if ($isClosed && $endingCash !== null) {
                                // Calculate discrepancy if available
                                $diff = (float)$endingCash - ((float)$shift->starting_cash + (float)$shift->total_sales);
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($shift->status === 'active')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            Selesai
                                        </span>
                                    @endif
                                    <span class="font-mono text-xs text-slate-400">#{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-800 font-medium">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs shadow-sm">
                                        {{ substr($shift->user->name ?? 'K', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-sm">{{ $shift->user->name ?? 'Kasir Sistem' }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono">{{ $shift->user->nip ?? 'NIP: -' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <div class="font-medium text-slate-700">{{ $shift->start_time->format('d M Y') }}</div>
                                <div class="font-mono text-slate-400 text-[11px]">{{ $shift->start_time->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                @if($shift->end_time)
                                    <div class="font-medium text-slate-700">{{ $shift->end_time->format('d M Y') }}</div>
                                    <div class="font-mono text-slate-400 text-[11px]">{{ $shift->end_time->format('H:i') }} WIB</div>
                                @else
                                    <span class="text-slate-400 italic">Sedang Berjalan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-right font-mono text-xs">
                                Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-indigo-600 text-right font-mono text-sm">
                                Rp {{ number_format($shift->total_sales, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-xs text-slate-700">
                                @if($shift->ending_cash !== null)
                                    <span class="font-bold">Rp {{ number_format($shift->ending_cash, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isClosed && $shift->ending_cash !== null)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 shadow-sm" title="{{ $shift->notes ?: 'Tutup shift normal' }}">
                                        @if($shift->notes)
                                            <span class="material-symbols-outlined text-[14px] mr-1 text-slate-400">notes</span>
                                        @endif
                                        Terekonsiliasi
                                    </span>
                                @elseif($shift->status === 'active')
                                    <span class="text-xs text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Menunggu Tutup</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Print Shift Receipt -->
                                    <button type="button" 
                                            @click="printShiftReceipt({{ $shift->id }})" 
                                            class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white flex items-center justify-center transition-all shadow-sm" 
                                            title="Cetak Struk Rekap Shift">
                                        <span class="material-symbols-outlined text-[17px]">print</span>
                                    </button>

                                    <!-- View Details Modal -->
                                    <button type="button" 
                                            @click="viewShiftTransactions({{ $shift->id }})" 
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-800 text-slate-600 hover:text-white flex items-center justify-center transition-all shadow-sm" 
                                            title="Lihat Daftar Transaksi Shift">
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: Buka Shift Kasir -->
    <div id="openShiftModal" class="fixed inset-0 z-50 {{ $errors->any() && !old('ending_cash') ? '' : 'hidden' }} bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-sm">
                            <span class="material-symbols-outlined text-[22px]">play_circle</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-headline font-bold text-slate-800">Buka Shift Kasir Baru</h3>
                            <p class="text-xs text-slate-400">Mulai sesi kasir dan input modal awal</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form action="{{ route('pos.shifts.open') }}" method="POST" class="p-6 space-y-5" hx-boost="false">
                    @csrf
                    <div>
                        <label for="starting_cash" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Modal Kas Awal di Laci (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold font-mono">Rp</span>
                            <input type="number" id="starting_cash" name="starting_cash" min="0" step="1000" value="{{ old('starting_cash', 0) }}"
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono font-bold text-base" required>
                        </div>
                        @error('starting_cash')
                            <p class="text-xs text-rose-500 mt-2 font-bold">{{ $message }}</p>
                        @else
                            <p class="text-xs text-slate-400 mt-2">Masukkan jumlah uang tunai kembalian yang ada di laci kasir saat ini.</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">play_circle</span>
                            Mulai Sesi Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Tutup Shift & Rekonsiliasi Kas -->
    <div id="closeShiftModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shadow-sm">
                            <span class="material-symbols-outlined text-[22px]">stop_circle</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-headline font-bold text-slate-800">Tutup Shift & Rekonsiliasi Kas</h3>
                            <p class="text-xs text-slate-400">Hitung fisik uang laci dan selesaikan shift</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                @php
                    $expCash = (float)($activeShiftStats['expected_cash'] ?? ($activeShift->starting_cash ?? 0));
                    $startCash = (float)($activeShift->starting_cash ?? 0);
                    $cSales = (float)($activeShiftStats['cash_sales'] ?? 0);
                    $ncSales = (float)($activeShiftStats['non_cash_sales'] ?? 0);
                @endphp

                <form action="{{ route('pos.shifts.close') }}" method="POST" class="p-6 space-y-5" hx-boost="false" x-data="{
                    expectedCash: {{ $expCash }},
                    countedCash: {{ $expCash }},
                    get discrepancy() {
                        return this.countedCash - this.expectedCash;
                    },
                    formatRupiah(num) {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num || 0);
                    }
                }">
                    @csrf

                    <!-- Rekap Singkat Keuangan Sistem -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Modal Kas Awal:</span>
                            <span class="font-mono font-semibold">Rp {{ number_format($startCash, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>(+) Penjualan Tunai:</span>
                            <span class="font-mono font-semibold text-emerald-600">Rp {{ number_format($cSales, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Penjualan Non-Tunai (QRIS/Debit):</span>
                            <span class="font-mono font-semibold text-indigo-600">Rp {{ number_format($ncSales, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between font-bold text-slate-800 text-sm">
                            <span>Kas Seharusnya di Laci:</span>
                            <span class="font-mono text-indigo-700">Rp {{ number_format($expCash, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Input Kas Fisik Aktual -->
                    <div>
                        <label for="ending_cash" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Kas Fisik Aktual di Laci (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold font-mono">Rp</span>
                            <input type="number" id="ending_cash" name="ending_cash" min="0" step="1000" x-model.number="countedCash"
                                class="w-full pl-12 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all font-mono font-bold text-base" required>
                        </div>
                        
                        <!-- Live Discrepancy Indicator -->
                        <div class="mt-3 p-3 rounded-xl border flex items-center justify-between text-xs font-bold"
                             :class="{
                                'bg-emerald-50 border-emerald-200 text-emerald-800': discrepancy === 0,
                                'bg-blue-50 border-blue-200 text-blue-800': discrepancy > 0,
                                'bg-rose-50 border-rose-200 text-rose-800': discrepancy < 0
                             }">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">
                                    <template x-if="discrepancy === 0"><span>verified</span></template>
                                    <template x-if="discrepancy > 0"><span>trending_up</span></template>
                                    <template x-if="discrepancy < 0"><span>warning</span></template>
                                </span>
                                <span>Status Selisih Kas:</span>
                            </div>
                            <div class="font-mono text-sm">
                                <template x-if="discrepancy === 0">
                                    <span>Kas Sesuai (PAS)</span>
                                </template>
                                <template x-if="discrepancy > 0">
                                    <span x-text="'Lebih Kas: +' + formatRupiah(discrepancy)"></span>
                                </template>
                                <template x-if="discrepancy < 0">
                                    <span x-text="'Kurang Kas: ' + formatRupiah(discrepancy)"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Input Catatan Shift -->
                    <div>
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan Serah Terima / Alasan Selisih (Opsional)</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Tuliskan catatan operasional kasir atau keterangan jika terdapat selisih uang kas..." class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"></textarea>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">lock</span>
                            Tutup Shift & Cetak Rekap
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Detail Transaksi Shift (AJAX) -->
    <div id="shiftDetailsModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shadow-sm">
                            <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-headline font-bold text-slate-800" x-text="'Daftar Transaksi Shift #' + currentShiftId"></h3>
                            <p class="text-xs text-slate-400">Rincian seluruh transaksi kasir yang diproses pada sesi shift ini</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal('shiftDetailsModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <div x-show="loadingDetails" class="py-12 flex flex-col items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined animate-spin text-3xl text-indigo-600 mb-2">progress_activity</span>
                        <p class="text-xs font-semibold">Memuat rincian transaksi...</p>
                    </div>

                    <div x-show="!loadingDetails && shiftTxs.length === 0" class="py-12 text-center text-slate-400">
                        <p class="text-sm font-semibold text-slate-600">Tidak ada transaksi pada sesi shift ini.</p>
                    </div>

                    <div x-show="!loadingDetails && shiftTxs.length > 0" class="space-y-3">
                        <template x-for="tx in shiftTxs" :key="tx.id">
                            <div class="p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-white transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono font-bold text-indigo-700" x-text="tx.ref_number"></span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" 
                                              :class="tx.type === 'return' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'" 
                                              x-text="tx.type === 'return' ? 'RETUR' : 'PENJUALAN'"></span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700" x-text="tx.payment_method || 'Cash'"></span>
                                    </div>
                                    <p class="text-slate-500" x-text="new Date(tx.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB • ' + (tx.lines ? tx.lines.length : 0) + ' item diproses'"></p>
                                </div>
                                <div class="text-right flex sm:flex-col items-center sm:items-end justify-between">
                                    <p class="font-mono font-bold text-sm" :class="tx.type === 'return' ? 'text-rose-600' : 'text-slate-800'" x-text="formatRupiah(tx.total_amount)"></p>
                                    <button type="button" @click="printReceipt(tx.ref_number)" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[13px]">print</span> Struk Nota
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="printShiftReceipt(currentShiftId)" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                        Cetak Struk Rekap Shift Ini
                    </button>
                    <button type="button" @click="closeModal('shiftDetailsModal')" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function shiftManagement() {
        return {
            currentShiftId: '',
            shiftTxs: [],
            loadingDetails: false,

            openModal(modalId) {
                document.getElementById(modalId)?.classList.remove('hidden');
            },

            closeModal(modalId) {
                document.getElementById(modalId)?.classList.add('hidden');
            },

            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num || 0);
            },

            printShiftReceipt(shiftId) {
                if (!shiftId) return;
                const url = `{{ url('pos/shifts') }}/${shiftId}/receipt`;
                window.open(url, '_blank', 'width=450,height=700');
            },

            printReceipt(refNumber) {
                if (!refNumber) return;
                const url = `{{ url('pos/receipt') }}/${refNumber}`;
                window.open(url, '_blank', 'width=420,height=650');
            },

            async viewShiftTransactions(shiftId) {
                this.currentShiftId = shiftId;
                this.shiftTxs = [];
                this.loadingDetails = true;
                this.openModal('shiftDetailsModal');

                try {
                    const res = await fetch(`{{ url('pos/shifts') }}/${shiftId}/transactions`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.shiftTxs = data.transactions || [];
                    }
                } catch (e) {
                    console.error('Failed to load shift transactions', e);
                    alert('Gagal memuat rincian transaksi shift.');
                } finally {
                    this.loadingDetails = false;
                }
            }
        };
    }
</script>
@endpush
@endsection
