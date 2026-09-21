<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Kasir - Inventra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F3F4F6; }
        .font-headline { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="h-screen overflow-hidden text-[#111827]" x-data="posApp()">
    <!-- Top Navigation for Tablet -->
    <header class="bg-white border-b border-[#E5E7EB] h-16 flex items-center justify-between px-6 shrink-0 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition">
                <span class="material-symbols-outlined text-[#4B5563]">arrow_back</span>
            </a>
            <h1 class="font-headline font-bold text-xl text-[#4F46E5]">Inventra POS</h1>
        </div>
        <div class="flex items-center gap-3 bg-gray-100 p-1 rounded-xl">
            <!-- Mode Toggle -->
            <button @click="currentType = 'goods'" :class="currentType === 'goods' ? 'bg-white shadow-sm text-[#4F46E5]' : 'text-[#6B7280] hover:text-[#111827]'" class="px-5 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">inventory_2</span> Barang Fisik
            </button>
            <button @click="currentType = 'service'" :class="currentType === 'service' ? 'bg-white shadow-sm text-[#4F46E5]' : 'text-[#6B7280] hover:text-[#111827]'" class="px-5 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">handyman</span> Jasa / Layanan
            </button>
        </div>
        <div class="flex items-center gap-3 text-sm font-semibold text-[#4B5563]">
            <span class="material-symbols-outlined">person</span> {{ auth()->user()->name }}
        </div>
    </header>

    <div class="flex h-[calc(100vh-64px)]">
        <!-- Main Products Area (Left) -->
        <div class="flex-1 flex flex-col bg-[#F9FAFB]">
            <!-- Search & Categories -->
            <div class="p-6 pb-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">search</span>
                    <input type="text" x-model="searchQuery" placeholder="Cari SKU atau Nama produk..." class="w-full h-12 pl-12 pr-4 text-base bg-white border border-[#E5E7EB] rounded-xl text-[#111827] shadow-sm focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                </div>
            </div>

            <!-- Products Grid (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 pt-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="item in filteredItems" :key="item.id">
                        <div @click="addToCart(item)" class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-4 cursor-pointer hover:border-[#4F46E5] hover:shadow-md transition-all group flex flex-col h-full relative overflow-hidden" :class="(item.type === 'goods' && item.stock <= 0) ? 'opacity-50 pointer-events-none' : ''">
                            
                            <!-- Indicator Icon -->
                            <div class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center" :class="item.type === 'goods' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'">
                                <span class="material-symbols-outlined text-[16px]" x-text="item.type === 'goods' ? 'package' : 'design_services'"></span>
                            </div>

                            <div class="mt-4 mb-2">
                                <p class="text-xs font-mono text-[#9CA3AF]" x-text="item.sku"></p>
                                <h3 class="font-bold text-[#111827] text-lg leading-tight mt-1 group-hover:text-[#4F46E5] transition-colors" x-text="item.name"></h3>
                            </div>
                            
                            <div class="mt-auto pt-4 flex items-end justify-between">
                                <div>
                                    <p class="text-xs text-[#6B7280] font-semibold mb-0.5">Harga</p>
                                    <p class="font-bold text-[#4F46E5]">Rp <span x-text="formatNumber(item.price)"></span></p>
                                </div>
                                <div x-show="item.type === 'goods'" class="text-right">
                                    <p class="text-[10px] text-[#6B7280] uppercase font-bold tracking-wider">Stok</p>
                                    <p class="text-sm font-semibold" :class="item.stock > 10 ? 'text-emerald-600' : 'text-red-600'" x-text="item.stock"></p>
                                </div>
                                <div x-show="item.type === 'service'" class="text-right">
                                    <span class="inline-block px-2 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded">TERSEDIA</span>
                                </div>
                            </div>

                            <!-- Out of stock overlay -->
                            <div x-show="item.type === 'goods' && item.stock <= 0" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center">
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-bold border border-red-200 shadow-sm">Habis</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredItems.length === 0" class="flex flex-col items-center justify-center h-full text-[#9CA3AF]">
                    <span class="material-symbols-outlined text-5xl mb-2">search_off</span>
                    <p class="font-semibold">Barang/Jasa tidak ditemukan</p>
                </div>
            </div>
        </div>

        <!-- Cart / Checkout Pane (Right) -->
        <div class="w-96 bg-white border-l border-[#E5E7EB] flex flex-col shrink-0 z-10 shadow-xl">
            <!-- Cart Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between">
                <h2 class="font-headline text-xl font-bold text-[#111827] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#4F46E5]">shopping_cart</span> Keranjang
                </h2>
                <button @click="clearCart()" class="text-xs font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded transition-colors">Kosongkan</button>
            </div>

            <!-- Cart Items (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50">
                <template x-for="(cartItem, index) in cart" :key="index">
                    <div class="bg-white p-3 rounded-xl border border-[#E5E7EB] shadow-sm flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-[#111827] truncate" x-text="cartItem.name"></h4>
                            <p class="text-xs text-[#6B7280]">Rp <span x-text="formatNumber(cartItem.price)"></span></p>
                        </div>
                        
                        <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                            <button @click="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-[#4B5563] hover:text-[#4F46E5]">
                                <span class="material-symbols-outlined text-[16px]">remove</span>
                            </button>
                            <span class="w-6 text-center text-sm font-bold text-[#111827]" x-text="cartItem.quantity"></span>
                            <button @click="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-[#4B5563] hover:text-[#4F46E5]">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                            </button>
                        </div>
                    </div>
                </template>
                
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-[#9CA3AF] py-12">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">shopping_basket</span>
                    <p class="text-sm font-semibold">Keranjang kosong</p>
                </div>
            </div>

            <!-- Checkout Area (Bottom Sticky) -->
            <div class="p-6 border-t border-[#E5E7EB] bg-white">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-[#6B7280]">Total Tagihan</span>
                    <span class="font-headline text-2xl font-bold text-[#4F46E5]">Rp <span x-text="formatNumber(cartTotal)"></span></span>
                </div>

                <div class="space-y-3 mb-6">
                    <label class="block text-xs font-semibold text-[#374151]">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="paymentMethod = 'Cash'" :class="paymentMethod === 'Cash' ? 'bg-[#4F46E5] text-white' : 'bg-gray-100 text-[#4B5563]'" class="h-10 rounded-lg text-sm font-semibold transition-colors">Tunai</button>
                        <button @click="paymentMethod = 'Transfer'" :class="paymentMethod === 'Transfer' ? 'bg-[#4F46E5] text-white' : 'bg-gray-100 text-[#4B5563]'" class="h-10 rounded-lg text-sm font-semibold transition-colors">Transfer / Qris</button>
                    </div>

                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jumlah Bayar (Rp)</label>
                        <input type="number" x-model="paidAmount" class="w-full h-12 px-3 text-lg font-bold bg-gray-50 border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:bg-white transition-all text-right">
                    </div>
                    
                    <div x-show="paidAmount > 0 && paidAmount >= cartTotal" class="flex justify-between items-center text-sm p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <span class="font-semibold text-emerald-800">Kembalian</span>
                        <span class="font-bold text-emerald-600">Rp <span x-text="formatNumber(paidAmount - cartTotal)"></span></span>
                    </div>
                </div>

                <button @click="processCheckout()" :disabled="cart.length === 0 || isProcessing" :class="(cart.length === 0 || isProcessing) ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#111827] hover:bg-[#374151] hover:shadow-lg'" class="w-full h-14 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-2 transition-all">
                    <span x-show="!isProcessing" class="material-symbols-outlined">point_of_sale</span> 
                    <span x-text="isProcessing ? 'Memproses...' : 'Proses Pembayaran'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function posApp() {
            return {
                items: @json($items->map(function($i) { 
                    return [
                        'id' => $i->id, 
                        'sku' => $i->sku, 
                        'name' => $i->name, 
                        'type' => $i->type, 
                        'price' => $i->standard_price, 
                        'stock' => $i->stock
                    ]; 
                })),
                searchQuery: '',
                currentType: 'goods', // 'goods' or 'service'
                cart: [],
                paymentMethod: 'Cash',
                paidAmount: '',
                isProcessing: false,

                get filteredItems() {
                    return this.items.filter(item => {
                        const matchesType = item.type === this.currentType;
                        const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                              item.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                        return matchesType && matchesSearch;
                    });
                },

                get cartTotal() {
                    return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                },

                addToCart(item) {
                    if (item.type === 'goods' && item.stock <= 0) return;
                    
                    const existing = this.cart.find(c => c.id === item.id);
                    if (existing) {
                        if (item.type === 'goods' && existing.quantity >= item.stock) {
                            alert('Stok tidak mencukupi');
                            return;
                        }
                        existing.quantity++;
                    } else {
                        this.cart.push({ ...item, quantity: 1 });
                    }
                },

                updateQuantity(index, change) {
                    const item = this.cart[index];
                    const newQty = item.quantity + change;
                    
                    if (newQty <= 0) {
                        this.cart.splice(index, 1);
                    } else {
                        // Check stock constraint
                        if (item.type === 'goods' && newQty > item.stock) {
                            alert('Stok tidak mencukupi');
                            return;
                        }
                        item.quantity = newQty;
                    }
                },

                clearCart() {
                    if(confirm('Kosongkan keranjang?')) this.cart = [];
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                async processCheckout() {
                    if (this.cart.length === 0) return;
                    if (!this.paidAmount || this.paidAmount < this.cartTotal) {
                        alert('Uang bayar tidak mencukupi total tagihan!');
                        return;
                    }

                    this.isProcessing = true;
                    try {
                        const response = await fetch('{{ route("pos.checkout") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                payment_method: this.paymentMethod,
                                paid_amount: this.paidAmount,
                                cart: this.cart
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            alert('Transaksi Berhasil! Struk akan dicetak.');
                            // Refresh page to reset state and update stocks
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + result.message);
                            this.isProcessing = false;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan koneksi.');
                        this.isProcessing = false;
                    }
                }
            }
        }
    </script>
</body>
</html>