# Panduan Desain & UI (INVENTRA Design System)

Dokumen ini berisi panduan *styling*, palet warna, tipografi, dan konvensi antarmuka pengguna (UI) untuk memastikan sistem INVENTRA tetap serasi, rapi, dan konsisten di seluruh modul, halaman, dan fitur baru yang akan dikembangkan.

## 1. Tipografi (Typography)

Sistem INVENTRA menggunakan dua kombinasi font dari Google Fonts:
- **Font Utama (Body / UI Text): `Inter`**
  Digunakan untuk teks paragraf, label formulir, tabel, tombol, dan elemen UI lainnya. Memberikan kesan bersih, modern, dan sangat mudah dibaca.
- **Font Headline (Judul / Angka): `Outfit`**
  Digunakan secara khusus untuk `h1`, `h2`, `h3`, dan angka metrik penting di *Dashboard* atau statistik. Memberikan nuansa elegan dan premium.

**Penggunaan Class Tailwind:**
- `font-sans` atau default tanpa deklarasi class: Akan menggunakan font `Inter`.
- `font-headline`: Gunakan class kustom ini untuk menggunakan font `Outfit`. (Sudah di-setup di `layouts.app`).
- `font-mono`: Digunakan untuk angka statistik, nomor SKU, nominal Rupiah, atau kode unik agar presisi dan sejajar.

## 2. Palet Warna (Color Palette)

Skema warna dirancang untuk memberikan nuansa *Enterprise / Clean Corporate*. Kami menghindari warna oranye mencolok secara global dan lebih memilih rona biru laut dan indigo sebagai warna utama (*primary*).

### Warna Utama (Primary)
- **Primary Brand**: Indigo (`bg-indigo-600` / `#4F46E5`)
  - Digunakan untuk: Tombol aksi utama (CTA), garis batas aktif, elemen sorotan.
  - Hover State: `bg-indigo-700`.
- **Secondary Accent**: Blue (`bg-blue-600` / `#2563EB`)
  - Digunakan untuk: Aksi sekunder, tautan (links), grafik analitik sekunder.

### Background & Surface (Warna Latar)
- **Latar Utama (Body)**: Slate 50 (`bg-slate-50` / `#F8FAFC`)
- **Permukaan (Cards/Modals)**: White (`bg-white` / `#FFFFFF`)
- **Dekorasi**: *Glassmorphism background* dengan gradasi tipis di bagian atas layar `bg-gradient-to-br from-indigo-500/10 via-purple-500/5 to-transparent`.

### Teks (Text Colors)
- **Teks Utama (Headings)**: Gray 900 (`text-[#111827]`)
- **Teks Sekunder (Body/Sub)**: Gray 500 (`text-[#6B7280]`) atau Slate 500 (`text-[#64748B]`)
- **Label Lemah (Muted)**: Gray 400 (`text-[#9CA3AF]`)

### Warna Status (Semantic Colors)
Selalu patuhi kode warna ini untuk status atau metrik inventaris:
- **Success (Aman/Berhasil)**: Emerald/Green (`bg-[#F0FDF4]`, `text-[#166534]`, `border-[#BBF7D0]`)
- **Warning (Kritis/Menipis/Pending)**: Amber/Orange (`bg-[#FFFBEB]`, `text-[#92400E]`, `border-[#FDE68A]`)
- **Danger (Habis/Error/Ditolak)**: Red (`bg-[#FEF2F2]`, `text-[#991B1B]`, `border-[#FECACA]`)
- **Info (Netral/Proses)**: Blue (`bg-[#EFF6FF]`, `text-[#1E40AF]`, `border-[#BFDBFE]`)

## 3. Komponen Dasar (Base Components)

### 3.1. Kartu (Cards & Containers)
Semua wadah data seperti tabel, form, atau metrik harus dibungkus dengan kartu yang seragam:
```html
<div class="bg-white rounded-xl border border-[#E5E7EB] p-5 shadow-sm">
    <!-- Konten di sini -->
</div>
```

### 3.2. Tombol (Buttons)
**Primary Button:**
```html
<button class="h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition-colors shadow-sm">
    Simpan Data
</button>
```
**Secondary Button (Outline/White):**
```html
<button class="h-9 px-4 rounded-lg bg-white border border-[#E5E7EB] hover:bg-[#F8FAFC] text-[#1F2937] font-medium text-xs transition-colors shadow-sm">
    Batal
</button>
```

### 3.3. Input Form
Elemen input harus memiliki tinggi standar (`h-10`), label yang jelas, dan efek fokus:
```html
<label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Barang</label>
<input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-[#9CA3AF]">
```

### 3.4. Tabel (Data Tables)
Gunakan pendekatan desain *ERP/Pristine Linear* untuk tabel:
- Header: Latar belakang sangat muda (`bg-[#F8FAFC]`), teks *uppercase* kecil (`text-[11px]`), warna abu-abu.
- Baris (Rows): Transisi warna saat di-*hover* (`hover:bg-[#F8FAFC] transition-colors h-12`).
- Batas Antar Baris: Garis pembatas horizontal tipis (`divide-y divide-[#F1F5F9]`).

## 4. Konvensi Animasi & Interaksi

- **Shadow & Hover**: Gunakan kelas `shadow-sm` secara default, dan efek perubahan warna batas/latar belakang secara halus (contoh: `hover:border-[#D1D5DB] transition-all`). Hindari penggunaan bayangan berat (`shadow-xl`) kecuali untuk *modal popup*.
- **Iconography**: Gunakan `Google Material Symbols Outlined`. Pastikan ikon dan teks didampingi selaras secara vertikal (`flex items-center gap-2`).
- **Realtime Loading**: Untuk data *real-time* atau *loading states*, gunakan indikator *pulse* kecil atau tombol *spin* dengan transparansi lembut.
- **Glassmorphism**: Digunakan di *backdrop overlay modal* (`bg-slate-900/50 backdrop-blur-sm`).

## 5. Ringkasan Pengingat

1. Hindari penggunaan warna bawaan browser atau Tailwind dasar yang terlalu "mentah" tanpa kustomisasi palet *hex* (contoh: lebih baik pakai `#111827` ketimbang `text-black`).
2. Pastikan `margin` dan `padding` konsisten (`p-5`, `gap-4`, `space-y-6`).
3. Selalu manfaatkan Alpine.js (via `x-data`) untuk *state* ringan di UI seperti buka tutup *dropdown*, *modal*, atau *tab* konten agar tidak perlu menulis kode JS *Vanilla* yang rumit.
