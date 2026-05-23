# PinkStock ERP — Setup Guide

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ & npm

---

## Installation Steps

### 1. Clone / Copy project

```bash
cd /your/webserver/htdocs
# copy or clone the pinkstock-erp folder here
cd pinkstock-erp
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_DATABASE=pinkstock_erp
DB_USERNAME=root
DB_PASSWORD=your_password

APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
```

### 4. Create database

```sql
CREATE DATABASE pinkstock_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations & seed

```bash
php artisan migrate
php artisan db:seed
```

This creates:
- 4 roles (super_admin, admin_gudang, admin_keuangan, owner)
- 4 demo users (see credentials below)
- 6 product categories (hijab segi empat, pashmina, instant, ciput, gamis, mukena)
- 2 suppliers
- 6 products with realistic hijab/fashion data
- ~1 month of incoming + outgoing transaction history

### 6. Storage link

```bash
php artisan storage:link
```

### 7. Install frontend assets

```bash
npm install
npm run build
# or for development:
npm run dev
```

### 8. Run the server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## Demo Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@pinkstock.id | password | Super Admin |
| owner@pinkstock.id | password | Owner |
| gudang@pinkstock.id | password | Admin Gudang |
| keuangan@pinkstock.id | password | Admin Keuangan |

---

## Module Summary

### Dashboard
- KPI cards: total produk, stok, unit masuk/keluar, pendapatan, omzet, laba kotor
- **Filter Bulan & Tahun** — lihat data historis bulan manapun sejak pertama pakai
- Grafik penjualan & arus kas (30 hari terakhir atau full bulan yang dipilih)
- Low stock warning & transaksi terbaru
- Label & grafik otomatis menyesuaikan periode yang dipilih

### Master Barang
- CRUD produk dengan auto SKU generation
- Auto barcode dari SKU
- Print label barcode (multiple copies)
- Low stock warning per produk
- **Hapus produk** — hanya bisa jika stok = 0, dengan konfirmasi modal

### Barang Masuk
- Scan barcode workflow
- Setiap transaksi masuk membuat **FIFO Batch baru**
- Otomatis update stok & kartu persediaan
- Otomatis post ke jurnal keuangan (cash out - pembelian)

### Barang Keluar
- Scan barcode workflow
- **FIFO otomatis**: stok batch terlama diambil dulu
- Kalkulasi HPP akurat per transaksi
- Detail batch mana yang digunakan (fifo_details table)
- Otomatis post pendapatan ke jurnal keuangan

### FIFO Batch
- Lihat semua batch aktif/habis
- History pemakaian per batch
- Valuasi stok real-time

### Barang Reject
- Catat kerusakan, cacat, kadaluarsa, hilang
- Otomatis kurangi stok & batch
- Otomatis catat kerugian ke jurnal keuangan

### Kartu Persediaan
- Ledger lengkap per produk
- Semua mutasi: masuk, keluar, reject
- Running balance otomatis

### Kas & Keuangan
- Jurnal kas masuk/keluar otomatis dari transaksi
- Input manual untuk biaya operasional
- Laporan laba rugi bulanan

### Laporan
- Laporan Stok (PDF, Excel)
- Laporan Penjualan dengan FIFO HPP (PDF, Excel)
- Laporan FIFO Batch (PDF)
- Laporan Reject (PDF)
- Laporan Keuangan (PDF, Excel)
- Laporan Laba Rugi (PDF)

### Role-Based Access
| Fitur | Super Admin | Admin Gudang | Admin Keuangan | Owner |
|-------|:-----------:|:------------:|:--------------:|:-----:|
| Dashboard | ✅ | ✅ | ✅ | ✅ |
| Master Barang | ✅ | ✅ | ❌ | ❌ |
| Barang Masuk/Keluar | ✅ | ✅ | ❌ | ❌ |
| Reject | ✅ | ✅ | ❌ | ❌ |
| Batch FIFO | ✅ | ✅ | ❌ | ✅ |
| Kartu Persediaan | ✅ | ✅ | ✅ | ✅ |
| Keuangan | ✅ | ❌ | ✅ | ❌ |
| Laporan | ✅ | ❌ | ✅ | ✅ |
| User Management | ✅ | ❌ | ❌ | ❌ |

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/          # Login, password reset
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── IncomingTransactionController.php
│   │   ├── OutgoingTransactionController.php
│   │   ├── BatchController.php
│   │   ├── RejectItemController.php
│   │   ├── InventoryCardController.php
│   │   ├── FinancialController.php
│   │   ├── ReportController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php     # role:super_admin,admin_gudang
│   └── Requests/                  # Form validation per module
├── Models/                        # 12 Eloquent models
├── Services/
│   ├── FifoService.php            # Core FIFO logic (semua operasi stok)
│   └── BarcodeService.php         # Barcode/QR generation
└── Exports/                       # Excel export classes

resources/views/
├── layouts/
│   ├── app.blade.php              # Master layout (favicon, metadata, confirm modal global)
│   ├── sidebar.blade.php          # Sidebar + brand logo
│   └── navbar.blade.php           # Top navbar
├── dashboard/index.blade.php      # Dashboard dengan filter periode
├── products/                      # Master barang
├── incoming/, outgoing/           # Transaksi (tabel responsif)
├── financial/index.blade.php      # Kas & keuangan (tabel responsif)
└── ...
```

