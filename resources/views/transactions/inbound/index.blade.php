@extends('layouts.app')

@section('header_title', 'Transaksi Barang Masuk')

@section('main_content')
<div class="space-y-6" x-data="inboundManager()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Barang Masuk (Inbound)</h2>
            <p class="text-sm text-[#6B7280]">Catat dan kelola penerimaan stok barang dari supplier ke gudang.</p>
        </div>
        <div class="flex items-center gap-3">
            @if(isset($approvedPOs) && $approvedPOs->count() > 0)
            <button @click="openModalWithPo()" class="h-10 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">shopping_cart_checkout</span>
                <span>Tarik dari PO ({{ $approvedPOs->count() }})</span>
            </button>
            @endif
            <button @click="openModalManual()" class="h-10 px-4 rounded-xl bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Buat Inbound</span>
            </button>
        </div>
    </div>

    @if(isset($approvedPOs) && $approvedPOs->count() > 0)
    <!-- Banner PO Siap Diterima -->
    <div class="bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 border border-purple-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-500/20">
                <span class="material-symbols-outlined text-[20px]">fact_check</span>
            </div>
            <div>
                <h4 class="text-sm font-bold text-purple-900">Terdapat {{ $approvedPOs->count() }} Purchase Order (PO) Siap Diterima</h4>
                <p class="text-xs text-purple-700">Barang dari PO yang telah disetujui dapat ditarik langsung ke transaksi Inbound tanpa input manual.</p>
            </div>
        </div>
        <button @click="openModalWithPo()" class="px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap">
            Proses Penerimaan PO
        </button>
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E5E7EB] shadow-sm relative">
        <form action="{{ route('transactions.inbound') }}" method="GET" class="flex flex-col sm:flex-row gap-4 relative"
              hx-get="{{ route('transactions.inbound') }}"
              hx-target="#table-container"
              hx-select="#table-container"
              hx-swap="outerHTML"
              hx-trigger="input changed delay:500ms from:input[name='search'], change from:select"
              x-data="{ loading: false }"
              @htmx:before-request.camel="loading = true"
              @htmx:after-request.camel="loading = false">
            <div x-show="loading" style="display: none;" class="absolute -top-3 right-4 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Loading...
            </div>
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Referensi..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
            <div class="sm:w-48">
                <select name="status" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">Semua Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="sm:w-32">
                <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('transactions.inbound') }}" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold flex items-center transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <div id="table-container" class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto w-full pb-4">
            <table class="w-full text-left text-sm text-[#4B5563]">
                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                    <tr>
                        <th scope="col" class="px-6 py-4">No. Referensi</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Pembuat</th>
                        <th scope="col" class="px-6 py-4">Catatan / PO Link</th>
                        <th scope="col" class="px-6 py-4 text-center">Status Penerimaan</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($transactions as $inbound)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $inbound->ref_number }}</td>
                        <td class="px-6 py-4 text-[#6B7280]">{{ \Carbon\Carbon::parse($inbound->transaction_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-[#6B7280]">{{ $inbound->user->name ?? 'Sistem' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate">
                            {{ $inbound->notes ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $inbound->status === 'Completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $inbound->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('transactions.inbound.show', $inbound->id) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs transition-colors inline-block">Detail & Item</a>
                            <form action="{{ route('transactions.inbound.destroy', $inbound->id) }}" method="POST" class="inline ml-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" onclick="return confirm('Yakin hapus transaksi ini?')" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-slate-300 mb-4">inbox</span>
                                <p class="text-slate-500 font-medium">Tidak ada transaksi inbound yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Modal Buat Inbound -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showModal" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200 z-10">
                <div class="bg-white px-6 pb-6 pt-6">
                    <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">login</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-headline font-bold text-slate-900">Buat Transaksi Inbound</h3>
                                <p class="text-xs text-slate-500">Penerimaan barang masuk ke gudang logistik.</p>
                            </div>
                        </div>
                        <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors rounded-xl p-1.5 hover:bg-slate-100">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>

                    <!-- Mode Selection Tabs -->
                    <div class="flex bg-slate-100 p-1 rounded-xl mb-5">
                        <button type="button" @click="activeTab = 'manual'" 
                                :class="activeTab === 'manual' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                class="flex-1 py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">edit_note</span> Input Manual
                        </button>
                        <button type="button" @click="activeTab = 'po'" 
                                :class="activeTab === 'po' ? 'bg-white text-purple-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                class="flex-1 py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">shopping_cart_checkout</span> Tarik dari PO Disetujui
                            @if(isset($approvedPOs) && $approvedPOs->count() > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-700 text-[10px] font-bold">{{ $approvedPOs->count() }}</span>
                            @endif
                        </button>
                    </div>
                    
                    <form action="{{ route('transactions.inbound') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- PO Selection (When Tab is 'po') -->
                        <div x-show="activeTab === 'po'" class="space-y-3 bg-purple-50/50 p-4 rounded-2xl border border-purple-100">
                            <label class="block text-xs font-bold text-purple-900">Pilih Purchase Order (Disetujui) <span class="text-red-500">*</span></label>
                            <select name="po_id" x-model="selectedPoId" @change="onPoSelect()" class="w-full h-10 px-3 text-sm bg-white border border-purple-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 font-medium">
                                <option value="">-- Pilih Purchase Order --</option>
                                @if(isset($approvedPOs))
                                @foreach($approvedPOs as $poItem)
                                <option value="{{ $poItem->id }}" data-ref="{{ $poItem->ref_number }}" data-lines-count="{{ $poItem->lines->count() }}" data-total="{{ $poItem->total_amount }}">
                                    {{ $poItem->ref_number }} ({{ $poItem->lines->count() }} Barang • Rp {{ number_format($poItem->total_amount, 0, ',', '.') }})
                                </option>
                                @endforeach
                                @endif
                            </select>

                            <div x-show="selectedPoSummary" class="p-3 bg-white rounded-xl border border-purple-200 text-xs space-y-1.5">
                                <div class="flex justify-between text-slate-600">
                                    <span>Referensi PO:</span>
                                    <span class="font-bold text-purple-700" x-text="selectedPoSummary.ref"></span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Jumlah Item:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedPoSummary.linesCount + ' Jenis Barang'"></span>
                                </div>
                                <p class="text-[11px] text-emerald-600 font-medium pt-1 border-t border-purple-50">
                                    ✓ Semua barang dari PO ini akan otomatis disalin ke Inbound dan PO akan ditandai Selesai.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Referensi Inbound <span class="text-red-500">*</span></label>
                            <input type="text" name="ref_number" x-model="refNumber" required class="w-full h-10 px-3 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Penerimaan <span class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Supplier (Opsional)</label>
                            <select name="supplier_id" class="w-full h-10 px-3 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20">
                                <option value="">-- Tanpa Supplier --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan</label>
                            <input type="text" name="notes" placeholder="Contoh: Diterima oleh tim shift pagi gudang 1" class="w-full h-10 px-3 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Awal <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full h-10 px-3 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20">
                                <option value="Draft">Draft (Input/Cek Fisik Dulu)</option>
                                <option value="Completed">Selesai (Langsung Tambah ke Stok)</option>
                            </select>
                        </div>
                        
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 sm:w-auto transition-colors">
                                Buat Transaksi Inbound
                            </button>
                            <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function inboundManager() {
    return {
        showModal: false,
        activeTab: 'manual',
        selectedPoId: '',
        selectedPoSummary: null,
        refNumber: 'INB-{{ time() }}',
        openModalManual() {
            this.activeTab = 'manual';
            this.selectedPoId = '';
            this.selectedPoSummary = null;
            this.refNumber = 'INB-' + Math.floor(Date.now() / 1000);
            this.showModal = true;
        },
        openModalWithPo() {
            this.activeTab = 'po';
            this.showModal = true;
        },
        onPoSelect() {
            const selectEl = document.querySelector('select[name="po_id"]');
            if (selectEl && selectEl.selectedIndex > 0) {
                const opt = selectEl.options[selectEl.selectedIndex];
                this.selectedPoSummary = {
                    ref: opt.dataset.ref,
                    linesCount: opt.dataset.linesCount,
                    total: opt.dataset.total
                };
                this.refNumber = 'INB-' + opt.dataset.ref;
            } else {
                this.selectedPoSummary = null;
                this.refNumber = 'INB-' + Math.floor(Date.now() / 1000);
            }
        }
    }
}
</script>
@endsection
