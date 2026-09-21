@extends('layouts.app')

@section('header_title', 'Buat Purchase Order Baru')

@section('main_content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('transactions.po.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 font-headline">Buat Purchase Order Baru</h2>
            <p class="text-sm text-slate-500">Form pembuatan dokumen pemesanan pembelian ke vendor.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="{{ route('transactions.po') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ref Number -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi (PO)</label>
                        <input type="text" name="ref_number" value="PO-{{ date('Ymd') }}-{{ rand(100,999) }}" required
                               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pemesanan</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Hidden Status -->
                <input type="hidden" name="status" value="Draft">

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('transactions.po.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan & Lanjut Isi Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
