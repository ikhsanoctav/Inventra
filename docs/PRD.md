# Product Requirements Document (PRD) - INVENTRA Enterprise ERP

## 1. Executive Summary & Visi Produk
**INVENTRA (Sistem Manajemen Inventaris Terintegrasi)** adalah platform Enterprise Resource Planning (ERP) berbasis web modern yang dirancang untuk mengelola tata kelola aset, persediaan stok gudang, dan sistem Point of Sale (POS) yang terintegrasi penuh. 

Visi utama dari INVENTRA adalah menciptakan sistem yang **elegan, responsif (tablet-able), *user-oriented*, dan dapat diadaptasikan untuk segala jenis skala bisnis**, dengan otomatisasi cerdas melalui fitur unggulan **Rule Builder (RBL)** serta kapabilitas **Real-time Synchronization** antar cabang dan pengguna.

---

## 2. Target Pengguna & Personas (Role-Based Access)

Sistem dirancang dengan arsitektur *Role-Based Access Control* (RBAC) yang memisahkan hak akses, tampilan (views), dan logika (controllers) secara terstruktur.

1. **Super Admin**
   - **Deskripsi:** Pengendali utama sistem (SysAdmin).
   - **Wewenang:** Memiliki akses absolut ke semua modul, konfigurasi sistem, pengaturan *Rule Builder*, dan manajemen hak akses pengguna.
2. **Manager (Eksekutif)**
   - **Deskripsi:** Pemilik bisnis atau pimpinan divisi.
   - **Wewenang:** Memantau dasbor eksekutif, analisis tren stok, laporan laba/rugi, dan melakukan *audit trail* terhadap semua transaksi.
3. **Admin Gudang (Logistik)**
   - **Deskripsi:** Penanggung jawab arus keluar-masuk barang fisik di gudang.
   - **Wewenang:** Melakukan penerimaan barang (*Inbound*), pengeluaran barang (*Outbound*), dan pencatatan *Stock Opname*.
4. **Purchasing (Pengadaan)**
   - **Deskripsi:** Staf yang bertanggung jawab atas pengadaan barang ke vendor/supplier.
   - **Wewenang:** Mengelola data *supplier*, membuat dan memantau status *Purchase Order* (PO).
5. **Kasir (Front-liner)**
   - **Deskripsi:** Staf operasional di garis depan toko/outlet.
   - **Wewenang:** Mengoperasikan Point of Sale (POS) untuk memproses penjualan barang/jasa secara langsung kepada pelanggan.

---

## 3. Spesifikasi Fungsional (Functional Requirements)

### 3.1. Master Data Management
* **Manajemen Barang & Jasa:** Pencatatan nama barang, SKU, harga beli, harga jual, batas stok minimum (*Reorder Point*), dan *flag* indikator tipe (Barang Fisik vs Jasa).
* **Kategori & Sub-Kategori:** Pengelompokan barang secara bertingkat untuk memudahkan pelaporan.
* **Satuan (Unit of Measurement):** Pengaturan multi-satuan (Pcs, Box, Lusin, Kg).
* **Data Supplier:** Informasi kontak vendor, riwayat pemesanan, dan performa vendor.

### 3.2. Manajemen Inventaris (Inventory Control)
* **Barang Masuk (Inbound):** Pencatatan penerimaan stok berdasarkan *Purchase Order* atau retur. Validasi otomatis untuk memperbarui ketersediaan stok riil.
* **Barang Keluar (Outbound):** Surat jalan, pengeluaran barang untuk penjualan, mutasi ke gudang lain, atau pemusnahan barang rusak.
* **Stock Opname:** Fitur rekonsiliasi stok fisik dan sistem dengan log penyesuaian (Audit).

### 3.3. Point of Sale (POS)
* **Antarmuka Kasir Interaktif:** Tampilan *grid* produk dan keranjang belanja di satu sisi, dirancang secara spesifik agar responsif di layar **Tablet (Tablet-able)**.
* **Tipe Pesanan:** Sebelum memulai transaksi, kasir dapat memilih alur kerja: Transaksi Barang atau Transaksi Jasa.
* **Checkout & Struk:** Perhitungan total, diskon (jika ada), pajak, dan dukungan cetak struk (Thermal Printer) atau faktur PDF.

