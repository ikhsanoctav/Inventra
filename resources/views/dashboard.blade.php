@extends('layouts.app')



@section('header_title', 'Super Admin Overview')



@section('main_content')

<div class="space-y-6">

    <!-- Title -->

    <div>

        <h1 class="font-headline text-2xl font-bold text-[#111827]">Dashboard Super Admin</h1>

        <p class="text-sm text-[#6B7280]">Pusat kendali seluruh sistem, pengguna, dan konfigurasi global.</p>

    </div>



    <!-- Stats Grid -->

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Stat 1 -->

        <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">

            <div class="flex items-center justify-between mb-4">

                <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Total Pengguna</p>

                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">

                    <span class="material-symbols-outlined text-[18px]">group</span>

                </div>

            </div>

            <div>

                <h3 class="font-headline text-2xl font-bold text-[#111827]">{{ \App\Models\User::count() }} <span class="text-sm font-normal text-[#9CA3AF]">User</span></h3>

                <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-1">

                    Aktif di Sistem

                </p>

            </div>

        </div>



        <!-- Stat 2 -->

        <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">

            <div class="flex items-center justify-between mb-4">

                <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Audit & Aktivitas (Global)</p>

                <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#4F46E5] flex items-center justify-center">

                    <span class="material-symbols-outlined text-[18px]">history</span>

                </div>

            </div>

            <div>

                <h3 class="font-headline text-2xl font-bold text-[#111827]">Sistem <span class="text-sm font-normal text-[#9CA3AF]">Aman</span></h3>

                <p class="text-[10px] text-[#9CA3AF] mt-1">Dipantau secara real-time</p>

            </div>

        </div>



        <!-- Stat 3 -->

        <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">

            <div class="flex items-center justify-between mb-4">

                <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Pengaturan Sistem</p>

                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">

                    <span class="material-symbols-outlined text-[18px]">settings</span>

                </div>

            </div>

            <div>

                <h3 class="font-headline text-2xl font-bold text-[#111827]">Global <span class="text-sm font-normal text-[#9CA3AF]">Config</span></h3>

                <p class="text-[11px] text-[#6B7280] font-medium flex items-center gap-1 mt-1">

                    Atur master dan rbl

                </p>

            </div>

        </div>



        <!-- Stat 4 -->

        <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">

            <div class="flex items-center justify-between mb-4">

                <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Status Server</p>

                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">

                    <span class="material-symbols-outlined text-[18px]">dns</span>

                </div>

            </div>

            <div>

                <h3 class="font-headline text-2xl font-bold text-emerald-600">Online</h3>

                <p class="text-[11px] text-[#6B7280] font-medium flex items-center gap-1 mt-1">

                    Semua layanan berjalan lancar

                </p>

            </div>

        </div>

    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Area -->

        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6 flex flex-col gap-4">

            <div class="border-b border-[#E5E7EB] pb-4">

                <h3 class="font-semibold text-[#111827] text-lg">Akses Penuh Fitur (Super Admin Only)</h3>

                <p class="text-sm text-[#6B7280]">Anda memiliki akses tanpa batas ke semua modul di bawah ini:</p>

            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">

                <a href="{{ route('system.users') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">

                    <span class="material-symbols-outlined text-blue-600">group</span>

                    <div>

                        <p class="font-medium text-sm text-[#111827]">User Management</p>

                        <p class="text-xs text-[#6B7280]">Kelola hak akses pengguna</p>

                    </div>

                </a>

                <a href="{{ route('master.items') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">

                    <span class="material-symbols-outlined text-blue-600">inventory_2</span>

                    <div>

                        <p class="font-medium text-sm text-[#111827]">Master Barang</p>

                        <p class="text-xs text-[#6B7280]">Kelola seluruh data barang</p>

                    </div>

                </a>

                <a href="{{ route('system.settings') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">

                    <span class="material-symbols-outlined text-emerald-600">settings_applications</span>

                    <div>

                        <p class="font-medium text-sm text-[#111827]">System Settings</p>

                        <p class="text-xs text-[#6B7280]">Konfigurasi utama aplikasi</p>

                    </div>

                </a>

                <a href="{{ route('system.rbl') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">

                    <span class="material-symbols-outlined text-orange-600">rule_folder</span>

                    <div>

                        <p class="font-medium text-sm text-[#111827]">Rule Builder (RBL)</p>

                        <p class="text-xs text-[#6B7280]">Atur engine logika otomasi</p>

                    </div>

                </a>

                <a href="{{ route('system.audit') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">

                    <span class="material-symbols-outlined text-purple-600">policy</span>

                    <div>

                        <p class="font-medium text-sm text-[#111827]">Audit Logs</p>

                        <p class="text-xs text-[#6B7280]">Pantau aktivitas semua user</p>

                    </div>

                </a>

            </div>

        </div>



        <!-- Right Side List -->

        <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6">

            <h3 class="font-semibold text-[#111827] text-sm mb-4">Pengguna Terdaftar</h3>

            <div class="space-y-4">

                @foreach(\App\Models\User::take(5)->get() as $user)

                <div class="flex items-start gap-3">

                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center shrink-0">

                        <span class="material-symbols-outlined text-[16px]">person</span>

                    </div>

                    <div>

                        <p class="text-xs font-semibold text-[#111827]">{{ $user->name }}</p>

                        <p class="text-[11px] text-[#6B7280] mt-0.5">{{ $user->email }}</p>

                        <p class="text-[10px] text-blue-600 mt-1 font-medium bg-blue-50 inline-block px-2 py-0.5 rounded">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</p>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endsection