<!DOCTYPE html>
2: <html lang="id">
3: <head>
4:     <meta charset="utf-8"/>
5:     <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
6:     <title>@yield('title', 'INVENTRA — Sistem Manajemen Inventaris')</title>
7: 
8:     <!-- Google Fonts & Material Symbols -->
9:     <link rel="preconnect" href="https://fonts.googleapis.com"/>
10:     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
11:     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet"/>
12:     <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
13:     
14:     <!-- Tailwind CSS CDN -->
15:     <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
16:     
17:     <!-- Custom Tailwind Config & Styles -->
18:     @yield('styles')
19:     
20: </head>
21: <body class="@yield('body_class', 'antialiased')">
22: 
23:     @yield('content')
24:     
25:     <!-- Interactive Scripts -->
26:     @yield('scripts')
27: 
28: </body>
29: </html>