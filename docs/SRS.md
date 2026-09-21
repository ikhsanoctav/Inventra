# Software Requirements Specification (SRS) - INVENTRA Enterprise ERP

## 1. Pendahuluan
### 1.1 Tujuan
Dokumen Software Requirements Specification (SRS) ini bertujuan untuk mendefinisikan spesifikasi teknis dan kebutuhan fungsional dari **INVENTRA (Sistem Manajemen Inventaris Terintegrasi)**. Dokumen ini ditujukan untuk *developer*, arsitek perangkat lunak, dan *stakeholders* teknis sebagai acuan dalam membangun dan memelihara sistem.

### 1.2 Ruang Lingkup
Sistem INVENTRA mencakup pengelolaan *Master Data* (Barang & Jasa, Kategori, Satuan), *Inventory Control* (Inbound, Outbound, Stock Opname), Point of Sale (POS) untuk ritel langsung, serta pelaporan logistik tingkat eksekutif. Sistem ini menggunakan teknologi *Real-time WebSockets* dan memiliki fitur *Rule Builder (RBL)* untuk otomatisasi.

---

## 2. Deskripsi Keseluruhan
### 2.1 Lingkungan Sistem
Sistem ini berbasis *Web (Web-Based Application)* yang di-*hosting* pada arsitektur *Cloud* (seperti AWS atau Laravel Cloud). 
*   **Web Server:** Nginx / Apache
*   **Database Server:** MySQL 8.0+ atau PostgreSQL 15+
*   **OS Server:** Ubuntu Linux (Standar Server) / Windows Server

### 2.2 Batasan dan Asumsi
*   Sistem berjalan mulus pada *browser* modern (Chrome, Firefox, Safari, Edge).
*   Koneksi internet yang stabil diwajibkan, khususnya untuk sinkronisasi POS dan notifikasi *real-time*.
*   Halaman POS wajib dirancang responsif, khususnya untuk resolusi *Tablet* (iPad, Samsung Galaxy Tab).

---

## 3. Persyaratan Antarmuka Eksternal (External Interfaces)
### 3.1 Antarmuka Pengguna (UI/UX)
*   Menggunakan *framework* **Tailwind CSS**.
*   Tema didominasi warna **Biru Profesional** (elegan, bukan oranye).
*   Layout responsif dengan *Sidebar* yang *collapsible* (bisa diperkecil menjadi *icon*).
*   *Styling* mencakup elemen *Glassmorphism* (semi-transparan dengan *blur*) pada *Top Bar* dan kartu/panel tertentu.

### 3.2 Antarmuka Perangkat Keras (Hardware Interfaces)
*   **Barcode Scanner:** Didukung di area Gudang (untuk *Inbound/Outbound*) dan area Kasir (POS) menggunakan input *Keyboard Emulation* (USB/Bluetooth Scanner).
*   **Thermal Printer:** Dukungan cetak struk POS (*ESC/POS compatible*) melalui antarmuka *print browser*.

### 3.3 Antarmuka Komunikasi (Communication Interfaces)
*   Penerapan **Laravel Reverb (WebSockets)** menggunakan protokol `ws://` dan `wss://` untuk notifikasi *real-time*, *live-search*, dan sinkronisasi data (*Dashboard*).

---

## 4. Persyaratan Fungsional (System Features)

### 4.1 Modul Autentikasi & RBAC (Role-Based Access Control)
*   **FR-AUTH-1:** Sistem harus mengautentikasi pengguna melalui email/NIP dan *password*.
*   **FR-AUTH-2:** Akses rute/URL dibatasi berdasarkan Middleware *Role* (Super Admin, Manager, Admin Gudang, Purchasing, Kasir).
*   **FR-AUTH-3:** Layout *view* dan *controller* dipisah/disusun ke dalam folder per *Role* agar basis kode terstruktur.

### 4.2 Modul Master Data
*   **FR-MD-1:** Pengguna dapat menambah, mengedit, dan menghapus (Soft Delete) data Barang, Jasa, Kategori, Satuan, dan Supplier.
*   **FR-MD-2:** Sistem membedakan secara spesifik antara aset fisik (*flag* `is_service = false`) yang mengurangi stok, dan Jasa (*flag* `is_service = true`) yang tidak berwujud/tidak memiliki perhitungan stok fisik.

### 4.3 Modul Transaksi Gudang (Inbound & Outbound)
*   **FR-INV-1:** Mutasi barang (*Inbound* / *Outbound*) harus divalidasi dengan logika *Database Transaction* (Commit/Rollback) untuk mencegah anomali penghitungan stok saat bersamaan.
*   **FR-INV-2:** Pencatatan otomatis ID pengguna yang melakukan mutasi (Audit Trail).

### 4.4 Modul Point of Sale (POS)
*   **FR-POS-1:** Sistem menyediakan antarmuka khusus (berbasis *grid*) untuk Kasir.
*   **FR-POS-2:** Saat kasir masuk ke menu POS, sistem harus memberikan *prompt* "Tipe Transaksi" (Barang atau Jasa) sebelum menampilkan *item*.
*   **FR-POS-3:** Total penjualan POS akan memotong stok secara langsung pada *database* gudang/toko terkait.

### 4.5 Modul Rule Builder (RBL) - Otomatisasi
*   **FR-RBL-1:** Admin dapat mendefinisikan *Triggers* (contoh: *Stock Updated*).
*   **FR-RBL-2:** Admin dapat menentukan *Conditions* (contoh: *Quantity < Reorder Point*).
*   **FR-RBL-3:** Admin dapat menentukan *Actions* (contoh: *Send System Notification to Purchasing*).

---

## 5. Persyaratan Non-Fungsional (Non-Functional Requirements)
### 5.1 Kinerja (Performance)
*   Waktu muat halaman (Page Load Time) ditargetkan kurang dari **2.5 detik**.
*   Waktu *response API* untuk *Realtime Search* maksimum **200ms**.

### 5.2 Keamanan (Security)
*   Validasi perlindungan terhadap SQL Injection, XSS, dan CSRF (bawaan Middleware Laravel).
*   Menggunakan *Bcrypt* atau *Argon2id* untuk *hashing password*.
*   Data finansial dan transaksi harus terjaga dengan integritas *Database Constraints* (Foreign Key) yang ketat.

### 5.3 Ketersediaan & Keandalan (Availability & Reliability)
*   Target SLA ketersediaan adalah **99.9%**.
*   Mengimplementasikan sistem *Auto-Backup* database harian.

---

## 6. Spesifikasi Lingkungan Teknologi (Tech Stack)
*   **Bahasa Pemrograman Utama:** PHP 8.5
*   **Web Framework:** Laravel 13.x
*   **Frontend Templating:** Laravel Blade (memanfaatkan pola *Layout Inheritance* seperti `@extends('layouts.app')` dan `@extends('layouts.guest')`).
*   **Frontend CSS:** Tailwind CSS v3 / v4
*   **Broadcasting/WebSocket:** Laravel Reverb + Laravel Echo
*   **Database:** MySQL Server / MariaDB
*   **Development Tools:** Git, Composer, NPM, Laravel Artisan Server.

---
*Catatan: Dokumen ini mengacu pada PRD INVENTRA Fase 3, dan difokuskan pada panduan teknis bagi tim pengembang untuk melakukan coding, standarisasi folder, dan database.*
