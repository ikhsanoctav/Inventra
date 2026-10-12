@extends('layouts.app')

@section('header_title', 'Cetak Barcode & Label')

@section('main_content')
<div class="space-y-6" x-data="barcodePrintApp()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[26px]">qr_code_2</span>
                <h2 class="font-headline text-2xl font-bold text-slate-800">Generator & Cetak Label Barcode</h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">Buat dan cetak stiker barcode SKU untuk rak gudang atau kemasan produk.</p>
        </div>

        <!-- Print Action Button -->
        <button type="button" 
                @click="submitPrintForm"
                :disabled="selectedCount === 0"
                :class="selectedCount === 0 ? 'opacity-40 cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-600/20'"
                class="px-5 py-2.5 rounded-2xl text-sm font-bold flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak <span x-text="totalLabelsToPrint"></span> Label</span>
        </button>
    </div>

    <!-- Template Options & Preview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Configuration & Template Selector (4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 space-y-4">
                <h3 class="font-bold text-slate-800 font-headline text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600 text-[20px]">tune</span>
                    Pilih Format Template
                </h3>

                <!-- Template Choices -->
                <div class="space-y-2.5">
                    <!-- Template 1: Rack Label -->
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                           :class="template === 'rack_label' ? 'bg-indigo-50/60 border-indigo-400 ring-2 ring-indigo-400/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/70'">
                        <input type="radio" name="template" value="rack_label" x-model="template" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <p class="font-bold text-slate-800 text-xs sm:text-sm">Label Rak Gudang (100 x 40 mm)</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format besar untuk ditempel di rak besi gudang. Lengkap dengan SKU, Nama Barang, dan UoM.</p>
                        </div>
                    </label>

                    <!-- Template 2: Product Barcode -->
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                           :class="template === 'product_barcode' ? 'bg-indigo-50/60 border-indigo-400 ring-2 ring-indigo-400/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/70'">
                        <input type="radio" name="template" value="product_barcode" x-model="template" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <p class="font-bold text-slate-800 text-xs sm:text-sm">Label SKU Produk (50 x 30 mm)</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format ringkas untuk stiker satuan pada kardus atau kemasan barang.</p>
                        </div>
                    </label>

                    <!-- Template 3: A4 Grid -->
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                           :class="template === 'a4_grid' ? 'bg-indigo-50/60 border-indigo-400 ring-2 ring-indigo-400/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/70'">
                        <input type="radio" name="template" value="a4_grid" x-model="template" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <p class="font-bold text-slate-800 text-xs sm:text-sm">Lembar Stiker A4 (Grid 3 x 8)</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Cetak massal dalam kertas stiker lembaran A4 standar printer kantor.</p>
                        </div>
                    </label>
                </div>

                <!-- Code Type Selection (Barcode 1D vs QR Code 2D) -->
                <div class="space-y-1.5 pt-3 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700">Tipe Simbol Kode</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-2xl border text-xs font-bold cursor-pointer transition-all"
                               :class="codeType === 'barcode' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 ring-2 ring-indigo-400/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="code_type_choice" value="barcode" x-model="codeType" @change="renderPreview()" class="sr-only">
                            <span class="material-symbols-outlined text-[16px]">barcode</span>
                            <span>Barcode (1D)</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-2xl border text-xs font-bold cursor-pointer transition-all"
                               :class="codeType === 'qrcode' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 ring-2 ring-indigo-400/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                            <input type="radio" name="code_type_choice" value="qrcode" x-model="codeType" @change="renderPreview()" class="sr-only">
                            <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                            <span>QR Code (2D)</span>
                        </label>
                    </div>
                </div>

                <!-- Additional Printing Options -->
                <div class="space-y-2 pt-3 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700">Preferensi Cetak Label</label>
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                        <input type="checkbox" x-model="autoFormat" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Format Standar Sistem (<code>KAT-KODE-YYMMDD-URUT</code>)</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                        <input type="checkbox" x-model="showBorder" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Tampilkan Garis Batas Potong Stiker</span>
                    </label>
                </div>

                <!-- Template Preview Box -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Preview Contoh Stiker</p>
                    <div class="bg-slate-100 p-4 rounded-2xl flex items-center justify-center">
                        <div class="bg-white p-3 rounded-xl border border-slate-300 shadow-sm text-center max-w-[220px] w-full">
                            <p class="text-[10px] font-bold text-slate-800 truncate">Kertas HVS A4 80 GSM</p>
                            <div class="my-1.5 flex justify-center items-center min-h-[42px]">
                                <svg id="previewBarcodeSvg" x-show="codeType === 'barcode'" class="max-w-full h-10"></svg>
                                <div id="previewQrContainer" x-show="codeType === 'qrcode'" class="flex justify-center my-0.5"></div>
                            </div>
                            <p class="font-mono text-[9px] font-bold text-slate-600">ATK-BRG-261009-001</p>
                            <p class="text-[9px] font-bold text-indigo-600" x-show="template === 'rack_label'">Lokasi: Rak A-01</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table with Multi-Select & Copies (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <!-- Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
                <form method="GET" action="{{ route('gudang.barcode.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                    <div class="relative flex-1 min-w-[200px]">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari SKU atau nama barang..." 
                               class="w-full h-9 pl-9 pr-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 bg-white font-medium">
                    </div>

                    <select name="category_id" onchange="this.form.submit()" class="h-9 px-2.5 rounded-xl border border-slate-200 bg-white text-xs font-medium focus:outline-none focus:border-indigo-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="h-9 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                        Cari
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAllToggle" class="text-xs font-bold text-indigo-600 hover:underline">
                        <span x-text="isAllSelected ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs uppercase text-slate-400 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 w-10 text-center">
                                <input type="checkbox" @click="selectAllToggle" :checked="isAllSelected" class="rounded text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-5 py-3.5">Barang & SKU</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5 text-center">Stok Fisik</th>
                            <th class="px-5 py-3.5 text-center w-28">Jumlah Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-xs">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors" :class="selectedItems['{{ $item->id }}'] ? 'bg-indigo-50/30' : ''">
                            <td class="px-5 py-3 text-center">
                                <input type="checkbox" 
                                       @change="toggleItem('{{ $item->id }}')" 
                                       :checked="Boolean(selectedItems['{{ $item->id }}'])" 
                                       class="rounded text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-bold text-slate-800 text-sm leading-snug">{{ $item->name }}</p>
                                <p class="font-mono text-slate-400 text-[11px]">{{ $item->sku }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ $item->category->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-mono font-bold text-slate-700">{{ $item->stock }}</span> {{ $item->uom->symbol ?? 'Unit' }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" 
                                           min="1" 
                                           max="50" 
                                           :disabled="!selectedItems['{{ $item->id }}']"
                                           x-model.number="itemQuantities['{{ $item->id }}']" 
                                           class="w-16 h-8 text-center font-bold font-mono text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-indigo-500 bg-white disabled:bg-slate-100 disabled:text-slate-400">
                                    <span class="text-[10px] text-slate-400 font-medium">lbr</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Tidak ada data barang fisik ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Hidden POST Form for Printing -->
    <form id="barcodePrintForm" method="POST" action="{{ route('gudang.barcode.print') }}" target="_blank" style="display: none;">
        @csrf
        <input type="hidden" name="template" :value="template">
        <input type="hidden" name="code_type" :value="codeType">
        <input type="hidden" name="auto_format" :value="autoFormat ? '1' : '0'">
        <input type="hidden" name="show_border" :value="showBorder ? '1' : '0'">
        <template x-for="(qty, id) in getSelectedPayload()" :key="id">
            <input type="hidden" :name="`items[${id}]`" :value="qty">
        </template>
    </form>
</div>

<!-- JsBarcode & QRCode CDN -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('barcodePrintApp', () => ({
            template: 'rack_label',
            codeType: 'barcode',
            autoFormat: true,
            showBorder: true,
            selectedItems: {},
            itemQuantities: {},
            allItemIds: @json($items->pluck('id')),

            init() {
                // Initialize quantities
                this.allItemIds.forEach(id => {
                    this.itemQuantities[id] = 1;
                });

                this.renderPreview();
            },

            renderPreview() {
                this.$nextTick(() => {
                    if (this.codeType === 'barcode') {
                        try {
                            JsBarcode("#previewBarcodeSvg", "ATK-BRG-261009-001", {
                                format: "CODE128",
                                lineColor: "#000",
                                width: 1.5,
                                height: 35,
                                displayValue: false
                            });
                        } catch(e){}
                    } else if (this.codeType === 'qrcode') {
                        const container = document.getElementById("previewQrContainer");
                        if (container && typeof QRCode !== 'undefined') {
                            container.innerHTML = '';
                            new QRCode(container, {
                                text: "ATK-BRG-261009-001",
                                width: 50,
                                height: 50,
                                correctLevel: QRCode.CorrectLevel.M
                            });
                        }
                    }
                });
            },

            toggleItem(id) {
                if (this.selectedItems[id]) {
                    delete this.selectedItems[id];
                } else {
                    this.selectedItems[id] = true;
                    if (!this.itemQuantities[id]) {
                        this.itemQuantities[id] = 1;
                    }
                }
            },

            get selectedCount() {
                return Object.keys(this.selectedItems).length;
            },

            get isAllSelected() {
                return this.allItemIds.length > 0 && this.allItemIds.every(id => this.selectedItems[id]);
            },

            selectAllToggle() {
                if (this.isAllSelected) {
                    this.selectedItems = {};
                } else {
                    this.allItemIds.forEach(id => {
                        this.selectedItems[id] = true;
                        if (!this.itemQuantities[id]) {
                            this.itemQuantities[id] = 1;
                        }
                    });
                }
            },

            get totalLabelsToPrint() {
                let total = 0;
                for (const id in this.selectedItems) {
                    total += (this.itemQuantities[id] || 1);
                }
                return total;
            },

            getSelectedPayload() {
                const payload = {};
                for (const id in this.selectedItems) {
                    payload[id] = this.itemQuantities[id] || 1;
                }
                return payload;
            },

            submitPrintForm() {
                if (this.selectedCount === 0) return;
                this.$nextTick(() => {
                    document.getElementById('barcodePrintForm').submit();
                });
            }
        }));
    });
</script>
@endsection
