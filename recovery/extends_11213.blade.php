@extends('layouts.app')
2: 
3: @section('header_title', 'Rekanan Supplier')
4: 
5: @section('main_content')
6: <div class="space-y-6" x-data="{ showModal: false }">
7:     <!-- Header Section -->
8:     <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
9:         <div>
10:             <h2 class="font-headline text-2xl font-bold text-[#111827]">Rekanan Supplier</h2>
11:             <p class="text-sm text-[#6B7280]">Kelola dan pantau informasi rekanan supplier dalam sistem logistik.</p>
12:         </div>
13:         
14:         <div class="flex items-center gap-3">
15:             <div class="relative">
16:                 <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
17:                 <input type="text" placeholder="Cari data..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all shadow-sm">
18:             </div>
19:             
20:             <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#F97316] hover:bg-[#EA580C] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-orange-500/20 transition-all shrink-0">
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
32:                         <th scope="col" class="px-6 py-4">Nama Perusahaan</th>
                        <th scope="col" class="px-6 py-4">Kontak</th>
                        <th scope="col" class="px-6 py-4">Email</th>
                        <th scope="col" class="px-6 py-4">Status Kontrak</th>
33:                         <th scope="col" class="px-6 py-4 text-right">Aksi</th>
34:                     </tr>
35:                 </thead>
36:                 <tbody class="divide-y divide-[#E5E7EB]">
37:                     @forelse($suppliers as $supplier)
38:                     <tr class="hover:bg-gray-50 transition-colors">
39:                         <td class="px-6 py-4 font-medium text-[#111827]">{{ $supplier->name }}</td>
40:                         <td class="px-6 py-4 text-[#6B7280]">{{ $supplier->contact_person ?? '-' }}<br><span class="text-xs">{{ $supplier->phone }}</span></td>
41:                         <td class="px-6 py-4 text-[#6B7280]">{{ $supplier->email ?? '-' }}</td>
42:                         <td class="px-6 py-4">
43:                             <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $supplier->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
44:                                 {{ ucfirst($supplier->status) }}
45:                             </span>
46:                         </td>
47:                         <td class="px-6 py-4 text-right">
48:                             <button class="text-[#F97316] hover:text-[#EA580C] mr-3">Edit</button>
49:                             <button class="text-red-500 hover:text-red-700">Hapus</button>
50:                         </td>
51:                     </tr>
52:                     @empty
53:                     <!-- Empty State Placeholder -->
54:                     <tr>
55:                         <td colspan="5" class="px-6 py-12 text-center">
56:                             <div class="flex flex-col items-center justify-center">
57:                                 <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
58:                                     <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">inventory_2</span>
59:                                 </div>
60:                                 <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada rekanan supplier</h3>
61:                                 <p class="text-xs text-[#6B7280]">Data yang Anda tambahkan akan muncul di sini.</p>
62:                             </div>
63:                         </td>
64:                     </tr>
65:                     @endforelse
66:                 </tbody>
67:             </table>
68:         </div>
69:         
70:         <!-- Pagination Placeholder -->
71:         <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
72:             <span>Menampilkan 0 data</span>
73:             <div class="flex items-center gap-1">
74:                 <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_left</span></button>
75:                 <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_right</span></button>
76:             </div>
77:         </div>
78:     </div>
79: 
80:     <!-- Add Data Modal Overlay -->
81:     <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
82:         <!-- Background backdrop -->
83:         <div x-show="showModal" 
84:              x-transition:enter="ease-out duration-300" 
85:              x-transition:enter-start="opacity-0" 
86:              x-transition:enter-end="opacity-100" 
87:              x-transition:leave="ease-in duration-200" 
88:              x-transition:leave-start="opacity-100" 
89:              x-transition:leave-end="opacity-0" 
90:              class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
91:              @click="showModal = false"></div>
92: 
93:         <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
94:             <!-- Modal panel -->
95:             <div x-show="showModal" 
96:                  x-transition:enter="ease-out duration-300" 
97:                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
98:                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
99:                  x-transition:leave="ease-in duration-200" 
100:                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
101:                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
102:                  class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB]">
103:                 
104:                 <div class="bg-white px-6 pb-6 pt-6">
105:                     <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">
106:                         <h3 class="text-lg font-headline font-bold text-[#111827]" id="modal-title">Tambah Rekanan Supplier</h3>
107:                         <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">
108:                             <span class="material-symbols-outlined text-[20px]">close</span>
109:                         </button>
110:                     </div>
111:                     
112:                     <form action="{{ route('master.suppliers') }}" method="POST" class="space-y-4">
113:                         @csrf
114:                         
115:                         <div>
116:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Perusahaan</label>
117:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" name="name" required placeholder="Masukkan nama perusahaan...">
118:                         </div>
119:                         <div>
120:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kontak</label>
121:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" name="contact_person" placeholder="Masukkan kontak person...">
122:                         </div>
123:                         <div>
124:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Email</label>
125:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" name="email" type="email" placeholder="Masukkan email...">
126:                         </div>
127:                         <div>
128:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Status Kontrak</label>
129:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" name="phone" placeholder="Masukkan nomor telepon...">
130:                         </div>
131:                         
132:                         <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">
133:                             <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-[#F97316] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#EA580C] sm:w-auto transition-colors">
134:                                 Simpan Data
135:                             </button>
136:                             <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
137:                                 Batal
138:                             </button>
139:                         </div>
140:                     </form>
141:                 </div>
142:             </div>
143:         </div>
144:     </div>
145: </div>
146: 
147: <!-- Add Alpine.js for simple modal state management without writing custom JS -->
148: <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
149: @endsection