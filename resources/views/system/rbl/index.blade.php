@extends('layouts.app')

@section('header_title', 'Rule Base Logic (RBL)')

@section('main_content')
<div class="space-y-6" x-data="rblSystem()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[28px]">rule</span>
                Rule Base Logic (RBL) Engine
            </h2>
            <p class="mt-1 text-sm text-slate-500">Konfigurasi otomatisasi logika bisnis berdasarkan event, kondisi IF, dan aksi THEN.</p>
        </div>
        <button @click="showModal = true" class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold text-sm hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-sm shadow-indigo-500/20 shrink-0">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Buat Rule Baru
        </button>
    </div>

    <!-- Rule List -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto w-full pb-2">
            <table class="min-w-full divide-y divide-slate-100 text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Nama Rule</th>
                        <th scope="col" class="px-6 py-4">Event Trigger</th>
                        <th scope="col" class="px-6 py-4 text-center">Kondisi (IF)</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi (THEN)</th>
                        <th scope="col" class="px-6 py-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rules as $rule)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $rule->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                {{ $rule->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('system.rbl.show', $rule->id) }}" class="text-sm font-bold text-slate-800 hover:text-indigo-600 transition-colors">{{ $rule->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100 font-mono">
                                {{ $rule->event_trigger }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $rule->conditions->count() > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500' }}">
                                {{ $rule->conditions->count() }} Kondisi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $rule->actions->count() > 0 ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-500' }}">
                                {{ $rule->actions->count() }} Aksi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('system.rbl.show', $rule->id) }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">tune</span> Kelola
                                </a>
                                <form action="{{ route('system.rbl.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rule ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Rule">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">rule</span>
                            <p class="text-sm font-medium">Belum ada rule otomatisasi. Klik tombol <strong>Buat Rule Baru</strong> di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Rule Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-200" @click.away="showModal = false">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 font-headline flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600">add_task</span>
                    Buat Rule Baru
                </h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="{{ route('system.rbl') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Rule Otomatisasi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Contoh: Notifikasi Stok Kritis">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Event Trigger (Pemicu) <span class="text-red-500">*</span></label>
                        <select name="event_trigger" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="stock.updated">Stock Updated (Perubahan Stok)</option>
                            <option value="pos.sale">POS Sale (Penjualan Kasir Selesai)</option>
                            <option value="transaction.inbound">Inbound Created (Barang Masuk)</option>
                            <option value="transaction.outbound">Outbound Created (Barang Keluar)</option>
                            <option value="po.created">Purchase Order Created (PO Baru)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-semibold text-slate-700 cursor-pointer">Aktifkan Rule Ini Secara Langsung</label>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm">Lanjut ke Pembuatan Logika</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rblSystem', () => ({
            showModal: false
        }));
    });
</script>
@endpush
@endsection
