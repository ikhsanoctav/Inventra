@extends('layouts.app')

@section('header_title', 'Buat Purchase Order Baru')

@section('main_content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('transactions.po.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-2xs">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-headline">Buat Purchase Order Baru</h2>
            <p class="text-sm text-slate-500">Penerbitan dokumen pemesanan pembelian resmi ke rekanan vendor.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-8">
            <form action="{{ route('transactions.po') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ref Number -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nomor Referensi (PO) <span class="text-rose-500">*</span></label>
                        <input type="text" name="ref_number" value="{{ $autoRef ?? 'PO-'.date('Ymd').'-'.rand(1000,9999) }}" required
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono text-sm font-bold focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Nomor unik dokumen PO (dihasilkan otomatis).</p>
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tanggal Pemesanan <span class="text-rose-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <!-- Notes / Keterangan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan Pengadaan / Vendor Tertuju</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Pengadaan bahan logistik kuartal 4 - Rekanan PT Global Distribusi..."
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"></textarea>
                </div>

                <!-- Status Selection -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Status Awal Dokumen</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-blue-500 has-checked:bg-blue-50/40 transition-all">
                            <input type="radio" name="status" value="Draft" checked class="text-blue-600 focus:ring-blue-500">
                            <div>
                                <div class="text-sm font-bold text-slate-900">Simpan sebagai Draft</div>
                                <div class="text-xs text-slate-500">Anda dapat mengisi item terlebih dahulu sebelum diajukan.</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-blue-500 has-checked:bg-blue-50/40 transition-all">
                            <input type="radio" name="status" value="Pending Approval" class="text-blue-600 focus:ring-blue-500">
                            <div>
                                <div class="text-sm font-bold text-slate-900">Langsung Ajukan ke Manager</div>
                                <div class="text-xs text-slate-500">Kirim notifikasi persetujuan ke pimpinan setelah item diisi.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('transactions.po.index') }}" class="px-5 py-2.5 bg-slate-100 border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan &amp; Lanjut Isi Item</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
