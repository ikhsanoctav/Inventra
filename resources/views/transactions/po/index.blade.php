@extends('layouts.app')

@section('header_title', 'Purchase Order (PO)')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false }">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>Dokumen Pengadaan Resmi</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-headline tracking-tight">Daftar Purchase Order (PO)</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola permohonan dan persetujuan pesanan pengadaan barang ke vendor.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5">
            <form action="{{ route('transactions.po.bulk_auto_draft') }}" method="POST" onsubmit="return confirm('Generate otomatis draft PO untuk seluruh barang stok kritis?');">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                    <span>Auto-Draft Stok Kritis</span>
                </button>
            </form>

            <button @click="showModal = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Buat PO Baru</span>
            </button>
        </div>
    </div>

    <!-- Quick Status Pill Filter -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('transactions.po.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua PO ({{ $stats['total'] ?? count($pos) }})
        </a>
        <a href="{{ route('transactions.po.index', ['status' => 'Draft']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'Draft' ? 'bg-slate-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Draft ({{ $stats['draft'] ?? 0 }})
        </a>
        <a href="{{ route('transactions.po.index', ['status' => 'Pending Approval']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'Pending Approval' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Approval ({{ $stats['pending'] ?? 0 }})
        </a>
        <a href="{{ route('transactions.po.index', ['status' => 'Approved']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'Approved' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Disetujui / Siap Inbound ({{ $stats['approved'] ?? 0 }})
        </a>
        <a href="{{ route('transactions.po.index', ['status' => 'Completed']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'Completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Selesai Diterima ({{ $stats['completed'] ?? 0 }})
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs relative">
        <form action="{{ route('transactions.po.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 relative">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. PO, nama pembuat, atau catatan..." 
                       class="w-full h-11 pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
            </div>
            <div class="sm:w-48">
                <select name="status" class="w-full h-11 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
                    <option value="">Semua Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending Approval" {{ request('status') == 'Pending Approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 transition-colors shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    <span>Cari</span>
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('transactions.po.index') }}" class="h-11 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold flex items-center transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- PO Table -->
    <div id="table-container" class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">No. PO &amp; Catatan</th>
                        <th class="px-5 py-4">Tanggal Pesanan</th>
                        <th class="px-5 py-4">Staff Pembuat</th>
                        <th class="px-5 py-4 text-center">Rincian Item</th>
                        <th class="px-5 py-4 text-right">Total Anggaran</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pos as $po)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-blue-600 text-sm">{{ $po->ref_number }}</div>
                            @if($po->notes)
                            <p class="text-[11px] text-slate-500 mt-0.5 max-w-xs truncate">{{ $po->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-700">
                            {{ \Carbon\Carbon::parse($po->transaction_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-800">
                            <div class="font-semibold">{{ $po->user->name ?? 'System' }}</div>
                        </td>
                        <td class="px-5 py-4 text-center text-xs">
                            <span class="font-bold text-slate-800">{{ $po->lines->count() }} Item</span>
                            <div class="text-[10px] text-slate-400">({{ $po->lines->sum('quantity') }} unit)</div>
                        </td>
                        <td class="px-5 py-4 text-right text-xs font-bold text-slate-900">
                            Rp {{ number_format($po->total_amount ?: $po->lines->sum(fn($l) => $l->quantity * $l->unit_price), 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($po->status === 'Draft')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold border border-slate-200">Draft</span>
                            @elseif($po->status === 'Pending Approval')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200">Pending Approval</span>
                            @elseif($po->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200">Approved</span>
                            @elseif($po->status === 'Completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">Completed</span>
                            @elseif($po->status === 'Rejected')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('transactions.po.show', $po->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white transition-colors border border-blue-200 shadow-2xs">
                                <span>Lihat PO</span>
                                <span class="material-symbols-outlined text-[16px] ml-1">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">description</span>
                                <p class="font-bold text-slate-700">Belum ada dokumen Purchase Order</p>
                                <p class="text-xs text-slate-400 mt-0.5">Klik tombol "Buat PO Baru" untuk memulai pemesanan barang ke vendor.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Buat PO Baru Cepat -->
    <div x-show="showModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 space-y-5" @click.stop>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-extrabold text-slate-900 font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">post_add</span>
                        <span>Buat Purchase Order Baru</span>
                    </h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>
                
                <form action="{{ route('transactions.po') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor Referensi PO</label>
                        <input type="text" name="ref_number" value="PO-{{ date('Ymd') }}-{{ rand(1000, 9999) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Dokumen</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan / Vendor</label>
                        <textarea name="notes" rows="2" placeholder="Tuliskan catatan peruntukan atau nama vendor..." class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status Awal</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                            <option value="Draft">Draft (Lengkapi Item Dahulu)</option>
                            <option value="Pending Approval">Langsung Ajukan ke Manager</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20">Buat &amp; Lanjut Isi Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
