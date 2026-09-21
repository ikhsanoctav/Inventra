@extends('layouts.app')
2: 
3: @section('header_title', 'Super Admin Overview')
4: 
5: @section('main_content')
6: <div class="space-y-6">
7:     <!-- Title -->
8:     <div>
9:         <h1 class="font-headline text-2xl font-bold text-[#111827]">Dashboard Super Admin</h1>
10:         <p class="text-sm text-[#6B7280]">Pusat kendali seluruh sistem, pengguna, dan konfigurasi global.</p>
11:     </div>
12: 
13:     <!-- Stats Grid -->
14:     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
15:         <!-- Stat 1 -->
16:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
17:             <div class="flex items-center justify-between mb-4">
18:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Total Pengguna</p>
19:                 <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
20:                     <span class="material-symbols-outlined text-[18px]">group</span>
21:                 </div>
22:             </div>
23:             <div>
24:                 <h3 class="font-headline text-2xl font-bold text-[#111827]">{{ \App\Models\User::count() }} <span class="text-sm font-normal text-[#9CA3AF]">User</span></h3>
25:                 <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-1">
26:                     Aktif di Sistem
27:                 </p>
28:             </div>
29:         </div>
30: 
31:         <!-- Stat 2 -->
32:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
33:             <div class="flex items-center justify-between mb-4">
34:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Audit & Aktivitas (Global)</p>
35:                 <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#4F46E5] flex items-center justify-center">
36:                     <span class="material-symbols-outlined text-[18px]">history</span>
37:                 </div>
38:             </div>
39:             <div>
40:                 <h3 class="font-headline text-2xl font-bold text-[#111827]">Sistem <span class="text-sm font-normal text-[#9CA3AF]">Aman</span></h3>
41:                 <p class="text-[10px] text-[#9CA3AF] mt-1">Dipantau secara real-time</p>
42:             </div>
43:         </div>
44: 
45:         <!-- Stat 3 -->
46:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
47:             <div class="flex items-center justify-between mb-4">
48:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Pengaturan Sistem</p>
49:                 <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
50:                     <span class="material-symbols-outlined text-[18px]">settings</span>
51:                 </div>
52:             </div>
53:             <div>
54:                 <h3 class="font-headline text-2xl font-bold text-[#111827]">Global <span class="text-sm font-normal text-[#9CA3AF]">Config</span></h3>
55:                 <p class="text-[11px] text-[#6B7280] font-medium flex items-center gap-1 mt-1">
56:                     Atur master dan rbl
57:                 </p>
58:             </div>
59:         </div>
60: 
61:         <!-- Stat 4 -->
62:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
63:             <div class="flex items-center justify-between mb-4">
64:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Status Server</p>
65:                 <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
66:                     <span class="material-symbols-outlined text-[18px]">dns</span>
67:                 </div>
68:             </div>
69:             <div>
70:                 <h3 class="font-headline text-2xl font-bold text-emerald-600">Online</h3>
71:                 <p class="text-[11px] text-[#6B7280] font-medium flex items-center gap-1 mt-1">
72:                     Semua layanan berjalan lancar
73:                 </p>
74:             </div>
75:         </div>
76:     </div>
77: 
78:     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
79:         <!-- Main Area -->
80:         <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6 flex flex-col gap-4">
81:             <div class="border-b border-[#E5E7EB] pb-4">
82:                 <h3 class="font-semibold text-[#111827] text-lg">Akses Penuh Fitur (Super Admin Only)</h3>
83:                 <p class="text-sm text-[#6B7280]">Anda memiliki akses tanpa batas ke semua modul di bawah ini:</p>
84:             </div>
85:             <div class="grid grid-cols-2 gap-4">
86:                 <a href="{{ route('master.items') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">
87:                     <span class="material-symbols-outlined text-blue-600">inventory_2</span>
88:                     <div>
89:                         <p class="font-medium text-sm text-[#111827]">Master Barang</p>
90:                         <p class="text-xs text-[#6B7280]">Kelola seluruh data barang</p>
91:                     </div>
92:                 </a>
93:                 <a href="{{ route('system.settings') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">
94:                     <span class="material-symbols-outlined text-emerald-600">settings_applications</span>
95:                     <div>
96:                         <p class="font-medium text-sm text-[#111827]">System Settings</p>
97:                         <p class="text-xs text-[#6B7280]">Konfigurasi utama aplikasi</p>
98:                     </div>
99:                 </a>
100:                 <a href="{{ route('system.rbl') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">
101:                     <span class="material-symbols-outlined text-orange-600">rule_folder</span>
102:                     <div>
103:                         <p class="font-medium text-sm text-[#111827]">Rule Builder (RBL)</p>
104:                         <p class="text-xs text-[#6B7280]">Atur engine logika otomasi</p>
105:                     </div>
106:                 </a>
107:                 <a href="{{ route('system.audit') }}" class="p-4 border rounded-xl hover:bg-gray-50 flex items-center gap-3 transition">
108:                     <span class="material-symbols-outlined text-purple-600">policy</span>
109:                     <div>
110:                         <p class="font-medium text-sm text-[#111827]">Audit Logs</p>
111:                         <p class="text-xs text-[#6B7280]">Pantau aktivitas semua user</p>
112:                     </div>
113:                 </a>
114:             </div>
115:         </div>
116: 
117:         <!-- Right Side List -->
118:         <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6">
119:             <h3 class="font-semibold text-[#111827] text-sm mb-4">Pengguna Terdaftar</h3>
120:             <div class="space-y-4">
121:                 @foreach(\App\Models\User::take(5)->get() as $user)
122:                 <div class="flex items-start gap-3">
123:                     <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center shrink-0">
124:                         <span class="material-symbols-outlined text-[16px]">person</span>
125:                     </div>
126:                     <div>
127:                         <p class="text-xs font-semibold text-[#111827]">{{ $user->name }}</p>
128:                         <p class="text-[11px] text-[#6B7280] mt-0.5">{{ $user->email }}</p>
129:                         <p class="text-[10px] text-blue-600 mt-1 font-medium bg-blue-50 inline-block px-2 py-0.5 rounded">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</p>
130:                     </div>
131:                 </div>
132:                 @endforeach
133:             </div>
134:         </div>
135:     </div>
136: </div>
137: @endsection