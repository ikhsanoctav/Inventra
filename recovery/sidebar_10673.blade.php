<!-- Sidebar -->
<aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white border-r border-[#E5E7EB] h-screen flex flex-col transition-all duration-300 shadow-sm z-20 shrink-0 relative">
    
    <!-- Logo Area -->
    <div class="h-16 flex items-center border-b border-[#E5E7EB] px-4 shrink-0 transition-all overflow-hidden" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
        <div class="flex items-center gap-3 w-full">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-md shadow-indigo-500/30 shrink-0">
                <span class="material-symbols-outlined text-white text-[18px]">inventory_2</span>
            </div>
            <span x-show="sidebarOpen" class="font-headline font-bold text-xl tracking-tight text-slate-800 whitespace-nowrap">
                INVENTRA<span class="text-indigo-600">.</span>
            </span>
        </div>
    </div>

    <!-- Navigation Area -->
    <div class="flex-1 overflow-y-auto py-4 scrollbar-hide">
        <nav class="space-y-1 px-3">
            
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600' }} relative">
                <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }} transition-colors shrink-0">grid_view</span>
                <span x-show="sidebarOpen" class="text-sm whitespace-nowrap">Dashboard</span>
                
                <!-- Tooltip for collapsed mode -->
                <div x-show="!sidebarOpen" class="absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                    Dashboard
                </div>
            </a>

            <!-- Master Data Section -->
            <div x-data="{ open: {{ request()->is('master*') ? 'true' : 'false' }} }" class="pt-2">
                <div x-show="sidebarOpen" class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Master Data</div>
                
                <!-- Dropdown Trigger -->
                <button @click="sidebarOpen ? open = !open : null" class="w-full group flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors text-slate-600 relative">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[22px] text-slate-400 group-hover:text-indigo-500 transition-colors shrink-0">database</span>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Master</span>
                    </div>
                    <span x-show="sidebarOpen" class="material-symbols-outlined text-[18px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    
                    <!-- Tooltip -->
                    <div x-show="!sidebarOpen" class="absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                        Master Data
                    </div>
                </button>

                <!-- Dropdown Content -->
                <div x-show="open && sidebarOpen" x-collapse class="pl-11 pr-3 space-y-1 mt-1">
                    <a href="{{ route('master.items') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('master.items') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Barang & SKU</a>
                    <a href="{{ route('master.categories') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('master.categories') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Kategori</a>
                    <a href="{{ route('master.units') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('master.units') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Satuan</a>
                    <a href="{{ route('master.suppliers') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('master.suppliers') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Supplier / Vendor</a>
                </div>
            </div>

            <!-- Transactions Section -->
            <div x-data="{ open: {{ request()->is('transactions*') ? 'true' : 'false' }} }" class="pt-2">
                <div x-show="sidebarOpen" class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Transaksi Logistik</div>
                
                <button @click="sidebarOpen ? open = !open : null" class="w-full group flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors text-slate-600 relative">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[22px] text-slate-400 group-hover:text-indigo-500 transition-colors shrink-0">local_shipping</span>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Transaksi</span>
                    </div>
                    <span x-show="sidebarOpen" class="material-symbols-outlined text-[18px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    
                    <div x-show="!sidebarOpen" class="absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                        Transaksi Logistik
                    </div>
                </button>

                <div x-show="open && sidebarOpen" x-collapse class="pl-11 pr-3 space-y-1 mt-1">
                    <a href="{{ route('transactions.inbound.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('transactions.inbound.*') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Barang Masuk (Inbound)</a>
                    <a href="{{ route('transactions.outbound.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('transactions.outbound.*') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Barang Keluar (Outbound)</a>
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors text-slate-500 hover:text-slate-700">Mutasi / Transfer</a>
                    <a href="{{ route('transactions.history.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ request()->routeIs('transactions.history.*') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}">Riwayat & Audit Log</a>
                </div>
            </div>

            <!-- Settings / System Section -->
            <div class="pt-2">
                <div x-show="sidebarOpen" class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Sistem</div>
                
                <a href="{{ route('system.rbl.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors {{ request()->routeIs('system.rbl.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600' }} relative">
                    <span class="material-symbols-outlined text-[22px] {{ request()->routeIs('system.rbl.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }} transition-colors shrink-0">rule</span>
                    <span x-show="sidebarOpen" class="text-sm whitespace-nowrap">Rule Base Logic</span>
                    
                    <div x-show="!sidebarOpen" class="absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                        Rule Base Logic
                    </div>
                </a>

                <!-- POS Link (if user is user/admin) -->
                <a href="#" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors text-slate-600 relative">
                    <span class="material-symbols-outlined text-[22px] text-slate-400 group-hover:text-indigo-500 transition-colors shrink-0">point_of_sale</span>
                    <span x-show="sidebarOpen" class="text-sm whitespace-nowrap">Point of Sale</span>
                    
                    <div x-show="!sidebarOpen" class="absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                        Point of Sale
                    </div>
                </a>
            </div>

        </nav>
    </div>

    <!-- User Profile Footer -->
    <div class="border-t border-[#E5E7EB] p-4 shrink-0 bg-slate-50/50">
        <div class="flex items-center gap-3" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff" alt="Profile" class="w-9 h-9 rounded-full ring-2 ring-white shadow-sm shrink-0">
            <div x-show="sidebarOpen" class="flex-1 min-w-0 overflow-hidden">
                <p class="text-sm font-semibold text-slate-800 truncate">Admin User</p>
                <p class="text-[11px] text-slate-500 truncate">admin@inventra.com</p>
            </div>
            
            <form x-show="sidebarOpen" action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>