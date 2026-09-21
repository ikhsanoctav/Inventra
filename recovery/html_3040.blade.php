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
16:     <script>
17:         tailwind.config = {
18:             theme: {
19:                 extend: {
20:                     fontFamily: {
21:                         sans: ['Inter', 'sans-serif'],
22:                         body: ['Plus Jakarta Sans', 'sans-serif'],
23:                         headline: ['Outfit', 'sans-serif'],
24:                     },
25:                     colors: {
26:                         primary: {
27:                             50: '#eef2ff',
28:                             100: '#e0e7ff',
29:                             500: '#6366f1',
30:                             600: '#4f46e5',
31:                             700: '#4338ca',
32:                             900: '#312e81',
33:                         }
34:                     },
35:                     boxShadow: {
36:                         'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
37:                         'glow': '0 0 15px rgba(79, 70, 229, 0.3)',
38:                         'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
39:                     }
40:                 }
41:             }
42:         }
43:     </script>
44:     <style>
45:         .glass-panel {
46:             background: rgba(255, 255, 255, 0.85);
47:             backdrop-filter: blur(12px);
48:             -webkit-backdrop-filter: blur(12px);
49:             border: 1px solid rgba(255, 255, 255, 0.5);
50:         }
51:         .hover-scale {
52:             transition: transform 0.2s ease, box-shadow 0.2s ease;
53:         }
54:         .hover-scale:hover {
55:             transform: translateY(-2px);
56:             box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
57:         }
58:         .bg-gradient-primary {
59:             background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
60:         }
61:     </style>
62: </head>
63: <body class="@yield('body_class', 'bg-gray-50 text-gray-900 font-body antialiased')">
64: 
65:     @yield('content')
66:     
67:     <!-- Interactive Scripts -->
68:     @yield('scripts')
69: 
70: </body>
71: </html>