---

## FIFO Algorithm (FifoService)

```
processOutgoing($product, $qty):
  1. Validate stock >= qty
  2. Create OutgoingTransaction
  3. SELECT batches WHERE status=active ORDER BY received_date ASC (LOCK FOR UPDATE)
  4. FOR each batch:
       take = MIN(remaining_need, batch.qty_remaining)
       Create FifoDetail (batch_id, qty_taken, cost_price, subtotal)
       batch.qty_remaining -= take
       if batch.qty_remaining == 0: batch.status = 'depleted'
  5. total_hpp = SUM(fifo_details.subtotal_hpp)
  6. gross_profit = total_revenue - total_hpp
  7. product.stock_total -= qty
  8. Append InventoryCard row
  9. Post FinancialTransaction (cash_in, sales)
  (All wrapped in DB::transaction)
```

---

## UI Components

### Global Confirm Modal
Semua aksi hapus/nonaktifkan menggunakan modal konfirmasi modern (bukan browser `confirm()`).
Dipanggil dengan JS:
```js
openConfirm('Judul', 'Pesan konfirmasi.', 'Teks Tombol', 'form-id', isDanger)
// isDanger: true = tombol merah (hapus), false = tombol amber (nonaktifkan)
```

### Responsive Tables
Tabel dengan banyak kolom (financial, incoming, outgoing) menggunakan `overflow-x-auto`
dengan `min-w-[...]` agar bisa scroll horizontal di layar kecil tanpa memotong konten.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| PHP | 8.2+ |
| Database | MySQL 8.0 |
| Frontend | Laravel Blade + Tailwind CSS (CDN) |
| JS Interactivity | Alpine.js 3.x |
| Charts | Chart.js |
| Barcode | picqer/php-barcode-generator |
| QR Code | simplesoftwareio/simple-qrcode |
| PDF Export | barryvdh/laravel-dompdf |
| Excel Export | maatwebsite/excel |
| Auth | Laravel Breeze (custom) |

---

## Useful Commands

```bash
php artisan serve          # Jalankan development server
php artisan migrate        # Jalankan migrasi database
php artisan db:seed        # Isi data awal
php artisan migrate --seed # Migrasi + seed sekaligus
php artisan view:clear     # Hapus cache Blade yang dikompilasi
php artisan cache:clear    # Hapus semua cache aplikasi
php artisan key:generate   # Generate APP_KEY baru
php artisan storage:link   # Buat symlink storage ke public
```

---

*PinkStock ERP v1.1 — Built for fashion/hijab store management*
*Last updated: Mei 2026*