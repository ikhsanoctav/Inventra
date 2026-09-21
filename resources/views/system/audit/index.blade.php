@extends('layouts.app')

@section('header_title', 'Activity & Audit Log')

@section('main_content')
<div class="space-y-6" x-data="{ 
    detailModal: false, 
    selectedLog: null,
    viewDetails(log) {
        this.selectedLog = log;
        this.detailModal = true;
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600 text-[28px]">history</span>
                Activity & Audit Trail Log
            </h2>
            <p class="text-sm text-slate-500 mt-1">Rekam jejak seluruh aktivitas penting, otentikasi pengguna, dan perubahan data sistem secara real-time.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Audit Logging Aktif
            </span>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('system.audit') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, pengguna, entitas, IP..." class="w-full h-10 pl-10 pr-4 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>

            <div class="sm:w-48">
                <select name="action" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">Semua Aksi</option>
                    <option value="LOGIN_SUCCESS" {{ request('action') == 'LOGIN_SUCCESS' ? 'selected' : '' }}>Login Sukses</option>
                    <option value="LOGIN_FAILED" {{ request('action') == 'LOGIN_FAILED' ? 'selected' : '' }}>Login Gagal</option>
                    <option value="LOGIN_BLOCKED_INACTIVE" {{ request('action') == 'LOGIN_BLOCKED_INACTIVE' ? 'selected' : '' }}>Login Diblokir</option>
                    <option value="LOGOUT" {{ request('action') == 'LOGOUT' ? 'selected' : '' }}>Logout</option>
                    <option value="POS_SALE" {{ request('action') == 'POS_SALE' ? 'selected' : '' }}>Penjualan POS</option>
                    <option value="POS_RETURN" {{ request('action') == 'POS_RETURN' ? 'selected' : '' }}>Retur POS</option>
                    <option value="INBOUND_CREATED" {{ request('action') == 'INBOUND_CREATED' ? 'selected' : '' }}>Inbound Baru</option>
                    <option value="INBOUND_COMPLETED" {{ request('action') == 'INBOUND_COMPLETED' ? 'selected' : '' }}>Inbound Selesai</option>
                    <option value="OUTBOUND_CREATED" {{ request('action') == 'OUTBOUND_CREATED' ? 'selected' : '' }}>Outbound Baru</option>
                    <option value="OUTBOUND_COMPLETED" {{ request('action') == 'OUTBOUND_COMPLETED' ? 'selected' : '' }}>Outbound Selesai</option>
                    <option value="USER_CREATED" {{ request('action') == 'USER_CREATED' ? 'selected' : '' }}>User Dibuat</option>
                    <option value="USER_UPDATED" {{ request('action') == 'USER_UPDATED' ? 'selected' : '' }}>User Diupdate</option>
                    <option value="USER_STATUS_TOGGLED" {{ request('action') == 'USER_STATUS_TOGGLED' ? 'selected' : '' }}>Status User Diubah</option>
                </select>
            </div>

            <div class="sm:w-32">
                <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 baris</option>
                    <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
                </button>
                @if(request()->hasAny(['search', 'action', 'per_page']))
                    <a href="{{ route('system.audit') }}" class="h-10 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold flex items-center gap-1 transition-all" title="Reset Filter">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div id="table-container" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Waktu</th>
                        <th scope="col" class="px-6 py-4">Pengguna</th>
                        <th scope="col" class="px-6 py-4">Aktivitas</th>
                        <th scope="col" class="px-6 py-4">Entitas Target</th>
                        <th scope="col" class="px-6 py-4">IP & Info Klien</th>
                        <th scope="col" class="px-6 py-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                <div class="font-medium text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center border border-indigo-100">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800 text-sm">{{ $log->user->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $log->user->role }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center">
                                            SYS
                                        </div>
                                        <span class="text-xs font-medium text-slate-500">Sistem Otomatis</span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $action = $log->action;
                                    $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                    $icon = 'info';

                                    if (str_contains($action, 'LOGIN_SUCCESS')) {
                                        $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        $icon = 'login';
                                    } elseif (str_contains($action, 'LOGIN_FAILED') || str_contains($action, 'BLOCKED')) {
                                        $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                        $icon = 'gpp_bad';
                                    } elseif (str_contains($action, 'LOGOUT')) {
                                        $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                        $icon = 'logout';
                                    } elseif (str_contains($action, 'SALE') || str_contains($action, 'COMPLETED')) {
                                        $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                        $icon = 'check_circle';
                                    } elseif (str_contains($action, 'RETURN') || str_contains($action, 'DELETED')) {
                                        $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                        $icon = 'assignment_return';
                                    } elseif (str_contains($action, 'CREATED')) {
                                        $badgeClass = 'bg-sky-50 text-sky-700 border-sky-200';
                                        $icon = 'add_circle';
                                    } elseif (str_contains($action, 'UPDATED')) {
                                        $badgeClass = 'bg-violet-50 text-violet-700 border-violet-200';
                                        $icon = 'edit';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $badgeClass }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $icon }}</span>
                                    {{ $action }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if($log->entity_type)
                                    <span class="font-semibold text-slate-700">{{ class_basename($log->entity_type) }}</span>
                                    @if($log->entity_id)
                                        <span class="text-slate-400 font-mono">#{{ $log->entity_id }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                <div class="font-mono text-slate-700 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">lan</span>
                                    {{ $log->ip_address ?? 'Localhost' }}
                                </div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 35) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($log->new_values || $log->old_values)
                                    <button @click="viewDetails({{ json_encode([
                                        'action' => $log->action,
                                        'user' => $log->user->name ?? 'System',
                                        'entity' => $log->entity_type ? class_basename($log->entity_type).' #'.$log->entity_id : '-',
                                        'ip' => $log->ip_address ?? '-',
                                        'time' => $log->created_at->format('d M Y H:i:s'),
                                        'new_values' => $log->new_values,
                                        'old_values' => $log->old_values,
                                    ]) }})" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 transition-colors" title="Lihat Payload Data">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">history_toggle_off</span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-1">Belum Ada Aktivitas Tercatat</h3>
                                    <p class="text-xs text-slate-500 max-w-sm">Aktivitas login, transaksi kasir, perubahan user, dan perpindahan stok akan otomatis terekam di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
                {{ $logs->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-200 bg-slate-50/50 text-xs text-slate-500">
                Menampilkan {{ $logs->count() }} data log aktivitas.
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    <div x-show="detailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="detailModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="detailModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="detailModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95" 
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200">
                
                <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                    <div>
                        <h3 class="font-headline font-bold text-slate-800 text-lg flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-600 text-[20px]">data_object</span>
                            Detail Payload Audit Log
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="selectedLog ? selectedLog.action + ' • ' + selectedLog.time : ''"></p>
                    </div>
                    <button @click="detailModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <template x-if="selectedLog && selectedLog.old_values">
                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1 text-rose-600">
                                <span class="material-symbols-outlined text-[16px]">history</span> Data Sebelumnya (Old)
                            </div>
                            <pre class="bg-slate-900 text-slate-100 p-3 rounded-xl text-xs overflow-x-auto font-mono" x-text="JSON.stringify(selectedLog.old_values, null, 2)"></pre>
                        </div>
                    </template>

                    <template x-if="selectedLog && selectedLog.new_values">
                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1 text-emerald-600">
                                <span class="material-symbols-outlined text-[16px]">update</span> Data Baru / Parameter (New)
                            </div>
                            <pre class="bg-slate-900 text-slate-100 p-3 rounded-xl text-xs overflow-x-auto font-mono" x-text="JSON.stringify(selectedLog.new_values, null, 2)"></pre>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button @click="detailModal = false" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection