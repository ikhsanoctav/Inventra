@extends('layouts.app')

@section('header_title', 'Audit Stock Opname')

@section('main_content')
<div class="space-y-6" x-data="{
    searchQuery: '',
    statusFilter: 'all',
    matches(opname) {
        const matchesStatus = this.statusFilter === 'all' || opname.status.toLowerCase() === this.statusFilter.toLowerCase();
        const matchesSearch = !this.searchQuery || 
            opname.ref_number.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            (opname.user && opname.user.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        return matchesStatus && matchesSearch;
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[28px]">fact_check</span>
                <h2 class="font-headline text-2xl font-bold text-slate-800">Audit & Verifikasi Stock Opname</h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">Pemantauan akurasi stok fisik vs sistem, berita acara opname, dan rekonsiliasi selisih inventaris.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('manager.approvals') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>Pusat Otorisasi</span>
            </a>
            <a href="{{ route('reports.stock_valuation') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm shadow-indigo-500/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                <span>Valuasi Persediaan</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Sesi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">assignment</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Sesi Opname</p>
                <h3 class="text-2xl font-black text-slate-800 font-mono mt-0.5">{{ $opnames->count() }} <span class="text-xs font-normal text-slate-400">Sesi</span></h3>
            </div>
        </div>

        <!-- Card 2: Selesai Disinkronkan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Telah Disinkronkan</p>
                <h3 class="text-2xl font-black text-emerald-600 font-mono mt-0.5">{{ $opnames->where('status', 'Completed')->count() }} <span class="text-xs font-normal text-slate-400">Sesi</span></h3>
            </div>
        </div>

        <!-- Card 3: Draft / Sedang Berjalan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">pending</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Draft / Berjalan</p>
                <h3 class="text-2xl font-black text-amber-600 font-mono mt-0.5">{{ $opnames->where('status', '!=', 'Completed')->count() }} <span class="text-xs font-normal text-slate-400">Sesi</span></h3>
            </div>
        </div>

        <!-- Card 4: Total SKU Diuji -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">inventory_2</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Item Dihitung</p>
                <h3 class="text-2xl font-black text-slate-800 font-mono mt-0.5">{{ $opnames->sum(fn($o) => $o->lines->count()) }} <span class="text-xs font-normal text-slate-400">Item</span></h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-80 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
            <input type="text" x-model="searchQuery" placeholder="Cari no. referensi atau auditor..." class="w-full h-10 pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
        </div>

        <!-- Status Filter Pills -->
        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold self-start sm:self-auto shrink-0">
            <button type="button" @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                Semua
            </button>
            <button type="button" @click="statusFilter = 'completed'" :class="statusFilter === 'completed' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                Selesai (Completed)
            </button>
            <button type="button" @click="statusFilter = 'draft'" :class="statusFilter === 'draft' ? 'bg-white text-amber-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                Draft
            </button>
        </div>
    </div>

    <!-- Opname Sessions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-5">No. Referensi Sesi</th>
                        <th class="py-3.5 px-5">Tanggal Pelaksanaan</th>
                        <th class="py-3.5 px-5">Auditor / PIC Gudang</th>
                        <th class="py-3.5 px-5">Cakupan Item</th>
                        <th class="py-3.5 px-5 text-center">Status Audit</th>
                        <th class="py-3.5 px-5 text-right">Rincian Berita Acara</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($opnames as $opname)
                    <tr class="hover:bg-slate-50/80 transition-colors" x-show="matches({{ json_encode(['ref_number' => $opname->ref_number, 'status' => $opname->status, 'user' => ['name' => $opname->user->name ?? '']]) }})">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-500 text-[18px]">fact_check</span>
                                <span class="font-mono font-bold text-slate-800 text-sm">{{ $opname->ref_number }}</span>
                            </div>
                            @if($opname->notes)
                                <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 italic">{{ $opname->notes }}</p>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-slate-600 font-medium">
                            <span class="block text-slate-800 font-bold">{{ \Carbon\Carbon::parse($opname->transaction_date)->format('d M Y') }}</span>
                            <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($opname->created_at)->format('H:i') }} WIB</span>
                        </td>
                        <td class="py-4 px-5">
                            <span class="font-bold text-slate-700 block">{{ $opname->user->name ?? 'System' }}</span>
                            <span class="text-[11px] text-slate-400 capitalize">{{ str_replace('_', ' ', $opname->user->role ?? 'admin_gudang') }}</span>
                        </td>
                        <td class="py-4 px-5">
                            <span class="font-bold text-slate-800 block">{{ $opname->lines->count() }} Jenis SKU Terhitung</span>
                            <p class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">
                                {{ $opname->lines->map(fn($l) => $l->item->name ?? 'Item')->take(2)->implode(', ') }}
                                @if($opname->lines->count() > 2)...@endif
                            </p>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($opname->status === 'Completed')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Disinkronkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Draft / On-Going
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('transactions.opname.show', $opname->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold text-xs rounded-xl transition-all border border-indigo-100 shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                <span>Lihat Selisih Fisik</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <span class="material-symbols-outlined text-[24px]">fact_check</span>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Belum Ada Riwayat Sesi Stock Opname</p>
                            <p class="text-xs text-slate-400 mt-0.5">Sesi perhitungan fisik barang akan muncul di sini setelah tim gudang melakukan opname.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
