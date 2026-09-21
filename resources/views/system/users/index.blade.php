@extends('layouts.app')

@section('header_title', 'User & Access Management')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, item: {} }">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-slate-800 tracking-tight">Manajemen Akun</h2>
            <p class="text-sm text-slate-500">Kelola akses, pendaftaran karyawan baru, dan peran sistem.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="showModal = true; editMode = false; item = {}" class="h-10 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Akun</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 relative">
        <form action="{{ route('system.users') }}" method="GET" class="flex flex-col sm:flex-row gap-4 relative"
              hx-get="{{ route('system.users') }}"
              hx-target="#table-container"
              hx-select="#table-container"
              hx-swap="outerHTML"
              hx-trigger="input changed delay:500ms from:input[name='search'], change from:select"
              x-data="{ loading: false }"
              @htmx:before-request.camel="loading = true"
              @htmx:after-request.camel="loading = false">
            <div x-show="loading" style="display: none;" class="absolute -top-3 right-4 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Loading...
            </div>
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP, Nama atau Email..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
            <div class="sm:w-48">
                <select name="role" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">Semua Peran</option>
                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="admin_gudang" {{ request('role') == 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                    <option value="kasir" {{ request('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="purchasing" {{ request('role') == 'purchasing' ? 'selected' : '' }}>Purchasing</option>
                </select>
            </div>
            <div class="sm:w-32">
                <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
                </button>
                @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('system.users') }}" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold flex items-center transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 font-medium">Total Akun</p>
                <p class="text-lg font-bold text-slate-800">{{ count($users) }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 font-medium">Super Admin</p>
                <p class="text-lg font-bold text-slate-800">{{ $users->where('role', 'super_admin')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">manage_accounts</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 font-medium">Manager</p>
                <p class="text-lg font-bold text-slate-800">{{ $users->where('role', 'manager')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">inventory</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 font-medium">Admin Gudang</p>
                <p class="text-lg font-bold text-slate-800">{{ $users->where('role', 'admin_gudang')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">shopping_cart</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 font-medium">Purchasing</p>
                <p class="text-lg font-bold text-slate-800">{{ $users->where('role', 'purchasing')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">point_of_sale</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 font-medium">Kasir</p>
                <p class="text-lg font-bold text-slate-800">{{ $users->where('role', 'kasir')->count() }}</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-start gap-3">
        <span class="material-symbols-outlined text-red-600">error</span>
        <ul class="mt-0.5 list-disc list-inside">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Data Table -->
    <div id="table-container" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Informasi Pengguna</th>
                        <th scope="col" class="px-6 py-4">Role Akses</th>
                        <th scope="col" class="px-6 py-4">Terdaftar Pada</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="transition-colors {{ $user->trashed() ? 'bg-red-50/50 opacity-75' : ($user->is_active ? 'hover:bg-slate-50' : 'bg-slate-100 opacity-80') }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold uppercase shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->nip ? $user->nip . ' • ' : '' }}{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($user->role === 'super_admin')
                                    <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold border border-purple-100">Super Admin</span>
                                @elseif($user->role === 'manager')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">Manager</span>
                                @elseif($user->role === 'admin_gudang')
                                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">Admin Gudang</span>
                                @elseif($user->role === 'purchasing')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">Purchasing</span>
                                @elseif($user->role === 'kasir')
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-100">Kasir</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">{{ ucfirst($user->role) }}</span>
                                @endif

                                @if($user->trashed())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-red-600 bg-red-100 px-2 py-1 rounded-full border border-red-200">Terhapus</span>
                                @elseif(!$user->is_active)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 bg-slate-200 px-2 py-1 rounded-full border border-slate-300">Nonaktif</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full border border-emerald-200">Aktif</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1">
                                @if(!$user->trashed())
                                <button @click="item = {{ json_encode($user) }}; editMode = true; showModal = true" class="w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 flex items-center justify-center transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                @endif
                                
                                @if($user->id !== auth()->id())
                                    @if(!$user->trashed())
                                    <form action="{{ route('system.users.toggle_active', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan (block)' : 'mengaktifkan' }} akun ini?');" class="inline-block">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-lg {{ $user->is_active ? 'text-amber-500 hover:bg-amber-50' : 'text-emerald-500 hover:bg-emerald-50' }} flex items-center justify-center transition-colors" title="{{ $user->is_active ? 'Block Akun' : 'Aktifkan Akun' }}">
                                            <span class="material-symbols-outlined text-[18px]">{{ $user->is_active ? 'block' : 'check_circle' }}</span>
                                        </button>
                                    </form>
                                    @endif

                                    @if($user->trashed())
                                    <form action="{{ route('system.users.restore', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan akun ini?');" class="inline-block">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-lg text-emerald-600 hover:bg-emerald-50 flex items-center justify-center transition-colors" title="Pulihkan (Restore)">
                                            <span class="material-symbols-outlined text-[18px]">restore_from_trash</span>
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('system.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->trashed() ? 'Apakah Anda yakin ingin MENGHAPUS PERMANEN akun ini? Data tidak dapat dipulihkan!' : 'Apakah Anda yakin ingin menghapus akun ini secara sementara (Soft Delete)?' }}');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-colors" title="{{ $user->trashed() ? 'Hapus Permanen' : 'Soft Delete' }}">
                                            <span class="material-symbols-outlined text-[18px]">{{ $user->trashed() ? 'delete_forever' : 'delete' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <p class="text-sm text-slate-500">Belum ada pengguna terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal Panel -->
            <div x-show="showModal" x-transition.scale.origin.bottom class="inline-block w-full max-w-md px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:p-6 border border-slate-100 relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 font-headline" x-text="editMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form :action="editMode ? '{{ url('system/users') }}/' + item.id : '{{ route('system.users') }}'" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="space-y-4 mb-6 text-sm">
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">NIP (Nomor Induk Pegawai) <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" x-model="item.nip" placeholder="Cth: 10000001" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="item.name" placeholder="Cth: Budi Santoso" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Email Kedinasan <span class="text-red-500">*</span></label>
                            <input type="email" name="email" x-model="item.email" placeholder="Cth: budi@inventra.go.id" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Role Akses <span class="text-red-500">*</span></label>
                            <select name="role" x-model="item.role" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Role...</option>
                                <option value="super_admin">Super Admin (Full Access)</option>
                                <option value="manager">Manager (Approval & Reports)</option>
                                <option value="admin_gudang">Admin Gudang (Mutasi & Inbound)</option>
                                <option value="purchasing">Purchasing (PO & Supplier)</option>
                                <option value="kasir">Kasir (Front-liner POS)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">
                                Kata Sandi 
                                <span x-show="!editMode" class="text-red-500">*</span>
                                <span x-show="editMode" class="text-xs text-slate-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                            </label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" :required="!editMode">
                        </div>

                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex gap-3 justify-end">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors shadow-sm shadow-indigo-500/20">
                            <span x-text="editMode ? 'Simpan Perubahan' : 'Buat Akun'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection