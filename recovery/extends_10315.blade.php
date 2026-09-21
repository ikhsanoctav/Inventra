@extends('layouts.app')
2: 
3: @section('header_title', 'Barang Keluar')
4: 
5: @section('main_content')
6: <div class="space-y-6" x-data="{ showModal: false }">
7:     <!-- Header Section -->
8:     <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
9:         <div>
10:             <h2 class="font-headline text-2xl font-bold text-[#111827]">Barang Keluar</h2>
11:             <p class="text-sm text-[#6B7280]">Kelola dan pantau informasi barang keluar dalam sistem logistik.</p>
12:         </div>
13:         
14:         <div class="flex items-center gap-3">
15:             <div class="relative">
16:                 <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
17:                 <input type="text" placeholder="Cari data..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all shadow-sm">
18:             </div>
19:             
20:             <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all shrink-0">
21:                 <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Data</span>
22:             </button>
23:         </div>
24:     </div>
25: 
26:     <!-- Data Table -->
27:     <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
28:         <div class="overflow-x-auto">
29:             <table class="w-full text-left text-sm text-[#4B5563]">
30:                 <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
31:                     <tr>
32:                         <th scope="col" class="px-6 py-4">No. Keluar</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Tujuan</th>
                        <th scope="col" class="px-6 py-4">Total Item</th>
                        <th scope="col" class="px-6 py-4">Status Pengiriman</th>
33:                         <th scope="col" class="px-6 py-4 text-right">Aksi</th>
34:                     </tr>
35:                 </thead>
36:                 <tbody class="divide-y divide-[#E5E7EB]">
37:                     <!-- Empty State Placeholder -->
38:                     <tr>
39:                         <td colspan="6" class="px-6 py-12 text-center">
40:                             <div class="flex flex-col items-center justify-center">
41:                                 <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
42:                                     <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">inventory_2</span>
43:                                 </div>
44:                                 <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada barang keluar</h3>
45:                                 <p class="text-xs text-[#6B7280]">Data yang Anda tambahkan akan muncul di sini.</p>
46:                             </div>
47:                         </td>
48:                     </tr>
49:                 </tbody>
50:             </table>
51:         </div>
52:         
53:         <!-- Pagination Placeholder -->
54:         <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
55:             <span>Menampilkan 0 data</span>
56:             <div class="flex items-center gap-1">
57:                 <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_left</span></button>
58:                 <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_right</span></button>
59:             </div>
60:         </div>
61:     </div>
62: 
63:     <!-- Add Data Modal Overlay -->
64:     <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
65:         <!-- Background backdrop -->
66:         <div x-show="showModal" 
67:              x-transition:enter="ease-out duration-300" 
68:              x-transition:enter-start="opacity-0" 
69:              x-transition:enter-end="opacity-100" 
70:              x-transition:leave="ease-in duration-200" 
71:              x-transition:leave-start="opacity-100" 
72:              x-transition:leave-end="opacity-0" 
73:              class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
74:              @click="showModal = false"></div>
75: 
76:         <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
77:             <!-- Modal panel -->
78:             <div x-show="showModal" 
79:                  x-transition:enter="ease-out duration-300" 
80:                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
81:                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
82:                  x-transition:leave="ease-in duration-200" 
83:                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
84:                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
85:                  class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB]">
86:                 
87:                 <div class="bg-white px-6 pb-6 pt-6">
88:                     <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">
89:                         <h3 class="text-lg font-headline font-bold text-[#111827]" id="modal-title">Tambah Barang Keluar</h3>
90:                         <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">
91:                             <span class="material-symbols-outlined text-[20px]">close</span>
92:                         </button>
93:                     </div>
94:                     
95:                     <form action="#" method="POST" class="space-y-4">
96:                         @csrf
97:                         
98:                         <div>
99:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">No. Keluar</label>
100:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan no. keluar...">
101:                         </div>
102:                         <div>
103:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal</label>
104:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan tanggal...">
105:                         </div>
106:                         <div>
107:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tujuan</label>
108:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan tujuan...">
109:                         </div>
110:                         <div>
111:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Total Item</label>
112:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan total item...">
113:                         </div>
114:                         <div>
115:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Status Pengiriman</label>
116:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan status pengiriman...">
117:                         </div>
118:                         
119:                         <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">
120:                             <button type="button" class="inline-flex w-full justify-center rounded-lg bg-[#4F46E5] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#4338CA] sm:w-auto transition-colors">
121:                                 Simpan Data
122:                             </button>
123:                             <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
124:                                 Batal
125:                             </button>
126:                         </div>
127:                     </form>
128:                 </div>
129:             </div>
130:         </div>
131:     </div>
132: </div>
133: 
134: <!-- Add Alpine.js for simple modal state management without writing custom JS -->
135: <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
136: @endsection