### 3.4. RBL (Rule Builder) - *Core Differentiator*
* **Otomatisasi Logika Bisnis:** Antarmuka visual bagi admin untuk membuat aturan khusus (Contoh: "JIKA Stok < 10 MAKA Kirim Email ke Purchasing", atau "JIKA Penjualan Jasa > 5 MAKA Berikan Diskon").
* **Komponen:** Terdiri dari *Triggers* (Pemicu), *Conditions* (Syarat), dan *Actions* (Tindakan).

### 3.5. Real-time Synchronization
* **Notifikasi Instan:** Peringatan stok menipis atau persetujuan dokumen muncul secara langsung tanpa *reload* halaman.
* **Live Search (Global Search):** Pencarian di *Top Bar* memberikan hasil instan saat mengetik.
* **Dashboard Updates:** Grafik penjualan dan status gudang diperbarui secara otomatis ketika ada transaksi di sisi Kasir.

---

## 4. Struktur Navigasi & Menu (UI Mapping)

Berikut adalah struktur hirarki navigasi pada *Sidebar* berdasarkan *Role*:

* **A. Super Admin**
  * Dashboard
  * Master Data (Barang/Jasa, Kategori, Satuan, Supplier)
  * Transaksi (Inbound, Outbound, Stock Opname)
  * Modul POS (Akses Bypass Kasir)
  * Laporan (Stok, Mutasi, Penjualan)
  * Pengaturan Sistem (Manajemen Pengguna, Rule Builder, General Settings)

* **B. Admin Gudang**
  * Dashboard Gudang
  * Master Data (Hanya View/Request)
  * Transaksi Gudang (Inbound, Outbound, Stock Opname)
  * Laporan Gudang (Riwayat Mutasi)

* **C. Purchasing**
  * Dashboard Pengadaan
  * Kelola Supplier
  * Purchase Order (PO)
  * Laporan Pembelian

* **D. Kasir**
  * Menu Utama POS (Pilih Mode: Barang/Jasa)
  * Terminal POS (Keranjang & Checkout)
  * Riwayat Transaksi Shift Kasir

* **E. Manager**
  * Dashboard Eksekutif
  * Laporan Keuangan & Audit Komprehensif
  * Activity Log

---

## 5. UI/UX & Design Guidelines (Non-Functional)

* **Tema Visual:** Warna primer **Biru Elegan**, menghindari warna mencolok seperti oranye (sesuai instruksi pengguna). Sistem harus terlihat profesional, premium, dan kredibel.
* **Aksesibilitas & Tata Letak:**
  * **Top Bar:** Harus rapi. Tulisan logo "INVENTRA" diposisikan proporsional sejajar di kiri (tidak berada kaku di tengah). Global Search dan Notifikasi di kanan.
  * **Sidebar:** Harus mendukung fitur *Collapsible* (bisa dilipat menjadi *icon-only*) untuk memperluas area kerja (*workspace*).
  * **Komponen UI:** Menggunakan *Glassmorphism* halus pada area tertentu, *soft shadows*, *border radius* seragam, dan tidak "polosan".
* **Responsivitas:** Wajib mendukung Desktop, Laptop, dan Tablet (iPad/Android Tab) terutama pada halaman POS dan Dashboard.

---

## 6. Spesifikasi Teknis (Tech Stack)

* **Backend Framework:** Laravel 13.x
* **Language:** PHP 8.5
* **Frontend CSS:** Tailwind CSS v3/v4 (diimplementasikan via CDN atau Vite Build).
* **Realtime Engine:** Laravel Reverb (WebSockets).
* **Database Engine:** MySQL / PostgreSQL (terintegrasi dengan Eloquent ORM).
* **Templating:** Blade Engine (Menerapkan arsitektur layout terpusat seperti `@extends('layouts.app')` untuk konsistensi struktur).

---

## 7. Roadmap Implementasi Fase Berjalan (Fase 3)
Saat ini proyek berada pada **Fase 3**, dengan prioritas pengerjaan:
1. Penyempurnaan UI/UX keseluruhan (Standarisasi warna biru elegan, Sidebar *collapsible*, perbaikan *layout* Topbar).
2. Pengembangan sistem Point of Sale (POS) untuk *Role* Kasir beserta dukungan tablet.
3. Integrasi *Real-time WebSockets* untuk Notifikasi dan Search.
4. Implementasi logika *User Management* untuk Super Admin.
5. Inisiasi struktur awal untuk *Rule Builder (RBL)*.
