<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'INVENTRA — Sistem Manajemen Inventaris')</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Custom Tailwind Config & Styles -->
    @yield('styles')

    <script>
        if (!document.getElementById('tailwind-config')) {
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            body: ['Inter', 'sans-serif'],
                            headline: ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        colors: {
                            "primary-container": "#2563eb",
                            "primary": "#1d4ed8",
                            "on-primary": "#ffffff",
                            "surface": "#f9f9ff",
                            "surface-container-lowest": "#ffffff",
                            "surface-container-low": "#f0f8ff",
                            "surface-container": "#e0f2fe",
                            "surface-container-high": "#bae6fd",
                            "surface-container-highest": "#7dd3fc",
                            "on-surface": "#111827",
                            "secondary": "#6b7280",
                            "outline": "#e5e7eb",
                        },
                        boxShadow: {
                            'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                            'glow': '0 0 15px rgba(37, 99, 235, 0.3)',
                            'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .material-symbols-fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin 1s linear infinite;
        }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="@yield('body_class', 'bg-gray-50 text-gray-900 font-body antialiased')">

    @yield('content')
    
    <!-- Interactive Scripts -->
    @stack('scripts')

</body>
</html>
