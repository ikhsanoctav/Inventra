@extends('layouts.app')

@section('header_title', 'Rule Base Logic (RBL)')

@section('main_content')
<div class="space-y-6" x-data="rblSystem()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Rule Base Logic Engine</h2>
            <p class="mt-1 text-sm text-gray-500">Atur otomatisasi sistem berdasarkan event dan kondisi tertentu.</p>
        </div>
        <button @click="showModal = true" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined">add</span>
            Buat Rule Baru
        </button>
    </div>

    <!-- Rule List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto w-full pb-4">
<table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Rule</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Trigger</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi (IF)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi (THEN)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rules as $rule)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $rule->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $rule->name }}</div>
                        <div class="text-sm text-gray-500">{{ $rule->description ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-800 rounded border border-slate-200 font-mono">
                            {{ $rule->event }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if(empty($rule->conditions))
                            <span class="italic text-gray-400">Tidak ada kondisi</span>
                        @else
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($rule->conditions as $cond)
                                    <li><code>{{ $cond['field'] ?? '' }}</code> <strong>{{ $cond['operator'] ?? '' }}</strong> {{ $cond['value'] ?? '' }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if(empty($rule->actions))
                            <span class="italic text-gray-400">Tidak ada aksi</span>
                        @else
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($rule->actions as $act)
                                    <li><span class="font-bold text-indigo-600 uppercase">{{ $act['type'] ?? '' }}</span>: {{ $act['message'] ?? 'target: ' . ($act['target'] ?? '') }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Belum ada rule otomatisasi. Silakan buat baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
</div>
    </div>

    <!-- Create Rule Modal (Proof of Concept) -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Buat Rule Baru (PoC)</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="{{ url('system/rbl') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Rule</label>
                        <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Peringatan Stok Rendah">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <input type="text" name="description" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event Trigger</label>
                        <select name="event" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="stock.updated">Stock Updated (stock.updated)</option>
                            <option value="transaction.created">Transaction Created (transaction.created)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm text-gray-700">Aktifkan Rule Ini</label>
                    </div>
                    
                    <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                        <p class="text-xs text-indigo-800">
                            <strong>Catatan PoC:</strong> Pada versi purwarupa ini, kondisi dan aksi *default* kosong. Kondisi (JSON) dan Aksi (JSON) akan ditambahkan secara sistematis oleh backend engine sesuai *business logic* yang lebih kompleks.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Simpan Rule</button>
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
