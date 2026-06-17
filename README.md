# Hevafsid ERP System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Filament-3.x-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
</p>

<p align="center">
  <strong>Enterprise Resource Planning System untuk Toko Fashion & Hijab</strong><br/>
  Sistem manajemen terpadu untuk inventori, keuangan, penjualan, dan pemasok.
</p>

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Prasyarat](#prasyarat)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Direktori](#struktur-direktori)
- [Akun Default](#akun-default)
- [API Reference](#api-reference)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

---

## 🎯 Tentang Proyek

**Hevafsid ERP** adalah sistem manajemen sumber daya perusahaan (ERP) yang dirancang khusus untuk kebutuhan bisnis fashion dan hijab. Sistem ini mengintegrasikan seluruh proses bisnis mulai dari manajemen inventori, pencatatan transaksi penjualan, pengelolaan pemasok, hingga laporan keuangan — dalam satu platform terpadu berbasis web.

---

## ✨ Fitur Utama

### 📦 Manajemen Inventori
- CRUD produk dengan kategori, SKU, dan atribut (ukuran, warna, motif)
- Tracking stok real-time dengan alert stok minimum
- Manajemen varian produk (size & color matrix)
- Riwayat mutasi stok (masuk, keluar, adjustment)
- Barcode/QR Code generation per produk

### 💰 Manajemen Keuangan & Akuntansi
- Pencatatan jurnal umum (debit/kredit)
- Laporan laba rugi otomatis
- Neraca keuangan per periode
- Cash flow tracking
- Rekonsiliasi keuangan

### 🛒 Manajemen Penjualan
- Point of Sale (POS) sederhana
- CRUD transaksi penjualan
- Manajemen retur penjualan
- Invoice & receipt otomatis (PDF)
- Diskon & promo management

### 🏭 Manajemen Supplier
- Database pemasok lengkap
- Purchase Order (PO) management
- Tracking pengiriman & penerimaan barang
- Hutang dagang ke supplier
- Evaluasi performa supplier

### 📊 Laporan & Dashboard
- Dashboard real-time dengan KPI utama
- Grafik penjualan (harian, mingguan, bulanan)
- Laporan stok & valuasi inventori
- Laporan keuangan (P&L, Neraca, Cash Flow)
- Export laporan ke PDF & Excel

### 👥 Multi-user & Role Management
- Role-based access control (RBAC)
- Role: Super Admin, Admin, Kasir, Gudang, Akuntan
- Audit log setiap aktivitas user
- Manajemen sesi & keamanan login

---

## 🛠 Tech Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| **Backend Framework** | Laravel | 12.x |
| **Language** | PHP | 8.3 |
| **Database** | MySQL | 8.0 |
| **Admin Panel** | Filament PHP | 3.x |
| **CSS Framework** | Tailwind CSS | 3.x |
| **JS Framework** | Alpine.js | 3.x |
| **Build Tool** | Vite | 5.x |
| **Auth** | Laravel Sanctum | - |
| **Queue** | Laravel Queue (DB) | - |
| **Cache** | Laravel Cache (File/Redis) | - |

---

## 🏗 Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                    CLIENT LAYER                      │
│          Browser (Filament + Alpine.js)              │
└────────────────────┬────────────────────────────────┘
                     │ HTTP/HTTPS
┌────────────────────▼────────────────────────────────┐
│                 PRESENTATION LAYER                   │
│     Filament Admin Panel  │  Blade Views + Vite      │
└────────────────────┬────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────┐
│                 APPLICATION LAYER                    │
│  Controllers │ Services │ Form Requests │ Policies   │
└────────────────────┬────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────┐
│                  DOMAIN LAYER                        │
│   Models (Eloquent) │ Repositories │ Events/Jobs     │
└────────────────────┬────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────┐
│               INFRASTRUCTURE LAYER                   │
│        MySQL 8.0  │  File Storage  │  Queue/Cache    │
└─────────────────────────────────────────────────────┘
```

---

## ⚙️ Prasyarat

Pastikan sistem kamu memiliki:

- **PHP** >= 8.3 dengan ekstensi: `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PCRE`, `PDO`, `Tokenizer`, `XML`
- **Composer** >= 2.x
- **Node.js** >= 20.x & **NPM** >= 10.x
- **MySQL** >= 8.0
- **Git**

> 💡 **Windows users:** Gunakan XAMPP 8.3+ atau Laragon untuk local development.

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/your-username/hevafsid-erp.git
cd hevafsid-erp
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` sesuai konfigurasi database lokal kamu (lihat bagian [Konfigurasi Environment](#konfigurasi-environment)).

Lalu jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

### 6. Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

---

## 🔧 Konfigurasi Environment

Salin `.env.example` ke `.env` lalu sesuaikan nilai berikut:

```env
APP_NAME="Hevafsid ERP"
APP_ENV=local
APP_KEY=             # Di-generate otomatis via php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hevafsid_erp
DB_USERNAME=root
DB_PASSWORD=

# Mail (untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@hevafsid.com"
MAIL_FROM_NAME="${APP_NAME}"

# Filesystem
FILESYSTEM_DISK=local
```

---

## ▶️ Menjalankan Aplikasi

```bash
# Jalankan development server
php artisan serve

# Di terminal lain, jalankan Vite (hot reload)
npm run dev
```

Akses aplikasi di: **http://localhost:8000**

Akses Admin Panel di: **http://localhost:8000/admin**

---

## 📁 Struktur Direktori

```
hevafsid-erp/
├── app/
│   ├── Filament/
│   │   ├── Resources/          # Filament Resource (CRUD pages)
│   │   └── Widgets/            # Dashboard widgets
│   ├── Http/
│   │   ├── Controllers/        # API & Web Controllers
│   │   └── Requests/           # Form Request validation
│   ├── Models/                 # Eloquent Models
│   ├── Services/               # Business logic layer
│   └── Policies/               # Authorization policies
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── css/                    # Tailwind CSS
│   ├── js/                     # Alpine.js & Vite entry
│   └── views/                  # Blade templates
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
│   └── app/public/             # Uploaded files
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 👤 Akun Default

Setelah menjalankan `php artisan migrate --seed`, akun berikut tersedia:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@hevafsid.com | password |
| Admin | admin@hevafsid.com | password |
| Kasir | kasir@hevafsid.com | password |
| Gudang | gudang@hevafsid.com | password |
| Akuntan | akuntan@hevafsid.com | password |

> ⚠️ **Penting:** Segera ganti password default setelah login pertama di production!

---

## 📡 API Reference

Dokumentasi API tersedia di [API_DOCS.md](./API_DOCS.md).

Base URL: `http://localhost:8000/api/v1`

Authentication menggunakan **Bearer Token** (Laravel Sanctum).

---

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch fitur: `git checkout -b feature/nama-fitur`
3. Commit perubahan: `git commit -m 'feat: tambah fitur X'`
4. Push ke branch: `git push origin feature/nama-fitur`
5. Buat Pull Request

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  Dibuat dengan ❤️ untuk bisnis fashion Indonesia Hevafsid
</p>
