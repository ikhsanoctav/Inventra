@extends('layouts.app')

@section('header_title', 'Detail Otomatisasi Rule')

@section('main_content')
<div class="space-y-6" x-data="{ showConditionModal: false, showActionModal: false }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('system.rbl') }}" class="text-[#6B7280] hover:text-[#111827] transition-colors"><span class="material-symbols-outlined text-[20px]">arrow_back</span></a>
                <h2 class="font-headline text-2xl font-bold text-[#111827]">{{ $rule->name }}</h2>
            </div>
            <p class="text-sm text-[#6B7280]">Trigger: <span class="font-semibold">{{ $rule->event_trigger }}</span> | Status: <span class="font-semibold {{ $rule->is_active ? 'text-emerald-600' : 'text-gray-500' }}">{{ $rule->is_active ? 'Aktif' : 'Non-aktif' }}</span></p>
        </div>
    </div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Kondisi Section -->
        <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <h3 class="font-semibold text-[#111827]">Kondisi Logika (IF)</h3>
                <button @click="showConditionModal = true" class="text-sm text-[#4F46E5] hover:text-[#4338CA] font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah Kondisi
                </button>
            </div>
            <div class="p-5 flex-1">
                @if($rule->conditions->count() == 0)
                    <div class="text-center py-8 text-[#6B7280]">
                        <span class="material-symbols-outlined text-4xl mb-2 text-[#D1D5DB]">alt_route</span>
                        <p class="text-sm">Belum ada kondisi. Rule akan selalu dieksekusi saat trigger terjadi.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($rule->conditions as $index => $condition)
                            @if($index > 0)
                            <div class="text-center">
                                <span class="text-xs font-bold px-2 py-1 bg-gray-100 rounded text-gray-600 uppercase">{{ $condition->logic_operator }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                                <div>
                                    <span class="font-mono text-sm text-[#4F46E5] font-semibold">{{ $condition->field }}</span>
                                    <span class="text-sm text-gray-600 mx-1">{{ $condition->operator }}</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $condition->value }}</span>
                                </div>
                                <form action="{{ route('system.rbl.condition.destroy', $condition->id) }}" method="POST" onsubmit="return confirm('Hapus kondisi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Aksi Section -->
        <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <h3 class="font-semibold text-[#111827]">Aksi Eksekusi (THEN)</h3>
                <button @click="showActionModal = true" class="text-sm text-[#4F46E5] hover:text-[#4338CA] font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah Aksi
                </button>
            </div>
            <div class="p-5 flex-1">
                @if($rule->actions->count() == 0)
                    <div class="text-center py-8 text-[#6B7280]">
                        <span class="material-symbols-outlined text-4xl mb-2 text-[#D1D5DB]">electric_bolt</span>
                        <p class="text-sm">Belum ada aksi. Rule tidak akan melakukan apa-apa.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($rule->actions as $action)
                            <div class="flex items-center justify-between p-3 border border-indigo-100 rounded-lg bg-indigo-50/50">
                                <div>
                                    <div class="text-sm font-bold text-[#111827]">{{ $action->action_type }}</div>
                                    @if($action->action_params)
                                    <div class="text-xs text-gray-500 font-mono mt-1">{{ json_encode($action->action_params) }}</div>
                                    @endif
                                </div>
                                <form action="{{ route('system.rbl.action.destroy', $action->id) }}" method="POST" onsubmit="return confirm('Hapus aksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1"><span class="material-symbols-outlined text-[18px]">delete</span></button>
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
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Tambah Kondisi (IF)</h3>
                        <button @click="showConditionModal = false" class="text-gray-400 hover:text-gray-500"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('system.rbl.condition.store', $rule->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @if($rule->conditions->count() > 0)
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Operator Logika Sebelumnya</label>
                            <select name="logic_operator" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg">
                                <option value="AND">AND (Keduanya harus benar)</option>
                                <option value="OR">OR (Salah satu harus benar)</option>
                            </select>
                        </div>
                        @else
                            <input type="hidden" name="logic_operator" value="AND">
                        @endif
                        
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Field / Data</label>
                            <input type="text" name="field" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg" placeholder="Contoh: quantity">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Operator</label>
                            <select name="operator" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg">
                                <option value="==">Sama Dengan (==)</option>
                                <option value="!=">Tidak Sama (!=)</option>
                                <option value=">">Lebih Besar (>)</option>
                                <option value=">=">Lebih Besar atau Sama (>=)</option>
                                <option value="<">Lebih Kecil (<)</option>
                                <option value="<=">Lebih Kecil atau Sama (<=)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nilai (Value)</label>
                            <input type="text" name="value" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg" placeholder="Contoh: 100">
                        </div>
                        
                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button" @click="showConditionModal = false" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-[#4F46E5] rounded-lg">Simpan</button>
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
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Tambah Aksi (THEN)</h3>
                        <button @click="showActionModal = false" class="text-gray-400 hover:text-gray-500"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('system.rbl.action.store', $rule->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tipe Aksi</label>
                            <select name="action_type" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg">
                                <option value="App\Services\RuleEngine\Actions\UpdateStatusAction">Update Status Transaksi</option>
                                <option value="App\Services\RuleEngine\Actions\SendNotificationAction">Kirim Notifikasi</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Parameter Aksi (JSON) - Opsional</label>
                            <textarea name="action_params" rows="3" class="w-full px-3 py-2 text-sm bg-white border border-[#D1D5DB] rounded-lg font-mono" placeholder='{"status": "Pending Approval"}'></textarea>
                            <p class="text-xs text-gray-500 mt-1">Masukkan parameter dalam format JSON yang valid.</p>
                        </div>
                        
                        <div class="pt-4 flex justify-end gap-2">
                            <button type="button" @click="showActionModal = false" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-[#4F46E5] rounded-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection