@extends('layouts.guest')

@section('header_title', 'Point of Sale')

@section('content')
<div x-data="posSystem()" @keydown.window="handleGlobalKeys($event)" class="flex flex-col md:flex-row h-screen bg-slate-50 overflow-hidden font-sans relative select-none">
    
    <!-- MAIN CONTENT: Product Grid -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative z-10 w-full">
        <!-- POS Header -->
        <div class="bg-white px-4 md:px-6 py-3 md:py-3.5 border-b border-slate-200 flex flex-wrap md:flex-nowrap items-center justify-between gap-3 shadow-sm z-10">
            <div class="flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-600/20">
                    <span class="material-symbols-outlined text-[22px]">shopping_cart</span>
                </div>
                <div>
                    <h1 class="font-headline text-lg md:text-xl font-bold text-slate-800 leading-tight">Terminal Kasir</h1>
                    <p class="hidden sm:block text-xs text-slate-400 font-medium">Mode Kasir Cepat • {{ auth()->user()->name }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 md:gap-3 flex-1 justify-end">
                <!-- Search -->
                <div class="relative hidden lg:block w-full max-w-xs">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" 
                           id="posSearchInput"
                           x-model="searchQuery" 
                           @input.debounce.300ms="resetAndSearch" 
                           @keydown.enter.prevent="handleSearchEnter"
                           placeholder="Scan barcode / cari nama... (F2)" 
                           class="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>

                <!-- Type Toggle (Fisik / Jasa) -->
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200/80 shrink-0">
                    <button type="button" @click="changeType('barang')" :class="itemType === 'barang' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 md:px-4 py-1.5 text-xs md:text-sm font-bold rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                        <span>Fisik</span>
                    </button>
                    <button type="button" @click="changeType('jasa')" :class="itemType === 'jasa' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 md:px-4 py-1.5 text-xs md:text-sm font-bold rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">design_services</span>
                        <span>Jasa</span>
                    </button>
                </div>

                <!-- Camera Barcode Scanner Button -->
                <button type="button" 
                        @click="openCameraScanner()" 
                        class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-700 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm cursor-pointer" 
                        title="Scan Barcode Kamera (F3)">
                    <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                </button>

                <!-- Held Carts Button -->
                <button type="button" 
                        @click="showHeldCartsModal = true" 
                        class="relative px-3 py-2 rounded-xl border border-slate-200 text-slate-700 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 font-bold text-xs md:text-sm flex items-center gap-2 transition-all cursor-pointer shadow-sm">
                    <span class="material-symbols-outlined text-amber-500 text-[18px]">pause_circle</span>
                    <span class="hidden sm:inline">Antrian</span>
                    <span x-show="heldCarts.length > 0" 
                          class="bg-amber-500 text-white text-[11px] font-black px-1.5 py-0.2 rounded-full" 
                          x-text="heldCarts.length"></span>
                </button>
                
                <!-- Size Toggle -->
                <div class="hidden xl:flex bg-slate-100 p-1 rounded-xl border border-slate-200/80 shrink-0 text-xs font-bold">
                    <button type="button" @click="cardSize = 'S'" :class="cardSize === 'S' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1 rounded-lg transition-all">S</button>
                    <button type="button" @click="cardSize = 'M'" :class="cardSize === 'M' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1 rounded-lg transition-all">M</button>
                    <button type="button" @click="cardSize = 'L'" :class="cardSize === 'L' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1 rounded-lg transition-all">L</button>
                </div>
                
                <!-- History -->
                <a href="{{ route('pos.history.today') }}" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-indigo-600 bg-indigo-50/50 hover:bg-indigo-100/70 hover:border-indigo-200 transition-all shadow-sm" title="Riwayat Penjualan">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                </a>
                
                <!-- Dashboard Exit -->
                <a href="{{ route('dashboard') }}" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 transition-all shadow-sm" title="Keluar ke Dashboard">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="lg:hidden px-4 py-2.5 bg-white border-b border-slate-200 flex items-center gap-2">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.300ms="resetAndSearch" 
                       @keydown.enter.prevent="handleSearchEnter"
                       placeholder="Scan barcode / cari barang..." 
                       class="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <button type="button" @click="openCameraScanner()" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0" title="Scan Barcode Kamera">
                <span class="material-symbols-outlined text-[20px]">photo_camera</span>
            </button>
        </div>

        <!-- Product Grid Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 pb-32 md:pb-6" id="productGrid" @scroll="checkScroll">
            <div :class="{
                'grid gap-2.5 grid-cols-2 sm:grid-cols-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-7': cardSize === 'S',
                'grid gap-3.5 grid-cols-2 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5': cardSize === 'M',
                'grid gap-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4': cardSize === 'L'
            }">
                
                <template x-for="item in items" :key="item.id">
                    <button @click="addToCart(item.id, item.name, item.standard_price, item.stock)" 
                            class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden hover:border-indigo-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group flex flex-col text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <!-- Image Container -->
                        <div :class="{
                                'h-18 md:h-22': cardSize === 'S',
                                'h-28 md:h-36': cardSize === 'M',
                                'h-36 md:h-48': cardSize === 'L'
                            }" class="bg-slate-100 flex items-center justify-center w-full relative overflow-hidden">
                            
                            <template x-if="item.image">
                                <img :src="'/' + item.image" :alt="item.name" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                            </template>

                            <template x-if="!item.image">
                                <span class="material-symbols-outlined text-slate-300 group-hover:text-indigo-400 transition-colors text-[36px]">
                                    <span x-text="item.type === 'jasa' ? 'design_services' : 'inventory_2'"></span>
                                </span>
                            </template>
                            
                            <template x-if="item.type === 'barang'">
                                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold shadow-sm backdrop-blur-sm" 
                                     :class="item.stock > 10 ? 'bg-emerald-500/90 text-white' : (item.stock > 0 ? 'bg-amber-500/90 text-white' : 'bg-rose-500/90 text-white')">
                                    <span x-text="item.stock > 0 ? 'Stok ' + item.stock : 'Habis'"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Details -->
                        <div class="p-3 flex-1 flex flex-col justify-between w-full">
                            <div>
                                <p class="font-mono text-slate-400 text-[10px] uppercase tracking-wider mb-0.5" x-text="item.sku"></p>
                                <h3 class="font-bold text-slate-800 leading-snug line-clamp-2 text-xs md:text-sm group-hover:text-indigo-600 transition-colors" x-text="item.name"></h3>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between">
                                <span class="text-indigo-600 font-extrabold font-mono text-sm md:text-base" x-text="formatRupiah(item.standard_price)"></span>
                                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                </span>
                            </div>
                        </div>
                    </button>
                </template>

                <!-- Loading Indicator -->
                <div x-show="loadingItems" class="col-span-full flex justify-center py-6">
                    <span class="material-symbols-outlined animate-spin text-indigo-600 text-[36px]">progress_activity</span>
                </div>

                <!-- Empty State -->
                <div x-show="!loadingItems && items.length === 0" class="col-span-full flex flex-col items-center justify-center h-64 text-slate-400">
                    <span class="material-symbols-outlined text-[48px] mb-2 text-slate-300">production_quantity_limits</span>
                    <p class="text-sm font-semibold text-slate-600">Tidak ada item ditemukan.</p>
                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci pencarian lain atau ubah filter kategori.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Mobile Cart Button -->
    <button @click="mobileCartOpen = !mobileCartOpen" 
            class="md:hidden fixed bottom-4 right-4 z-40 bg-indigo-600 text-white rounded-2xl px-5 py-3.5 shadow-2xl flex items-center gap-3 hover:bg-indigo-700 transition-all active:scale-95">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span x-show="cart.length > 0" class="bg-rose-500 text-white text-xs font-black px-2 py-0.5 rounded-full" x-text="cart.length"></span>
        <span class="font-bold text-sm" x-text="formatRupiah(total)"></span>
    </button>

    <!-- RIGHT SIDE: Cart Sidebar -->
    <div :class="mobileCartOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
         class="fixed inset-y-0 right-0 w-full sm:w-96 md:relative md:w-84 lg:w-96 bg-white border-l border-slate-200 shadow-2xl md:shadow-none flex flex-col z-30 transition-transform duration-300 ease-in-out">
        
        <!-- Cart Header with Hold Action -->
        <div class="px-4 md:px-5 py-3.5 border-b border-slate-100 bg-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                </div>
                <div>
                    <h2 class="font-bold text-sm md:text-base text-slate-800 leading-tight">Keranjang Belanja</h2>
                    <p class="text-[11px] text-slate-400" x-text="cart.length + ' item dipilih'"></p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Hold Cart Button -->
                <button type="button" 
                        @click="openHoldModal"
                        :disabled="cart.length === 0"
                        :class="cart.length === 0 ? 'opacity-40 cursor-not-allowed text-slate-400' : 'text-amber-600 hover:bg-amber-50 hover:border-amber-200'"
                        class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-bold flex items-center gap-1 transition-all cursor-pointer"
                        title="Tahan Antrian (F4)">
                    <span class="material-symbols-outlined text-[15px]">pause_circle</span>
                    <span>Tahan (F4)</span>
                </button>

                <!-- Clear Cart -->
                <button @click="clearCart" 
                        :disabled="cart.length === 0"
                        :class="cart.length === 0 ? 'opacity-40 cursor-not-allowed' : 'text-rose-600 hover:bg-rose-50'"
                        class="p-1.5 rounded-xl text-rose-500 transition-colors" title="Kosongkan Keranjang">
                    <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                </button>

                <!-- Close mobile drawer -->
                <button @click="mobileCartOpen = false" class="md:hidden text-slate-400 p-1 rounded-lg hover:bg-slate-100">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <!-- Customer & Note Quick Bar -->
        <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400 text-[16px]">person</span>
            <input type="text" 
                   x-model="customerName" 
                   placeholder="Nama Pelanggan (Opsional / Walk-in)" 
                   class="bg-transparent text-xs w-full focus:outline-none text-slate-700 placeholder:text-slate-400 font-medium">
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2.5 bg-slate-50/60">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-slate-400 p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3 text-slate-300">
                        <span class="material-symbols-outlined text-[32px]">shopping_basket</span>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Keranjang Masih Kosong</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[200px]">Klik item pada layar di sebelah kiri untuk menambahkannya ke transaksi.</p>
                </div>
            </template>
            
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col gap-2 relative group hover:border-indigo-300 transition-all">
                    <div class="flex justify-between items-start pr-6">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 leading-tight" x-text="item.name"></h4>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="formatRupiah(item.price) + ' / unit'"></p>
                        </div>
                        <p class="text-xs sm:text-sm font-black text-indigo-600 font-mono whitespace-nowrap" x-text="formatRupiah(item.price * item.quantity)"></p>
                    </div>
                    
                    <div class="flex items-center justify-between pt-1 border-t border-slate-50">
                        <!-- Max Stock Alert -->
                        <span class="text-[10px] text-slate-400">
                            <template x-if="item.maxStock !== undefined">
                                <span>Maks: <span x-text="item.maxStock"></span></span>
                            </template>
                        </span>
                        
                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-2 bg-slate-100 rounded-xl p-0.5">
                            <button @click="updateQuantity(index, -1)" class="w-6 h-6 flex items-center justify-center rounded-lg bg-white shadow-xs text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-colors">
                                <span class="material-symbols-outlined text-[14px]">remove</span>
                            </button>
                            <span class="w-6 text-center text-xs font-black text-slate-800" x-text="item.quantity"></span>
                            <button @click="updateQuantity(index, 1)" class="w-6 h-6 flex items-center justify-center rounded-lg bg-white shadow-xs text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-colors">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remove Item Button -->
                    <button @click="removeItem(index)" class="absolute top-2.5 right-2.5 w-6 h-6 flex items-center justify-center rounded-lg text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                        <span class="material-symbols-outlined text-[15px]">close</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Checkout Summary Area -->
        <div class="bg-white border-t border-slate-200 p-4 sm:p-5 shadow-[0_-4px_10px_rgba(0,0,0,0.03)] space-y-3">
            
            <!-- Breakdown Calculations -->
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal</span>
                    <span class="font-mono font-bold text-slate-700" x-text="formatRupiah(subtotal)"></span>
                </div>
                
                <!-- Discount Input Toggle -->
                <div class="flex justify-between items-center text-slate-500">
                    <span class="flex items-center gap-1">
                        <span>Diskon</span>
                        <template x-if="discountAmount > 0">
                            <span class="text-[10px] text-emerald-600 bg-emerald-50 px-1 rounded font-bold">Aktif</span>
                        </template>
                    </span>
                    <div class="relative w-28">
                        <input type="number" 
                               x-model.number="discountAmount" 
                               placeholder="Rp 0" 
                               class="w-full h-6 px-2 text-right font-mono text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex justify-between text-slate-500">
                    <span>PPN (11%)</span>
                    <span class="font-mono font-bold text-slate-700" x-text="formatRupiah(tax)"></span>
                </div>

                <div class="pt-2 border-t border-dashed border-slate-200 flex justify-between items-end">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</span>
                        <div class="text-xl sm:text-2xl font-black text-slate-800 font-headline tracking-tight" x-text="formatRupiah(total)"></div>
                    </div>
                </div>
            </div>

            <!-- Multi-Payment Method Tabs (Tunai / QRIS / Debit / Transfer) -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Metode Pembayaran</label>
                <div class="grid grid-cols-4 gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200/80 text-xs font-bold">
                    <button type="button" @click="setPaymentMethod('Cash')" :class="paymentMethod === 'Cash' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-1.5 rounded-lg transition-all flex flex-col items-center gap-0.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">payments</span>
                        <span class="text-[10px]">Tunai</span>
                    </button>
                    <button type="button" @click="setPaymentMethod('QRIS')" :class="paymentMethod === 'QRIS' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-1.5 rounded-lg transition-all flex flex-col items-center gap-0.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                        <span class="text-[10px]">QRIS</span>
                    </button>
                    <button type="button" @click="setPaymentMethod('Debit')" :class="paymentMethod === 'Debit' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-1.5 rounded-lg transition-all flex flex-col items-center gap-0.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">credit_card</span>
                        <span class="text-[10px]">Debit/EDC</span>
                    </button>
                    <button type="button" @click="setPaymentMethod('Transfer')" :class="paymentMethod === 'Transfer' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-1.5 rounded-lg transition-all flex flex-col items-center gap-0.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">account_balance</span>
                        <span class="text-[10px]">Transfer</span>
                    </button>
                </div>
            </div>

            <!-- Cash Input Controls -->
            <div x-show="paymentMethod === 'Cash'" class="space-y-2 pt-1">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" 
                           id="posPaidInput"
                           x-model.number="paidAmount" 
                           class="w-full h-11 pl-9 pr-3 text-right text-base font-black font-mono rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" 
                           placeholder="0">
                </div>
                <!-- Quick Money Pills -->
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button" @click="paidAmount = total" class="py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition-colors">Uang Pas</button>
                    <button type="button" @click="paidAmount = 50000" class="py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition-colors">50rb</button>
                    <button type="button" @click="paidAmount = 100000" class="py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition-colors">100rb</button>
                    <button type="button" @click="paidAmount = 200000" class="py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition-colors">200rb</button>
                </div>
                
                <div class="flex justify-between items-center p-2 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800" x-show="paidAmount >= total && total > 0">
                    <span class="text-xs font-bold">Kembalian:</span>
                    <span class="text-base font-black font-mono" x-text="formatRupiah(paidAmount - total)"></span>
                </div>
            </div>

            <!-- Card / EDC Details -->
            <div x-show="paymentMethod === 'Debit'" class="space-y-2 pt-1 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <select x-model="selectedBank" class="h-9 px-2 rounded-xl border border-slate-200 bg-white font-medium focus:outline-none focus:border-indigo-500">
                        <option value="BCA">BCA (EDC)</option>
                        <option value="Mandiri">Mandiri (EDC)</option>
                        <option value="BRI">BRI (EDC)</option>
                        <option value="BNI">BNI (EDC)</option>
                        <option value="Lainnya">Bank Lainnya</option>
                    </select>
                    <input type="text" x-model="cardRef" placeholder="4 Digit / No. Approval" class="h-9 px-3 rounded-xl border border-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Bank Transfer Details -->
            <div x-show="paymentMethod === 'Transfer'" class="space-y-2 pt-1 text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <p class="font-bold text-slate-700">Rekening Tujuan:</p>
                <p class="font-mono text-indigo-600 font-bold text-[11px]">BCA 123-456-7890 a.n. PT Inventra</p>
                <input type="text" x-model="transferRef" placeholder="No. Referensi / ID Pengirim" class="w-full h-8 px-2.5 rounded-lg border border-slate-200 bg-white font-mono focus:outline-none focus:border-indigo-500 text-xs">
            </div>

            <!-- Checkout Action Button -->
            <button @click="initiateCheckout" 
                    :disabled="cart.length === 0 || (paymentMethod === 'Cash' && paidAmount < total) || processing"
                    :class="cart.length === 0 || (paymentMethod === 'Cash' && paidAmount < total) || processing ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-600/20 active:scale-98'"
                    class="w-full h-12 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all cursor-pointer">
                <span x-show="!processing" class="material-symbols-outlined text-[20px]">check_circle</span>
                <span x-show="processing" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                <span x-text="processing ? 'Memproses Transaksi...' : 'Bayar Sekarang (F8)'"></span>
            </button>
        </div>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- 1. HELD CARTS (ANTRIAN TERTARIK) MODAL -->
    <div x-show="showHeldCartsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-6 max-w-lg w-full transform transition-all max-h-[85vh] flex flex-col" @click.away="showHeldCartsModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-[24px]">pause_circle</span>
                    <h3 class="text-lg font-bold text-slate-800 font-headline">Daftar Antrian Keranjang Tertahan</h3>
                </div>
                <button @click="showHeldCartsModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-4 space-y-3">
                <template x-if="heldCarts.length === 0">
                    <div class="py-12 text-center text-slate-400">
                        <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">hourglass_empty</span>
                        <p class="font-semibold text-sm">Tidak ada antrian tertahan.</p>
                        <p class="text-xs text-slate-400 mt-1">Anda dapat menekan tombol "Tahan (F4)" saat pelanggan meninggalkan kasir sementara.</p>
                    </div>
                </template>

                <template x-for="(held, index) in heldCarts" :key="held.id">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex flex-col gap-3 hover:border-amber-300 transition-all">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm" x-text="held.customerName || ('Antrian #' + (index + 1))"></h4>
                                <p class="text-[11px] text-slate-400" x-text="'Disimpan: ' + held.heldAt"></p>
                            </div>
                            <span class="font-black text-indigo-600 font-mono text-sm" x-text="formatRupiah(held.total)"></span>
                        </div>

                        <!-- Item Snippet -->
                        <div class="bg-white p-2.5 rounded-xl border border-slate-100 text-xs text-slate-600 space-y-1">
                            <template x-for="item in held.items" :key="item.id">
                                <div class="flex justify-between">
                                    <span class="truncate max-w-[200px]" x-text="item.quantity + 'x ' + item.name"></span>
                                    <span class="font-mono font-semibold" x-text="formatRupiah(item.price * item.quantity)"></span>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button @click="deleteHeldCart(index)" class="px-3 py-1.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition-colors">
                                Hapus
                            </button>
                            <button @click="recallHeldCart(index)" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                                <span>Panggil Kembali</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button @click="showHeldCartsModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 2. HOLD CART PROMPT MODAL -->
    <div x-show="showHoldPromptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-6 max-w-sm w-full text-center transform transition-all" @click.away="showHoldPromptModal = false">
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[28px]">pause_circle</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 font-headline mb-1">Tahan Keranjang Ini?</h3>
            <p class="text-xs text-slate-500 mb-4">Keranjang akan disimpan di daftar antrian dan kasir siap melayani pelanggan berikutnya.</p>
            
            <input type="text" 
                   x-model="holdCustomerNameInput" 
                   placeholder="Nama / No. Meja Pelanggan" 
                   class="w-full h-11 px-3.5 mb-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500 font-medium">

            <div class="flex gap-2">
                <button @click="showHoldPromptModal = false" class="flex-1 h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                    Batal
                </button>
                <button @click="confirmHoldCart" class="flex-1 h-11 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-amber-500/20">
                    Tahan Antrian
                </button>
            </div>
        </div>
    </div>

    <!-- 3. QRIS DYNAMIC PAYMENT MODAL -->
    <div x-show="showQRModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-7 max-w-sm w-full text-center transform transition-all relative">
            <button @click="showQRModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
            
            <div class="flex justify-center mb-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-7">
            </div>
            
            <h3 class="text-base font-bold text-slate-800 mb-3">Total Tagihan: <span x-text="formatRupiah(total)" class="text-indigo-600 font-mono font-black"></span></h3>
            
            <div class="bg-white p-3 rounded-2xl border-4 border-indigo-50 shadow-inner inline-block mb-4 relative overflow-hidden">
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=INVENTRA-PAY-${total}-${Date.now()}`" alt="QRIS Code" class="w-44 h-44">
                <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500 opacity-60 shadow-[0_0_10px_#6366f1] animate-[scan_2s_ease-in-out_infinite]"></div>
            </div>
            
            <p class="text-xs text-slate-500 mb-5">Arahkan kamera aplikasi GoPay, OVO, Dana, ShopeePay, atau BCA Mobile ke QR di atas.</p>
            
            <button @click="executeCheckout" class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">verified</span>
                <span>Konfirmasi Pembayaran Sukses</span>
            </button>
        </div>
    </div>
    
    <!-- 4. CLEAR CART CONFIRM MODAL -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-6 max-w-sm w-full text-center transform transition-all" @click.away="showConfirmModal = false">
            <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[28px]">warning</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1 font-headline">Kosongkan Keranjang?</h3>
            <p class="text-xs text-slate-500 mb-5">Semua item dalam pesanan ini akan dihapus dari terminal.</p>
            
            <div class="flex gap-2">
                <button @click="showConfirmModal = false" class="flex-1 h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                    Batal
                </button>
                <button @click="confirmClearCart" class="flex-1 h-11 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors shadow-md shadow-rose-600/20">
                    Ya, Kosongkan
                </button>
            </div>
        </div>
    </div>

    <!-- 5. SUCCESS TRANSACTION MODAL -->
    <div x-show="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-7 max-w-md w-full text-center transform transition-all">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-md shadow-emerald-500/10">
                <span class="material-symbols-outlined text-[36px]">check_circle</span>
            </div>
            <h3 class="text-2xl font-black font-headline text-slate-800 mb-1">Transaksi Berhasil!</h3>
            <p class="text-xs text-slate-500 mb-4">No. Dokumen: <span class="font-mono font-bold text-slate-800" x-text="lastTransactionRef"></span></p>
            
            <!-- Summary Info -->
            <div class="bg-slate-50 rounded-2xl p-4 mb-5 border border-slate-100 space-y-2 text-left text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Metode Bayar:</span>
                    <span class="font-bold text-slate-800" x-text="paymentMethod"></span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Total Pembayaran:</span>
                    <span class="font-bold text-slate-800 font-mono" x-text="formatRupiah(lastGrandTotal)"></span>
                </div>
                <template x-if="paymentMethod === 'Cash'">
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                        <span class="font-bold text-emerald-700">Kembalian:</span>
                        <span class="text-xl font-black text-emerald-600 font-mono" x-text="formatRupiah(lastChange)"></span>
                    </div>
                </template>
            </div>
            
            <div class="flex gap-2">
                <button @click="resetPOS" class="flex-1 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-indigo-600/20 cursor-pointer">
                    Transaksi Baru
                </button>
                <button @click="printReceipt" class="h-12 px-5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Cetak Struk</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL CAMERA SCANNER -->
    <div x-show="showCameraScannerModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600 text-[22px]">qr_code_scanner</span>
                    <div>
                        <h3 class="font-headline font-bold text-slate-800 text-sm">Pemindai Barcode Kamera</h3>
                        <p class="text-[11px] text-slate-400">Pindai barcode produk langsung via kamera</p>
                    </div>
                </div>
                <button type="button" @click="closeCameraScanner()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-4 flex flex-col items-center justify-center">
                <div id="cameraScannerViewport" class="w-full aspect-square bg-slate-900 rounded-2xl overflow-hidden relative shadow-inner flex items-center justify-center">
                    <div x-show="isScannerInitializing" class="text-white text-xs flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined animate-spin text-2xl text-indigo-400">progress_activity</span>
                        <span>Mengaktifkan kamera...</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-3 text-center">Arahkan kamera ke barcode / QR produk. Item akan otomatis dimasukkan ke keranjang.</p>
            </div>
            <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400 font-mono">Status: Kamera Aktif</span>
                <button type="button" @click="closeCameraScanner()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                    Tutup Kamera
                </button>
            </div>
        </div>
    </div>

    <!-- Scan Toast Notification -->
    <div x-show="showScanToast" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900/90 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 backdrop-blur-md border border-white/10 pointer-events-none">
        <span class="material-symbols-outlined text-emerald-400 text-[20px]">check_circle</span>
        <span class="text-xs font-bold" x-text="scanToastMessage"></span>
    </div>

</div>

<style>
    @keyframes scan {
        0%, 100% { top: 0; }
        50% { top: 100%; }
    }
</style>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('posSystem', () => ({
            searchQuery: '',
            cart: [],
            items: {{ Js::from($initialItems->items()) }},
            page: 1,
            hasMoreItems: {{ $initialItems->hasMorePages() ? 'true' : 'false' }},
            loadingItems: false,
            paymentMethod: 'Cash',
            paidAmount: 0,
            discountAmount: 0,
            customerName: '',
            selectedBank: 'BCA',
            cardRef: '',
            transferRef: '',
            processing: false,
            showQRModal: false,
            showSuccessModal: false,
            showConfirmModal: false,
            showHeldCartsModal: false,
            showHoldPromptModal: false,
            showCameraScannerModal: false,
            isScannerInitializing: false,
            html5QrScanner: null,
            showScanToast: false,
            scanToastMessage: '',
            holdCustomerNameInput: '',
            activeIdempotencyKey: null,
            mobileCartOpen: false,
            lastTransactionRef: '',
            lastChange: 0,
            lastGrandTotal: 0,
            itemType: '{{ $type }}',
            cardSize: 'M',
            heldCarts: [],
            barcodeBuffer: '',
            lastScanTime: 0,

            init() {
                // Load held carts from LocalStorage
                try {
                    const saved = localStorage.getItem('inventra_pos_held_carts');
                    if (saved) {
                        this.heldCarts = JSON.parse(saved);
                    }
                } catch (e) {
                    console.error('Failed to parse held carts:', e);
                }

                this.$watch('paymentMethod', value => {
                    if (value !== 'Cash') {
                        this.paidAmount = this.total;
                    } else {
                        this.paidAmount = 0;
                    }
                });
            },

            saveHeldCartsToStorage() {
                localStorage.setItem('inventra_pos_held_carts', JSON.stringify(this.heldCarts));
            },

            openHoldModal() {
                if (this.cart.length === 0) return;
                this.holdCustomerNameInput = this.customerName || '';
                this.showHoldPromptModal = true;
            },

            confirmHoldCart() {
                const heldObj = {
                    id: 'HOLD-' + Date.now(),
                    customerName: this.holdCustomerNameInput || 'Pelanggan #' + (this.heldCarts.length + 1),
                    items: JSON.parse(JSON.stringify(this.cart)),
                    subtotal: this.subtotal,
                    discount: this.discountAmount,
                    total: this.total,
                    heldAt: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                };

                this.heldCarts.unshift(heldObj);
                this.saveHeldCartsToStorage();

                // Clear current cart
                this.cart = [];
                this.paidAmount = 0;
                this.discountAmount = 0;
                this.customerName = '';
                this.showHoldPromptModal = false;
                this.showToast('Keranjang berhasil ditahan di daftar antrian.');
            },

            recallHeldCart(index) {
                const held = this.heldCarts[index];
                if (!held) return;

                if (this.cart.length > 0) {
                    if (!confirm('Keranjang saat ini tidak kosong. Ganti dengan keranjang yang dipanggil?')) {
                        return;
                    }
                }

                this.cart = JSON.parse(JSON.stringify(held.items));
                this.customerName = held.customerName || '';
                this.discountAmount = held.discount || 0;
                this.heldCarts.splice(index, 1);
                this.saveHeldCartsToStorage();
                this.showHeldCartsModal = false;
                this.showToast('Keranjang antrian berhasil dipanggil kembali.');
            },

            deleteHeldCart(index) {
                if (confirm('Hapus antrian tertahan ini?')) {
                    this.heldCarts.splice(index, 1);
                    this.saveHeldCartsToStorage();
                }
            },

            setPaymentMethod(method) {
                this.paymentMethod = method;
                if (method !== 'Cash') {
                    this.paidAmount = this.total;
                }
            },

            handleSearchEnter() {
                if (this.searchQuery && this.searchQuery.trim()) {
                    const code = this.searchQuery.trim();
                    this.handleBarcodeScanned(code);
                }
            },

            handleGlobalKeys(e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('posSearchInput')?.focus();
                    return;
                } else if (e.key === 'F3') {
                    e.preventDefault();
                    this.openCameraScanner();
                    return;
                } else if (e.key === 'F4') {
                    e.preventDefault();
                    this.openHoldModal();
                    return;
                } else if (e.key === 'F8') {
                    e.preventDefault();
                    this.initiateCheckout();
                    return;
                } else if (e.key === 'F9' && this.paymentMethod === 'Cash') {
                    e.preventDefault();
                    this.paidAmount = this.total;
                    return;
                } else if (e.key === 'Escape') {
                    this.showQRModal = false;
                    this.showConfirmModal = false;
                    this.showHeldCartsModal = false;
                    this.showHoldPromptModal = false;
                    this.closeCameraScanner();
                    return;
                }

                // Automatic Hardware Barcode Scanner Buffer (USB / Wireless Scanner Gun)
                const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                const activeId = document.activeElement ? document.activeElement.id : '';
                const isModalInput = (activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select') && activeId !== 'posSearchInput';

                const now = Date.now();

                if (e.key === 'Enter') {
                    if (this.barcodeBuffer.length >= 2) {
                        e.preventDefault();
                        const scanned = this.barcodeBuffer;
                        this.barcodeBuffer = '';
                        this.handleBarcodeScanned(scanned);
                    }
                } else if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey && !isModalInput) {
                    if (now - this.lastScanTime > 200) {
                        this.barcodeBuffer = e.key;
                    } else {
                        this.barcodeBuffer += e.key;
                    }
                    this.lastScanTime = now;
                }
            },

            playBeep() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(1046.5, audioCtx.currentTime); // C6 tone
                    gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.1);
                } catch(e) {}
            },

            showToast(message) {
                this.scanToastMessage = message;
                this.showScanToast = true;
                setTimeout(() => {
                    this.showScanToast = false;
                }, 2500);
            },

            openCameraScanner() {
                this.showCameraScannerModal = true;
                this.isScannerInitializing = true;
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.startCameraScanner();
                    }, 300);
                });
            },

            startCameraScanner() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Library kamera belum selesai dimuat.');
                    this.isScannerInitializing = false;
                    return;
                }

                if (this.html5QrScanner) {
                    try { this.html5QrScanner.stop(); } catch(e) {}
                }

                this.html5QrScanner = new Html5Qrcode("cameraScannerViewport");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                this.html5QrScanner.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        this.handleBarcodeScanned(decodedText);
                    },
                    (errorMessage) => {
                        // scanning frame ignore
                    }
                ).then(() => {
                    this.isScannerInitializing = false;
                }).catch((err) => {
                    console.error("Camera scanner error:", err);
                    this.isScannerInitializing = false;
                    alert("Gagal mengakses kamera. Pastikan izin kamera telah diberikan.");
                    this.closeCameraScanner();
                });
            },

            closeCameraScanner() {
                if (this.html5QrScanner) {
                    this.html5QrScanner.stop().then(() => {
                        this.html5QrScanner.clear();
                        this.html5QrScanner = null;
                    }).catch(() => {
                        this.html5QrScanner = null;
                    });
                }
                this.showCameraScannerModal = false;
                this.isScannerInitializing = false;
            },

            async handleBarcodeScanned(barcode) {
                if (!barcode || !barcode.trim()) return;
                const cleanCode = barcode.trim();
                this.closeCameraScanner();

                // 1. Check in local items first (match exact SKU or exact name)
                let matchedItem = this.items.find(i => (i.sku && i.sku.toLowerCase() === cleanCode.toLowerCase()) || (i.name && i.name.toLowerCase() === cleanCode.toLowerCase()));

                if (matchedItem) {
                    this.addToCart(matchedItem.id, matchedItem.name, matchedItem.standard_price, matchedItem.stock);
                    this.showToast(`[${matchedItem.name}] ditambahkan ke keranjang!`);
                    this.searchQuery = '';
                    return;
                }

                // 2. Query backend if not in currently loaded page
                try {
                    const url = new URL('{{ route("pos.items") }}', window.location.origin);
                    url.searchParams.append('search', cleanCode);
                    url.searchParams.append('type', this.itemType);
                    const res = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    if (data.data && data.data.length > 0) {
                        const item = data.data[0];
                        this.addToCart(item.id, item.name, item.standard_price, item.stock);
                        this.showToast(`[${item.name}] ditambahkan ke keranjang!`);
                        this.searchQuery = '';
                    } else {
                        // Fallback check regardless of type
                        const fallbackUrl = new URL('{{ route("pos.items") }}', window.location.origin);
                        fallbackUrl.searchParams.append('search', cleanCode);
                        const fbRes = await fetch(fallbackUrl.toString(), {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin'
                        });
                        const fbData = await fbRes.json();
                        if (fbData.data && fbData.data.length > 0) {
                            const item = fbData.data[0];
                            this.addToCart(item.id, item.name, item.standard_price, item.stock);
                            this.showToast(`[${item.name}] ditambahkan ke keranjang!`);
                            this.searchQuery = '';
                        } else {
                            alert(`Barang dengan barcode/SKU "${cleanCode}" tidak ditemukan.`);
                        }
                    }
                } catch(e) {
                    console.error("Error searching scanned item:", e);
                }
            },

            async loadItems(append = false) {
                if (this.loadingItems || (!append && !this.hasMoreItems && this.page > 1)) return;
                
                this.loadingItems = true;
                try {
                    const url = new URL('{{ route("pos.items") }}', window.location.origin);
                    url.searchParams.append('type', this.itemType);
                    url.searchParams.append('page', this.page);
                    if (this.searchQuery) url.searchParams.append('search', this.searchQuery);

                    const res = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store',
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    
                    if (append) {
                        this.items = [...this.items, ...data.data];
                    } else {
                        this.items = data.data;
                    }
                    
                    this.hasMoreItems = data.current_page < data.last_page;
                } catch (e) {
                    console.error('Failed to load items', e);
                } finally {
                    this.loadingItems = false;
                }
            },

            resetAndSearch() {
                this.page = 1;
                this.hasMoreItems = true;
                this.loadItems(false);
            },

            changeType(type) {
                if (this.itemType === type) return;
                this.itemType = type;
                this.items = [];
                this.resetAndSearch();
            },

            checkScroll(e) {
                const el = e.target;
                if (el.scrollHeight - el.scrollTop <= el.clientHeight + 100) {
                    if (this.hasMoreItems && !this.loadingItems) {
                        this.page++;
                        this.loadItems(true);
                    }
                }
            },

            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },
            
            get tax() {
                const taxable = Math.max(0, this.subtotal - this.discountAmount);
                return taxable * 0.11; // 11% PPN
            },
            
            get total() {
                const taxable = Math.max(0, this.subtotal - this.discountAmount);
                return taxable + this.tax;
            },

            formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0);
            },

            addToCart(id, name, price, stock) {
                this.playBeep();
                const existingIndex = this.cart.findIndex(item => item.id === id);
                
                if (existingIndex > -1) {
                    if (this.itemType === 'barang' && this.cart[existingIndex].quantity >= stock) {
                        alert('Stok tidak mencukupi!');
                        return;
                    }
                    this.cart[existingIndex].quantity++;
                } else {
                    if (this.itemType === 'barang' && stock < 1) {
                        alert('Stok barang ini telah habis!');
                        return;
                    }
                    this.cart.push({ id, name, price, quantity: 1, maxStock: stock });
                }
                
                if (this.paymentMethod !== 'Cash') {
                    this.paidAmount = this.total;
                }
            },

            updateQuantity(index, change) {
                const item = this.cart[index];
                const newQuantity = item.quantity + change;
                
                if (newQuantity < 1) {
                    this.removeItem(index);
                    return;
                }
                
                if (this.itemType === 'barang' && item.maxStock !== undefined && newQuantity > item.maxStock) {
                    alert('Kuantitas melampaui sisa stok (' + item.maxStock + ')!');
                    return;
                }
                
                item.quantity = newQuantity;
                
                if (this.paymentMethod !== 'Cash') {
                    this.paidAmount = this.total;
                }
            },

            removeItem(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                this.showConfirmModal = true;
            },

            confirmClearCart() {
                this.cart = [];
                this.paidAmount = 0;
                this.discountAmount = 0;
                this.customerName = '';
                this.showConfirmModal = false;
            },
            
            initiateCheckout() {
                if (this.cart.length === 0) return;

                if (this.paymentMethod === 'Cash' && this.paidAmount < this.total) {
                    alert('Uang pembayaran kurang dari total tagihan!');
                    document.getElementById('posPaidInput')?.focus();
                    return;
                }

                if (this.paymentMethod === 'QRIS') {
                    this.showQRModal = true;
                } else {
                    this.executeCheckout();
                }
            },

            async executeCheckout() {
                this.showQRModal = false;
                this.processing = true;

                if (this.paymentMethod !== 'Cash') {
                    this.paidAmount = this.total;
                }

                const paymentDetails = {};
                if (this.paymentMethod === 'Debit') {
                    paymentDetails.bank = this.selectedBank;
                    paymentDetails.ref = this.cardRef;
                } else if (this.paymentMethod === 'Transfer') {
                    paymentDetails.bank = 'BCA';
                    paymentDetails.ref = this.transferRef;
                }

                // Generate idempotency key for this checkout if not already created
                if (!this.activeIdempotencyKey) {
                    this.activeIdempotencyKey = 'pos-' + Date.now() + '-' + Math.random().toString(36).substring(2, 10);
                }

                try {
                    const response = await fetch("{{ route('pos.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Idempotency-Key': this.activeIdempotencyKey
                        },
                        body: JSON.stringify({
                            items: this.cart,
                            payment_method: this.paymentMethod,
                            paid_amount: this.paidAmount,
                            customer_name: this.customerName,
                            discount_amount: this.discountAmount,
                            payment_details: paymentDetails
                        })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        this.activeIdempotencyKey = null; // Successfully processed, clear key
                        this.lastTransactionRef = result.transaction_ref;
                        this.lastChange = result.change;
                        this.lastGrandTotal = result.grand_total || this.total;
                        this.showSuccessModal = true;
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan sistem'));
                    }
                } catch (error) {
                    // On connection failure, keep this.activeIdempotencyKey so retries deduplicate safely!
                    alert('Gagal terhubung ke server. Silakan coba kembali.');
                    console.error(error);
                } finally {
                    this.processing = false;
                }
            },

            resetPOS() {
                this.activeIdempotencyKey = null;
                this.cart = [];
                this.paidAmount = 0;
                this.discountAmount = 0;
                this.customerName = '';
                this.paymentMethod = 'Cash';
                this.searchQuery = '';
                this.showSuccessModal = false;
                this.resetAndSearch();
            },

            printReceipt() {
                if (this.lastTransactionRef) {
                    const printUrl = `{{ url('pos/receipt') }}/${this.lastTransactionRef}`;
                    window.open(printUrl, '_blank', 'width=420,height=650');
                }
            }
        }));
    });
</script>
@endpush
@endsection