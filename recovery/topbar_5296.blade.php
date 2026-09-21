<!-- Topbar -->
<header class="h-16 bg-white/80 backdrop-blur-md border-b border-[#E5E7EB] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-10 shrink-0">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition-colors flex items-center justify-center">
            <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>
        
        <!-- Breadcrumbs placeholder -->
        <nav class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
            <a href="#" class="hover:text-indigo-600 transition-colors"><span class="material-symbols-outlined text-[18px]">home</span></a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="font-medium text-slate-700">@yield('header_title', 'Dashboard')</span>
        </nav>
    </div>

    <!-- Search & Profile -->
    <div class="flex items-center gap-3 sm:gap-5">
        
        <!-- Global Search using Alpine -->
        <div x-data="{ searchOpen: false, search: '' }" class="relative hidden sm:block">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input @focus="searchOpen = true" @click.outside="searchOpen = false" x-model="search" type="text" placeholder="Cari SKU, Barang..." class="w-64 h-9 pl-9 pr-4 text-sm bg-slate-100 border-transparent rounded-full text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:text-slate-400">
            </div>
            
            <!-- Search Results Dropdown -->
            <div x-show="searchOpen && search.length > 0" style="display: none;" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-[#E5E7EB] py-2 z-50 max-h-64 overflow-y-auto">
                <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Hasil Pencarian</div>
                <!-- Logic to be implemented -->
                <div class="px-4 py-2 text-sm text-slate-500 italic">Tekan enter untuk mencari...</div>
            </div>
        </div>

        <!-- Notification Bell (Reverb Ready) -->
        <div x-data="{ notifOpen: false, hasUnread: true }" class="relative">
            <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false" class="relative p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                <span x-show="hasUnread" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </button>
            
            <!-- Notification Dropdown -->
            <div x-show="notifOpen" style="display: none;" class="absolute top-full right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-[#E5E7EB] overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-[#E5E7EB] flex items-center justify-between bg-slate-50">
                    <h3 class="font-semibold text-slate-800">Notifikasi</h3>
                    <button class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Tandai sudah dibaca</button>
                </div>
                <div class="max-h-80 overflow-y-auto divide-y divide-[#E5E7EB]">
                    <!-- Dummy Notification -->
                    <div class="p-4 hover:bg-slate-50 transition-colors cursor-pointer bg-indigo-50/30">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px] text-red-600">warning</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-800">Stok Menipis: Kertas A4</p>
                                <p class="text-xs text-slate-500 mt-0.5">Stok tersisa 5 rim (Batas aman: 10). Segera lakukan restock.</p>
                                <p class="text-[10px] text-slate-400 mt-1.5">Baru saja</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2 border-t border-[#E5E7EB] text-center bg-slate-50">
                    <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Lihat Semua Notifikasi</a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

        <!-- Mobile Profile (Hamburger replacement for small screens) -->
        <button class="sm:hidden w-8 h-8 rounded-full overflow-hidden border border-slate-200">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff" alt="Profile" class="w-full h-full object-cover">
        </button>
    </div>
</header>