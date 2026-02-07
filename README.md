# Point of Sale (POS) System

Sistem Manajemen Penjualan (POS) modern yang dibangun dengan Laravel dan Filament PHP, dirancang untuk efisiensi transaksi dan manajemen stok yang akurat.

## 🚀 Fitur Utama

### 🔐 Authentication & RBAC
- **Multi-Role System**: Dashboard khusus untuk **Admin** dan **Kasir**.
- **Security**: Proteksi route ketat dan halaman error 403 kustom.
- **Custom Login**: Interface login modern dengan skema warna yang elegan.

### 📦 Manajemen Produk & Kategori
- **Product Master**: Pengelolaan produk dengan dukungan barcode dan multi-satuan (Unit Conversion).
- **Category System**: Pengorganisasian produk berdasarkan kategori dengan fitur proteksi penghapusan (mencegah penghapusan kategori yang masih memiliki produk).
- **Stock Tracking**: Visualisasi stok real-time langsung di daftar produk.

### ⚖️ Manajemen Stok (FIFO)
- **Advanced FIFO Engine**: Pengurangan stok otomatis berdasarkan batch masuk (First-In, First-Out) dengan tracking internal yang mendetail (`sale_item_batches`).
- **Batch Management**: Pelacakan stok per batch dengan fitur interactive sorting, pencarian batch, dan log mutasi yang transparan.
- **Audit Trail**: Pencatatan riwayat perubahan stok lengkap dengan metadata perubahan (`before` vs `after`) dan alasan perubahan.
- **Unit Multiplier**: Konversi satuan otomatis (misal: Dus ke Pcs) yang presisi saat transaksi.

### 💰 Point of Sale (POS)
- **Fast Checkout**: Interface kasir yang responsif dengan dukungan barcode scanner dan auto-add product.
- **Payment Handling**: Sistem pembayaran tunai dengan validasi kembalian real-time dan format angka (thousand separator).
- **Invoice System**: Cetak struk belanja otomatis yang dioptimalkan untuk printer thermal 80mm, menampilkan detail item sesuai unit yang dipilih.
- **Debt Management**: Manajemen piutang pelanggan dengan pencatatan jaminan dan sistem pembayaran cicilan.

## 🛠️ Tech Stack
- **Framework**: [Laravel 11/12](https://laravel.com)
- **Admin Panel**: [Filament PHP v3](https://filamentphp.com)
- **Interactive UI**: [Livewire v3](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev)
- **Styling**: [Tailwind CSS](https://tailwindcss.com)
- **Database**: MySQL / MariaDB

## ⚙️ Instalasi

1. **Clone repositori**
   ```bash
   git clone [url-repo]
   cd project
   ```

2. **Install dependensi**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan pengaturan database di file `.env`.*

4. **Migrasi Database & Seeding**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   npm run dev
   ```

## 📝 UAT & Dokumentasi
Proyek ini melalui proses User Acceptance Testing (UAT) yang ketat:
- [UAT Index](UAT-INDEX.md)
- [UAT Part 1: Auth & Master Data](UAT-POS-Part1.md)
- [UAT Part 2: POS Core & Cash Payment](UAT-POS-Part2.md)
- [UAT Part 3: Advanced FIFO & Invoice Detail](UAT-POS-Part3.md)
- [UAT Bugs: Tracker Perbaikan Bug](UAT-BUGS.md)

## 📄 Lisensi
Sistem ini dikembangkan untuk kebutuhan internal/freelance. Seluruh hak cipta dilindungi.
