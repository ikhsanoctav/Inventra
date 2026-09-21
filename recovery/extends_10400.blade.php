@extends('layouts.app')
2: 
3: @section('header_title', 'Kategori Barang')
4: 
5: @section('main_content')
6: <div class="space-y-6" x-data="{ showModal: false }">
7:     <!-- Header Section -->
8:     <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
9:         <div>
10:             <h2 class="font-headline text-2xl font-bold text-[#111827]">Kategori Barang</h2>
11:             <p class="text-sm text-[#6B7280]">Kelola dan pantau informasi kategori barang dalam sistem logistik.</p>
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
32:                         <th scope="col" class="px-6 py-4">Nama Kategori</th>
                        <th scope="col" class="px-6 py-4">Deskripsi Kategori</th>
                        <th scope="col" class="px-6 py-4">Jumlah Item</th>
33:                         <th scope="col" class="px-6 py-4 text-right">Aksi</th>
34:                     </tr>
35:                 </thead>
36:                 <tbody class="divide-y divide-[#E5E7EB]">
37:                     @forelse($categories as $category)
38:                     <tr class="hover:bg-gray-50 transition-colors">
39:                         <td class="px-6 py-4 font-medium text-[#111827]">{{ $category->name }}</td>
40:                         <td class="px-6 py-4 text-[#6B7280]">{{ $category->description ?? '-' }}</td>
41:                         <td class="px-6 py-4">
42:                             <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
43:                                 {{ $category->items_count }} SKU
44:                             </span>
45:                         </td>
46:                         <td class="px-6 py-4 text-right">
47:                             <button class="text-[#F97316] hover:text-[#EA580C] mr-3">Edit</button>
48:                             <button class="text-red-500 hover:text-red-700">Hapus</button>
49:                         </td>
50:                     </tr>
51:                     @empty
52:                     <!-- Empty State Placeholder -->
53:                     <tr>
54:                         <td colspan="4" class="px-6 py-12 text-center">
55:                             <div class="flex flex-col items-center justify-center">
56:                                 <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
57:                                     <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">inventory_2</span>
58:                                 </div>
59:                                 <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada kategori barang</h3>
60:                                 <p class="text-xs text-[#6B7280]">Data yang Anda tambahkan akan muncul di sini.</p>
61:                             </div>
62:                         </td>
63:                     </tr>
64:                     @endforelse
65:                 </tbody>
66:             </table>
67:         </div>
68:         
69:         <!-- Pagination Placeholder -->
70:         <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
71:             <span>Menampilkan 0 data</span>
72:             <div class="flex items-center gap-1">
73:                 <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_left</span></button>
74:                 <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_right</span></button>
75:             </div>
76:         </div>
77:     </div>
78: 
79:     <!-- Add Data Modal Overlay -->
80:     <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
81:         <!-- Background backdrop -->
82:         <div x-show="showModal" 
83:              x-transition:enter="ease-out duration-300" 
84:              x-transition:enter-start="opacity-0" 
85:              x-transition:enter-end="opacity-100" 
86:              x-transition:leave="ease-in duration-200" 
87:              x-transition:leave-start="opacity-100" 
88:              x-transition:leave-end="opacity-0" 
89:              class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
90:              @click="showModal = false"></div>
91: 
92:         <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
93:             <!-- Modal panel -->
94:             <div x-show="showModal" 
95:                  x-transition:enter="ease-out duration-300" 
96:                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
97:                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
98:                  x-transition:leave="ease-in duration-200" 
99:                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
100:                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
101:                  class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB]">
102:                 
103:                 <div class="bg-white px-6 pb-6 pt-6">
104:                     <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">
105:                         <h3 class="text-lg font-headline font-bold text-[#111827]" id="modal-title">Tambah Kategori Barang</h3>
106:                         <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">
107:                             <span class="material-symbols-outlined text-[20px]">close</span>
108:                         </button>
109:                     </div>
110:                     
111:                     <form action="{{ route('master.categories') }}" method="POST" class="space-y-4">
112:                         @csrf
113:                         
114:                         <div>
115:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Kategori</label>
116:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" name="name" required placeholder="Masukkan nama kategori...">
117:                         </div>
118:                         <div>
119:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Deskripsi Kategori</label>
120:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" name="description" placeholder="Masukkan deskripsi kategori...">
121:                         </div>
122:                         <div>
123:                             <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jumlah Item</label>
124:                             <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/20 transition-all" placeholder="Masukkan jumlah item...">
125:                         </div>
126:                         
127:                         <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">
128:                             <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-[#F97316] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#EA580C] sm:w-auto transition-colors">
129:                                 Simpan Data
130:                             </button>
131:                             <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
132:                                 Batal
133:                             </button>
134:                         </div>
135:                     </form>
136:                 </div>
137:             </div>
138:         </div>
139:     </div>
140: </div>
141: 
142: <!-- Add Alpine.js for simple modal state management without writing custom JS -->
143: <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
144: @endsection