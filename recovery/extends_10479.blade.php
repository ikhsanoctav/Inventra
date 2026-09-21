@extends('layouts.app')

@section('header_title', 'User Management')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, currentId: null, form: { name: '', email: '', role: 'kasir', password: '' } }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">User Management</h2>
            <p class="text-sm text-[#6B7280]">Kelola hak akses dan akun pengguna sistem.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="showModal = true; editMode = false; form = { name: '', email: '', role: 'kasir', password: '' }" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Akun</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#4B5563]">
                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama User</th>
                        <th scope="col" class="px-6 py-4">Email</th>
                        <th scope="col" class="px-6 py-4">Role Akses</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-[#111827]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">person</span>
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $roleColors = [
                                    'super_admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'manager' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'admin_gudang' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'purchasing' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'kasir' => 'bg-pink-100 text-pink-800 border-pink-200'
                                ];
                                $color = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $color }}">
                                {{ strtoupper(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="showModal = true; editMode = true; currentId = {{ $user->id }}; form = { name: '{{ $user->name }}', email: '{{ $user->email }}', role: '{{ $user->role }}', password: '' }" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-600 border border-gray-200 hover:bg-white hover:text-[#4F46E5] hover:border-[#4F46E5] flex items-center justify-center transition-all">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                
                                @if($user->id !== auth()->id())
                                <form action="{{ route('system.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showModal" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB]">
                <div class="bg-white px-6 pb-6 pt-6">
                    <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">
                        <h3 class="text-lg font-headline font-bold text-[#111827]" x-text="editMode ? 'Edit User' : 'Tambah User Baru'"></h3>
                        <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    
                    <form :action="editMode ? '/system/users/' + currentId : '/system/users'" method="POST" class="space-y-4">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" x-model="form.name" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Email</label>
                            <input type="email" name="email" x-model="form.email" required class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Role (Hak Akses)</label>
                            <select name="role" x-model="form.role" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                                <option value="kasir">Kasir (POS)</option>
                                <option value="admin_gudang">Admin Gudang</option>
                                <option value="purchasing">Purchasing</option>
                                <option value="manager">Manager</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Password <span x-show="editMode" class="text-gray-400 font-normal">(Kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" x-model="form.password" :required="!editMode" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20">
                        </div>
                        
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-[#4F46E5] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#4338CA] sm:w-auto transition-colors">
                                Simpan Data
                            </button>
                            <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection