<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>INVENTRA — Sistem Manajemen Inventaris & Logistik Terintegrasi</title>
  
  <!-- Material Symbols Outlined -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
  <!-- Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800;900&amp;display=swap" rel="stylesheet"/>
  
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .font-headline {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

  <!-- 1. HEADER (Clean Sleek Navigation Bar) -->
  <header x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" 
          :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-sm border-slate-200' : 'bg-white border-slate-200'" 
          class="fixed top-0 z-50 w-full transition-all duration-200 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <!-- Brand Logo (Visually Enlarged without expanding topbar) -->
      <a class="flex items-center gap-2 group relative overflow-visible" href="#">
        <img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" 
             alt="Inventra Logo" 
             class="h-10 sm:h-11 w-auto object-contain scale-140 sm:scale-150 origin-left transition-transform group-hover:scale-[1.55]">
      </a>

      <!-- Desktop Nav Links -->
      <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
        <a class="text-blue-600 hover:text-blue-700 transition-colors" href="#beranda">Home</a>
        <a class="hover:text-blue-600 transition-colors" href="#tentang">Tentang Sistem</a>
        <a class="hover:text-blue-600 transition-colors" href="#fitur">Fitur</a>
        <a class="hover:text-blue-600 transition-colors" href="#alur">Panduan Alur</a>
        <a class="hover:text-blue-600 transition-colors" href="#testimoni">Testimoni</a>
        <a class="hover:text-blue-600 transition-colors" href="#kontak">Kontak</a>
      </nav>

      <!-- Trailing Action -->
      <div class="flex items-center gap-3">
        @auth
          <a class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm shadow-blue-500/20 transition-all active:scale-95" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined text-[18px]">dashboard</span>
            <span>Ke Dashboard</span>
          </a>
        @else
          <a class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm shadow-blue-500/20 transition-all active:scale-95" href="{{ route('login') }}">
            <span>Masuk ke Sistem</span>
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </a>
        @endauth
      </div>
    </div>
  </header>

  <main>
    <!-- 2. HERO SECTION With Background Image Carousel -->
    <section class="relative pt-28 pb-20 overflow-hidden border-b border-slate-200" 
             id="beranda"
             x-data="{
                 activeSlide: 0,
                 slides: [
                     {
                         image: '{{ asset('images/hero_carousel_1.jpg') }}',
                         title: 'Automated Smart Warehouse'
                     },
                     {
                         image: '{{ asset('images/hero_carousel_2.jpg') }}',
                         title: 'Global Logistics Hub & Freight'
                     },
                     {
                         image: '{{ asset('images/hero_carousel_3.jpg') }}',
                         title: 'Enterprise Supply Chain & Storage'
                     }
                 ],
                 autoplayTimer: null,
                 init() {
                     this.startAutoplay();
                 },
                 startAutoplay() {
                     if (this.autoplayTimer) clearInterval(this.autoplayTimer);
                     this.autoplayTimer = setInterval(() => {
                         this.nextSlide();
                     }, 5000);
                 },
                 stopAutoplay() {
                     if (this.autoplayTimer) clearInterval(this.autoplayTimer);
                 },
                 nextSlide() {
                     this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                 },
                 prevSlide() {
                     this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
                 },
                 goTo(index) {
                     this.activeSlide = index;
                     this.startAutoplay();
                 }
             }"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()">

      <!-- Background Carousel Slides (Horizontal Sliding Track) -->
      <div class="absolute inset-0 -z-20 overflow-hidden select-none pointer-events-none">
        <div class="flex h-full w-full transition-transform duration-700 ease-in-out"
             :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
          <template x-for="(slide, index) in slides" :key="index">
            <div class="w-full h-full shrink-0 relative">
              <img :src="slide.image" 
                   :alt="slide.title" 
                   class="w-full h-full object-cover object-center filter brightness-[0.85] contrast-[1.10]">
            </div>
          </template>
        </div>
      </div>

      <!-- Uniform Crisp Blue & Dark Atmospheric Overlays (No Faded/Washed-Out White Patches) -->
      <!-- Layer 1: Sapphire Blue Tint -->
      <div class="absolute inset-0 -z-10 bg-blue-950/45 mix-blend-multiply pointer-events-none"></div>

      <!-- Layer 2: Deep Slate Tone for Rich Contrast -->
      <div class="absolute inset-0 -z-10 bg-slate-950/50 pointer-events-none"></div>

      <!-- Layer 3: Top and Bottom Vignette Blend -->
      <div class="absolute inset-0 -z-10 bg-gradient-to-b from-slate-950/60 via-transparent to-slate-950/80 pointer-events-none"></div>
      
      <!-- Layer 4: Tech Grid Dots in Glowing Blue -->
      <div class="absolute inset-0 -z-10 bg-[radial-gradient(#38bdf8_1.2px,transparent_1.2px)] bg-[size:32px_32px] opacity-[0.10] pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <!-- Hero Left Column -->
          <div class="lg:col-span-6 flex flex-col items-start" data-aos="fade-right">

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-6 leading-tight font-headline drop-shadow-md">
              Kelola Inventaris Lebih Mudah, Cepat, dan <span class="text-cyan-400 font-extrabold">Terintegrasi</span>
            </h1>

            <!-- Supporting Text -->
            <p class="text-base sm:text-lg text-slate-200 mb-8 leading-relaxed max-w-xl font-normal drop-shadow-xs">
              INVENTRA membantu instansi dan perusahaan mengelola data barang, stok fisik gudang, mutasi logistik, pengadaan, serta kasir POS dalam satu ekosistem terpadu.
            </p>

            <!-- Actions Buttons -->
            <div class="flex flex-wrap items-center gap-3.5 w-full sm:w-auto">
              <a class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all active:scale-95" href="{{ route('login') }}">
                <span class="material-symbols-outlined text-[20px]">login</span>
                <span>Masuk ke Sistem</span>
              </a>
              <a class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-md font-bold text-sm transition-all shadow-xs" href="#fitur">
                <span class="material-symbols-outlined text-[20px] text-cyan-400">play_circle</span>
                <span>Pelajari Fitur</span>
              </a>
            </div>
          </div>

          <!-- Hero Right Column: Dashboard Mockup Preview Card (Biru Tua / Dark Blue Glass Card) -->
          <div class="lg:col-span-6" data-aos="fade-left" data-aos-delay="200">
            <div class="bg-slate-900/95 rounded-2xl border border-blue-500/30 shadow-2xl shadow-blue-950/60 overflow-hidden text-slate-100 backdrop-blur-sm">
              
              <!-- Window Control Bar -->
              <div class="px-4 py-3 bg-slate-950/90 text-white flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                  <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                  <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                  <span class="ml-3 text-xs font-mono text-slate-400">inventra.go.id/dashboard/preview</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-950/80 text-[10px] font-bold text-emerald-300 border border-emerald-500/30">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                  <span>Database Online</span>
                </div>
              </div>
              
              <div class="p-5 space-y-4 bg-slate-900/90">
                
                <!-- Mockup Metrics Row -->
                <div class="grid grid-cols-2 gap-3">
                  <div class="p-3.5 rounded-xl bg-blue-950/60 border border-blue-800/50 shadow-inner">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                      <span class="text-xs font-medium">Total Valuasi Aset</span>
                      <span class="material-symbols-outlined text-[18px] text-blue-400">account_balance_wallet</span>
                    </div>
                    <div class="text-lg font-bold text-white font-headline">Rp 4.82 Miliar</div>
                    <div class="text-[11px] text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                      <span class="material-symbols-outlined text-[14px]">trending_up</span>
                      <span>+8.4% bulan ini</span>
                    </div>
                  </div>

                  <div class="p-3.5 rounded-xl bg-blue-950/60 border border-blue-800/50 shadow-inner">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                      <span class="text-xs font-medium">Mutasi Hari Ini</span>
                      <span class="material-symbols-outlined text-[18px] text-blue-400">swap_horiz</span>
                    </div>
                    <div class="text-lg font-bold text-white font-headline">348 Transaksi</div>
                    <div class="text-[11px] text-blue-300 font-medium mt-1">210 Inbound • 138 Outbound</div>
                  </div>
                </div>

                <!-- Alert Strip -->
                <div class="p-3 rounded-xl bg-amber-950/50 border border-amber-500/40 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-400">warning</span>
                    <span class="text-xs text-amber-200 font-semibold">3 Item mendekati batas minimum (Reorder Point)</span>
                  </div>
                  <span class="text-xs font-bold text-amber-300 underline cursor-pointer">Lihat</span>
                </div>

                <!-- Mini Data Table -->
                <div class="border border-blue-900/50 rounded-xl overflow-hidden bg-slate-950/90 shadow-inner">
                  <div class="px-3.5 py-2.5 bg-blue-950/50 border-b border-blue-900/40 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Daftar Inventaris Terkini</span>
                    <span class="text-[11px] font-bold text-blue-400">Gudang Utama</span>
                  </div>

                  <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-xs border-collapse">
                      <thead>
                        <tr class="border-b border-blue-900/30 text-slate-400 uppercase bg-blue-950/30 font-semibold text-[10px]">
                          <th class="py-2 px-3">Kode SKU</th>
                          <th class="py-2 px-3">Nama Barang</th>
                          <th class="py-2 px-3 text-right">Stok</th>
                          <th class="py-2 px-3 text-center">Status</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-blue-900/20 text-slate-300 font-medium">
                        <tr class="hover:bg-blue-950/40 transition-colors">
                          <td class="py-2.5 px-3 font-mono font-bold text-blue-400">INV-LOG-102</td>
                          <td class="py-2.5 px-3 font-semibold text-white">Kertas HVS A4 80 GSM</td>
                          <td class="py-2.5 px-3 text-right font-bold text-white">120 Rim</td>
                          <td class="py-2.5 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/80 text-emerald-300 border border-emerald-500/30">
                              Stok Aman
                            </span>
                          </td>
                        </tr>
                        <tr class="hover:bg-blue-950/40 transition-colors">
                          <td class="py-2.5 px-3 font-mono font-bold text-blue-400">INV-LOG-205</td>
                          <td class="py-2.5 px-3 font-semibold text-white">Lakban Packing Coklat 2"</td>
                          <td class="py-2.5 px-3 text-right font-bold text-amber-300">3 Roll</td>
                          <td class="py-2.5 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-950/80 text-amber-300 border border-amber-500/30">
                              Perlu Restock
                            </span>
                          </td>
                        </tr>
                        <tr class="hover:bg-blue-950/40 transition-colors">
                          <td class="py-2.5 px-3 font-mono font-bold text-blue-400">INV-LOG-318</td>
                          <td class="py-2.5 px-3 font-semibold text-white">Kardus Box Master 60x40x40</td>
                          <td class="py-2.5 px-3 text-right font-bold text-white">48 Pcs</td>
                          <td class="py-2.5 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/80 text-emerald-300 border border-emerald-500/30">
                              Stok Aman
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Bottom Status Bar -->
                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                  <span class="flex items-center gap-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Sinkronisasi Realtime Aktif
                  </span>
                  <span class="text-slate-500 font-mono text-[10px]">v2.4.0</span>
                </div>

              </div>
            </div>
          </div>

        </div>

        <!-- Carousel Indicators & Navigation Bar -->
        <div class="mt-12 pt-6 border-t border-white/10 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <template x-for="(slide, index) in slides" :key="index">
              <button type="button" 
                      @click="goTo(index)" 
                      class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                      :class="activeSlide === index ? 'w-8 bg-cyan-400 shadow-sm shadow-cyan-400/50' : 'w-2.5 bg-white/30 hover:bg-white/50'"
                      :title="slide.title">
              </button>
            </template>
            <span class="ml-2 text-xs font-medium text-slate-300 hidden sm:inline" x-text="slides[activeSlide]?.title"></span>
          </div>

          <div class="flex items-center gap-1.5 bg-slate-900/60 backdrop-blur-md px-2 py-1 rounded-full border border-white/15 shadow-sm">
            <button type="button" 
                    @click="prevSlide()" 
                    class="w-7 h-7 rounded-full hover:bg-white/10 flex items-center justify-center text-slate-200 hover:text-cyan-400 transition-colors cursor-pointer"
                    title="Sebelumnya">
              <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <button type="button" 
                    @click="nextSlide()" 
                    class="w-7 h-7 rounded-full hover:bg-white/10 flex items-center justify-center text-slate-200 hover:text-cyan-400 transition-colors cursor-pointer"
                    title="Selanjutnya">
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. HIGHLIGHT FEATURES SECTION (Fitur Unggulan) -->
    <section class="py-14 bg-white border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <!-- Fitur 1 -->
          <div data-aos="fade-up" data-aos-delay="100" class="group bg-slate-50 hover:bg-white p-6 rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Kontrol Stok</span>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <span class="material-symbols-outlined text-[22px]">monitoring</span>
                </div>
              </div>
              <h3 class="text-base font-bold text-slate-900 mb-2 font-headline group-hover:text-blue-600 transition-colors">Stok &amp; Buffer Real-Time</h3>
              <p class="text-xs text-slate-500 leading-relaxed">
                Pelacakan mutasi barang masuk &amp; keluar akurat dengan peringatan dini otomatis batas minimum reorder point.
              </p>
            </div>
          </div>

          <!-- Fitur 2 -->
          <div data-aos="fade-up" data-aos-delay="200" class="group bg-slate-50 hover:bg-white p-6 rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Pengadaan PO</span>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <span class="material-symbols-outlined text-[22px]">shopping_cart_checkout</span>
                </div>
              </div>
              <h3 class="text-base font-bold text-slate-900 mb-2 font-headline group-hover:text-blue-600 transition-colors">Integrasi Pengadaan</h3>
              <p class="text-xs text-slate-500 leading-relaxed">
                Penerimaan barang terhubung langsung dengan data Purchase Order (PO) yang telah diverifikasi Manager.
              </p>
            </div>
          </div>

          <!-- Fitur 3 -->
          <div data-aos="fade-up" data-aos-delay="300" class="group bg-slate-50 hover:bg-white p-6 rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Kasir &amp; POS</span>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <span class="material-symbols-outlined text-[22px]">point_of_sale</span>
                </div>
              </div>
              <h3 class="text-base font-bold text-slate-900 mb-2 font-headline group-hover:text-blue-600 transition-colors">Point of Sale &amp; Kas</h3>
              <p class="text-xs text-slate-500 leading-relaxed">
                Terminal kasir modern dengan scan barcode kamera, fitur hold cart transaksi, dan rekonsiliasi shift transparan.
              </p>
            </div>
          </div>

          <!-- Fitur 4 -->
          <div data-aos="fade-up" data-aos-delay="400" class="group bg-slate-50 hover:bg-white p-6 rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Audit &amp; Barcode</span>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <span class="material-symbols-outlined text-[22px]">qr_code_scanner</span>
                </div>
              </div>
              <h3 class="text-base font-bold text-slate-900 mb-2 font-headline group-hover:text-blue-600 transition-colors">Barcode &amp; Stock Opname</h3>
              <p class="text-xs text-slate-500 leading-relaxed">
                Cetak label barcode rak siap pakai serta verifikasi fisik akurat dengan modul stock opname berkala.
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 4. FEATURE SECTION (8 Structured Cards) -->
    <section class="py-20 bg-slate-50 border-b border-slate-200" id="fitur">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-xs font-bold uppercase tracking-wider text-blue-800 mb-3">
            Kapabilitas Lengkap
          </div>
          <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4 font-headline">
            Semua Modul Bisnis dalam Satu Sistem
          </h2>
          <p class="text-base text-slate-600">
            Dirancang terpadu untuk efisiensi operasional harian, transparansi pengadaan, dan akurasi kasir.
          </p>
        </div>

        <!-- 8 Feature Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <!-- Card 1 -->
          <div data-aos="zoom-in-up" data-aos-delay="50" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">inventory_2</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Master Barang &amp; SKU</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Pencatatan spesifikasi, SKU, kategori bertingkat, konversi satuan multi-UOM, dan penetapan harga standar.
            </p>
          </div>

          <!-- Card 2 -->
          <div data-aos="zoom-in-up" data-aos-delay="100" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">tune</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Manajemen Stok &amp; Buffer</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Penetapan batas minimum stok, peringatan reorder point instan, dan kartu riwayat mutasi barang.
            </p>
          </div>

          <!-- Card 3 -->
          <div data-aos="zoom-in-up" data-aos-delay="150" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">login</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Penerimaan &amp; Tarik PO</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Penerimaan barang dari supplier dengan integrasi tarik data langsung dari Purchase Order (PO) yang disetujui.
            </p>
          </div>

          <!-- Card 4 -->
          <div data-aos="zoom-in-up" data-aos-delay="200" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">local_shipping</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Outbound &amp; Surat Jalan</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Distribusi barang keluar gudang, kontrol kuantitas real-time, dan pencetakan Surat Jalan resmi.
            </p>
          </div>

          <!-- Card 5 -->
          <div data-aos="zoom-in-up" data-aos-delay="250" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">point_of_sale</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Point of Sale (POS)</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Terminal kasir tablet-ready, barcode scanner webcam, fitur tahan transaksi (hold cart), dan cetak struk nota.
            </p>
          </div>

          <!-- Card 6 -->
          <div data-aos="zoom-in-up" data-aos-delay="300" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">receipt_long</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Shift &amp; Rekonsiliasi Kas</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Buka/tutup shift kasir, input kas fisik aktual, kalkulasi selisih kas, dan cetak struk serah terima shift.
            </p>
          </div>

          <!-- Card 7 -->
          <div data-aos="zoom-in-up" data-aos-delay="350" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">qr_code_scanner</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Cetak Stiker &amp; Label Barcode</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Cetak stiker label rak gudang (100x40mm), label SKU produk (50x30mm), dan kertas stiker lembaran A4 grid siap tempel.
            </p>
          </div>

          <!-- Card 8 -->
          <div data-aos="zoom-in-up" data-aos-delay="400" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-[24px]">sensors</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Live WebSockets Sync</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
              Broadcasting mutasi stok secara instan ke seluruh dashboard dan kasir tanpa perlu refresh halaman.
            </p>
          </div>

        </div>
      </div>
    </section>

    <!-- 5. HOW IT WORKS (3-Step Workflow) -->
    <section class="py-20 bg-white border-b border-slate-200" id="alur">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-xs font-bold uppercase tracking-wider text-blue-800 mb-3">
            Alur Kerja Sistem
          </div>
          <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4 font-headline">
            Tiga Langkah Sederhana
          </h2>
          <p class="text-base text-slate-600">
            Alur kerja yang dirancang intuitif untuk meminimalkan kurva pelatihan personil.
          </p>
        </div>

        <!-- 3-Step Horizontal Layout -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <!-- Step 1 -->
          <div data-aos="fade-right" data-aos-delay="100" class="p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center flex flex-col items-center">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-bold font-headline mb-5">
              01
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Master &amp; Pengadaan</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Daftarkan master barang, tentukan batas minimum stok aman, dan ajukan Purchase Order ke supplier.
            </p>
          </div>

          <!-- Step 2 -->
          <div data-aos="fade-up" data-aos-delay="200" class="p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center flex flex-col items-center">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-bold font-headline mb-5">
              02
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Operasional &amp; Transaksi</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Penerimaan barang (Inbound), pengeluaran distribusi (Outbound), dan penjualan di kasir POS.
            </p>
          </div>

          <!-- Step 3 -->
          <div data-aos="fade-left" data-aos-delay="300" class="p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center flex flex-col items-center">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-bold font-headline mb-5">
              03
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2 font-headline">Audit &amp; Rekonsiliasi</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Stock opname berkala, pencatatan barang rusak/karantina, dan cetak laporan akuntansi siap audit.
            </p>
          </div>

        </div>
      </div>
    </section>

    <!-- 6. WHY INVENTRA SECTION -->
    <section class="py-20 bg-slate-50 border-b border-slate-200" id="tentang">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <div class="lg:col-span-5">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-xs font-bold uppercase tracking-wider text-blue-800 mb-3">
              Keunggulan Utama
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-6 font-headline leading-tight">
              Mengapa Memilih INVENTRA?
            </h2>
            <p class="text-base text-slate-600 mb-8 leading-relaxed">
              Arsitektur enterprise modern yang mengutamakan integritas data, transparansi, dan kemudahan bagi setiap peran pengguna.
            </p>

            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-start gap-4">
              <span class="material-symbols-outlined text-blue-600 text-[26px] shrink-0">verified</span>
              <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Standarisasi Tata Kelola Nasional</h4>
                <p class="text-xs text-slate-600">
                  Format kodefikasi barang dan kartu mutasi persediaan selaras dengan kepatuhan audit institusi.
                </p>
              </div>
            </div>
          </div>

          <div class="lg:col-span-7">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              
              <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                  <span class="material-symbols-outlined text-[22px]">hub</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1 font-headline">Data Terpusat</h3>
                <p class="text-xs text-slate-500">
                  Menghilangkan duplikasi pencatatan antar divisi gudang dan kasir toko.
                </p>
              </div>

              <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                  <span class="material-symbols-outlined text-[22px]">speed</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1 font-headline">Proses Lebih Cepat</h3>
                <p class="text-xs text-slate-500">
                  Memangkas waktu approval pengadaan dan opname fisik hingga 65%.
                </p>
              </div>

              <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                  <span class="material-symbols-outlined text-[22px]">flaky</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1 font-headline">Nol Kesalahan Catat</h3>
                <p class="text-xs text-slate-500">
                  Validasi barcode otomatis mengeliminasi galat pengetikan secara total.
                </p>
              </div>

              <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                  <span class="material-symbols-outlined text-[22px]">insights</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1 font-headline">Monitoring Real-time</h3>
                <p class="text-xs text-slate-500">
                  Pimpinan dapat memeriksa saldo stok dan omzet kasir kapan saja.
                </p>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 7. TESTIMONIALS SECTION -->
    <section class="py-24 bg-white border-b border-slate-200" id="testimoni">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
          <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-xs font-bold uppercase tracking-wider text-blue-700 mb-3">
            <span class="material-symbols-outlined text-[16px] text-blue-600">reviews</span>
            <span>Ulasan &amp; Kisah Sukses</span>
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4 font-headline">
            Dipercaya oleh Ratusan Praktisi &amp; Institusi
          </h2>
          <p class="text-base text-slate-600">
            Dengarkan bagaimana INVENTRA mentransformasi efisiensi gudang logistik, pengadaan transparan, dan akurasi kasir harian.
          </p>
        </div>

        <!-- 3 Testimonial Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <!-- Card 1: Logistics & Warehouse -->
          <div data-aos="fade-up" data-aos-delay="100" class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between">
            <div class="space-y-4">
              <!-- Metric Badge & Star Rating -->
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center text-amber-400 text-sm">
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  ⚡ Inbound 60% Lebih Cepat
                </span>
              </div>

              <!-- Testimonial Quote -->
              <p class="text-xs text-slate-700 leading-relaxed italic">
                "Integrasi tarik data PO langsung ke formulir Inbound di INVENTRA sangat mempermudah tim gudang. Waktu verifikasi barang saat truk bongkar muat terpangkas lebih dari separuh, dan mutasi stok langsung ter-broadcast secara realtime tanpa input ulang."
              </p>
            </div>

            <!-- Profile Info -->
            <div class="pt-6 border-t border-slate-200 mt-6 flex items-center gap-3">
              <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white font-black font-headline flex items-center justify-center text-sm shadow-sm shadow-blue-500/20 shrink-0">
                BP
              </div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 font-headline">Budi Pratama, S.T.</h4>
                <p class="text-[11px] text-slate-500 font-medium">Head of Logistics &amp; Warehouse</p>
                <p class="text-[10px] text-blue-600 font-semibold">PT Nusantara Distribusi Utama</p>
              </div>
            </div>
          </div>

          <!-- Card 2: Healthcare / FMCG Procurement -->
          <div data-aos="fade-up" data-aos-delay="200" class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between">
            <div class="space-y-4">
              <!-- Metric Badge & Star Rating -->
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center text-amber-400 text-sm">
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  📈 Akurasi Stok 99.8%
                </span>
              </div>

              <!-- Testimonial Quote -->
              <p class="text-xs text-slate-700 leading-relaxed italic">
                "Fitur peringatan reorder point dan auto-draft PO berdasarkan batas stok kritis luar biasa membantu. Kami tidak pernah lagi mengalami kehabisan stok obat dan barang esensial. Seluruh catatan mutasi rapi dan siap saat proses audit reguler."
              </p>
            </div>

            <!-- Profile Info -->
            <div class="pt-6 border-t border-slate-200 mt-6 flex items-center gap-3">
              <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white font-black font-headline flex items-center justify-center text-sm shadow-sm shadow-indigo-500/20 shrink-0">
                MA
              </div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 font-headline">dr. Maya Anggraini, M.M.</h4>
                <p class="text-[11px] text-slate-500 font-medium">Direktur Operasional &amp; Pengadaan</p>
                <p class="text-[10px] text-indigo-600 font-semibold">Klinik Medika Farma Pratama</p>
              </div>
            </div>
          </div>

          <!-- Card 3: Retail & POS Operations -->
          <div data-aos="fade-up" data-aos-delay="300" class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between">
            <div class="space-y-4">
              <!-- Metric Badge & Star Rating -->
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center text-amber-400 text-sm">
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                  💰 Selisih Kas 0%
                </span>
              </div>

              <!-- Testimonial Quote -->
              <p class="text-xs text-slate-700 leading-relaxed italic">
                "Terminal POS tablet dengan barcode scanner kamera dan sistem rekonsiliasi kas shift membuat pergantian kasir sore ke malam berjalan tertib. Tidak ada lagi kendala selisih uang fisik kasir dengan laporan sistem."
              </p>
            </div>

            <!-- Profile Info -->
            <div class="pt-6 border-t border-slate-200 mt-6 flex items-center gap-3">
              <div class="w-11 h-11 rounded-2xl bg-cyan-600 text-white font-black font-headline flex items-center justify-center text-sm shadow-sm shadow-cyan-500/20 shrink-0">
                HK
              </div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 font-headline">Hendra Kusuma</h4>
                <p class="text-[11px] text-slate-500 font-medium">Head of Retail Operations</p>
                <p class="text-[10px] text-cyan-600 font-semibold">Surya Niaga Retail Group</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 8. CTA BANNER CARD (Solid Blue Card - No Gradient) -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-blue-600 rounded-3xl p-10 lg:p-14 text-white relative shadow-xl overflow-hidden" data-aos="zoom-in">
          
          <div class="max-w-3xl relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-700/80 text-xs font-bold text-white mb-4 border border-blue-400/30">
              <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
              Sistem Siap Digunakan Secara Institusional
            </div>
            
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-4 font-headline leading-tight text-white">
              Mulai Transformasi Inventaris Digital Anda Bersama INVENTRA
            </h2>
            
            <p class="text-sm sm:text-base text-blue-50 mb-8 leading-relaxed max-w-2xl">
              Tingkatkan akurasi stok fisik, transparansi arus pengadaan barang, dan otomasi kasir di seluruh lini kerja sekarang juga.
            </p>
            
            <div class="flex flex-wrap items-center gap-4">
              <a class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-blue-600 hover:bg-blue-50 font-bold text-sm shadow-md transition-all active:scale-95" href="{{ route('login') }}">
                <span class="material-symbols-outlined text-[20px] text-blue-600">rocket_launch</span>
                <span>Masuk ke Portal INVENTRA</span>
              </a>
              <a class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-blue-700 hover:bg-blue-800 border border-blue-400/30 text-white font-bold text-sm transition-all" href="#kontak">
                <span class="material-symbols-outlined text-[20px] text-blue-100">support_agent</span>
                <span>Layanan Bantuan Teknis</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- 8. FOOTER -->
  <footer class="w-full py-12 px-4 sm:px-6 lg:px-8 border-t border-slate-800 bg-slate-950 text-slate-400" id="kontak">
    <div class="max-w-7xl mx-auto w-full">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-slate-800">
        
        <!-- Identity -->
        <div class="md:col-span-4 flex flex-col">
          <div class="flex items-center gap-2 mb-3">
            <img src="{{ \App\Models\Setting::get('app_logo') ? Storage::url(\App\Models\Setting::get('app_logo')) : asset('images/logo.png') }}" alt="Inventra Logo" class="h-12 w-auto object-contain brightness-0 invert opacity-95">
          </div>
          <p class="text-xs text-slate-400 max-w-sm mb-4 leading-relaxed">
            Platform Enterprise Resource Planning (ERP) dan logistik terpadu untuk efisiensi persediaan barang dan kasir POS.
          </p>
          <div class="flex items-center gap-2 text-xs text-slate-400">
            <span class="material-symbols-outlined text-[16px] text-blue-400">verified</span>
            <span>Standar Keamanan Siber ISO/IEC 27001</span>
          </div>
        </div>

        <!-- Links Column 1: Modul -->
        <div class="md:col-span-2">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Modul Utama</h4>
          <ul class="space-y-2 text-xs text-slate-400">
            <li><a class="hover:text-blue-400 transition-colors" href="#">Master Barang</a></li>
            <li><a class="hover:text-blue-400 transition-colors" href="#">Inbound &amp; Outbound</a></li>
            <li><a class="hover:text-blue-400 transition-colors" href="#">Stock Opname</a></li>
            <li><a class="hover:text-blue-400 transition-colors" href="#">Point of Sale</a></li>
          </ul>
        </div>

        <!-- Links Column 2: Panduan -->
        <div class="md:col-span-3">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Dokumentasi</h4>
          <ul class="space-y-2 text-xs text-slate-400">
            <li><a class="hover:text-blue-400 transition-colors" href="#">Petunjuk Kasir</a></li>
            <li><a class="hover:text-blue-400 transition-colors" href="#">Alur Purchase Order</a></li>
            <li><a class="hover:text-blue-400 transition-colors" href="#">Ketentuan Layanan</a></li>
            <li><a class="hover:text-blue-400 transition-colors" href="#">Kebijakan Privasi</a></li>
          </ul>
        </div>

        <!-- Contact Support -->
        <div class="md:col-span-3">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Dukungan Teknis</h4>
          <div class="space-y-2 text-xs text-slate-400">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[16px] text-blue-400">mail</span>
              <span>helpdesk@inventra.go.id</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[16px] text-blue-400">call</span>
              <span>(021) 5082-9900</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[16px] text-blue-400">schedule</span>
              <span>Senin — Jumat: 08.00 - 17.00 WIB</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Row -->
      <div class="pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
        <p>© 2026 INVENTRA Enterprise ERP. Hak Cipta Dilindungi Undang-Undang.</p>
        <div class="flex flex-wrap items-center gap-6">
          <a class="hover:text-blue-400 transition-colors" href="#">Kebijakan Privasi</a>
          <a class="hover:text-blue-400 transition-colors" href="#">Ketentuan Layanan</a>
          <a class="hover:text-blue-400 transition-colors" href="#">Keamanan Data ISO 27001</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ once: true, duration: 800, offset: 100 });
  </script>
</body>
</html>