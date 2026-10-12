@extends('layouts.app')

@section('header_title', 'Detail Otomatisasi Rule')

@section('main_content')
<div class="space-y-6" x-data="{ showConditionModal: false, showActionModal: false }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('system.rbl') }}" class="text-slate-400 hover:text-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">arrow_back</span>
                </a>
                <h2 class="font-headline text-2xl font-bold text-slate-800">{{ $rule->name }}</h2>
            </div>
            <p class="text-sm text-slate-500 ml-8">
                Trigger: <span class="font-mono font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $rule->event_trigger }}</span> | 
                Status: <span class="font-semibold {{ $rule->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $rule->is_active ? 'Aktif' : 'Non-aktif' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('system.rbl') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Rule Condition & Action Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Kondisi Section -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600">alt_route</span>
                    Kondisi Logika (IF)
                </h3>
                <button @click="showConditionModal = true" class="text-xs text-indigo-600 hover:text-indigo-700 font-bold flex items-center gap-1 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah Kondisi
                </button>
            </div>
            <div class="p-5 flex-1">
                @if($rule->conditions->count() == 0)
                    <div class="text-center py-10 text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">alt_route</span>
                        <p class="text-sm font-medium">Belum ada kondisi logika.</p>
                        <p class="text-xs text-slate-400 mt-0.5">Rule akan selalu mengeksekusi aksi setiap kali event trigger terjadi.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($rule->conditions as $index => $condition)
                            @if($index > 0)
                            <div class="text-center">
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 rounded text-slate-500 uppercase font-mono">{{ $condition->logic_operator ?? 'AND' }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between p-3.5 border border-slate-200 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs text-indigo-700 bg-indigo-50 px-2 py-1 rounded font-semibold">{{ $condition->field }}</span>
                                    <span class="text-xs font-bold text-slate-600 font-mono">{{ $condition->operator }}</span>
                                    <span class="text-xs font-bold text-slate-900 bg-white px-2 py-1 rounded border border-slate-200">{{ $condition->value }}</span>
                                </div>
                                <form action="{{ route('system.rbl.condition.destroy', $condition->id) }}" method="POST" onsubmit="return confirm('Hapus kondisi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 p-1 rounded transition-colors" title="Hapus Kondisi">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Aksi Section -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">electric_bolt</span>
                    Aksi Eksekusi (THEN)
                </h3>
                <button @click="showActionModal = true" class="text-xs text-purple-600 hover:text-purple-700 font-bold flex items-center gap-1 bg-purple-50 px-3 py-1.5 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah Aksi
                </button>
            </div>
            <div class="p-5 flex-1">
                @if($rule->actions->count() == 0)
                    <div class="text-center py-10 text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">electric_bolt</span>
                        <p class="text-sm font-medium">Belum ada aksi yang ditentukan.</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tambahkan aksi yang akan dijalankan saat kondisi terpenuhi.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($rule->actions as $action)
                            <div class="flex items-center justify-between p-3.5 border border-purple-100 rounded-xl bg-purple-50/30 hover:bg-purple-50/50 transition-colors">
                                <div>
                                    <div class="text-xs font-bold text-purple-900 font-mono">{{ class_basename($action->action_type) }}</div>
                                    @if($action->action_params)
                                    <div class="text-[11px] text-slate-500 font-mono mt-1 bg-white/70 px-2 py-0.5 rounded border border-purple-100 inline-block">
                                        {{ json_encode($action->action_params) }}
                                    </div>
                                    @endif
                                </div>
                                <form action="{{ route('system.rbl.action.destroy', $action->id) }}" method="POST" onsubmit="return confirm('Hapus aksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 p-1 rounded transition-colors" title="Hapus Aksi">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Kondisi -->
    <div x-show="showConditionModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showConditionModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl border border-slate-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-slate-800 font-headline">Tambah Kondisi Logika (IF)</h3>
                        <button @click="showConditionModal = false" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('system.rbl.condition.store', $rule->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @if($rule->conditions->count() > 0)
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Operator Logika Antar Kondisi</label>
                            <select name="logic_operator" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl">
                                <option value="AND">AND (Semua kondisi harus terpenuhi)</option>
                                <option value="OR">OR (Salah satu kondisi terpenuhi)</option>
                            </select>
                        </div>
                        @else
                            <input type="hidden" name="logic_operator" value="AND">
                        @endif
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Field / Atribut Data</label>
                            <input type="text" name="field" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl" placeholder="Contoh: stock atau total_amount">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Operator Perbandingan</label>
                            <select name="operator" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl">
                                <option value="==">Sama Dengan (==)</option>
                                <option value="!=">Tidak Sama Dengan (!=)</option>
                                <option value=">">Lebih Besar (>)</option>
                                <option value=">=">Lebih Besar atau Sama Dengan (>=)</option>
                                <option value="<">Lebih Kecil (<)</option>
                                <option value="<=">Lebih Kecil atau Sama Dengan (<=)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nilai Komparasi (Value)</label>
                            <input type="text" name="value" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl" placeholder="Contoh: 10 atau Draft">
                        </div>
                        
                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button" @click="showConditionModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm">Simpan Kondisi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Aksi -->
    <div x-show="showActionModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showActionModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl border border-slate-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-slate-800 font-headline">Tambah Aksi Eksekusi (THEN)</h3>
                        <button @click="showActionModal = false" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('system.rbl.action.store', $rule->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Aksi</label>
                            <select name="action_type" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-xl">
                                <option value="App\Services\RuleEngine\Actions\SendNotificationAction">Kirim Notifikasi Sistem</option>
                                <option value="App\Services\RuleEngine\Actions\UpdateStatusAction">Perbarui Status Entitas</option>
                                <option value="App\Services\RuleEngine\Actions\AutoReorderAction">Buat Draft Pengadaan Otomatis</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Parameter Aksi (JSON Format)</label>
                            <textarea name="action_params" rows="3" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-xl font-mono" placeholder='{"target_role": "manager", "message": "Peringatan stok kritis"}'></textarea>
                            <p class="text-xs text-slate-400 mt-1">Opsional: Masukkan payload konfigurasi dalam format JSON.</p>
                        </div>
                        
                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button" @click="showActionModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-sm">Simpan Aksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection