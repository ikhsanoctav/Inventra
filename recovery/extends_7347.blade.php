@extends('layouts.app')
2: 
3: @section('header_title', 'Overview')
4: 
5: @section('main_content')
6: <div class="space-y-6">
7:     <!-- Title -->
8:     <div>
9:         <h1 class="font-headline text-2xl font-bold text-[#111827]">Dashboard {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</h1>
10:         <p class="text-sm text-[#6B7280]">Pantau kondisi inventaris, alur pergerakan stok, dan aktivitas sistem secara menyeluruh.</p>
11:     </div>
12: 
13:     <!-- Stats Grid -->
14:     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
15:         <!-- Stat 1 -->
16:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
17:             <div class="flex items-center justify-between mb-4">
18:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Total SKU Barang</p>
19:                 <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
20:                     <span class="material-symbols-outlined text-[18px]">category</span>
21:                 </div>
22:             </div>
23:             <div>
24:                 <h3 class="font-headline text-2xl font-bold text-[#111827]">{{ number_format($totalItems, 0, ',', '.') }} <span class="text-sm font-normal text-[#9CA3AF]">Item</span></h3>
25:                 <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-1">
26:                     Aktif di Master Data
27:                 </p>
28:             </div>
29:         </div>
30: 
31:         <!-- Stat 2 -->
32:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
33:             <div class="flex items-center justify-between mb-4">
34:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Stok Terdistribusi</p>
35:                 <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#4F46E5] flex items-center justify-center">
36:                     <span class="material-symbols-outlined text-[18px]">inventory</span>
37:                 </div>
38:             </div>
39:             <div>
40:                 <h3 class="font-headline text-2xl font-bold text-[#111827]">{{ number_format($totalStock, 0, ',', '.') }} <span class="text-sm font-normal text-[#9CA3AF]">Unit</span></h3>
41:                 <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
42:                     <div class="bg-[#4F46E5] h-1.5 rounded-full" style="width: 100%"></div>
43:                 </div>
44:                 <p class="text-[10px] text-[#9CA3AF] mt-1 text-right">Total Fisik</p>
45:             </div>
46:         </div>
47: 
48:         <!-- Stat 3 -->
49:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
50:             <div class="flex items-center justify-between mb-4">
51:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Rekanan Supplier</p>
52:                 <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
53:                     <span class="material-symbols-outlined text-[18px]">local_shipping</span>
54:                 </div>
55:             </div>
56:             <div>
57:                 <h3 class="font-headline text-2xl font-bold text-[#111827]">{{ $totalSuppliers }} <span class="text-sm font-normal text-[#9CA3AF]">Vendor</span></h3>
58:                 <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-1">
59:                     Terdaftar di sistem
60:                 </p>
61:             </div>
62:         </div>
63: 
64:         <!-- Stat 4 -->
65:         <div class="bg-white p-5 rounded-2xl border border-[#E5E7EB] shadow-sm flex flex-col justify-between">
66:             <div class="flex items-center justify-between mb-4">
67:                 <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wide">Kesehatan Stok</p>
68:                 <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
69:                     <span class="material-symbols-outlined text-[18px]">warning</span>
70:                 </div>
71:             </div>
72:             <div>
73:                 <h3 class="font-headline text-2xl font-bold {{ $criticalStock > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $criticalStock }} <span class="text-sm font-normal text-[#9CA3AF]">Item Kritis</span></h3>
74:                 <p class="text-[11px] text-[#6B7280] font-medium flex items-center gap-1 mt-1">
75:                     Stok kurang dari 10 unit
76:                 </p>
77:             </div>
78:         </div>
79:     </div>
80: 
81:     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
82:         <!-- Main Chart Area -->
83:         <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6 h-80 flex items-center justify-center">
84:             <div class="text-center">
85:                 <span class="material-symbols-outlined text-4xl text-[#D1D5DB] mb-2">bar_chart</span>
86:                 <p class="text-sm font-medium text-[#6B7280]">Grafik Pergerakan Inventaris & Valuasi</p>
87:                 <p class="text-xs text-[#9CA3AF]">(Segera Hadir)</p>
88:             </div>
89:         </div>
90: 
91:         <!-- Right Side List -->
92:         <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm p-6">
93:             <h3 class="font-semibold text-[#111827] text-sm mb-4">Aktivitas Audit Terkini (RBL)</h3>
94:             <div class="space-y-4">
95:                 <div class="flex items-start gap-3">
96:                     <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
97:                         <span class="material-symbols-outlined text-[16px]">sync</span>
98:                     </div>
99:                     <div>
100:                         <p class="text-xs font-semibold text-[#111827]">Penyesuaian Stok SKU-IT-0082</p>
101:                         <p class="text-[11px] text-[#6B7280] mt-0.5">Penambahan +20 Unit (Laptop ThinkPad L14) via Audit Fisik Triwulan.</p>
102:                         <p class="text-[10px] text-[#9CA3AF] mt-1">Baru saja</p>
103:                     </div>
104:                 </div>
105:                 <div class="flex items-start gap-3">
106:                     <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
107:                         <span class="material-symbols-outlined text-[16px]">check_circle</span>
108:                     </div>
109:                     <div>
110:                         <p class="text-xs font-semibold text-[#111827]">Approval PO Pengadaan #PO-2024-991</p>
111:                         <p class="text-[11px] text-[#6B7280] mt-0.5">Disetujui untuk vendor PT Sentra Medika Utama (Rp 84.500.000).</p>
112:                         <p class="text-[10px] text-[#9CA3AF] mt-1">12m lalu</p>
113:                     </div>
114:                 </div>
115:             </div>
116:         </div>
117:     </div>
118: </div>
119: @endsection