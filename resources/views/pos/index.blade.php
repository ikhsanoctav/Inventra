@extends('layouts.guest') <!-- Using guest layout to hide standard sidebar and topbar, giving full screen to POS -->

@section('header_title', 'Point of Sale')

@section('content')
<div x-data="posSystem()" class="flex flex-col md:flex-row h-screen bg-slate-50 overflow-hidden font-sans relative">
    
    <!-- MAIN CONTENT: Product Grid -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative z-10 w-full">
        <!-- POS Header -->
        <div class="bg-white px-4 md:px-6 py-3 md:py-4 border-b border-[#E5E7EB] flex flex-wrap md:flex-nowrap items-center justify-between gap-4 shadow-sm z-10">
            <div class="flex items-center gap-3 md:gap-4 shrink-0">
                <div class="shrink-0 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-headline font-bold text-xl shadow-sm">
                    IN
                </div>
                <div class="whitespace-nowrap">
                    <h1 class="font-headline text-lg md:text-xl font-bold text-[#111827] leading-tight">Terminal Kasir</h1>
                    <p class="hidden md:block text-xs text-[#6B7280]">Mode Layar Penuh (Tablet Optimized)</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 md:gap-3 flex-1 justify-end">
                <!-- Search -->
                <div class="relative hidden lg:block w-full max-w-xs">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[20px]">search</span>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="resetAndSearch" placeholder="Cari nama barang atau SKU..." class="w-full h-10 pl-10 pr-4 rounded-lg border border-[#E5E7EB] bg-[#F8FAFC] text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                </div>

                <!-- Type Toggle -->
                <div class="flex bg-[#F1F5F9] p-1 rounded-lg border border-[#E2E8F0] shrink-0">
                    <button type="button" @click="changeType('barang')" :class="itemType === 'barang' ? 'bg-white text-indigo-700 shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-3 md:px-4 py-1.5 text-xs md:text-sm font-semibold rounded-md transition-colors">
                        Fisik
                    </button>
                    <button type="button" @click="changeType('jasa')" :class="itemType === 'jasa' ? 'bg-white text-indigo-700 shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-3 md:px-4 py-1.5 text-xs md:text-sm font-semibold rounded-md transition-colors">
                        Jasa
                    </button>
                </div>
                
                <!-- Size Toggle -->
                <div class="flex bg-[#F1F5F9] p-1 rounded-lg border border-[#E2E8F0] shrink-0">
                    <button type="button" @click="cardSize = 'S'" :class="cardSize === 'S' ? 'bg-white text-indigo-700 shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2 md:px-3 py-1.5 text-xs md:text-sm font-semibold rounded-md transition-colors">
                        S
                    </button>
                    <button type="button" @click="cardSize = 'M'" :class="cardSize === 'M' ? 'bg-white text-indigo-700 shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2 md:px-3 py-1.5 text-xs md:text-sm font-semibold rounded-md transition-colors">
                        M
                    </button>
                    <button type="button" @click="cardSize = 'L'" :class="cardSize === 'L' ? 'bg-white text-indigo-700 shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2 md:px-3 py-1.5 text-xs md:text-sm font-semibold rounded-md transition-colors">
                        L
                    </button>
                    <button type="button" @click="cardSize = 'XL'" :class="cardSize === 'XL' ? 'bg-white text-indigo-700 shadow-sm' : 'text-[#64748B] hover:text-[#111827]'" class="px-2 md:px-3 py-1.5 text-xs md:text-sm font-semibold rounded-md transition-colors">
                        XL
                    </button>
                </div>
                
                <a href="{{ route('pos.history.today') }}" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-lg border border-[#E5E7EB] text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-200 transition-colors ml-1 md:ml-2" title="Riwayat Transaksi">
                    <span class="material-symbols-outlined text-[20px] md:text-[24px]">history</span>
                </a>
                
                <a href="{{ route('dashboard') }}" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-lg border border-[#E5E7EB] text-[#4B5563] hover:bg-[#F1F5F9] transition-colors ml-1" title="Kembali ke Dashboard">
                    <span class="material-symbols-outlined text-[20px] md:text-[24px]">logout</span>
                </a>
            </div>
        </div>

        <!-- Product Grid Area -->
        <!-- Mobile Search Bar (Only visible on small screens) -->
        <div class="lg:hidden px-4 py-2 bg-white border-b border-[#E5E7EB]">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[20px]">search</span>
                <input type="text" x-model="searchQuery" @input.debounce.500ms="resetAndSearch" placeholder="Cari barang/jasa..." class="w-full h-10 pl-10 pr-4 rounded-lg border border-[#E5E7EB] bg-[#F8FAFC] text-sm focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 pb-32 md:pb-6" id="productGrid" @scroll="checkScroll">
            <div :class="{
                'grid gap-2 grid-cols-3 sm:grid-cols-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-7': cardSize === 'S',
                'grid gap-3 md:gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5': cardSize === 'M',
                'grid gap-4 md:gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4': cardSize === 'L',
                'grid gap-6 md:gap-8 grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3': cardSize === 'XL'
            }">
                
                <template x-for="item in items" :key="item.id">
                <!-- Product Card (Large tap target) -->
                <button @click="addToCart(item.id, item.name, item.standard_price, item.stock)" 
                        class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden hover:border-indigo-400 hover:shadow-md transition-all group flex flex-col text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <!-- Image Placeholder -->
                    <div :class="{
                            'h-16 md:h-20': cardSize === 'S',
                            'h-24 md:h-32': cardSize === 'M',
                            'h-32 md:h-48': cardSize === 'L',
                            'h-48 md:h-64': cardSize === 'XL'
                        }" class="bg-slate-100 flex items-center justify-center w-full relative overflow-hidden">
                        
                        <template x-if="item.image">
                            <img :src="'/' + item.image" :alt="item.name" class="w-full h-full object-cover transition-transform group-hover:scale-105" />
                        </template>

                        <template x-if="!item.image">
                            <span class="material-symbols-outlined text-slate-300 group-hover:text-indigo-300 transition-colors"
                                :class="{
                                    'text-[24px] md:text-[28px]': cardSize === 'S',
                                    'text-[32px] md:text-[40px]': cardSize === 'M',
                                    'text-[48px] md:text-[64px]': cardSize === 'L',
                                    'text-[64px] md:text-[80px]': cardSize === 'XL'
                                }">
                                <span x-text="item.type === 'jasa' ? 'design_services' : 'inventory_2'"></span>
                            </span>
                        </template>
                        
                        <template x-if="item.type === 'barang'">
                            <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold" 
                                 :class="item.stock > 10 ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#FEF3C7] text-[#B45309]'">
                                Sisa <span x-text="item.stock"></span>
                            </div>
                        </template>
                    </div>
                    <!-- Details -->
                    <div class="p-2 flex-1 flex flex-col justify-between w-full" :class="{ 'md:p-2': cardSize === 'S', 'md:p-3': cardSize === 'M' || cardSize === 'L', 'md:p-4': cardSize === 'XL' }">
                        <div>
                            <p class="font-mono text-[#6B7280] mb-0.5" :class="{ 'text-[8px] md:text-[9px]': cardSize === 'S', 'text-[9px] md:text-[10px]': cardSize === 'M' || cardSize === 'L', 'text-[10px] md:text-[11px]': cardSize === 'XL' }" x-text="item.sku"></p>
                            <h3 class="font-bold text-[#111827] leading-tight line-clamp-2" :class="{ 'text-[10px] md:text-xs': cardSize === 'S', 'text-xs md:text-sm': cardSize === 'M', 'text-sm md:text-base': cardSize === 'L', 'text-base md:text-lg': cardSize === 'XL' }" x-text="item.name"></h3>
                        </div>
                        <div class="mt-1 md:mt-2 text-indigo-700 font-bold font-mono" :class="{ 'text-xs md:text-sm': cardSize === 'S', 'text-sm md:text-base': cardSize === 'M', 'text-base md:text-lg': cardSize === 'L', 'text-lg md:text-xl': cardSize === 'XL' }">
                            <span x-text="formatRupiah(item.standard_price)"></span>
                        </div>
                    </div>
                </button>
                </template>

                <!-- Loading Indicator -->
                <div x-show="loadingItems" class="col-span-full flex justify-center py-4">
                    <span class="material-symbols-outlined animate-spin text-indigo-600 text-[32px]">progress_activity</span>
                </div>

                <!-- Empty State -->
                <div x-show="!loadingItems && items.length === 0" class="col-span-full flex flex-col items-center justify-center h-48 md:h-64 text-slate-400">
                    <span class="material-symbols-outlined text-[36px] md:text-[48px] mb-2">production_quantity_limits</span>
                    <p class="text-sm md:text-base">Tidak ada item ditemukan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Cart Toggle Button (Floating) -->
    <button @click="mobileCartOpen = !mobileCartOpen" 
            class="md:hidden fixed bottom-4 right-4 z-40 bg-indigo-600 text-white rounded-full p-4 shadow-xl flex items-center justify-center gap-2 hover:bg-indigo-700 transition-transform hover:scale-105 active:scale-95">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span x-show="cart.length > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center border-2 border-white" x-text="cart.length"></span>
        <span class="font-bold whitespace-nowrap">Rp <span x-text="total > 0 ? (total/1000) + 'K' : '0'"></span></span>
    </button>

    <!-- RIGHT SIDE: Cart Sidebar / Mobile Drawer -->
    <div :class="mobileCartOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
         class="fixed inset-y-0 right-0 w-full sm:w-96 md:relative md:w-80 lg:w-96 bg-white border-l border-[#E5E7EB] shadow-2xl md:shadow-xl flex flex-col z-30 transition-transform duration-300 ease-in-out">
        
        <!-- Cart Header -->
        <div class="px-4 md:px-5 py-3 md:py-4 border-b border-[#E5E7EB] bg-slate-50 flex items-center justify-between">
            <h2 class="font-bold text-base md:text-lg text-[#111827] flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">shopping_cart</span>
                Pesanan
            </h2>
            <div class="flex items-center gap-4">
                <button @click="clearCart" class="text-xs font-semibold text-red-600 hover:text-red-800 transition-colors">Kosongkan</button>
                <button @click="mobileCartOpen = false" class="md:hidden text-[#6B7280] p-1 rounded hover:bg-slate-200">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-2 bg-[#F8FAFC]">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-slate-400 p-6 text-center">
                    <span class="material-symbols-outlined text-[48px] mb-3 opacity-50">shopping_basket</span>
                    <p class="text-sm font-medium">Keranjang masih kosong.</p>
                    <p class="text-xs mt-1">Pilih item dari layar sebelah kiri untuk menambahkan ke pesanan.</p>
                </div>
            </template>
            
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="bg-white p-3 rounded-lg border border-[#E5E7EB] shadow-sm flex flex-col gap-2 relative group">
                    <div class="flex justify-between items-start pr-6">
                        <h4 class="text-sm font-bold text-[#111827] leading-tight" x-text="item.name"></h4>
                        <p class="text-sm font-bold text-indigo-700 font-mono whitespace-nowrap" x-text="formatRupiah(item.price * item.quantity)"></p>
                    </div>
                    
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-[#6B7280] font-mono" x-text="formatRupiah(item.price) + ' / unit'"></p>
                        
                        <!-- Quantity Controls (Large tap targets) -->
                        <div class="flex items-center gap-3 bg-[#F1F5F9] rounded-lg p-0.5">
                            <button @click="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white border border-[#E2E8F0] shadow-sm text-[#4B5563] hover:text-indigo-600 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">remove</span>
                            </button>
                            <span class="w-4 text-center text-sm font-bold text-[#111827]" x-text="item.quantity"></span>
                            <button @click="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white border border-[#E2E8F0] shadow-sm text-[#4B5563] hover:text-indigo-600 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remove Item Button -->
                    <button @click="removeItem(index)" class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Checkout Summary Area -->
        <div class="bg-white border-t border-[#E5E7EB] p-5 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            
            <div class="space-y-2 mb-4 text-sm">
                <div class="flex justify-between text-[#6B7280]">
                    <span>Subtotal</span>
                    <span class="font-mono font-medium" x-text="formatRupiah(subtotal)"></span>
                </div>
                <div class="flex justify-between text-[#6B7280]">
                    <span>Pajak (11%)</span>
                    <span class="font-mono font-medium" x-text="formatRupiah(tax)"></span>
                </div>
                <div class="pt-2 border-t border-dashed border-[#CBD5E1] flex justify-between items-end">
                    <span class="text-[#374151] font-semibold text-base">Total Akhir</span>
                    <span class="text-2xl font-bold text-[#111827] font-headline tracking-tight" x-text="formatRupiah(total)"></span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#374151] mb-2 uppercase tracking-wide">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-2">
                    <button @click="paymentMethod = 'Cash'" :class="paymentMethod === 'Cash' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 ring-1 ring-indigo-500' : 'bg-white border-[#E5E7EB] text-[#4B5563] hover:bg-[#F8FAFC]'" class="h-10 border rounded-lg flex items-center justify-center gap-2 font-medium text-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">payments</span> Tunai
                    </button>
                    <button @click="paymentMethod = 'Debit'" :class="paymentMethod === 'Debit' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 ring-1 ring-indigo-500' : 'bg-white border-[#E5E7EB] text-[#4B5563] hover:bg-[#F8FAFC]'" class="h-10 border rounded-lg flex items-center justify-center gap-2 font-medium text-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">credit_card</span> Debit/QRIS
                    </button>
                </div>
            </div>

            <!-- Amount Given Input (For Cash) -->
            <div x-show="paymentMethod === 'Cash'" class="mb-4">
                <label class="block text-xs font-semibold text-[#374151] mb-2 uppercase tracking-wide">Uang Diterima</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#6B7280] font-semibold">Rp</span>
                    <input type="number" x-model.number="paidAmount" class="w-full h-12 pl-10 pr-4 text-right text-lg font-bold font-mono rounded-lg border border-[#D1D5DB] focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="0">
                </div>
                <!-- Quick Amount Buttons -->
                <div class="flex gap-2 mt-2">
                    <button @click="paidAmount = total" class="flex-1 py-1 bg-[#F1F5F9] hover:bg-[#E2E8F0] rounded text-xs font-semibold text-[#475569] transition-colors">Pas</button>
                    <button @click="paidAmount = 50000" class="flex-1 py-1 bg-[#F1F5F9] hover:bg-[#E2E8F0] rounded text-xs font-semibold text-[#475569] transition-colors">50rb</button>
                    <button @click="paidAmount = 100000" class="flex-1 py-1 bg-[#F1F5F9] hover:bg-[#E2E8F0] rounded text-xs font-semibold text-[#475569] transition-colors">100rb</button>
                </div>
                
                <div class="mt-3 flex justify-between items-center" x-show="paidAmount >= total && total > 0">
                    <span class="text-sm font-semibold text-[#059669]">Kembalian:</span>
                    <span class="text-lg font-bold text-[#059669] font-mono" x-text="formatRupiah(paidAmount - total)"></span>
                </div>
            </div>

            <!-- QRIS Display (For Debit/QRIS) -->
            <div x-show="paymentMethod === 'Debit'" class="mb-4 bg-[#F8FAFC] border border-indigo-100 rounded-lg p-4 flex flex-col items-center justify-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-indigo-600">qr_code_scanner</span>
                </div>
                <p class="text-sm font-bold text-indigo-900 mb-1 text-center">Pembayaran via QRIS</p>
                <p class="text-xs text-center text-[#6B7280]">Kode QR akan muncul setelah Anda menekan tombol Proses Pembayaran.</p>
            </div>

            <button @click="initiateCheckout" 
                    :disabled="cart.length === 0 || (paymentMethod === 'Cash' && paidAmount < total) || processing"
                    :class="cart.length === 0 || (paymentMethod === 'Cash' && paidAmount < total) || processing ? 'bg-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg'"
                    class="w-full h-14 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-2 transition-all">
                <span x-show="!processing" class="material-symbols-outlined">receipt_long</span>
                <span x-text="processing ? 'Memproses...' : 'Proses Pembayaran'"></span>
            </button>
        </div>
    </div>
    
    <!-- QR Payment Modal -->
    <div x-show="showQRModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4" style="display: none;">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full text-center transform transition-all relative">
            <!-- Close button -->
            <button @click="showQRModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
            
            <div class="flex justify-center mb-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-8">
            </div>
            
            <h3 class="text-lg font-bold text-slate-800 mb-4">Total: <span x-text="formatRupiah(total)" class="text-indigo-600"></span></h3>
            
            <div class="bg-white p-3 rounded-2xl border-4 border-indigo-50 shadow-inner inline-block mb-6 relative">
                <!-- Placeholder QR using QRServer -->
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=INVENTRA-PAY-${total}-${Date.now()}`" alt="QRIS Code" class="w-48 h-48">
                
                <!-- Scanning animation line -->
                <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500 opacity-50 shadow-[0_0_10px_#6366f1] animate-[scan_2s_ease-in-out_infinite]"></div>
            </div>
            
            <p class="text-sm text-slate-500 mb-6">Silakan arahkan kamera/aplikasi e-wallet Anda ke kode QR di atas untuk menyelesaikan pembayaran.</p>
            
            <!-- Simulation button since we don't have real webhook integration yet -->
            <button @click="executeCheckout" class="w-full h-12 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">verified</span>
                Simulasi: Pembayaran Sukses
            </button>
        </div>
    </div>
    
    <!-- Confirm Modal -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full text-center transform transition-all" @click.away="showConfirmModal = false">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
                <span class="material-symbols-outlined text-[32px]">warning</span>
            </div>
            <h3 class="text-xl font-bold text-[#111827] mb-2">Kosongkan Pesanan?</h3>
            <p class="text-sm text-[#6B7280] mb-6">Semua item dalam keranjang akan dihapus dan tidak bisa dikembalikan.</p>
            
            <div class="flex gap-3">
                <button @click="showConfirmModal = false" class="flex-1 h-11 bg-white border border-[#E5E7EB] hover:bg-[#F8FAFC] text-[#374151] rounded-xl font-semibold transition-colors">
                    Batal
                </button>
                <button @click="confirmClearCart" class="flex-1 h-11 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-colors">
                    Ya, Kosongkan
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center transform transition-all">
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-emerald-600 text-[40px]">check_circle</span>
            </div>
            <h3 class="text-2xl font-bold font-headline text-[#111827] mb-2">Transaksi Berhasil!</h3>
            <p class="text-[#6B7280] mb-4">No. Ref: <span class="font-mono font-semibold text-[#111827]" x-text="lastTransactionRef"></span></p>
            
            <div x-show="paymentMethod === 'Cash'" class="bg-[#F8FAFC] rounded-xl p-4 mb-6 border border-[#E5E7EB]">
                <p class="text-sm font-semibold text-[#6B7280] uppercase tracking-wider mb-1">Kembalian</p>
                <p class="text-3xl font-bold text-emerald-600 font-mono tracking-tight" x-text="formatRupiah(lastChange)"></p>
            </div>
            
            <div class="flex gap-3">
                <button @click="resetPOS" class="flex-1 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors">
                    Transaksi Baru
                </button>
                <button @click="printReceipt" class="h-12 px-4 bg-white border border-[#E5E7EB] hover:bg-[#F8FAFC] text-[#1F2937] rounded-xl font-semibold transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#6B7280]">print</span>
                </button>
            </div>
        </div>
    </div>

</div>

<style>
    @keyframes scan {
        0%, 100% { top: 0; }
        50% { top: 100%; }
    }
</style>

@push('scripts')
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
            processing: false,
            showQRModal: false,
            showSuccessModal: false,
            showConfirmModal: false,
            mobileCartOpen: false,
            lastTransactionRef: '',
            lastChange: 0,
            itemType: '{{ $type }}',
            cardSize: 'M',

            init() {
                this.$watch('paymentMethod', value => {
                    if (value === 'Debit') {
                        this.paidAmount = this.total;
                    } else {
                        this.paidAmount = 0;
                    }
                });
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
                this.items = []; // Kosongkan layar saat mengambil data baru
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
                return this.subtotal * 0.11; // 11% PPN
            },
            
            get total() {
                return this.subtotal + this.tax;
            },

            formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            },

            addToCart(id, name, price, stock) {
                const existingIndex = this.cart.findIndex(item => item.id === id);
                
                if (existingIndex > -1) {
                    if (this.itemType === 'barang' && this.cart[existingIndex].quantity >= stock) {
                        alert('Stok tidak mencukupi!');
                        return;
                    }
                    this.cart[existingIndex].quantity++;
                } else {
                    if (this.itemType === 'barang' && stock < 1) {
                        alert('Stok habis!');
                        return;
                    }
                    this.cart.push({ id, name, price, quantity: 1, maxStock: stock });
                }
                
                // Auto-set paid amount to exact total if using debit
                if (this.paymentMethod === 'Debit') {
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
                
                if (this.itemType === 'barang' && newQuantity > item.maxStock) {
                    alert('Melampaui ketersediaan stok!');
                    return;
                }
                
                item.quantity = newQuantity;
                
                if (this.paymentMethod === 'Debit') {
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
                this.showConfirmModal = false;
            },
            
            initiateCheckout() {
                if (this.cart.length === 0) return;

                if (this.paymentMethod === 'Cash' && this.paidAmount < this.total) {
                    alert('Uang pembayaran kurang dari total belanja!');
                    return;
                }

                if (this.paymentMethod === 'Debit') {
                    this.showQRModal = true;
                } else {
                    this.executeCheckout();
                }
            },

            async executeCheckout() {
                this.showQRModal = false;
                this.processing = true;

                // Auto-adjust paidAmount if debit
                if (this.paymentMethod === 'Debit') {
                    this.paidAmount = this.total;
                }

                try {
                    const response = await fetch("{{ route('pos.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            items: this.cart,
                            payment_method: this.paymentMethod,
                            paid_amount: this.paidAmount
                        })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        this.lastTransactionRef = result.transaction_ref;
                        this.lastChange = result.change;
                        this.showSuccessModal = true;
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan sistem'));
                    }
                } catch (error) {
                    alert('Gagal terhubung ke server');
                    console.error(error);
                } finally {
                    this.processing = false;
                }
            },

            resetPOS() {
                this.cart = [];
                this.paidAmount = 0;
                this.paymentMethod = 'Cash';
                this.searchQuery = '';
                this.showSuccessModal = false;
                window.location.reload();
            },

            printReceipt() {
                if (this.lastTransactionRef) {
                    const printUrl = `{{ url('pos/receipt') }}/${this.lastTransactionRef}`;
                    window.open(printUrl, '_blank', 'width=400,height=600');
                }
            }
        }));
    });
</script>
@endpush
@endsection