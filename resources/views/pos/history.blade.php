@extends('layouts.app')

@section('header_title', 'Riwayat Transaksi')

@section('main_content')
<div x-data="historyModal()" class="space-y-6 pb-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                    <span class="material-symbols-outlined text-[18px]">today</span>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 font-headline tracking-tight">Riwayat Transaksi</h1>
            </div>
            <p class="text-sm text-slate-500 font-medium mt-1 ml-10">Daftar seluruh transaksi POS yang pernah dilakukan.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.index') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm" hx-boost="false">
                <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                Buka Terminal POS
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-500">Total Pendapatan</h3>
                <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-500">Jumlah Transaksi</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $totalTransactions }} <span class="text-sm text-slate-500 font-medium">Struk</span></p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100">
                <span class="material-symbols-outlined text-2xl">shopping_cart</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-500">Total Item Terjual</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $totalItems }} <span class="text-sm text-slate-500 font-medium">Item</span></p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('pos.history.today') }}" 
          class="bg-white p-4 rounded-3xl border border-slate-200/60 shadow-sm flex flex-col sm:flex-row flex-wrap gap-4 items-end relative z-10">
        <div class="flex-1 min-w-[200px] w-full sm:w-auto">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Transaksi</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No Referensi..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
        </div>
        <div class="w-full sm:w-32">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilkan</label>
            <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer" onchange="this.form.submit()">
                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 baris</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
        </div>
        @if(request()->anyFilled(['search']) || (request('per_page') && request('per_page') != 15))
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('pos.history.today') }}" class="h-10 px-4 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold flex items-center justify-center gap-2 shadow-sm transition-all w-full md:w-auto">
                    <span class="material-symbols-outlined text-[18px]">clear_all</span> <span class="hidden lg:inline">Reset</span>
                </a>
            </div>
        @endif
        <div class="flex gap-2 shrink-0">
            <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-sm transition-all w-full md:w-auto">
                <span class="material-symbols-outlined text-[18px]">search</span> <span class="hidden lg:inline">Cari</span>
            </button>
        </div>
    </form>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-[18px]">list_alt</span>
                </div>
                Daftar Transaksi
            </h3>
        </div>
        @if($transactions->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                    <span class="material-symbols-outlined text-3xl">inbox</span>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Belum ada transaksi</h3>
                <p class="text-slate-500 text-sm mt-1">Transaksi yang diselesaikan di terminal POS akan otomatis muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto w-full pb-2">
                <table class="w-full text-left whitespace-nowrap text-sm">
                    <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-bold">No. Referensi</th>
                            <th class="px-6 py-4 font-bold">Waktu</th>
                            <th class="px-6 py-4 font-bold">Kasir</th>
                            <th class="px-6 py-4 font-bold">Total Item</th>
                            <th class="px-6 py-4 font-bold text-right">Total Nominal</th>
                            <th class="px-6 py-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($groupedTransactions as $date => $trxs)
                            <!-- Date Group Header -->
                            <tr class="bg-slate-50/80 border-y border-slate-100">
                                <td colspan="6" class="px-6 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ $date }}
                                </td>
                            </tr>
                            
                            @foreach($trxs as $trx)
                            <tr @click="openModal({{ json_encode($trx) }})" class="hover:bg-slate-50/80 transition-colors cursor-pointer bg-white">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800">{{ $trx->ref_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $trx->created_at->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] border border-slate-200">
                                            {{ substr($trx->user->name ?? 'S', 0, 1) }}
                                        </div>
                                        {{ $trx->user->name ?? 'Sistem' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $trx->lines->sum('quantity') }} Item
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 text-right">
                                    Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a @click.stop href="{{ route('pos.receipt', $trx->ref_number) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-slate-300 text-slate-600 rounded-lg transition-colors" title="Cetak Setruk">
                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Transaksi -->
    <div x-show="showModal" 
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="closeModal()" 
                 aria-hidden="true"></div>

            <!-- Modal Panel -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full border border-slate-100"
                 style="max-width: 600px;">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                            <span class="material-symbols-outlined">receipt_long</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Detail Transaksi</h3>
                            <p class="text-sm text-slate-500 font-medium" x-text="selectedTrx?.ref_number"></p>
                        </div>
                    </div>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none p-2 hover:bg-slate-100 rounded-full">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6 bg-white">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-xs text-slate-500 font-semibold mb-1">Kasir</p>
                            <p class="font-bold text-slate-800" x-text="selectedTrx?.user?.name || 'Sistem'"></p>
                        </div>
                        <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                            <p class="text-xs text-indigo-500 font-semibold mb-1">Total Nominal</p>
                            <p class="font-black text-indigo-700 text-lg" x-text="formatRupiah(selectedTrx?.total_amount)"></p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Daftar Barang</span>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full" x-text="(selectedTrx?.lines?.length || 0) + ' Item'"></span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto p-2 space-y-1">
                            <template x-for="line in (selectedTrx?.lines || [])" :key="line.id">
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-slate-800" x-text="line.item?.name || 'Item tidak diketahui'"></p>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="formatRupiah(line.unit_price) + ' x ' + line.quantity"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-slate-800" x-text="formatRupiah(line.unit_price * line.quantity)"></p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedTrx?.lines || selectedTrx.lines.length === 0">
                                <div class="text-center p-4">
                                    <p class="text-sm text-slate-500">Tidak ada detail barang.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 pt-0 flex gap-3">
                    <button @click="printReceipt()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Cetak Struk Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('historyModal', () => ({
            showModal: false,
            selectedTrx: null,
            openModal(trx) {
                this.selectedTrx = trx;
                this.showModal = true;
                document.body.classList.add('overflow-hidden');
            },
            closeModal() {
                this.showModal = false;
                setTimeout(() => { this.selectedTrx = null; }, 300);
                document.body.classList.remove('overflow-hidden');
            },
            printReceipt() {
                if(this.selectedTrx) {
                    window.open(`/pos/receipt/${this.selectedTrx.ref_number}`, '_blank', 'width=400,height=600');
                }
            },
            formatRupiah(number) {
                if(!number) return 'Rp0';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            }
        }));
    });
</script>
@endpush
