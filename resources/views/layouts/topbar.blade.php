<!-- Topbar -->
<header class="h-16 bg-white/95 backdrop-blur-md border-b border-[#E5E7EB] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-40 shrink-0" style="z-index: 40;">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition-colors flex items-center justify-center">
            <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>
        
        <!-- Breadcrumbs placeholder -->
        <nav class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
            <a href="javascript:void(0)" class="hover:text-indigo-600 transition-colors"><span class="material-symbols-outlined text-[18px]">home</span></a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="font-medium text-slate-700">@yield('header_title', 'Dashboard')</span>
        </nav>
    </div>

    <!-- Search & Profile -->
    <div class="flex items-center gap-3 sm:gap-5">
        
        <!-- Real-time Clock -->
        <div x-data="{ 
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                init() {
                    setInterval(() => {
                        this.time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    }, 1000);
                }
             }" 
             class="hidden md:flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-full text-slate-600 shadow-sm">
            <span class="material-symbols-outlined text-[18px] text-indigo-500">schedule</span>
            <span class="text-sm font-bold font-mono tracking-wider" x-text="time"></span>
        </div>

        <!-- Global Search using Alpine -->
        <div x-data="{ searchOpen: false, search: '', results: [], isSearching: false,
    fetchResults() {
        if(this.search.length < 2) {
            this.results = [];
            return;
        }
        this.isSearching = true;
        fetch('/search?q=' + this.search)
            .then(res => res.json())
            .then(data => {
                this.results = data;
                this.isSearching = false;
            });
    }
}" x-init="$watch('search', () => fetchResults())" class="relative hidden sm:block">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input @focus="searchOpen = true" @click.outside="searchOpen = false" x-model="search" type="text" placeholder="Cari SKU, Barang..." class="w-64 h-9 pl-9 pr-4 text-sm bg-slate-100 border-transparent rounded-full text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:text-slate-400">
            </div>
            
            <!-- Search Results Dropdown -->
            <div x-show="searchOpen && search.length > 0" style="display: none;" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-[#E5E7EB] py-2 z-50 max-h-64 overflow-y-auto">
                <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Hasil Pencarian</div>
                
                <div x-show="isSearching" class="px-4 py-2 text-sm text-slate-500 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span> Mencari...
                </div>
                
                <div x-show="!isSearching && results.length === 0" class="px-4 py-2 text-sm text-slate-500 italic">Tidak ditemukan.</div>
                
                <template x-for="item in results">
                    <a :href="item.url" class="block px-4 py-2 hover:bg-slate-50 transition-colors border-l-2 border-transparent hover:border-indigo-500">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-slate-800" x-text="item.title"></div>
                            <div class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded" x-text="item.type"></div>
                        </div>
                        <div class="text-xs text-slate-500" x-text="item.subtitle"></div>
                    </a>
                </template>
            </div>
        </div>

        <!-- Notification Bell (Auto-Polling & Real-Time Ready) -->
        @if(auth()->check())
        <div x-data="{ 
            notifOpen: false, 
            hasUnread: {{ auth()->user()->unreadNotifications->count() > 0 ? 'true' : 'false' }},
            unreadCount: {{ auth()->user()->unreadNotifications->count() }},
            totalCount: {{ auth()->user()->notifications()->count() }},
            viewingAll: {{ (auth()->user()->unreadNotifications->count() === 0 && auth()->user()->notifications()->count() > 0) ? 'true' : 'false' }},
            isLoading: false,
            notifications: @js(
                (auth()->user()->unreadNotifications->count() === 0 && auth()->user()->notifications()->count() > 0
                    ? auth()->user()->notifications()->take(50)->get()
                    : auth()->user()->unreadNotifications
                )->map(fn($n) => [
                    'id' => $n->id,
                    'read' => $n->read_at !== null,
                    'type' => $n->data['type'] ?? 'info',
                    'item_name' => $n->data['item_name'] ?? 'Pemberitahuan',
                    'message' => $n->data['message'] ?? 'Ada pembaruan sistem.',
                    'time' => $n->created_at->diffForHumans(),
                ])
            ),
            init() {
                // Listen for window event (Echo or local)
                window.addEventListener('new-notification', (e) => {
                    const notif = e.detail;
                    if (!this.notifications.some(n => n.id === notif.id)) {
                        this.notifications.unshift({
                            id: notif.id || Date.now(),
                            read: false,
                            type: notif.type || 'info',
                            item_name: notif.item_name || 'Notifikasi Baru',
                            message: notif.message || '',
                            time: notif.time || 'Baru saja'
                        });
                        this.unreadCount++;
                        this.totalCount++;
                        this.hasUnread = true;
                    }
                });

                // Auto-poll notifications every 10 seconds in background (only when viewing unread)
                setInterval(() => {
                    this.poll();
                }, 10000);
            },
            poll() {
                if (this.viewingAll) return;
                fetch('{{ route('notifications.unread') }}')
                    .then(res => res.json())
                    .then(data => {
                        const previousCount = this.unreadCount;
                        this.unreadCount = data.count;
                        this.hasUnread = data.count > 0;
                        this.notifications = data.notifications;

                        // If new notification arrived while viewing, fire toast
                        if (data.count > previousCount && data.notifications.length > 0) {
                            window.dispatchEvent(new CustomEvent('new-notification', { detail: data.notifications[0] }));
                        }
                    })
                    .catch(() => {});
            },
            loadAllNotifications() {
                this.isLoading = true;
                this.viewingAll = true;
                fetch('{{ route('notifications.all') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                        this.totalCount = data.count;
                        this.hasUnread = data.unread_count > 0;
                        this.isLoading = false;
                        this.$nextTick(() => {
                            if (this.$refs.notifList) {
                                this.$refs.notifList.scrollTo({
                                    top: 160,
                                    behavior: 'smooth'
                                });
                            }
                        });
                    })
                    .catch(() => {
                        this.isLoading = false;
                    });
            },
            loadUnreadNotifications() {
                this.isLoading = true;
                this.viewingAll = false;
                fetch('{{ route('notifications.unread') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.notifications = data.notifications;
                        this.unreadCount = data.count;
                        this.hasUnread = data.count > 0;
                        this.isLoading = false;
                        this.$nextTick(() => {
                            if (this.$refs.notifList) {
                                this.$refs.notifList.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            }
                        });
                    })
                    .catch(() => {
                        this.isLoading = false;
                    });
            },
            markAllRead() {
                fetch('{{ route('notifications.read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    if (this.viewingAll) {
                        this.notifications = this.notifications.map(n => ({ ...n, read: true }));
                    } else {
                        this.notifications = [];
                    }
                    this.unreadCount = 0;
                    this.hasUnread = false;
                });
            },
            markSingleRead(id) {
                const formData = new FormData();
                formData.append('id', id);
                fetch('{{ route('notifications.read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                }).then(() => {
                    if (this.viewingAll) {
                        this.notifications = this.notifications.map(n => n.id === id ? { ...n, read: true } : n);
                        this.unreadCount = this.notifications.filter(n => !n.read).length;
                    } else {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                        this.unreadCount = this.notifications.length;
                    }
                    this.hasUnread = this.unreadCount > 0;
                });
            }
        }" @click.outside="notifOpen = false" class="relative inline-flex items-center">
            <button @click="notifOpen = !notifOpen" type="button" class="w-10 h-10 rounded-full text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors flex items-center justify-center focus:outline-none" title="Notifikasi">
                <span class="material-symbols-outlined text-[22px] select-none">notifications</span>
            </button>
            <span x-show="hasUnread" 
                  x-cloak
                  style="position: absolute; top: 1px; right: 1px; min-width: 18px; height: 18px; line-height: 1; padding: 0 4px; background-color: #ef4444; color: #ffffff; font-size: 10px; font-weight: 700; border-radius: 9999px; border: 2px solid #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.15); pointer-events: none; z-index: 20; display: inline-flex; align-items: center; justify-content: center;"
                  x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            
            <!-- Notification Dropdown -->
            <div x-show="notifOpen" 
                 x-transition:enter="transition ease-out duration-200 transform"
                 x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
                 style="display: none; z-index: 100;" 
                 class="absolute top-full right-0 mt-2.5 w-96 sm:w-[420px] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-[100] ring-1 ring-black/5">
                
                <!-- Card Header -->
                <div class="p-4 border-b border-slate-100 bg-white space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">notifications</span>
                            </div>
                            <h3 class="font-bold text-slate-800 text-sm tracking-tight">Notifikasi</h3>
                            <span x-show="unreadCount > 0" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100" x-text="unreadCount + ' Baru'"></span>
                        </div>
                        <button x-show="unreadCount > 0" @click="markAllRead()" type="button" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">done_all</span>
                            <span>Tandai dibaca</span>
                        </button>
                    </div>

                    <!-- Filter Tabs: Semua vs Belum Dibaca -->
                    <div class="flex p-1 bg-slate-100/90 rounded-xl text-xs font-semibold">
                        <button type="button" 
                                @click="loadAllNotifications()" 
                                class="flex-1 py-1.5 rounded-lg text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                :class="viewingAll ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800'">
                            <span>Semua Riwayat</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold"
                                  :class="viewingAll ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-600'"
                                  x-text="totalCount"></span>
                        </button>
                        <button type="button" 
                                @click="loadUnreadNotifications()" 
                                class="flex-1 py-1.5 rounded-lg text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                :class="!viewingAll ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800'">
                            <span>Belum Dibaca</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold"
                                  :class="!viewingAll ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-600'"
                                  x-text="unreadCount"></span>
                        </button>
                    </div>
                </div>

                <!-- Scrollable Notification List Container -->
                <div x-ref="notifList" class="max-h-80 sm:max-h-96 overflow-y-auto divide-y divide-slate-100 scroll-smooth bg-white">
                    <!-- Loading state -->
                    <div x-show="isLoading" class="p-8 text-center text-slate-500 flex flex-col items-center justify-center gap-2 bg-white">
                        <span class="material-symbols-outlined text-[24px] animate-spin text-indigo-600">progress_activity</span>
                        <span class="text-xs font-medium">Memuat notifikasi...</span>
                    </div>

                    <div x-show="!isLoading" class="bg-white">
                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="p-3.5 hover:bg-slate-50 transition-colors flex items-start gap-3 group border-b border-slate-100 last:border-b-0"
                                 :class="notif.read ? 'bg-white' : 'bg-indigo-50/50'">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 border" 
                                     :class="{
                                         'bg-amber-50 text-amber-600 border-amber-200/60': notif.type === 'low_stock',
                                         'bg-emerald-50 text-emerald-600 border-emerald-200/60': notif.type === 'inbound_completed',
                                         'bg-sky-50 text-sky-600 border-sky-200/60': notif.type.includes('po'),
                                         'bg-violet-50 text-violet-600 border-violet-200/60': notif.type === 'gudang_request',
                                         'bg-indigo-50 text-indigo-600 border-indigo-200/60': !['low_stock', 'inbound_completed', 'gudang_request'].includes(notif.type)
                                     }">
                                    <span class="material-symbols-outlined text-[17px]"
                                          x-text="notif.type === 'low_stock' ? 'warning' : (notif.type === 'inbound_completed' ? 'move_to_inbox' : (notif.type === 'gudang_request' ? 'inventory_2' : 'notifications'))"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1.5">
                                        <p class="text-xs font-bold text-slate-800 truncate" x-text="notif.item_name"></p>
                                        <span x-show="!notif.read" class="w-2 h-2 rounded-full bg-indigo-600 shrink-0" title="Belum dibaca"></span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed" x-text="notif.message"></p>
                                    <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100/70">
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1 font-medium">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                                            <span x-text="notif.time"></span>
                                        </span>
                                        <div>
                                            <button x-show="!notif.read" @click="markSingleRead(notif.id)" type="button" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold hover:underline cursor-pointer">
                                                Tandai dibaca
                                            </button>
                                            <span x-show="notif.read" class="text-[10px] text-slate-400 flex items-center gap-0.5 font-medium">
                                                <span class="material-symbols-outlined text-[13px] text-emerald-500">done_all</span> Dibaca
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="notifications.length === 0" class="py-10 px-6 text-center">
                            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                                <span class="material-symbols-outlined text-[22px]">notifications_off</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800" x-text="viewingAll ? 'Belum Ada Notifikasi' : 'Semua Notifikasi Telah Dibaca'"></p>
                            <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto" x-text="viewingAll ? 'Riwayat aktivitas logistik akan muncul di sini.' : 'Tidak ada pemberitahuan baru yang menunggu tindakan Anda.'"></p>
                            <button x-show="!viewingAll && totalCount > 0" 
                                    @click="loadAllNotifications()" 
                                    type="button" 
                                    class="mt-3.5 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">history</span>
                                <span>Buka Semua Riwayat (<span x-text="totalCount"></span>)</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Bar -->
                <div class="p-3 border-t border-slate-100 bg-slate-50/90">
                    <button x-show="!viewingAll" 
                            type="button" 
                            @click="loadAllNotifications()" 
                            class="w-full py-2 px-3 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-xl text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-all flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">history</span>
                        <span>Lihat Semua Notifikasi (<span x-text="totalCount"></span>)</span>
                    </button>
                    
                    <div x-show="viewingAll" class="flex items-center justify-between px-1.5 text-xs">
                        <span class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Menampilkan seluruh riwayat</span>
                        </span>
                        <button type="button" 
                                @click="loadUnreadNotifications()" 
                                class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer hover:underline">
                            Hanya Belum Dibaca
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Divider -->
        <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

        <!-- Profile Dropdown -->
        <div x-data="{ profileOpen: false }" @click.outside="profileOpen = false" class="relative">
            <button @click="profileOpen = !profileOpen" type="button" class="flex items-center gap-2 focus:outline-none rounded-full sm:rounded-xl hover:bg-slate-50 p-1 pr-2 transition-colors">
                <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-200 shrink-0">
                    <img src="{{ auth()->user()->photo ? Storage::url(auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'User') . '&background=4f46e5&color=fff' }}" alt="Profile" class="w-full h-full object-cover">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-semibold text-slate-800 leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">{{ auth()->user()->role ?? 'Super Admin' }}</p>
                </div>
                <span class="material-symbols-outlined text-[18px] text-slate-400 hidden sm:block transition-transform duration-200" :class="profileOpen ? 'rotate-180' : ''">expand_more</span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="profileOpen" x-transition.opacity.duration.200ms style="display: none;" class="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-[#E5E7EB] overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-[#E5E7EB] sm:hidden">
                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->role ?? 'Super Admin' }}</p>
                </div>
                <div class="p-2 space-y-1">
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-600 font-medium hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Profil Saya
                    </a>
                    <a href="{{ route('system.settings') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-600 font-medium hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                        Pengaturan Akun
                    </a>
                </div>
                <div class="p-2 border-t border-[#E5E7EB]">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-600 font-medium hover:bg-red-50 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            Keluar / Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>