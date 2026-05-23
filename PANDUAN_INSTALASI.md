# PANDUAN INSTALASI & PENGGUNAAN
# PinkStock ERP — Sistem Manajemen Inventaris & Keuangan

---

## DAFTAR ISI

1. [Yang Perlu Didownload](#1-yang-perlu-didownload)
2. [Langkah Instalasi](#2-langkah-instalasi)
3. [Menjalankan Aplikasi](#3-menjalankan-aplikasi)
4. [Akun Login Demo](#4-akun-login-demo)
5. [Alur & Penjelasan Fitur Aplikasi](#5-alur--penjelasan-fitur-aplikasi)

---

## 1. YANG PERLU DIDOWNLOAD

Sebelum memulai, pastikan sudah menginstall semua perangkat lunak berikut:

### A. XAMPP
- **Download:** https://www.apachefriends.org/download.html
- Pilih versi **PHP 8.2** atau lebih baru
- XAMPP menyediakan **PHP** dan **MySQL** sekaligus dalam satu paket
- Ikuti wizard instalasi hingga selesai

### B. Composer
- **Download:** https://getcomposer.org/Composer-Setup.exe
- Composer adalah package manager untuk PHP
- Ikuti wizard instalasi, akan otomatis mendeteksi PHP dari XAMPP
- Setelah install, **restart komputer atau terminal** agar Composer terbaca

### C. File ZIP Proyek (PinkStock ERP)
- Extract isi ZIP ke folder mana saja, misalnya: `C:\pinkstock-erp\`

---

## 2. LANGKAH INSTALASI

### Langkah 1 — Jalankan XAMPP
1. Buka aplikasi **XAMPP Control Panel**
2. Klik **Start** pada **Apache**
3. Klik **Start** pada **MySQL**
4. Pastikan keduanya berstatus **Running** (hijau)

### Langkah 2 — Buat Database
1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik menu **"New"** di panel kiri
3. Isi nama database: `pinkstock_erp`
4. Pilih collation: `utf8mb4_unicode_ci`
5. Klik tombol **"Create"**

### Langkah 3 — Buka Terminal / Command Prompt
1. Tekan `Windows + R`, ketik `cmd`, lalu Enter
2. Masuk ke folder proyek dengan perintah:
   ```
   cd C:\pinkstock-erp
   ```
   *(sesuaikan dengan lokasi folder Anda)*

### Langkah 4 — Install Dependensi PHP
Jalankan perintah berikut di terminal:
```
composer install
```
Proses ini akan mengunduh semua library yang dibutuhkan. Tunggu hingga selesai (beberapa menit tergantung koneksi internet).

### Langkah 5 — Konfigurasi File Environment
1. Salin file `.env.example` menjadi `.env`:
   ```
   copy .env.example .env
   ```
2. Buka file `.env` dengan Notepad, pastikan bagian database seperti ini:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pinkstock_erp
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Jika MySQL Anda memiliki password, isi di bagian DB_PASSWORD)*

3. Generate application key:
   ```
   php artisan key:generate
   ```

### Langkah 6 — Setup Database (Migrasi & Data Awal)
Jalankan perintah berikut untuk membuat semua tabel dan mengisi data awal:
```
php artisan migrate --seed
```
Perintah ini akan membuat:
- Semua tabel database secara otomatis
- 4 akun pengguna demo
- 6 kategori produk
- 2 supplier
- 6 produk contoh dengan data realistis
- Riwayat transaksi selama ±1 bulan

### Langkah 7 — Storage Link
```
php artisan storage:link
```

---

## 3. MENJALANKAN APLIKASI

Setiap kali ingin menggunakan aplikasi, lakukan langkah berikut:

1. Buka **XAMPP Control Panel**, Start **Apache** dan **MySQL**
2. Buka terminal, masuk ke folder proyek:
   ```
   cd C:\pinkstock-erp
   ```
3. Jalankan server:
   ```
   php artisan serve
   ```
4. Buka browser dan akses:
   ```
   http://localhost:8000
   ```

> Untuk menghentikan server, tekan `Ctrl + C` di terminal.

---

## 4. AKUN LOGIN DEMO

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@pinkstock.id | password |
| Owner | owner@pinkstock.id | password |
| Admin Gudang | gudang@pinkstock.id | password |
| Admin Keuangan | keuangan@pinkstock.id | password |

---

## 5. ALUR & PENJELASAN FITUR APLIKASI

### Gambaran Umum

PinkStock ERP adalah sistem manajemen terpadu untuk toko fashion/hijab yang mencakup pengelolaan inventaris dengan metode **FIFO (First In, First Out)** dan pencatatan keuangan otomatis.

---

### ALUR UTAMA PENGGUNAAN

```
Login → Dashboard → Master Barang → Barang Masuk → Barang Keluar
                                                        ↓
                                               Kartu Persediaan
                                               Laporan Keuangan
                                               Laporan Stok
```

---

### PENJELASAN FITUR PER MENU

#### Dashboard
- Menampilkan ringkasan kondisi toko secara real-time
- Total stok, total produk, nilai inventaris
- Transaksi masuk/keluar terbaru
- Grafik penjualan dan arus kas
- Notifikasi produk yang stoknya menipis (low stock warning)
- **Filter Bulan & Tahun** — lihat data historis bulan manapun sejak pertama pakai

**Cara pakai filter:**
1. Di bagian atas dashboard terdapat dropdown **Bulan** dan **Tahun**
2. Pilih bulan dan tahun yang ingin dilihat, lalu klik **"Tampilkan"**
3. Semua kartu KPI, grafik, dan tabel transaksi akan menyesuaikan periode tersebut
4. Klik **"Hari Ini"** untuk kembali ke data bulan berjalan
5. Saat melihat data historis, muncul label kuning bertuliskan periode yang sedang dilihat

---

#### Master Barang
- Kelola data semua produk toko (tambah, ubah, hapus)
- Setiap produk otomatis mendapat **kode SKU unik**
- **Barcode** otomatis dibuat dari SKU — bisa dicetak sebagai label harga
- Cetak label barcode dalam jumlah banyak sekaligus
- Atur batas minimum stok per produk untuk notifikasi low stock
- **Hapus produk**: klik ikon tempat sampah di kolom aksi → muncul modal konfirmasi. Produk yang masih memiliki stok tidak dapat dihapus

---

#### Barang Masuk
Digunakan saat toko menerima kiriman produk dari supplier.

**Alur:**
1. Pilih produk (bisa scan barcode atau cari manual)
2. Input jumlah barang yang diterima dan harga beli per unit
3. Sistem otomatis membuat **Batch FIFO baru** untuk barang tersebut
4. Stok produk otomatis bertambah
5. Kartu persediaan otomatis tercatat
6. Jurnal keuangan otomatis dicatat sebagai **pengeluaran kas (pembelian)**

---

#### Barang Keluar
Digunakan saat toko menjual produk ke pelanggan.

**Alur:**
1. Pilih produk (bisa scan barcode atau cari manual)
2. Input jumlah barang yang dijual dan harga jual per unit
3. Sistem otomatis menggunakan stok **batch terlama terlebih dahulu (FIFO)**
4. **HPP (Harga Pokok Penjualan)** dihitung otomatis berdasarkan batch yang dipakai
5. Stok produk otomatis berkurang
6. Kartu persediaan otomatis tercatat
7. Jurnal keuangan otomatis dicatat sebagai **pemasukan kas (penjualan)**

---

#### Batch FIFO
- Melihat semua batch stok yang aktif maupun sudah habis
- Setiap batch menampilkan: tanggal masuk, harga beli, sisa stok
- Riwayat pemakaian per batch (digunakan di transaksi mana saja)
- Valuasi stok real-time berdasarkan harga beli per batch

> **Apa itu FIFO?** Metode FIFO (First In, First Out) berarti stok yang masuk lebih dulu akan dijual lebih dulu. Ini memastikan perhitungan HPP yang akurat dan mencegah barang terlalu lama di gudang.

---

#### Barang Reject
Digunakan untuk mencatat barang yang rusak, cacat, kadaluarsa, atau hilang.

- Input produk yang di-reject beserta alasannya
- Stok otomatis dikurangi dari batch yang sesuai
- Kerugian otomatis tercatat di jurnal keuangan

---

#### Kartu Persediaan
- Buku besar stok per produk (seperti buku kas, tapi untuk barang)
- Menampilkan semua mutasi: barang masuk, barang keluar, reject
- Saldo stok berjalan (running balance) di setiap baris transaksi
- Bisa filter per produk dan per periode

---

#### Kas & Keuangan
- Semua transaksi barang masuk/keluar/reject otomatis tercatat di sini
- Input manual untuk biaya operasional (listrik, gaji, dll)
- Laporan laba rugi bulanan otomatis
- Tabel bisa di-scroll ke kanan jika layar kecil

---

#### Laporan
Semua laporan bisa diexport ke **PDF** dan **Excel**:

| Laporan | Format | Isi |
|---------|--------|-----|
| Laporan Stok | PDF, Excel | Stok semua produk saat ini |
| Laporan Penjualan | PDF, Excel | Detail penjualan + HPP FIFO |
| Laporan FIFO Batch | PDF | Status semua batch stok |
| Laporan Reject | PDF | Rekap barang reject |
| Laporan Keuangan | PDF, Excel | Semua transaksi kas |
| Laporan Laba Rugi | PDF | Laba/rugi per bulan |

---

#### Manajemen Pengguna (Super Admin only)
- Tambah, ubah, hapus akun pengguna
- Atur role/jabatan masing-masing pengguna

---

### HAK AKSES PER ROLE

| Fitur | Super Admin | Admin Gudang | Admin Keuangan | Owner |
|-------|:-----------:|:------------:|:--------------:|:-----:|
| Dashboard | ✅ | ✅ | ✅ | ✅ |
| Master Barang | ✅ | ✅ | ❌ | ❌ |
| Barang Masuk/Keluar | ✅ | ✅ | ❌ | ❌ |
| Barang Reject | ✅ | ✅ | ❌ | ❌ |
| Batch FIFO | ✅ | ✅ | ❌ | ✅ |
| Kartu Persediaan | ✅ | ✅ | ✅ | ✅ |
| Kas & Keuangan | ✅ | ❌ | ✅ | ❌ |
| Laporan | ✅ | ❌ | ✅ | ✅ |
| Manajemen User | ✅ | ❌ | ❌ | ❌ |

---

### TAMPILAN & UI

#### Konfirmasi Hapus / Nonaktifkan
Setiap aksi hapus atau nonaktifkan akan memunculkan **dialog konfirmasi modern** (bukan popup bawaan browser). Dialog ini:
- Tampil di tengah layar dengan animasi halus
- Berwarna merah untuk aksi hapus permanen
- Berwarna kuning/amber untuk aksi nonaktifkan
- Ada tombol **"Batalkan"** (batal) dan tombol konfirmasi
- Bisa ditutup dengan menekan tombol **Escape** atau klik di luar kotak

#### Ikon Browser & Identitas Aplikasi
- Ikon tab browser (favicon) menampilkan logo **hevafsid**
- Sidebar menampilkan logo hevafsid dengan tampilan rapi dan indikator status online

#### Tabel Responsif
Tabel dengan banyak kolom (Barang Masuk, Barang Keluar, Kas & Keuangan) dapat **di-scroll ke kanan** pada layar kecil/mobile tanpa konten terpotong.

---

## TROUBLESHOOTING

**Masalah: Halaman tidak muncul / error setelah jalankan server**
- Pastikan XAMPP sudah Start Apache dan MySQL
- Pastikan tidak ada aplikasi lain yang menggunakan port 8000
- Coba jalankan: `php artisan cache:clear`

**Masalah: "composer" tidak dikenali di terminal**
- Restart komputer setelah install Composer
- Pastikan PHP XAMPP sudah di PATH sistem

**Masalah: Database error / tabel tidak ada**
- Pastikan database `pinkstock_erp` sudah dibuat di phpMyAdmin
- Jalankan ulang: `php artisan migrate --seed`

---

**Masalah: Tampilan lama masih muncul setelah update**
- Jalankan: `php artisan view:clear` untuk menghapus cache tampilan
- Kemudian: `php artisan cache:clear` untuk membersihkan semua cache

---

*PinkStock ERP v1.1 — Dibuat untuk manajemen toko fashion & hijab*
*Terakhir diperbarui: Mei 2026*