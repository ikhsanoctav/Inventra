<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64 lg:translate-x-0 lg:w-20'" class="fixed lg:relative inset-y-0 left-0 bg-white border-r border-[#E5E7EB] h-screen flex flex-col transition-all duration-300 shadow-sm z-30 shrink-0">
    
    <!-- Logo Area -->
    <div class="h-16 flex items-center justify-center border-b border-[#E5E7EB] shrink-0 transition-all overflow-hidden bg-white">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full h-full px-4 relative">
            <img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" alt="Inventra Logo" class="object-contain h-12 w-auto transition-all duration-300 mix-blend-multiply">
        </a>
    </div>

    <!-- Navigation Area -->
    <div class="flex-1 overflow-y-auto py-4 scrollbar-hide">
        <nav class="space-y-1 px-3">
            @php
                $role = auth()->user()->role;
                $menus = config("sidebar.{$role}", []);
            @endphp

            @foreach($menus as $index => $menu)
                @if(isset($menu['submenus']))
                    <!-- Section with Submenus -->
                    @php
                        $isActive = false;
                        foreach($menu['submenus'] as $sub) {
                            // Check if current route matches or is a child of the submenu route
                            if(request()->routeIs($sub['route']) || request()->routeIs($sub['route'] . '.*')) {
                                $isActive = true;
                                break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="{{ $index > 0 ? 'pt-2' : '' }}">
                        <div x-show="sidebarOpen" class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $menu['title'] }}</div>
                        
                        <button @click="if(!sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }" class="w-full group flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors {{ $isActive ? 'text-indigo-700 font-semibold bg-indigo-50/30' : 'text-slate-600' }} relative">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[22px] {{ $isActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }} transition-colors shrink-0">{{ $menu['icon'] }}</span>
                                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap lg:block">{{ $menu['title'] }}</span>
                            </div>
                            <span x-show="sidebarOpen" class="material-symbols-outlined text-[18px] {{ $isActive ? 'text-indigo-600' : 'text-slate-400' }} transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                            
                            <!-- Removed tooltip as per user request -->
                        </button>

                        <div x-show="open && sidebarOpen" x-collapse class="pl-11 pr-3 space-y-1 mt-1">
                            @foreach($menu['submenus'] as $submenu)
                                @php
                                    $isSubActive = request()->routeIs($submenu['route']) || request()->routeIs($submenu['route'] . '.*');
                                @endphp
                                <a href="{{ route($submenu['route']) }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-slate-50 transition-colors {{ $isSubActive ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-700' }}" {!! $submenu['route'] === 'pos.index' ? 'hx-boost="false"' : '' !!}>{{ $submenu['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Single Link -->
                    @php
                        $isActive = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*');
                    @endphp
                    <div class="{{ $index > 0 ? 'pt-2' : '' }}">
                        <a href="{{ route($menu['route']) }}" @click="if(!sidebarOpen && window.innerWidth >= 1024) { sidebarOpen = true; $event.preventDefault(); } else if(window.innerWidth < 1024) { sidebarOpen = false; }" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600' }} relative" {!! $menu['route'] === 'pos.index' ? 'hx-boost="false"' : '' !!}>
                            <span class="material-symbols-outlined text-[22px] {{ $isActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }} transition-colors shrink-0">{{ $menu['icon'] }}</span>
                            <span x-show="sidebarOpen" class="text-sm whitespace-nowrap font-medium lg:block">{{ $menu['title'] }}</span>
                            
                            <!-- Removed tooltip as per user request -->
                        </a>
                    </div>
                @endif
            @endforeach

        </nav>
    </div>

    <!-- User Profile Footer -->
    <div class="border-t border-[#E5E7EB] p-4 shrink-0 bg-slate-50/50">
        <div class="flex items-center gap-3" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <img src="{{ auth()->user()->photo ? Storage::url(auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'User') . '&background=4f46e5&color=fff' }}" alt="Profile" class="w-9 h-9 object-cover rounded-full ring-2 ring-white shadow-sm shrink-0">
            <div x-show="sidebarOpen" class="flex-1 min-w-0 overflow-hidden">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Guest User' }}</p>
                <p class="text-[11px] text-slate-500 truncate capitalize">{{ str_replace('_', ' ', auth()->user()->role ?? 'Unknown Role') }}</p>
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
