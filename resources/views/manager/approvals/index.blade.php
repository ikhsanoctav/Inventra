@extends('layouts.app')

@section('header_title', 'Approval Center (Pusat Persetujuan)')

@section('main_content')
<div class="space-y-6" x-data="{
    activeTab: 'all',
    showRejectPoModal: false,
    rejectPoId: null,
    rejectPoRef: '',
    showRejectRequestModal: false,
    rejectRequestId: null,
    rejectRequestRef: '',
    rejectRequestTitle: '',

    openRejectPo(id, ref) {
        this.rejectPoId = id;
        this.rejectPoRef = ref;
        this.showRejectPoModal = true;
    },

    openRejectRequest(id, ref, title) {
        this.rejectRequestId = id;
        this.rejectRequestRef = ref;
        this.rejectRequestTitle = title;
        this.showRejectRequestModal = true;
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[28px]">verified_user</span>
                Approval Center (Pusat Persetujuan)
            </h2>
            <p class="text-sm text-slate-500 mt-1">Otorisasi dan review Purchase Order (PO), permintaan data gudang, dan hasil Stock Opname.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.history') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">policy</span>
                Audit Trail Logistik
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Pending POs -->
        <div @click="activeTab = 'po'" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 cursor-pointer hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">receipt_long</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">PO Menunggu</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $pendingPOs->count() }} Dokumen</h3>
            </div>
        </div>

        <!-- Card 2: Pending Gudang Requests -->
        <div @click="activeTab = 'gudang'" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 cursor-pointer hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">assignment_turned_in</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Request Gudang</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $pendingRequests->count() }} Tiket</h3>
            </div>
        </div>

        <!-- Card 3: Pending Damaged / Quarantine -->
        <div @click="activeTab = 'damaged'" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 cursor-pointer hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Barang Karantina</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $pendingDamagedItems->count() }} Item</h3>
            </div>
        </div>

        <!-- Card 4: Stock Opnames -->
        <div @click="activeTab = 'opname'" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 cursor-pointer hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">fact_check</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stock Opname</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $pendingOpnames->count() }} Periode</h3>
            </div>
        </div>

        <!-- Card 5: Recent Decisions -->
        <div @click="activeTab = 'history'" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 cursor-pointer hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">history_edu</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Riwayat Otorisasi</p>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $recentPOs->count() + $recentRequests->count() + $recentDamagedItems->count() }} Selesai</h3>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto">
        <button type="button" @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm">
            <span class="material-symbols-outlined text-[18px]">apps</span>
            <span>Semua Dokumen</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $pendingPOs->count() + $pendingRequests->count() + $pendingDamagedItems->count() }}</span>
        </button>

        <button type="button" @click="activeTab = 'po'" :class="activeTab === 'po' ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            <span>Purchase Order (PO)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'po' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $pendingPOs->count() }}</span>
        </button>

        <button type="button" @click="activeTab = 'gudang'" :class="activeTab === 'gudang' ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm">
            <span class="material-symbols-outlined text-[18px]">send</span>
            <span>Request Data Gudang</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'gudang' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $pendingRequests->count() }}</span>
        </button>

        <button type="button" @click="activeTab = 'damaged'" :class="activeTab === 'damaged' ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm">
            <span class="material-symbols-outlined text-[18px]">warning</span>
            <span>Barang Karantina & Rusak</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'damaged' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $pendingDamagedItems->count() }}</span>
        </button>

        <button type="button" @click="activeTab = 'opname'" :class="activeTab === 'opname' ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm">
            <span class="material-symbols-outlined text-[18px]">fact_check</span>
            <span>Audit Opname</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'opname' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $pendingOpnames->count() }}</span>
        </button>

        <button type="button" @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm">
            <span class="material-symbols-outlined text-[18px]">history</span>
            <span>Riwayat Keputusan</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'history' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">{{ $recentPOs->count() + $recentRequests->count() + $recentDamagedItems->count() }}</span>
        </button>
    </div>

    <!-- SECTION 1: Purchase Orders (PO) Pending Approval -->
    <div x-show="activeTab === 'all' || activeTab === 'po'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 font-headline">Purchase Orders (PO) Menunggu Persetujuan</h3>
                        <p class="text-xs text-slate-500">Setujui pengadaan barang atau tolak dengan alasan bila tidak sesuai rencana anggaran.</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">{{ $pendingPOs->count() }} Menunggu</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">No. PO & Tanggal</th>
                            <th class="py-3 px-4">Diajukan Oleh</th>
                            <th class="py-3 px-4">Rincian Item</th>
                            <th class="py-3 px-4">Total Estimasi</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi Manajer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pendingPOs as $po)
                        @php
                            $totalPoAmount = $po->lines->sum(fn($l) => $l->quantity * $l->unit_price);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-indigo-500 text-[18px]">description</span>
                                    <span>{{ $po->ref_number }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-700 block">{{ $po->user->name ?? 'Staff Purchasing' }}</span>
                                <span class="text-[11px] text-slate-400 capitalize">{{ str_replace('_', ' ', $po->user->role ?? 'purchasing') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800">{{ $po->lines->count() }} Jenis Item</span>
                                <p class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">
                                    {{ $po->lines->map(fn($l) => ($l->item->name ?? 'Item') . ' (' . $l->quantity . ')')->take(2)->implode(', ') }}
                                    @if($po->lines->count() > 2)...@endif
                                </p>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-800">
                                Rp {{ number_format($totalPoAmount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ $po->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Review Detail Button -->
                                    <a href="{{ route('transactions.po.show', $po->id) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all" title="Review Detail PO">
                                        Review
                                    </a>

                                    <!-- Quick Approve Button -->
                                    <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui Purchase Order {{ $po->ref_number }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-emerald-500/20 flex items-center gap-1 cursor-pointer" title="Setujui PO">
                                            <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    <!-- Reject Button with Modal -->
                                    <button type="button" @click="openRejectPo({{ $po->id }}, '{{ $po->ref_number }}')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition-all flex items-center gap-1 cursor-pointer" title="Tolak PO dengan Alasan">
                                        <span class="material-symbols-outlined text-[15px]">cancel</span>
                                        <span>Tolak</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-[24px]">task_alt</span>
                                </div>
                                <p class="font-bold text-slate-700 text-sm">Tidak Ada Purchase Order Menunggu</p>
                                <p class="text-xs text-slate-400 mt-0.5">Semua pengajuan PO telah diproses atau belum ada pengajuan baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2: Request Perubahan Data Gudang Pending Approval -->
    <div x-show="activeTab === 'all' || activeTab === 'gudang'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">send</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 font-headline">Permintaan Perubahan Data dari Gudang</h3>
                        <p class="text-xs text-slate-500">Tiket permohonan penyesuaian stok, relokasi rak, atau update master data dari Admin Gudang.</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">{{ $pendingRequests->count() }} Menunggu</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">No. Tiket</th>
                            <th class="py-3 px-4">Pengirim (Gudang)</th>
                            <th class="py-3 px-4">Jenis Permintaan</th>
                            <th class="py-3 px-4">Judul & Keterangan</th>
                            <th class="py-3 px-4">Waktu Diajukan</th>
                            <th class="py-3 px-4 text-right">Aksi Manajer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pendingRequests as $req)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                {{ $req->request_number }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-700">
                                {{ $req->user->name ?? 'Admin Gudang' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $req->type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-sm">
                                <span class="font-bold text-slate-800 block">{{ $req->title }}</span>
                                <p class="text-slate-500 text-[11px] mt-0.5 line-clamp-2">{{ $req->description ?: 'Tidak ada keterangan tambahan.' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $req->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Approve Button -->
                                    <form action="{{ route('gudang.requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui permintaan {{ $req->request_number }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-emerald-500/20 flex items-center gap-1 cursor-pointer" title="Setujui Permintaan">
                                            <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    <!-- Reject Button -->
                                    <button type="button" @click="openRejectRequest({{ $req->id }}, '{{ $req->request_number }}', '{{ addslashes($req->title) }}')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition-all flex items-center gap-1 cursor-pointer" title="Tolak Permintaan dengan Alasan">
                                        <span class="material-symbols-outlined text-[15px]">cancel</span>
                                        <span>Tolak</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-[24px]">mark_email_read</span>
                                </div>
                                <p class="font-bold text-slate-700 text-sm">Tidak Ada Tiket Request Gudang</p>
                                <p class="text-xs text-slate-400 mt-0.5">Semua permohonan data dari tim gudang telah ditinjau.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 3: Barang Rusak & Karantina Pending Approval -->
    <div x-show="activeTab === 'all' || activeTab === 'damaged'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">warning</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 font-headline">Barang Rusak & Karantina Menunggu Otorisasi</h3>
                        <p class="text-xs text-slate-500">Otorisasi tindakan lanjutan: Pemusnahan (Write-Off), Retur ke Vendor, atau Rekondisi.</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-bold">{{ $pendingDamagedItems->count() }} Menunggu Tindakan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">No. Referensi & Tanggal</th>
                            <th class="py-3 px-4">Nama Barang & SKU</th>
                            <th class="py-3 px-4">Kuantitas</th>
                            <th class="py-3 px-4">Jenis Kerusakan</th>
                            <th class="py-3 px-4">Rekomendasi Tindakan</th>
                            <th class="py-3 px-4 text-right">Otorisasi Manajer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pendingDamagedItems as $dmg)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-slate-800 block">{{ $dmg->reference_number }}</span>
                                <span class="text-[11px] text-slate-400">{{ $dmg->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $dmg->item->name ?? 'Item Tidak Ditemukan' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $dmg->item->sku ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-rose-600">
                                {{ $dmg->quantity }} {{ $dmg->item->uom->symbol ?? 'Unit' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold text-[11px] border border-rose-200">
                                    {{ $dmg->damage_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold text-[11px] border border-amber-200">
                                    {{ $dmg->action_type }}
                                </span>
                                @if($dmg->notes)
                                    <p class="text-[11px] text-slate-500 mt-1 italic line-clamp-1">"{{ $dmg->notes }}"</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Disetujui Pemusnahan -->
                                    <form action="{{ route('gudang.damaged_items.status', $dmg->id) }}" method="POST" onsubmit="return confirm('Otorisasi pemusnahan (write-off) untuk item ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="Dimusnahkan">
                                        <button type="submit" class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1 cursor-pointer" title="Otorisasi Pemusnahan">
                                            <span class="material-symbols-outlined text-[14px]">delete_sweep</span>
                                            <span>Musnahkan</span>
                                        </button>
                                    </form>

                                    <!-- Disetujui Retur -->
                                    <form action="{{ route('gudang.damaged_items.status', $dmg->id) }}" method="POST" onsubmit="return confirm('Otorisasi retur ke supplier untuk item ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="Diretur">
                                        <button type="submit" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1 cursor-pointer" title="Otorisasi Retur Supplier">
                                            <span class="material-symbols-outlined text-[14px]">assignment_return</span>
                                            <span>Retur</span>
                                        </button>
                                    </form>

                                    <!-- Selesai Diproses / Rekondisi -->
                                    <form action="{{ route('gudang.damaged_items.status', $dmg->id) }}" method="POST" onsubmit="return confirm('Tandai barang karantina ini selesai diproses?')">
                                        @csrf
                                        <input type="hidden" name="status" value="Selesai Diproses">
                                        <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1 cursor-pointer" title="Tandai Selesai">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            <span>Selesai</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-[24px]">verified</span>
                                </div>
                                <p class="font-bold text-slate-700 text-sm">Tidak Ada Barang Karantina Menunggu</p>
                                <p class="text-xs text-slate-400 mt-0.5">Semua catatan barang rusak telah diotorisasi atau diproses.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 4: Stock Opname Audits -->
    <div x-show="activeTab === 'all' || activeTab === 'opname'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">fact_check</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 font-headline">Audit & Verifikasi Stock Opname</h3>
                        <p class="text-xs text-slate-500">Evaluasi selisih stok fisik vs sistem yang dicatat oleh tim gudang.</p>
                    </div>
                </div>
                <a href="{{ route('manager.audit.opname') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    <span>Laporan Audit Lengkap</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">No. Referensi</th>
                            <th class="py-3 px-4">Tanggal Pelaksanaan</th>
                            <th class="py-3 px-4">Auditor / Pelaksana</th>
                            <th class="py-3 px-4">Item Dihitung</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pendingOpnames as $opname)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                {{ $opname->ref_number }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($opname->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-700">
                                {{ $opname->user->name ?? 'System' }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $opname->lines->count() }} Jenis Item
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $opname->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $opname->status === 'Completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $opname->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('transactions.opname.show', $opname->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold text-xs rounded-xl transition-all border border-indigo-100">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                    <span>Review Detail</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-[24px]">fact_check</span>
                                </div>
                                <p class="font-bold text-slate-700 text-sm">Belum Ada Riwayat Stock Opname</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 5: Riwayat Keputusan Terakhir (Processed Decisions) -->
    <div x-show="activeTab === 'all' || activeTab === 'history'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">history_edu</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 font-headline">Riwayat Keputusan Terbaru (Approved & Rejected)</h3>
                        <p class="text-xs text-slate-500">Catatan dokumen yang telah disetujui, ditolak, atau diotorisasi beserta alasan manajerial.</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Tipe & Dokumen</th>
                            <th class="py-3 px-4">Waktu Proses</th>
                            <th class="py-3 px-4">Status Keputusan</th>
                            <th class="py-3 px-4">Catatan / Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <!-- Recent POs -->
                        @foreach($recentPOs as $rPo)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-bold text-indigo-600 block">Purchase Order</span>
                                <span class="font-mono text-slate-800 font-bold">{{ $rPo->ref_number }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $rPo->updated_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                @if(in_array($rPo->status, ['Approved', 'Completed']))
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Disetujui
                                    </span>
                                @elseif($rPo->status === 'Rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ $rPo->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs">
                                {{ $rPo->notes ?: '-' }}
                            </td>
                        </tr>
                        @endforeach

                        <!-- Recent Requests -->
                        @foreach($recentRequests as $rReq)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-bold text-amber-600 block">Request Gudang</span>
                                <span class="font-mono text-slate-800 font-bold">{{ $rReq->request_number }} ({{ $rReq->title }})</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $rReq->updated_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                @if($rReq->status === 'Disetujui')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs">
                                {{ $rReq->review_note ?: '-' }}
                            </td>
                        </tr>
                        @endforeach

                        <!-- Recent Damaged Items -->
                        @foreach($recentDamagedItems as $rDmg)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-bold text-rose-600 block">Barang Karantina</span>
                                <span class="font-mono text-slate-800 font-bold">{{ $rDmg->reference_number }} ({{ $rDmg->item->name ?? 'Item' }})</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $rDmg->updated_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $rDmg->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs">
                                {{ $rDmg->notes ?: '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 1: Tolak Purchase Order dengan Alasan -->
    <div x-show="showRejectPoModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showRejectPoModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">cancel</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 font-headline">Tolak Purchase Order</h3>
                            <p class="text-xs text-slate-500">Berikan alasan penolakan untuk tim Purchasing</p>
                        </div>
                    </div>
                    <button type="button" @click="showRejectPoModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Nomor Purchase Order:</span>
                    <p class="font-mono text-xs font-bold text-rose-700 break-all" x-text="rejectPoRef"></p>
                </div>

                <form :action="'/transactions/po/' + rejectPoId + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea name="reason" rows="3" required placeholder="Contoh: Anggaran bulan ini melebihi plafon / Harga satuan item terlalu tinggi / Vendor belum terverifikasi" class="w-full p-3 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none"></textarea>
                        <p class="text-[11px] text-slate-400">Alasan ini akan dikirimkan sebagai notifikasi ke pembuat PO.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showRejectPoModal = false" class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 text-xs font-bold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-rose-500/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                            Konfirmasi Tolak PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Tolak Request Data Gudang dengan Alasan -->
    <div x-show="showRejectRequestModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showRejectRequestModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">cancel</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 font-headline">Tolak Permintaan Gudang</h3>
                            <p class="text-xs text-slate-500">Berikan catatan penolakan untuk Admin Gudang</p>
                        </div>
                    </div>
                    <button type="button" @click="showRejectRequestModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Nomor Tiket:</span>
                    <p class="font-mono text-xs font-bold text-rose-700 break-all" x-text="rejectRequestRef + ' - ' + rejectRequestTitle"></p>
                </div>

                <form :action="'/gudang/requests/' + rejectRequestId + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea name="reason" rows="3" required placeholder="Contoh: Lokasi rak yang diminta tidak sesuai kategori / Data fisik belum diverifikasi" class="w-full p-3 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showRejectRequestModal = false" class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 text-xs font-bold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-rose-500/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                            Konfirmasi Tolak Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
