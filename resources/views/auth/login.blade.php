@extends('layouts.guest')
@section('title', 'INVENTRA — Enterprise Login')
@section('body_class', 'antialiased min-h-screen overflow-hidden selection:bg-indigo-500 selection:text-white font-body relative')

@section('content')
<!-- Animated Mesh Gradient Background -->
<div class="absolute inset-0 -z-10 h-full w-full bg-slate-50 overflow-hidden">
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-indigo-200/80 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute top-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-200/80 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-[10%] left-[20%] w-[50%] h-[50%] bg-sky-200/80 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] bg-[size:32px_32px] opacity-[0.15]"></div>
</div>

<div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8">
    
    <div class="w-full max-w-[1000px] grid grid-cols-1 lg:grid-cols-2 bg-white/70 backdrop-blur-2xl rounded-[2rem] border border-slate-200 shadow-2xl shadow-indigo-900/10 overflow-hidden relative">
        
        <!-- Inner glow for the card -->
        <div class="absolute inset-0 border border-white/50 rounded-[2rem] pointer-events-none"></div>

        <!-- LEFT SIDE: Branding & Value Prop -->
        <div class="hidden lg:flex flex-col p-12 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
            
            <div class="flex-1 flex flex-col justify-center relative z-10">
                <div class="mb-10">
                    <a href="javascript:void(0)" class="inline-flex items-center gap-3 group">
                        <img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" alt="Inventra Logo" class="w-80 h-auto group-hover:scale-105 transition-transform duration-300 object-contain mix-blend-multiply">
                    </a>
                </div>

                <div class="space-y-6">
                    <h1 class="font-headline text-4xl font-extrabold text-slate-900 leading-[1.15] tracking-tight">
                        Mengelola Aset <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Skala Nasional.</span>
                    </h1>
                    <p class="text-slate-600 text-sm leading-relaxed max-w-sm">
                        Platform manajemen inventaris real-time dengan akurasi tinggi, dirancang untuk efisiensi rantai pasok modern.
                    </p>

                    <div class="flex flex-col gap-3 pt-4">
                        <div class="flex items-center gap-3 text-sm text-slate-700 bg-white/50 py-2 px-4 rounded-xl border border-slate-200 w-max backdrop-blur-sm hover:bg-white transition-colors cursor-default">
                            <span class="material-symbols-outlined text-emerald-500 text-[18px]">verified_user</span>
                            Standar ISO 27001 Terenkripsi
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-700 bg-white/50 py-2 px-4 rounded-xl border border-slate-200 w-max backdrop-blur-sm hover:bg-white transition-colors cursor-default">
                            <span class="material-symbols-outlined text-blue-500 text-[18px]">bolt</span>
                            Real-time WebSockets Sync
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-xs text-slate-500 mt-8">
                <p>© 2026 INVENTRA Enterprise. All rights reserved.</p>
            </div>
        </div>

        <!-- RIGHT SIDE: Login Form -->
        <div class="p-8 sm:p-12 lg:p-14 bg-white/50 backdrop-blur-xl border-l border-slate-200 flex flex-col justify-center">
            
            <!-- Mobile Header (Visible only on small screens) -->
            <div class="lg:hidden flex flex-col items-center justify-center text-center mb-8">
                <img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" alt="Inventra Logo" class="w-64 h-auto mb-2 object-contain mix-blend-multiply">
            </div>

            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Selamat Datang</h2>
                <p class="text-sm text-slate-600">Masuk menggunakan kredensial NIP atau Email dinas Anda.</p>
            </div>

            @if(session('error') || $errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3 backdrop-blur-md">
                <span class="material-symbols-outlined text-red-500 text-[20px] shrink-0 mt-0.5">error</span>
                <div>
                    <p class="text-sm font-semibold text-red-800">Gagal Masuk</p>
                    <p class="text-xs text-red-600 mt-1">Kredensial tidak valid. Silakan periksa kembali email dan kata sandi Anda.</p>
                </div>
            </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Input: Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-xs font-medium text-slate-700 ml-1">NIP / Email <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] transition-colors group-focus-within:text-indigo-600 z-10">mail</span>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="10000001 atau admin@inventra.go.id" 
                            class="w-full h-12 pl-12 pr-4 text-sm bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all duration-200"
                            required
                        />
                    </div>
                </div>

                <!-- Input: Password -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label for="password" class="block text-xs font-medium text-slate-700">Kata Sandi <span class="text-red-500">*</span></label>
                        <a href="javascript:void(0)" class="text-xs text-indigo-600 hover:text-indigo-500 transition-colors">Lupa sandi?</a>
                    </div>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] transition-colors group-focus-within:text-indigo-600 z-10">lock</span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••" 
                            class="w-full h-12 pl-12 pr-12 text-sm bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all duration-200 font-mono tracking-wider"
                            required
                        />
                        <button 
                            type="button" 
                            onclick="togglePassword()" 
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none z-10"
                        >
                            <span id="eyeIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-1">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 bg-white text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 focus:ring-offset-transparent">
                    <label for="remember" class="text-xs text-slate-600 cursor-pointer">Ingat sesi saya pada perangkat ini</label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn" 
                    class="w-full h-12 mt-2 rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-400 hover:to-blue-500 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/25 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900"
                >
                    <span id="btnIcon" class="material-symbols-outlined text-[20px]">login</span>
                    <span id="btnText">Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Demo Accounts -->
            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-slate-50 px-2 text-slate-500">Akses Cepat (Demo)</span>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" onclick="fillDemo('admin@inventra.go.id')" class="flex flex-col items-center justify-center gap-1 p-3 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 hover:border-indigo-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <span class="material-symbols-outlined text-indigo-500 text-[20px]">admin_panel_settings</span>
                        <span class="text-xs font-semibold text-slate-700">Super Admin</span>
                    </button>
                    <button type="button" onclick="fillDemo('manager@inventra.go.id')" class="flex flex-col items-center justify-center gap-1 p-3 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 hover:border-indigo-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <span class="material-symbols-outlined text-purple-500 text-[20px]">monitoring</span>
                        <span class="text-xs font-semibold text-slate-700">Manager</span>
                    </button>
                    <button type="button" onclick="fillDemo('gudang@inventra.go.id')" class="flex flex-col items-center justify-center gap-1 p-3 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 hover:border-indigo-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <span class="material-symbols-outlined text-emerald-500 text-[20px]">inventory_2</span>
                        <span class="text-xs font-semibold text-slate-700">Admin Gudang</span>
                    </button>
                    <button type="button" onclick="fillDemo('kasir@inventra.go.id')" class="flex flex-col items-center justify-center gap-1 p-3 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 hover:border-indigo-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <span class="material-symbols-outlined text-sky-500 text-[20px]">point_of_sale</span>
                        <span class="text-xs font-semibold text-slate-700">Kasir POS</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>

<script>
    let isVisible = false;
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        isVisible = !isVisible;
        if(isVisible) {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    function fillDemo(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const icon = document.getElementById('btnIcon');
        const text = document.getElementById('btnText');
        
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        icon.textContent = 'progress_activity';
        icon.classList.add('animate-spin');
        text.textContent = 'Memverifikasi...';
    });
</script>
@endsection
