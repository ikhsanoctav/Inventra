<div align="center">
  <img src="public/images/logo.png" alt="Logo Inventra" width="200" style="border-radius: 20px; margin-bottom: 20px;">

  <h1>📦 Inventra (Sistem Inventaris Logistik)</h1>
  
  <p>
    <strong>Sistem Manajemen Inventaris dan Logistik berbasis Web dengan Laravel.</strong>
  </p>
</div>

---

## 📖 Tentang Sistem

**Inventra** adalah sebuah sistem informasi manajemen logistik berbasis web yang dirancang untuk mempermudah pencatatan, pemantauan, dan pengelolaan inventaris barang di gudang. Sistem ini memastikan setiap barang masuk (inbound) dan barang keluar (outbound) tercatat dengan akurat dan *real-time*.

Sistem ini sangat cocok digunakan oleh perusahaan, gudang, maupun instansi yang membutuhkan kontrol ketat terhadap pergerakan stok logistik mereka.

### ✨ Fitur Utama
*   **Point of Sale (POS) / Kasir**: Fitur transaksi penjualan langsung dengan antarmuka kasir yang responsif.
*   **Manajemen Shift (Karyawan/Kasir)**: Pengaturan shift kerja, modal awal (cash in drawer), laporan pendapatan per shift, dan serah terima shift.
*   **Dashboard Interaktif**: Ringkasan data stok barang, pendapatan, barang masuk, dan barang keluar.
*   **Manajemen Data Master**: Pengelolaan data kategori, satuan, rak penyimpanan, pelanggan, dan supplier.
*   **Manajemen Stok / Inventaris**: Pencatatan barang dengan detail yang lengkap dan otomatis terpotong saat ada transaksi POS.
*   **Transaksi Logistik**: 
    *   **Barang Masuk**: Mencatat restok atau penerimaan barang dari supplier.
    *   **Barang Keluar**: Mencatat pengeluaran barang (selain dari POS) untuk distribusi atau penggunaan.
*   **Laporan & Cetak**: Pembuatan laporan ketersediaan stok, mutasi barang, laporan penjualan kasir, dan laporan shift secara berkala.

---

## 🛠️ Teknologi yang Digunakan

Proyek ini dibangun menggunakan teknologi modern:
*   **Backend**: [Laravel 13.x](https://laravel.com) (PHP 8.3+)
*   **Database**: Mendukung MySQL / PostgreSQL / SQLite
*   **Websockets**: Laravel Reverb (Pusher) untuk dukungan notifikasi secara *real-time*
*   **Testing**: Pest / PHPUnit

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan sistem Inventra di komputer lokal Anda:

### Persyaratan
*   PHP >= 8.3
*   Composer
*   Node.js & NPM
*   Database (MySQL/MariaDB)

### Langkah-langkah

1. **Clone repository ini**
   ```bash
   git clone https://github.com/ikhsanoctav/Inventra.git
   cd inventory_Logistik
   ```

2. **Jalankan Setup Cepat**
   Sistem ini telah dikonfigurasi dengan perintah setup otomatis melalui composer:
   ```bash
   composer run setup
   ```
   *(Perintah di atas akan menginstal dependensi PHP, membuat file `.env`, *generate key*, menjalankan migrasi database, serta menginstal & mem-build asset frontend).*

3. **Konfigurasi Database**
   Pastikan Anda sudah menyesuaikan konfigurasi database di file `.env` jika menggunakan database selain SQLite default:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventra_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Server Lokal**
   Gunakan perintah bawaan composer yang sudah disediakan:
   ```bash
   composer run dev
   ```
   Aplikasi Anda sekarang dapat diakses melalui browser di `http://localhost:8000`.

---

## 📄 Lisensi

Sistem ini merupakan perangkat lunak *open-sourced* yang dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
