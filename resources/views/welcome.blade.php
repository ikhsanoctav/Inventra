<!DOCTYPE html>

<html lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>INVENTRA — Sistem Manajemen Inventaris Terintegrasi</title>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Material Symbols Outlined -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Fonts: Plus Jakarta Sans & Inter -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "error-container": "#ffdad6",
            "surface-container-highest": "#e2e8f0",
            "on-secondary-container": "#334155",
            "on-error": "#ffffff",
            "inverse-on-surface": "#f1f5f9",
            "outline-variant": "#cbd5e1",
            "on-primary-container": "#eff6ff",
            "on-error-container": "#93000a",
            "inverse-surface": "#1e293b",
            "on-secondary-fixed-variant": "#475569",
            "primary-container": "#2563eb",
            "surface-tint": "#1d4ed8",
            "inverse-primary": "#93c5fd",
            "secondary-fixed-dim": "#cbd5e1",
            "outline": "#64748b",
            "on-tertiary-fixed-variant": "#0369a1",
            "tertiary": "#0284c7",
            "secondary-container": "#e2e8f0",
            "on-background": "#0f172a",
            "surface-container-high": "#f1f5f9",
            "on-primary-fixed-variant": "#1e40af",
            "on-surface": "#0f172a",
            "surface-variant": "#e2e8f0",
            "secondary": "#64748b",
            "surface-container-low": "#f8fafc",
            "background": "#f9f9ff",
            "on-primary-fixed": "#1e3a8a",
            "secondary-fixed": "#f1f5f9",
            "on-tertiary-container": "#f0f9ff",
            "on-tertiary-fixed": "#0c4a6e",
            "tertiary-container": "#bae6fd",
            "tertiary-fixed": "#e0f2fe",
            "on-secondary": "#ffffff",
            "on-secondary-fixed": "#0f172a",
            "on-tertiary": "#ffffff",
            "surface-container-lowest": "#ffffff",
            "surface": "#ffffff",
            "on-surface-variant": "#475569",
            "error": "#ba1a1a",
            "surface-container": "#f8fafc",
            "on-primary": "#ffffff",
            "primary-fixed-dim": "#93c5fd",
            "primary": "#1d4ed8",
            "primary-fixed": "#dbeafe",
            "surface-dim": "#cbd5e1",
            "tertiary-fixed-dim": "#7dd3fc",
            "surface-bright": "#ffffff"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "spacing": {
            "space-sm": "0.5rem",
            "space-lg": "1.25rem",
            "sidebar-width": "16rem",
            "sidebar-collapsed": "4.5rem",
            "space-2xl": "2rem",
            "space-xs": "0.25rem",
            "space-3xl": "2.5rem",
            "space-xl": "1.5rem",
            "gutter-mobile": "1rem",
            "gutter-desktop": "1.5rem",
            "space-base": "1rem",
            "space-2xs": "0.125rem",
            "space-md": "0.75rem"
          },
          "fontFamily": {
            "headline-lg": ["Plus Jakarta Sans"],
            "display": ["Plus Jakarta Sans"],
            "body-md": ["Inter"],
            "label-sm": ["Inter"],
            "body-lg": ["Inter"],
            "label-md": ["Inter"],
            "headline-md": ["Plus Jakarta Sans"],
            "headline-sm": ["Plus Jakarta Sans"],
            "tabular-number": ["Inter"],
            "body-sm": ["Inter"],
            "display-mobile": ["Plus Jakarta Sans"]
          },
          "fontSize": {
            "headline-lg": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.02em", "fontWeight": "600" }],
            "display": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.025em", "fontWeight": "700" }],
            "body-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0em", "fontWeight": "400" }],
            "label-sm": ["11px", { "lineHeight": "14px", "letterSpacing": "0.03em", "fontWeight": "600" }],
            "body-lg": ["15px", { "lineHeight": "24px", "letterSpacing": "-0.005em", "fontWeight": "400" }],
            "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "500" }],
            "headline-md": ["20px", { "lineHeight": "28px", "letterSpacing": "-0.015em", "fontWeight": "600" }],
            "headline-sm": ["16px", { "lineHeight": "24px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
            "tabular-number": ["14px", { "lineHeight": "20px", "letterSpacing": "-0.01em", "fontWeight": "500" }],
            "body-sm": ["13px", { "lineHeight": "18px", "letterSpacing": "0em", "fontWeight": "400" }],
            "display-mobile": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.02em", "fontWeight": "700" }]
          }
        }
      }
    }
  </script>
