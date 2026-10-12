@extends('layouts.app')

@section('header_title', 'Detail Purchase Order')

@section('main_content')
<div class="space-y-8">
    
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs print:hidden">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.po.index') }}" class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-black text-slate-900 font-headline tracking-tight">{{ $po->ref_number }}</h2>
                    @if($po->status === 'Draft')
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">Draft</span>
                    @elseif($po->status === 'Pending Approval')
                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200">Menunggu Approval Manager</span>
                    @elseif($po->status === 'Approved')
                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">Disetujui (Approved)</span>
                    @elseif($po->status === 'Completed')
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">Selesai (Inbound Complete)</span>
                    @elseif($po->status === 'Rejected')
                        <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200">Ditolak</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Dibuat oleh <strong class="text-slate-700">{{ $po->user->name ?? 'Staff' }}</strong> pada {{ \Carbon\Carbon::parse($po->transaction_date)->format('d F Y') }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5" x-data="{ showRejectModal: false }">
            
            <!-- Submit for Approval (If Draft or Rejected) -->
            @if(in_array($po->status, ['Draft', 'Rejected']))
            <form action="{{ route('transactions.po.submit', $po->id) }}" method="POST" onsubmit="return confirm('Ajukan dokumen PO ini ke Manager untuk persetujuan?');">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Ajukan ke Manager</span>
                </button>
            </form>
            @endif

            <!-- Manager Approval / Reject Buttons -->
            @if(in_array($po->status, ['Pending Approval', 'Draft']) && auth()->user()->hasRole(['manager', 'super_admin']))
            <form action="{{ route('transactions.po.approve', $po->id) }}" method="POST" onsubmit="return confirm('Setujui dokumen Purchase Order ini?');">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 flex items-center gap-1.5 shadow-sm active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Setujui PO</span>
                </button>
            </form>

            <button type="button" @click="showRejectModal = true" class="px-4 py-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold hover:bg-rose-600 hover:text-white transition-colors flex items-center gap-1.5 active:scale-95">
                <span class="material-symbols-outlined text-[18px]">cancel</span>
                <span>Tolak PO</span>
            </button>

            <!-- Rejection Modal -->
            <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showRejectModal = false"></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 space-y-4" @click.stop>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-base font-extrabold text-slate-900 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-rose-600">cancel</span>
                                <span>Tolak Purchase Order</span>
                            </h3>
                            <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>
                        <form action="{{ route('transactions.po.reject', $po->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                                <textarea name="reason" rows="3" required placeholder="Tuliskan catatan revisi atau alasan penolakan..." class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm">Konfirmasi Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 flex items-center gap-1.5 transition-colors shadow-2xs">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Dokumen PO</span>
            </button>
        </div>
    </div>

    <!-- Status Progress Workflow Tracker -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs print:hidden">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            
            <!-- Step 1: Draft -->
            <div class="p-4 rounded-2xl {{ in_array($po->status, ['Draft', 'Pending Approval', 'Approved', 'Completed']) ? 'bg-blue-50 border border-blue-200 text-blue-900' : 'bg-slate-50 border border-slate-200 text-slate-400' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider">Tahap 01</span>
                    <span class="material-symbols-outlined text-[18px] text-blue-600">edit_note</span>
                </div>
                <div class="text-sm font-bold font-headline">Penyusunan Draft</div>
                <p class="text-[11px] text-slate-500 mt-0.5">Penetapan kuantitas &amp; harga beli item</p>
            </div>

            <!-- Step 2: Approval -->
            <div class="p-4 rounded-2xl {{ in_array($po->status, ['Pending Approval', 'Approved', 'Completed']) ? ($po->status === 'Pending Approval' ? 'bg-amber-50 border border-amber-300 text-amber-900 ring-2 ring-amber-400/30' : 'bg-blue-50 border border-blue-200 text-blue-900') : ($po->status === 'Rejected' ? 'bg-rose-50 border border-rose-300 text-rose-900' : 'bg-slate-50 border border-slate-200 text-slate-400') }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider">Tahap 02</span>
                    <span class="material-symbols-outlined text-[18px] {{ $po->status === 'Rejected' ? 'text-rose-600' : ($po->status === 'Pending Approval' ? 'text-amber-600' : 'text-blue-600') }}">
                        {{ $po->status === 'Rejected' ? 'cancel' : 'verified' }}
                    </span>
                </div>
                <div class="text-sm font-bold font-headline">
                    {{ $po->status === 'Rejected' ? 'Ditolak Manager' : 'Approval Manager' }}
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Verifikasi anggaran &amp; kuota pengadaan</p>
            </div>

            <!-- Step 3: Approved / Dikirim ke Vendor -->
            <div class="p-4 rounded-2xl {{ in_array($po->status, ['Approved', 'Completed']) ? 'bg-blue-50 border border-blue-200 text-blue-900' : 'bg-slate-50 border border-slate-200 text-slate-400' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider">Tahap 03</span>
                    <span class="material-symbols-outlined text-[18px] text-blue-600">local_shipping</span>
                </div>
                <div class="text-sm font-bold font-headline">Disetujui &amp; PO Rilis</div>
                <p class="text-[11px] text-slate-500 mt-0.5">Dokumen dikirim ke vendor supplier</p>
            </div>

            <!-- Step 4: Inbound Diterima Gudang -->
            <div class="p-4 rounded-2xl {{ $po->status === 'Completed' ? 'bg-emerald-50 border border-emerald-300 text-emerald-900' : 'bg-slate-50 border border-slate-200 text-slate-400' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider">Tahap 04</span>
                    <span class="material-symbols-outlined text-[18px] {{ $po->status === 'Completed' ? 'text-emerald-600' : 'text-slate-400' }}">inventory</span>
                </div>
                <div class="text-sm font-bold font-headline">Penerimaan Gudang</div>
                <p class="text-[11px] text-slate-500 mt-0.5">Barang fisik tiba &amp; dicatat Inbound</p>
            </div>

        </div>
    </div>

    @if($po->status === 'Rejected')
    <div class="p-5 bg-rose-50 border border-rose-200 rounded-3xl flex items-start gap-4">
        <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[24px]">error</span>
        </div>
        <div>
            <h4 class="text-sm font-bold text-rose-900">Purchase Order Ini Telah Ditolak oleh Manager</h4>
            <p class="text-xs text-rose-800 mt-1 whitespace-pre-line">{{ $po->notes ?: 'Ditolak tanpa catatan khusus.' }}</p>
            <p class="text-[11px] text-rose-600 mt-2 font-medium">Silakan sesuaikan item dan kuantitas pesanan, lalu klik tombol "Ajukan ke Manager" kembali.</p>
        </div>
    </div>
    @endif

    <!-- Printable Official Purchase Order Document Sheet -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-8 print:p-0 print:border-none print:shadow-none">
        
        <!-- Official Letterhead (Print-ready) -->
        <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-6">
            <div class="flex items-center gap-3">
                <img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" alt="Inventra Logo" class="h-12 w-auto object-contain">
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-900">INVENTRA ENTERPRISE</h1>
                    <p class="text-xs text-slate-500">Sistem Logistik &amp; Manajemen Pengadaan Terpadu</p>
                    <p class="text-[11px] text-slate-400">Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan • Telp: (021) 5082-9900</p>
                </div>
            </div>

            <div class="text-right">
                <h2 class="text-xl font-black font-headline text-blue-700">PURCHASE ORDER</h2>
                <p class="font-mono font-bold text-sm text-slate-900 mt-0.5">{{ $po->ref_number }}</p>
                <p class="text-xs text-slate-500 mt-1">Tanggal: {{ \Carbon\Carbon::parse($po->transaction_date)->format('d F Y') }}</p>
                <p class="text-xs text-slate-500">Status: <strong class="text-slate-800 uppercase">{{ $po->status }}</strong></p>
            </div>
        </div>

        @if($po->notes)
        <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Keterangan / Catatan Vendor:</span>
            <p class="text-xs text-slate-600">{{ $po->notes }}</p>
        </div>
        @endif

        <!-- Itemized Table -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-800 uppercase font-bold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode SKU &amp; Nama Barang</th>
                        <th class="py-3 px-4 text-right">Kuantitas</th>
                        <th class="py-3 px-4 text-right">Harga Satuan (Rp)</th>
                        <th class="py-3 px-4 text-right">Subtotal (Rp)</th>
                        <th class="py-3 px-4 text-center print:hidden w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @php $grandTotal = 0; @endphp
                    @forelse($po->lines as $index => $line)
                    @php
                        $subtotal = $line->quantity * $line->unit_price;
                        $grandTotal += $subtotal;
                    @endphp
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3 px-4 text-center text-slate-400 font-bold">{{ $index + 1 }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 text-sm">{{ $line->item->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $line->item->sku }} • {{ $line->item->category->name ?? 'Kategori Umum' }}</div>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-900 text-sm">
                            {{ $line->quantity }} {{ $line->item->uom->code ?? 'Unit' }}
                        </td>
                        <td class="py-3 px-4 text-right font-semibold">
                            Rp {{ number_format($line->unit_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right font-black text-blue-700 text-sm">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center print:hidden">
                            @if(in_array($po->status, ['Draft', 'Pending Approval', 'Rejected']))
                            <form action="{{ route('transactions.po.lines.destroy', $line->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus item ini dari PO?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors inline-flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                            Belum ada item barang di dalam Purchase Order ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($po->lines) > 0)
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right text-slate-700 uppercase tracking-wider">Total Nilai Pemesanan (Grand Total)</td>
                        <td class="py-3 px-4 text-right text-blue-800 text-base font-black">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                        <td class="print:hidden"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Official Dual Signatures Box -->
        <div class="grid grid-cols-2 gap-8 pt-8 border-t border-slate-200 text-center text-xs">
            <div>
                <p class="text-slate-500 mb-16">Dibuat Oleh (Purchasing Officer):</p>
                <p class="font-bold text-slate-900 uppercase">({{ $po->user->name ?? 'Staff Purchasing' }})</p>
                <p class="text-[11px] text-slate-400">Divisi Pengadaan &amp; Logistik</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Disetujui Oleh (General Manager / Pimpinan):</p>
                <p class="font-bold text-slate-900 uppercase">( {{ in_array($po->status, ['Approved', 'Completed']) ? 'Manager Operasional' : '....................................' }} )</p>
                <p class="text-[11px] text-slate-400">Otorisasi Anggaran Perusahaan</p>
            </div>
        </div>

    </div>

    <!-- Bottom Add Item Section (If editable) -->
    @if(in_array($po->status, ['Draft', 'Pending Approval', 'Rejected']))
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 print:hidden">
        <h3 class="text-base font-extrabold text-slate-900 font-headline mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">add_box</span>
            <span>Tambah Item Barang ke Purchase Order</span>
        </h3>
        
        <form action="{{ route('transactions.po.addLine', $po->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end" x-data="{ standardPrice: 0 }">
            @csrf
            
            <div class="sm:col-span-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Master Barang <span class="text-rose-500">*</span></label>
                <select name="item_id" required 
                        @change="standardPrice = $event.target.options[$event.target.selectedIndex].dataset.price || 0"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih barang...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->standard_price ?? 0 }}">
                            {{ $item->sku }} — {{ $item->name }} (Stok: {{ $item->stock }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jumlah Pesanan (Qty) <span class="text-rose-500">*</span></label>
                <input type="number" name="quantity" min="1" value="1" required 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Harga Beli Satuan (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="unit_price" min="0" x-model="standardPrice" required 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center justify-center gap-1.5 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Tambah Item</span>
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection
