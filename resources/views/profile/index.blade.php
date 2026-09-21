@extends('layouts.app')

@section('header_title', 'Profil Saya')

@section('main_content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 lg:p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Profil Pengguna</h2>
        <p class="text-slate-500 mt-1 mb-8">Kelola informasi pribadi dan keamanan akun Anda.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
            <!-- Profile Info -->
            <div class="md:col-span-1 flex flex-col items-center">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg mb-4 bg-slate-100 flex items-center justify-center relative group">
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4f46e5&color=fff&size=256" alt="Profile" class="w-full h-full object-cover">
                    @endif
                </div>
                <h3 class="font-bold text-xl text-slate-800">{{ $user->name }}</h3>
                <span class="mt-1 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider rounded-full">{{ str_replace('_', ' ', $user->role) }}</span>
            </div>

            <div class="md:col-span-2 space-y-8">
                <!-- Update Profile Info Form -->
                <div>
                    <h4 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Informasi Akun</h4>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Foto Profil</label>
                            <input type="file" name="photo" accept="image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('photo') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            <p class="text-xs text-slate-500 mt-1">Maksimal 2MB. Format: JPG, PNG.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" required>
                            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" required>
                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Update Password Form -->
                <div>
                    <h4 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Ubah Kata Sandi</h4>
                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" required>
                            @error('current_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi Baru</label>
                                <input type="password" name="password" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" required>
                                @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Sandi Baru</label>
                                <input type="password" name="password_confirmation" class="w-full h-11 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" required>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white font-medium rounded-xl hover:bg-slate-900 focus:ring-4 focus:ring-slate-900/20 transition-all shadow-sm">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