<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
    .custom-shadow-soft {
      box-shadow: 0 10px 40px -10px rgba(37, 99, 235, 0.1);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-shadow-soft:hover {
      box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.2);
      transform: translateY(-4px);
    }
    .custom-shadow-elevated {
      box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.25);
    }
    
    .gradient-text {
      background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .float-anim {
      animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
      100% { transform: translateY(0px); }
    }
    
    .glass-header {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }
  </style>
</head>
<body class="bg-background text-on-surface antialiased selection:bg-primary-fixed selection:text-on-primary-fixed">
<!-- 1. HEADER (Top Navigation Bar) -->
<header x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" :class="scrolled ? 'glass-header shadow-sm border-surface-container-highest/50' : 'bg-surface-container-lowest border-transparent'" class="fixed top-0 z-50 w-full transition-all duration-300">
<div class="max-w-[1440px] mx-auto px-gutter-desktop h-16 flex items-center justify-between">
<!-- Brand Logo -->
<a class="flex items-center gap-3 group" href="javascript:void(0)">
<img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" alt="Inventra Logo" class="h-10 w-auto">
</a>
<!-- Desktop Nav Links -->
<nav class="hidden md:flex items-center gap-8">
<a class="text-body-md font-body-md text-primary-container font-medium transition-colors" href="#beranda">Home</a>
<a class="text-body-md font-body-md text-secondary hover:text-on-surface transition-colors" href="#tentang">Tentang Sistem</a>
<a class="text-body-md font-body-md text-secondary hover:text-on-surface transition-colors" href="#fitur">Fitur</a>
<a class="text-body-md font-body-md text-secondary hover:text-on-surface transition-colors" href="#alur">Panduan</a>
<a class="text-body-md font-body-md text-secondary hover:text-on-surface transition-colors" href="#kontak">Kontak</a>
</nav>
<!-- Trailing Action -->
<div class="flex items-center gap-3">
<button class="hidden sm:flex items-center gap-1.5 px-3 py-2 text-body-sm font-body-sm text-secondary hover:text-on-surface transition-colors">
<span class="material-symbols-outlined text-[18px]">help_outline</span>
<span>Bantuan</span>
</button>
<a class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary-container hover:bg-primary active:scale-95 text-on-primary font-body-md font-medium text-white shadow-sm transition-all duration-150" href="{{ route('login') }}">
<span>Masuk ke Sistem</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
</div>
</header>
<main>
<!-- 2. HERO SECTION -->
<section class="relative pt-12 pb-20 overflow-hidden border-b border-surface-container-highest" id="beranda">
<div class="max-w-[1440px] mx-auto px-gutter-desktop">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
<!-- Hero Left Column -->
<div class="lg:col-span-6 flex flex-col items-start" data-aos="fade-right">
<!-- Version Badge -->
<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-high border border-surface-container-highest mb-6">
<span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
<span class="text-label-sm font-label-sm text-on-surface">Sistem Inventaris Nasional v2.4</span>
<span class="text-secondary text-[11px]">• Terintegrasi BPKP &amp; Kemenkeu</span>
</div>
<!-- Headline -->
<h1 class="text-display font-display text-on-surface tracking-tight mb-6 leading-tight">
              Kelola Inventaris Lebih Mudah, Cepat, dan <span class="gradient-text">Terintegrasi</span>
</h1>
<!-- Supporting Text -->
<p class="text-body-lg font-body-lg text-secondary mb-8 leading-relaxed max-w-xl">
              INVENTRA membantu organisasi mengelola data barang, stok, transaksi, supplier, dan laporan secara terstruktur dalam satu sistem terpadu berstandar audit institusi.
            </p>
<!-- Actions -->
<div class="flex flex-wrap items-center gap-4 w-full sm:w-auto mb-10">
<a class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary-container hover:bg-primary text-on-primary font-body-md font-semibold shadow-sm transition-all duration-150 text-white" href="{{ route('login') }}">
<span class="material-symbols-outlined text-[20px]">login</span>
<span>Masuk ke Sistem</span>
</a>
<a class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-surface-container-lowest border border-surface-container-highest hover:bg-surface-container-low text-on-surface font-body-md font-medium transition-all shadow-sm" href="#fitur">
<span class="material-symbols-outlined text-[20px] text-secondary">play_circle</span>
<span>Pelajari Sistem</span>
</a>
</div>
<!-- Trust Badges -->
<div class="pt-6 border-t border-surface-container-highest w-full flex flex-wrap items-center gap-6 text-secondary">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-primary-container">verified_user</span>
<span class="text-label-sm font-label-sm uppercase tracking-wider">ISO 27001 Certified</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-primary-container">enhanced_encryption</span>
<span class="text-label-sm font-label-sm uppercase tracking-wider">Keamanan Data Terenkripsi</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-primary-container">check_circle</span>
<span class="text-label-sm font-label-sm uppercase tracking-wider">SLA 99.9% Uptime</span>
</div>
</div>
</div>
<!-- Hero Right Column: Dashboard Mockup Preview Card -->
<div class="lg:col-span-6 relative float-anim" data-aos="fade-left" data-aos-delay="200">
<!-- Ambient Glow -->
<div class="absolute -top-10 -right-10 w-72 h-72 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>
<div class="relative bg-surface-container-lowest rounded-xl border border-surface-container-highest custom-shadow-elevated overflow-hidden">
<!-- Window Control Bar -->
<div class="px-4 py-3 bg-surface-container-low border-b border-surface-container-highest flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-[#ef4444]/70 inline-block"></span>
<span class="w-3 h-3 rounded-full bg-[#f59e0b]/70 inline-block"></span>
<span class="w-3 h-3 rounded-full bg-[#10b981]/70 inline-block"></span>
<span class="ml-2 text-label-sm font-label-sm text-secondary">inventra.go.id/dashboard/preview</span>
</div>
<div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-surface-container-lowest border border-surface-container-highest text-label-sm font-label-sm text-secondary">
<span class="material-symbols-outlined text-[14px]">lock</span>
<span>SSL Aktif</span>
</div>
</div>
<div class="p-5 space-y-4">
<!-- Mockup Metrics Row -->
<div class="grid grid-cols-2 gap-3">
<div class="p-3.5 rounded-lg bg-surface border border-surface-container-highest">
<div class="flex items-center justify-between text-secondary mb-1">
<span class="text-label-sm font-label-sm">Total Aset Aktif</span>
<span class="material-symbols-outlined text-[18px] text-primary-container">account_balance_wallet</span>
</div>
<div class="text-headline-sm font-headline-sm font-bold text-on-surface">Rp 4.82 Miliar</div>
<div class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">trending_up</span>
<span>+8.4% bulan ini</span>
</div>
</div>
<div class="p-3.5 rounded-lg bg-surface border border-surface-container-highest">
<div class="flex items-center justify-between text-secondary mb-1">
<span class="text-label-sm font-label-sm">Pergerakan Hari Ini</span>
<span class="material-symbols-outlined text-[18px] text-tertiary">swap_horiz</span>
</div>
<div class="text-headline-sm font-headline-sm font-bold text-on-surface">348 Mutasi</div>
<div class="text-[11px] text-secondary font-medium mt-1">210 Masuk • 138 Keluar</div>
</div>
</div>
<!-- Alert Strip -->
<div class="p-3 rounded-lg bg-[#fffbeb] border border-[#fde68a] flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="material-symbols-outlined text-[18px] text-[#92400e]">warning</span>
<span class="text-body-sm font-body-sm text-[#92400e] font-medium">3 Item mendekati batas stok minimum (Reorder Point)</span>
</div>
<span class="text-label-sm font-label-sm text-[#92400e] underline cursor-pointer">Lihat</span>
</div>
<!-- Mini Data Table -->
<div class="border border-surface-container-highest rounded-lg overflow-hidden bg-surface-container-lowest">
<div class="px-3 py-2 bg-surface-container-low border-b border-surface-container-highest flex items-center justify-between">
<span class="text-label-sm font-label-sm text-secondary uppercase">Daftar Inventaris Terkini</span>
<span class="text-[11px] font-medium text-primary-container cursor-pointer">Pusat Data Logistik</span>
</div>
<div class="overflow-x-auto w-full pb-4">
<table class="w-full text-left text-body-sm font-body-sm border-collapse">
<thead>
<tr class="border-b border-surface-container-highest text-[11px] font-label-sm text-secondary uppercase bg-surface">
<th class="py-2 px-3">Kode SKU</th>
<th class="py-2 px-3">Nama Barang</th>
<th class="py-2 px-3 text-right">Stok</th>
<th class="py-2 px-3">Status</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-highest text-on-surface">
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-2.5 px-3 font-mono text-[12px] text-secondary">INV-SRV-092</td>
<td class="py-2.5 px-3 font-medium">Server Rack Unit 42U</td>
<td class="py-2.5 px-3 text-right font-tabular-number">14 Unit</td>
<td class="py-2.5 px-3">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#f0fdf4] text-[#166534] border border-[#bbf7d0]">
                            Stok Aman
                          </span>
</td>
</tr>
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-2.5 px-3 font-mono text-[12px] text-secondary">INV-LPT-401</td>
<td class="py-2.5 px-3 font-medium">Laptop Operasional Core i7</td>
<td class="py-2.5 px-3 text-right font-tabular-number">3 Unit</td>
<td class="py-2.5 px-3">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#fffbeb] text-[#92400e] border border-[#fde68a]">
                            Perlu Restock
                          </span>
</td>
</tr>
<tr class="hover:bg-surface-container-low/60 transition-colors">
<td class="py-2.5 px-3 font-mono text-[12px] text-secondary">INV-NET-118</td>
<td class="py-2.5 px-3 font-medium">Switch 24-Port Gigabit L3</td>
<td class="py-2.5 px-3 text-right font-tabular-number">48 Unit</td>
<td class="py-2.5 px-3">
<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-[#f0fdf4] text-[#166534] border border-[#bbf7d0]">
                            Stok Aman
                          </span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- Bottom Status Bar -->
<div class="flex items-center justify-between text-[11px] text-secondary pt-1">
<span class="flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Sinkronisasi Database Aktif: 2 menit yang lalu
                  </span>
<span>Gudang Utama Jakarta</span>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 3. SYSTEM STATISTICS SECTION -->
<section class="py-16 bg-surface-container-low/60 border-b border-surface-container-highest">
<div class="max-w-[1440px] mx-auto px-gutter-desktop">
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
<!-- Stat Card 1 -->
<div data-aos="fade-up" data-aos-delay="100" class="bg-surface-container-lowest p-6 rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="flex items-center justify-between mb-4">
<span class="text-label-md font-label-md text-secondary">Total Master Barang</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-[20px]">category</span>
</div>
</div>
<div class="text-display font-display font-bold text-on-surface tracking-tight mb-1">142.850+</div>
<p class="text-body-sm font-body-sm text-secondary">Terklasifikasi standar kodefikasi BMN</p>
</div>
<!-- Stat Card 2 -->
<div data-aos="fade-up" data-aos-delay="200" class="bg-surface-container-lowest p-6 rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="flex items-center justify-between mb-4">
<span class="text-label-md font-label-md text-secondary">Stok Terkelola</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-[20px]">inventory</span>
</div>
</div>
<div class="text-display font-display font-bold text-on-surface tracking-tight mb-1">1.2M+</div>
<p class="text-body-sm font-body-sm text-secondary">Unit item di 24 gudang logistik</p>
</div>
<!-- Stat Card 3 -->
<div data-aos="fade-up" data-aos-delay="300" class="bg-surface-container-lowest p-6 rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="flex items-center justify-between mb-4">
<span class="text-label-md font-label-md text-secondary">Rekanan Supplier</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-[20px]">local_shipping</span>
</div>
</div>
<div class="text-display font-display font-bold text-on-surface tracking-tight mb-1">380+</div>
<p class="text-body-sm font-body-sm text-secondary">Vendor terverifikasi e-Procurement</p>
</div>
<!-- Stat Card 4 -->
<div data-aos="fade-up" data-aos-delay="400" class="bg-surface-container-lowest p-6 rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="flex items-center justify-between mb-4">
<span class="text-label-md font-label-md text-secondary">Akurasi Transaksi</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-[20px]">fact_check</span>
</div>
</div>
<div class="text-display font-display font-bold text-on-surface tracking-tight mb-1">99.8%</div>
<p class="text-body-sm font-body-sm text-secondary">Kesesuaian fisik dengan sistem audit</p>
</div>
</div>
</div>
</section>
<!-- 4. FEATURE SECTION (8 Structured Cards) -->
<section class="py-20 border-b border-surface-container-highest" id="fitur">
<div class="max-w-[1440px] mx-auto px-gutter-desktop">
<!-- Section Header -->
<div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-label-sm font-label-sm text-secondary uppercase tracking-wider mb-3">
            Kapabilitas Lengkap
          </div>
<h2 class="text-display font-display text-on-surface tracking-tight mb-4">
            Semua Kebutuhan Inventaris dalam Satu Sistem
          </h2>
<p class="text-body-lg font-body-lg text-secondary">
            Dirancang untuk efisiensi operasional tanpa kerumitan alur kerja, memenuhi standarisasi tata kelola barang modern.
          </p>
</div>
<!-- 8 Feature Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
<!-- Card 1 -->
<div data-aos="zoom-in-up" data-aos-delay="50" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">inventory_2</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Manajemen Barang</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Pencatatan spesifikasi detail, serial number, kategori bertingkat, dan amortisasi nilai aset secara otomatis.
            </p>
</div>
<!-- Card 2 -->
<div data-aos="zoom-in-up" data-aos-delay="100" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">tune</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Manajemen Stok</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Penetapan buffer stock, notifikasi reorder point instan, serta pemantauan stok minimum di setiap lokasi gudang.
            </p>
</div>
<!-- Card 3 -->
<div data-aos="zoom-in-up" data-aos-delay="150" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">move_to_inbox</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Barang Masuk</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Verifikasi Purchase Order, validasi kualitas penerimaan barang, dan penerbitan Bukti Penerimaan Barang (BPB).
            </p>
</div>
<!-- Card 4 -->
<div data-aos="zoom-in-up" data-aos-delay="200" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">outbox</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Barang Keluar</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Alur otorisasi multi-level untuk disposisi barang, Surat Jalan resmi, dan rekonsiliasi berkala unit peminjam.
            </p>
</div>
<!-- Card 5 -->
<div data-aos="zoom-in-up" data-aos-delay="250" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">store</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Supplier &amp; Rekanan</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Basis data profil vendor lengkap, evaluasi performa ketepatan pengiriman, dan riwayat histori pengadaan.
            </p>
</div>
<!-- Card 6 -->
<div data-aos="zoom-in-up" data-aos-delay="300" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">assignment</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Laporan Komprehensif</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Ekspor rekapitulasi mutasi, neraca persediaan, berita acara opname fisik, dan audit trail format PDF/Excel.
            </p>
</div>
<!-- Card 7 -->
<div data-aos="zoom-in-up" data-aos-delay="350" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">qr_code_scanner</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Barcode &amp; QR Scanner</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Mendukung pemindaian instan via perangkat mobile atau optical laser reader untuk input data tanpa galat ketik.
            </p>
</div>
<!-- Card 8 -->
<div data-aos="zoom-in-up" data-aos-delay="400" class="p-6 rounded-xl bg-surface-container-lowest border border-surface-container-highest hover:border-primary-container/40 transition-all custom-shadow-soft group">
<div class="w-11 h-11 rounded-lg bg-surface-container-low border border-surface-container-highest flex items-center justify-center text-on-surface group-hover:text-primary-container transition-colors mb-5">
<span class="material-symbols-outlined text-[24px]">monitoring</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-2">Monitoring Stok Real-time</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Sinkronisasi data langsung antar cabang gudang dengan visibilitas saldo inventaris per detik tanpa jeda.
            </p>
</div>
</div>
</div>
</section>
<!-- 5. HOW IT WORKS (3-STEP PROCESS) -->
<section class="py-20 bg-surface-container-low/40 border-b border-surface-container-highest" id="alur">
<div class="max-w-[1440px] mx-auto px-gutter-desktop">
<!-- Section Header -->
<div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-label-sm font-label-sm text-secondary uppercase tracking-wider mb-3">
            Alur Kerja Sistem
          </div>
<h2 class="text-display font-display text-on-surface tracking-tight mb-4">
            Tiga Langkah Sederhana Menuju Efisiensi Maksimal
          </h2>
<p class="text-body-lg font-body-lg text-secondary">
            Proses operasional dirancang intuitif untuk meminimalkan kurva pelatihan personil gudang dan pengelola aset.
          </p>
</div>
<!-- 3-Step Horizontal Layout -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
<!-- Connector Line (Desktop) -->
<div class="hidden md:block absolute top-12 left-1/6 right-1/6 h-0.5 bg-surface-container-highest z-0"></div>
<!-- Step 1 -->
<div data-aos="fade-right" data-aos-delay="100" class="relative z-10 flex flex-col items-center text-center p-6 bg-surface-container-lowest rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="w-16 h-16 rounded-full bg-surface border-2 border-primary-container text-primary-container flex items-center justify-center text-headline-md font-headline-md font-bold mb-6 shadow-sm">
              01
            </div>
<h3 class="text-headline-md font-headline-md text-on-surface mb-3">Kelola Data</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Input master barang, tentukan klasifikasi kategori standar, lokasi rak gudang, dan tetapkan batas threshold stok minimum.
            </p>
<div class="mt-6 inline-flex items-center gap-1 text-label-sm font-label-sm text-primary-container font-semibold">
<span class="material-symbols-outlined text-[16px]">check</span>
<span>Import Excel / CSV Didukung</span>
</div>
</div>
<!-- Step 2 -->
<div data-aos="fade-up" data-aos-delay="200" class="relative z-10 flex flex-col items-center text-center p-6 bg-surface-container-lowest rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="w-16 h-16 rounded-full bg-surface border-2 border-primary-container text-primary-container flex items-center justify-center text-headline-md font-headline-md font-bold mb-6 shadow-sm">
              02
            </div>
<h3 class="text-headline-md font-headline-md text-on-surface mb-3">Catat Transaksi</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Pencatatan mutasi barang masuk &amp; keluar via barcode scanner atau sistem persetujuan bertingkat oleh supervisor penanggung jawab.
            </p>
<div class="mt-6 inline-flex items-center gap-1 text-label-sm font-label-sm text-primary-container font-semibold">
<span class="material-symbols-outlined text-[16px]">check</span>
<span>Persetujuan Digital 1-Klik</span>
</div>
</div>
<!-- Step 3 -->
<div data-aos="fade-left" data-aos-delay="300" class="relative z-10 flex flex-col items-center text-center p-6 bg-surface-container-lowest rounded-xl border border-surface-container-highest custom-shadow-soft">
<div class="w-16 h-16 rounded-full bg-surface border-2 border-primary-container text-primary-container flex items-center justify-center text-headline-md font-headline-md font-bold mb-6 shadow-sm">
              03
            </div>
<h3 class="text-headline-md font-headline-md text-on-surface mb-3">Pantau dan Analisis</h3>
<p class="text-body-sm font-body-sm text-secondary leading-relaxed">
              Audit otomatis, rekonsiliasi sisa stok riil, serta generate laporan berkala yang dapat diserahkan langsung ke auditor internal maupun eksternal.
            </p>
<div class="mt-6 inline-flex items-center gap-1 text-label-sm font-label-sm text-primary-container font-semibold">
<span class="material-symbols-outlined text-[16px]">check</span>
<span>Siap Standar Audit BPK</span>
</div>
</div>
</div>
</div>
</section>
<!-- 6. WHY INVENTRA SECTION -->
<section class="py-20 border-b border-surface-container-highest" id="tentang">
<div class="max-w-[1440px] mx-auto px-gutter-desktop">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
<div class="lg:col-span-5">
<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-label-sm font-label-sm text-secondary uppercase tracking-wider mb-3">
              Keunggulan Kompetitif
            </div>
<h2 class="text-display font-display text-on-surface tracking-tight mb-6">
              Mengapa Institusi Mempercayakan Inventaris pada INVENTRA?
            </h2>
<p class="text-body-lg font-body-lg text-secondary mb-8 leading-relaxed">
              Dibangun dengan arsitektur enterprise-grade yang mengutamakan integritas data keuangan negara dan akuntabilitas kepemilikan aset jangka panjang.
            </p>
<div class="p-4 rounded-xl bg-surface-container-low border border-surface-container-highest flex items-start gap-4">
<span class="material-symbols-outlined text-primary-container text-[28px] shrink-0">verified</span>
<div>
<h4 class="text-headline-sm font-headline-sm text-on-surface mb-1">Standarisasi Tata Kelola Nasional</h4>
<p class="text-body-sm font-body-sm text-secondary">
                  Format penomoran barang, kodefikasi lokasi, dan bagan akun standar (BAS) selaras dengan regulasi kementerian terkait.
                </p>
</div>
</div>
</div>
<div class="lg:col-span-7">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Advantage 1 -->
<div class="p-5 rounded-xl bg-surface-container-lowest border border-surface-container-highest custom-shadow-soft">
<div class="w-8 h-8 rounded bg-primary-container/10 text-primary-container flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">hub</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-1">Data Terpusat</h3>
<p class="text-body-sm font-body-sm text-secondary">
                  Menghilangkan data ganda dan silo informasi antar divisi gudang maupun unit kerja pelaksana.
                </p>
</div>
<!-- Advantage 2 -->
<div class="p-5 rounded-xl bg-surface-container-lowest border border-surface-container-highest custom-shadow-soft">
<div class="w-8 h-8 rounded bg-primary-container/10 text-primary-container flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">speed</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-1">Proses Lebih Cepat</h3>
<p class="text-body-sm font-body-sm text-secondary">
                  Memangkas waktu approval mutasi dan stok opname hingga 65% dibandingkan metode manual kertas.
                </p>
</div>
<!-- Advantage 3 -->
<div class="p-5 rounded-xl bg-surface-container-lowest border border-surface-container-highest custom-shadow-soft">
<div class="w-8 h-8 rounded bg-primary-container/10 text-primary-container flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">flaky</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-1">Nol Kesalahan Catat</h3>
<p class="text-body-sm font-body-sm text-secondary">
                  Validasi otomatis via sistem barcode meminimalisir human-error saat penerimaan dan pengeluaran.
                </p>
</div>
<!-- Advantage 4 -->
<div class="p-5 rounded-xl bg-surface-container-lowest border border-surface-container-highest custom-shadow-soft">
<div class="w-8 h-8 rounded bg-primary-container/10 text-primary-container flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">sync</span>
</div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-1">Monitoring Real-time</h3>
<p class="text-body-sm font-body-sm text-secondary">
                  Pimpinan dapat memeriksa ketersediaan aset secara langsung kapan saja melalui perangkat apa pun.
                </p>
</div>
<!-- Advantage 5 (Full width) -->
<div class="sm:col-span-2 p-5 rounded-xl bg-surface-container-lowest border border-surface-container-highest custom-shadow-soft">
<div class="flex items-start gap-4">
<div class="w-8 h-8 rounded bg-primary-container/10 text-primary-container flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">policy</span>
</div>
<div>
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-1">Laporan Siap Audit BPK / Pengawas Internal</h3>
<p class="text-body-sm font-body-sm text-secondary">
                      Riwayat perubahan (audit logs) tercatat permanen dan tidak dapat dimanipulasi, mempermudah pemeriksaan kepatuhan tahunan.
                    </p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 7. MINIMAL CTA BANNER -->
<section class="py-16">
<div class="max-w-[1440px] mx-auto px-gutter-desktop">
<div class="bg-surface-container-lowest rounded-2xl border border-surface-container-highest p-10 lg:p-14 relative overflow-hidden custom-shadow-elevated">
<!-- Subtle Accent Pattern Background -->
<div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>
<div class="max-w-3xl relative z-10">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-high text-label-sm font-label-sm text-on-surface mb-4">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              Sistem Siap Digunakan Secara Institusional
            </div>
<h2 class="text-display font-display text-on-surface tracking-tight mb-4">
              Mulai Kelola Inventaris dengan Lebih Baik
            </h2>
<p class="text-body-lg font-body-lg text-secondary mb-8 leading-relaxed max-w-2xl">
              Tingkatkan transparansi pengadaan barang, akurasi penghitungan fisik, dan kemudahan pelaporan aset negara bersama INVENTRA hari ini.
            </p>
<div class="flex flex-wrap items-center gap-4">
<a class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary-container hover:bg-primary active:scale-95 text-on-primary font-body-md font-semibold text-white shadow-sm transition-all duration-150" href="{{ route('login') }}">
<span class="material-symbols-outlined text-[20px]">rocket_launch</span>
<span>Masuk ke INVENTRA</span>
</a>
<a class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-surface border border-surface-container-highest hover:bg-surface-container-low text-on-surface font-body-md font-medium transition-colors" href="#kontak">
<span class="material-symbols-outlined text-[20px] text-secondary">support_agent</span>
<span>Hubungi Layanan Teknis</span>
</a>
</div>
</div>
</div>
</div>
</section>
</main>
<!-- 8. FOOTER (Based on Landing Footer Blueprint) -->
<footer class="w-full py-space-2xl px-gutter-desktop border-t border-surface-container-highest flex flex-col bg-surface-container-low" id="kontak">
<div class="max-w-[1440px] mx-auto w-full">
<!-- Top Row: Brand, Summary, and Technical Contact -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-surface-container-highest">
<!-- Identity -->
<div class="md:col-span-4 flex flex-col">
<div class="flex items-center gap-3 mb-3">
<div class="w-8 h-8 rounded-lg bg-primary-container flex items-center justify-center text-on-primary shadow-sm">
<span class="material-symbols-outlined text-[18px]">inventory_2</span>
</div>
<span class="text-headline-md font-headline-md font-bold text-on-surface tracking-tight">INVENTRA</span>
</div>
<p class="text-body-sm font-body-sm text-secondary max-w-sm mb-4">
            Sistem Inventaris Nasional terpadu untuk efisiensi pengelolaan data barang, stok, supplier, dan pelaporan audit instansi pemerintah serta enterprise swasta.
          </p>
<div class="flex items-center gap-2 text-label-sm font-label-sm text-secondary">
<span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
<span>Standar Keamanan Siber ISO/IEC 27001</span>
</div>
</div>
<!-- Links Column 1: Fitur & Ekosistem -->
<div class="md:col-span-2">
<h4 class="text-label-md font-label-md text-on-surface uppercase tracking-wider mb-3">Ekosistem</h4>
<ul class="space-y-2 text-body-sm font-body-sm text-secondary">
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Master Barang</a></li>
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Stok Opname</a></li>
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Barcode Generator</a></li>
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Integrasi e-Katalog</a></li>
</ul>
</div>
<!-- Links Column 2: Panduan & Regulasi -->
<div class="md:col-span-3">
<h4 class="text-label-md font-label-md text-on-surface uppercase tracking-wider mb-3">Bantuan &amp; Regulasi</h4>
<ul class="space-y-2 text-body-sm font-body-sm text-secondary">
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Buku Petunjuk Operasional (PDF)</a></li>
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Standar Kodefikasi Aset BMN</a></li>
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Ketentuan Layanan &amp; SLA</a></li>
<li><a class="hover:text-primary transition-colors" href="javascript:void(0)">Kebijakan Privasi Data</a></li>
</ul>
</div>
<!-- Contact Support -->
<div class="md:col-span-3">
<h4 class="text-label-md font-label-md text-on-surface uppercase tracking-wider mb-3">Dukungan Teknis</h4>
<div class="space-y-2.5 text-body-sm font-body-sm text-secondary">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary-container">mail</span>
<span>helpdesk@inventra.go.id</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary-container">call</span>
<span>(021) 5082-9900 (Hunting)</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary-container">schedule</span>
<span>Senin — Jumat: 08.00 - 17.00 WIB</span>
</div>
</div>
</div>
</div>
<!-- Bottom Row: Copyright & Legal Mandatory Strings -->
<div class="pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
<p class="text-body-sm font-body-sm text-secondary">
          © 2024 INVENTRA Enterprise. Hak Cipta Dilindungi Undang-Undang Republik Indonesia.
        </p>
<div class="flex flex-wrap items-center gap-6">
<a class="text-body-sm font-body-sm text-secondary hover:text-primary transition-colors" href="javascript:void(0)">Kebijakan Privasi</a>
<a class="text-body-sm font-body-sm text-secondary hover:text-primary transition-colors" href="javascript:void(0)">Ketentuan Layanan</a>
<a class="text-body-sm font-body-sm text-secondary hover:text-primary transition-colors" href="javascript:void(0)">Keamanan Data ISO 27001</a>
<a class="text-body-sm font-body-sm text-secondary hover:text-primary transition-colors" href="javascript:void(0)">Hubungi Dukungan Teknis</a>
</div>
</div>
</div>
</footer>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 800, offset: 100 });
</script>
</body></html>