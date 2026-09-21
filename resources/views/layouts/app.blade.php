<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>INVENTRA - @yield('header_title', 'Sistem Manajemen Logistik')</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet" />

    <script>
        if (document.fonts) {
            document.documentElement.classList.add('fonts-loading');
            document.fonts.ready.then(function() {
                document.documentElement.classList.remove('fonts-loading');
            });
            setTimeout(function() {
                document.documentElement.classList.remove('fonts-loading');
            }, 1200);
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-headline { font-family: 'Outfit', sans-serif; }
        
        /* Smooth scrolling */
        html { scroll-behavior: smooth; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        [x-cloak] { display: none !important; }

        /* Prevent ligature text flash before Material Symbols font loads */
        .fonts-loading .material-symbols-outlined {
            visibility: hidden !important;
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'liga';
            user-select: none;
        }
    </style>

    <!-- NProgress CSS for HTMX Loading Bar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <style>
        #nprogress .bar { background: #4F46E5 !important; height: 3px !important; }
        #nprogress .peg { box-shadow: 0 0 10px #4F46E5, 0 0 5px #4F46E5 !important; }
        #nprogress .spinner-icon { border-top-color: #4F46E5 !important; border-left-color: #4F46E5 !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="if (window.innerWidth >= 1024) { sidebarOpen = true; } else { sidebarOpen = false; }" hx-boost="true" hx-indicator="#global-indicator">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Mobile Sidebar Overlay (Backdrop) -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/50 z-20 lg:hidden" style="display: none;"></div>

        <!-- Sidebar Component -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300 relative" id="main-content-area">
            
            <!-- Background Decoration (Glassmorphism effect underlying) -->
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-br from-indigo-500/10 via-purple-500/5 to-transparent -z-10 pointer-events-none"></div>

            <!-- Topbar Component -->
            @include('layouts.topbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-transparent p-4 sm:p-6 lg:p-8">
                
                <!-- Yield Main Content -->
                @yield('main_content')
            </main>
        </div>
    </div>
    
    <!-- Floating Toast Notifications -->
    <div x-data="toastManager()" class="fixed top-4 right-4 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none" id="toast-container">
        
        <!-- Dynamic Toasts from Echo -->
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-x-8 scale-95"
                 class="pointer-events-auto bg-white border px-4 py-3 rounded-xl flex items-start gap-3 shadow-xl"
                 :class="toast.type === 'low_stock' || toast.type === 'error' ? 'border-red-200 shadow-red-500/10' : 'border-indigo-200 shadow-indigo-500/10'">
                
                <div class="mt-0.5">
                    <span class="material-symbols-outlined text-[20px]" 
                          :class="toast.type === 'low_stock' || toast.type === 'error' ? 'text-red-500' : 'text-indigo-500'"
                          x-text="toast.type === 'low_stock' || toast.type === 'error' ? 'warning' : 'notifications'"></span>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-slate-800" x-text="toast.title"></h4>
                    <p class="text-sm text-slate-600 mt-0.5" x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </template>

        <!-- Session Toasts -->
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             x-init="setTimeout(() => show = false, 4000)"
             class="pointer-events-auto bg-white border border-emerald-200 px-4 py-3 rounded-xl flex items-start gap-3 shadow-xl shadow-emerald-500/10">
            <div class="mt-0.5"><span class="material-symbols-outlined text-emerald-500 text-[20px]">check_circle</span></div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-slate-800">Berhasil!</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             x-init="setTimeout(() => show = false, 5000)"
             class="pointer-events-auto bg-white border border-red-200 px-4 py-3 rounded-xl flex items-start gap-3 shadow-xl shadow-red-500/10">
            <div class="mt-0.5"><span class="material-symbols-outlined text-red-500 text-[20px]">error</span></div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-slate-800">Ups, Terjadi Kesalahan!</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border border-red-200 px-4 py-3 rounded-xl flex items-start gap-3 shadow-xl shadow-red-500/10">
            <div class="mt-0.5"><span class="material-symbols-outlined text-red-500 text-[20px]">error</span></div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-slate-800">Validasi Gagal!</h4>
                <div class="text-sm text-slate-600 mt-0.5">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif
    </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <!-- HTMX and NProgress -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <script>
        document.addEventListener('htmx:configRequest', () => { NProgress.start(); });
        document.addEventListener('htmx:afterOnLoad', () => { NProgress.done(); });
        document.addEventListener('htmx:responseError', () => { NProgress.done(); });
        document.addEventListener('htmx:sendError', () => { NProgress.done(); });

        document.addEventListener('alpine:init', () => {
            Alpine.data('toastManager', () => ({
                toasts: [],
                addToast(notification) {
                    const id = Date.now();
                    this.toasts.push({
                        id: id,
                        type: notification.type || 'info',
                        title: notification.item_name || 'Notifikasi Baru',
                        message: notification.message || 'Anda mendapatkan pemberitahuan.',
                        show: true
                    });

                    setTimeout(() => {
                        this.removeToast(id);
                    }, 5000);
                },
                removeToast(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) {
                        toast.show = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }
                },
                init() {
                    @auth
                    if (window.Echo) {
                        window.Echo.private('App.Models.User.{{ auth()->id() }}')
                            .notification((notification) => {
                                this.addToast(notification);
                                window.dispatchEvent(new CustomEvent('new-notification', { detail: notification }));
                            });
                    }
                    @endauth
                }
            }));
        });
    </script>
    
    @stack('scripts')
</body>
</html>
