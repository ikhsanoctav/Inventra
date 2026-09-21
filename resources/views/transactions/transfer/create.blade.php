@extends('layouts.app')

@section('header_title', 'Buat Mutasi Baru')

@section('main_content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
        <form action="{{ route('transactions.transfer.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi (TF)</label>
                    <input type="text" name="ref_number" value="TF-{{ date('Ymd') }}-{{ rand(100,999) }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Mutasi</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Gudang Asal</label>
                    <select name="from_warehouse_id" required class="w-full px-4 py-2 border rounded-lg bg-white">
                        <option value="">Pilih Gudang Asal</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Gudang Tujuan</label>
                    <select name="to_warehouse_id" required class="w-full px-4 py-2 border rounded-lg bg-white">
                        <option value="">Pilih Gudang Tujuan</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg" placeholder="Alasan mutasi..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('transactions.transfer') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-lg">Buat Mutasi</button>
            </div>
        </form>
    </div>
</div>
@endsection
