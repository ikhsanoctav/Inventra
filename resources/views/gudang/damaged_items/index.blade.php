@extends('layouts.app')

@section('header_title', 'Barang Rusak & Karantina')

@section('main_content')
<div class="space-y-6" x-data="{ 
    showCreateModal: false, 
    showStatusModal: false, 
    selectedItemStock: 0,
    selectedItemId: '',
    statusUpdateId: '',
    statusUpdateRef: '',
    statusUpdateCurrent: '',
    updateStockInfo(id, items) {
        const itm = items.find(i => i.id == id);
        this.selectedItemStock = itm ? itm.stock : 0;
    },
    openStatusModal(id, ref, status) {
        this.statusUpdateId = id;
        this.statusUpdateRef = ref;
        this.statusUpdateCurrent = status;
        this.showStatusModal = true;
    }
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-600 text-[26px]">warning</span>
                <h2 class="font-headline text-2xl font-bold text-slate-800">Barang Rusak & Karantina</h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">Pencatatan barang cacat, rusak fisik, kadaluarsa, atau penyesuaian stok persediaan (Write-Off).</p>
        </div>
        <button type="button" 
                @click="showCreateModal = true" 
                class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-rose-600/20 flex items-center gap-2 self-start sm:self-auto cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Catat Barang Rusak</span>
        </button>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Damaged Insidents -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Laporan</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1 font-mono">{{ $totalItemsCount }} <span class="text-xs font-normal text-slate-400">Kasus</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">assignment</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3 font-medium">Seluruh laporan tercatat</p>
        </div>

        <!-- Total Damaged Units -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Unit Rusak</p>
                    <h3 class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ number_format($totalDamagedUnits, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Unit</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">heart_broken</span>
                </div>
            </div>
            <p class="text-xs text-rose-600 mt-3 font-semibold">Telah dipotong dari stok aktif</p>
        </div>

        <!-- Quarantine Units -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dalam Karantina</p>
                    <h3 class="text-2xl font-black text-amber-600 mt-1 font-mono">{{ number_format($quarantineUnits, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Unit</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">pest_control</span>
                </div>
            </div>
            <p class="text-xs text-amber-600 mt-3 font-semibold">Menunggu pemeriksaan / retur</p>
        </div>

        <!-- Estimated Loss Valuation -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estimasi Kerugian</p>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-800 mt-1 font-mono">Rp {{ number_format($totalLossValuation, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3 font-medium">Berdasarkan harga standar barang</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Search & Filter Bar -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('gudang.damaged_items.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari ref / SKU / nama barang..." 
                           class="w-full h-10 pl-9 pr-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 bg-slate-50 focus:bg-white font-medium">
                </div>

                <select name="status" class="h-10 px-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="Dalam Karantina" {{ request('status') == 'Dalam Karantina' ? 'selected' : '' }}>Dalam Karantina</option>
                    <option value="Dimusnahkan" {{ request('status') == 'Dimusnahkan' ? 'selected' : '' }}>Dimusnahkan</option>
                    <option value="Diretur" {{ request('status') == 'Diretur' ? 'selected' : '' }}>Diretur</option>
                </select>

                <select name="damage_type" class="h-10 px-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Jenis Kerusakan</option>
                    <option value="Rusak Fisik" {{ request('damage_type') == 'Rusak Fisik' ? 'selected' : '' }}>Rusak Fisik</option>
                    <option value="Kadaluarsa / Expired" {{ request('damage_type') == 'Kadaluarsa / Expired' ? 'selected' : '' }}>Kadaluarsa</option>
                    <option value="Cacat Pabrik" {{ request('damage_type') == 'Cacat Pabrik' ? 'selected' : '' }}>Cacat Pabrik</option>
                    <option value="Hilang / Selisih" {{ request('damage_type') == 'Hilang / Selisih' ? 'selected' : '' }}>Hilang / Selisih</option>
                </select>

                <button type="submit" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-xs uppercase text-slate-400 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">No. Referensi</th>
                        <th class="px-6 py-3.5">Nama Barang & SKU</th>
                        <th class="px-6 py-3.5 text-center">Jumlah</th>
                        <th class="px-6 py-3.5">Jenis Kerusakan</th>
                        <th class="px-6 py-3.5">Tindakan / Status</th>
                        <th class="px-6 py-3.5">Pelapor & Waktu</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($damagedItems as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-slate-800 text-xs">
                            {{ $item->reference_number }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 text-sm">{{ $item->item->name ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $item->item->sku ?? '-' }} • {{ $item->item->category->name ?? 'Uncategorized' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-lg font-black font-mono text-xs">
                                {{ $item->quantity }} {{ $item->item->uom->symbol ?? 'Unit' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($item->damage_type == 'Kadaluarsa / Expired') bg-amber-50 text-amber-700
                                @elseif($item->damage_type == 'Rusak Fisik') bg-rose-50 text-rose-700
                                @elseif($item->damage_type == 'Cacat Pabrik') bg-blue-50 text-blue-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $item->damage_type }}
                            </span>
                            @if($item->notes)
                                <p class="text-[11px] text-slate-400 italic mt-0.5 max-w-[200px] truncate" title="{{ $item->notes }}">{{ $item->notes }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                                @if($item->status == 'Dalam Karantina') bg-amber-100 text-amber-800
                                @elseif($item->status == 'Dimusnahkan') bg-slate-200 text-slate-700
                                @elseif($item->status == 'Diretur') bg-blue-100 text-blue-800
                                @else bg-emerald-100 text-emerald-800 @endif">
                                <span class="w-1.5 h-1.5 rounded-full 
                                    @if($item->status == 'Dalam Karantina') bg-amber-500
                                    @elseif($item->status == 'Dimusnahkan') bg-slate-500
                                    @elseif($item->status == 'Diretur') bg-blue-500
                                    @else bg-emerald-500 @endif"></span>
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            <p class="font-bold text-slate-700">{{ $item->user->name ?? 'Admin Gudang' }}</p>
                            <p class="text-slate-400 text-[11px]">{{ $item->reported_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" 
                                    @click="openStatusModal('{{ $item->id }}', '{{ $item->reference_number }}', '{{ $item->status }}')"
                                    class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all cursor-pointer">
                                Update Status
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">check_circle</span>
                            <p class="font-bold text-slate-700 text-sm">Tidak ada catatan barang rusak.</p>
                            <p class="text-xs text-slate-400 mt-0.5">Semua stok barang tercatat dalam kondisi prima.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($damagedItems->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $damagedItems->links() }}
        </div>
        @endif
    </div>

    <!-- CREATE MODAL: RECORD DAMAGED GOODS -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 max-w-lg w-full transform transition-all" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-600 text-[24px]">report</span>
                    <h3 class="text-lg font-bold text-slate-800 font-headline">Catat Barang Rusak / Karantina</h3>
                </div>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('gudang.damaged_items.store') }}" method="POST" class="space-y-4 pt-4">
                @csrf
                <!-- Item Select -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Pilih Barang yang Rusak / Bermasalah *</label>
                    <select name="item_id" 
                            x-model="selectedItemId" 
                            @change="updateStockInfo(selectedItemId, {{ Js::from($items) }})" 
                            required 
                            class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-indigo-500 bg-white">
                        <option value="">-- Pilih Barang dari Gudang --</option>
                        @foreach($items as $i)
                            <option value="{{ $i->id }}">{{ $i->name }} (SKU: {{ $i->sku }} | Tersedia: {{ $i->stock }})</option>
                        @endforeach
                    </select>
                    <template x-if="selectedItemStock > 0">
                        <p class="text-[11px] text-emerald-600 font-semibold mt-1">Stok fisik tersedia di sistem: <span class="font-bold" x-text="selectedItemStock"></span> Unit</p>
                    </template>
                </div>

                <!-- Quantity & Damage Type -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Jumlah Rusak *</label>
                        <input type="number" 
                               name="quantity" 
                               min="1" 
                               :max="selectedItemStock || 9999" 
                               required 
                               placeholder="1" 
                               class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs font-bold font-mono focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Jenis Kerusakan *</label>
                        <select name="damage_type" required class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-indigo-500 bg-white">
                            <option value="Rusak Fisik">Rusak Fisik (Pecah/Penyok)</option>
                            <option value="Kadaluarsa / Expired">Kadaluarsa / Expired</option>
                            <option value="Cacat Pabrik">Cacat Pabrik / Malfungsi</option>
                            <option value="Hilang / Selisih">Hilang / Selisih Opname</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <!-- Action Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Tindakan Selanjutnya *</label>
                    <select name="action_type" required class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-indigo-500 bg-white">
                        <option value="Karantina">Karantina (Pisahkan ke rak karantina untuk evaluasi)</option>
                        <option value="Pemusnahan / Write-Off">Pemusnahan / Write-Off (Hapus & musnahkan)</option>
                        <option value="Retur Supplier">Retur Supplier (Klaim garansi / pengembalian ke vendor)</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Keterangan / Kronologi Kerusakan</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Kemasan basah saat pembongkaran muatan truk..." class="w-full p-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-rose-600/20">
                        Simpan & Kurangi Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- STATUS UPDATE MODAL -->
    <div x-show="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-6 max-w-sm w-full transform transition-all" @click.away="showStatusModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 font-headline">Update Status Karantina</h3>
                <button @click="showStatusModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="`{{ url('gudang/damaged-items') }}/${statusUpdateId}/status`" method="POST" class="space-y-4 pt-3">
                @csrf
                <div>
                    <p class="text-xs text-slate-400">No. Referensi: <span class="font-bold text-slate-800 font-mono" x-text="statusUpdateRef"></span></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Pilih Status Baru *</label>
                    <select name="status" required class="w-full h-11 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-indigo-500 bg-white">
                        <option value="Dalam Karantina">Dalam Karantina</option>
                        <option value="Dimusnahkan">Dimusnahkan (Selesai Write-Off)</option>
                        <option value="Diretur">Diretur (Dikembalikan ke Supplier)</option>
                        <option value="Selesai Diproses">Selesai Diproses</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Catatan Tindak Lanjut</label>
                    <textarea name="notes" rows="2" placeholder="Catatan hasil tindak lanjut barang..." class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showStatusModal = false" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        Perbarui Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
