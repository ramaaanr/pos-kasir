# 📋 Kasir FIFO - Rekap Teknis & Alur Bisnis

> **POS (Point of Sale) System** berbasis Laravel + Livewire dengan manajemen stok metode **FIFO (First In, First Out)**\
> Terakhir diupdate: 14 Maret 2026

---

## 🏗️ Arsitektur Teknis

### Stack Teknologi

| Layer | Teknologi |
|---|---|
| **Backend** | PHP 8.3, Laravel 11 |
| **Realtime UI** | Livewire 3 + Alpine.js |
| **Styling** | Tailwind CSS + Custom Design System |
| **Database** | MySQL 9.0 |
| **Icons** | Lucide Icons (via blade-lucide-icons) |
| **Auth & Roles** | Spatie Permission |
| **Container** | Docker (PHP-FPM + Nginx + MySQL) |
| **Dev Server** | `php artisan serve` + Vite (HMR) |

### Infrastruktur Docker

```
┌──────────────────────────────────────────────────┐
│                 docker-compose.yml               │
│                                                  │
│  ┌──────────────┐  ┌─────────┐  ┌────────────┐  │
│  │ kasir-fifo-  │  │ kasir-  │  │ kasir-fifo-│  │
│  │   app        │  │ fifo-   │  │   nginx    │  │
│  │ (PHP 8.3)    │  │  db     │  │ (Alpine)   │  │
│  │ :8000/:5173  │  │ (MySQL) │  │   :8888    │  │
│  │              │  │ :3307   │  │            │  │
│  └──────────────┘  └─────────┘  └────────────┘  │
│         ↕              ↕              ↕          │
│       kasir-network (bridge)                     │
└──────────────────────────────────────────────────┘
```

### Struktur Folder Utama

```
kasir-fifo/
├── app/                          # Laravel Application Root
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/      # ProductController, ProductBatchController, DashboardController
│   │   │   └── Middleware/
│   │   │       ├── AdminSessionGuard.php    # Proteksi route admin via session
│   │   │       ├── AutoLoginKasir.php       # Auto-login kasir tanpa login manual
│   │   │       └── CheckRole.php            # Role-based access
│   │   ├── Livewire/
│   │   │   ├── Admin/            # AdminDashboard, ReportPage, SalesPage, UserManagement, BackupCard
│   │   │   ├── Auth/             # AdminAccessModal (password modal)
│   │   │   ├── Kasir/            # KasirDashboard, PosPage, DebtPage
│   │   │   ├── Product/          # ProductList, ProductBatchList
│   │   │   ├── ProductCategory/  # CategoryList
│   │   │   └── StockAdjustment/  # StockAdjustmentList
│   │   ├── Models/               # 19 Eloquent Models
│   │   └── Services/             # 9 Service Classes (business logic layer)
│   ├── database/migrations/      # 27 migration files
│   └── resources/views/
│       ├── components/           # Reusable Blade components (layouts, toast, header)
│       └── livewire/             # Livewire component views
├── docker-compose.yml
├── Dockerfile
├── docker-entrypoint.sh
└── nginx/default.conf
```

---

## 🔐 Sistem Autentikasi & Akses

### Arsitektur Dual-Mode (Tanpa Login Page)

```
Pengguna Buka Aplikasi (/)
        │
        ▼
  ┌─────────────┐
  │  Auto-Login  │ ← Middleware: AutoLoginKasir
  │  sbg Kasir   │   (email: kasir@pos.com)
  └──────┬──────┘
         │
         ▼
  ┌──────────────────┐
  │   Layar Kasir    │  ← Route: /kasir/dashboard, /transaksi, /kasir/hutang
  │  (Publik/Kasir)  │
  └──────┬───────────┘
         │
    [Klik "Admin Panel"]
         │
         ▼
  ┌──────────────────┐
  │  Password Modal  │ ← Livewire: AdminAccessModal
  │  (KasirPos2026)  │
  └──────┬───────────┘
         │ ✅ Benar
         ▼
  ┌──────────────────┐
  │   Admin Panel    │ ← Middleware: AdminSessionGuard (session-based)
  │  (Protected)     │   Route: /dashboard, /products, /stok-masuk, dll.
  └──────────────────┘
```

### Role System (Spatie Permission)

| Role | Akses |
|---|---|
| **kasir** | Halaman POS, Dashboard Kasir, Pembayaran Hutang |
| **admin** | Semua fitur admin: Produk, Kategori, Stok Masuk, Stock Adjustment, Laporan, Penjualan |
| **owner** | Semua akses admin + Manajemen User |

---

## 📦 Domain Model (ERD)

### Entity Relationships

```
┌──────────────────┐     ┌─────────────────────┐
│  ProductCategory │     │       Product        │
│──────────────────│     │─────────────────────│
│  id              │◄────│  category_id         │
│  name            │     │  kode_produk (barcode)│
│  is_active       │     │  nama                │
└──────────────────┘     │  base_unit (Pcs)     │
                         │  harga_beli_default  │
┌──────────────────┐     │  harga_jual_default  │
│   ProductUnit    │     │  is_active           │
│──────────────────│     │  last_sold_at        │
│  product_id      │────►└─────────────────────┘
│  label (Box/Pack)│              │
│  multiplier (12) │              │ 1:N
└──────────────────┘              ▼
                         ┌─────────────────────┐
                         │    ProductBatch      │ ← INTI FIFO
                         │─────────────────────│
                         │  product_id          │
                         │  input_unit_name     │ ← NEW: Satuan saat input
                         │  qty_masuk_original  │ ← NEW: Qty asli (dlm unit input)
                         │  batch_code (BCH-*)  │
                         │  harga_beli_per_unit │
                         │  harga_jual_per_unit │
                         │  qty_masuk_base      │ ← Total dlm base unit (Pcs)
                         │  qty_sisa_base       │ ← Stok tersisa (FIFO tracker)
                         │  tanggal_masuk       │
                         └─────────────────────┘
                                  │
                                  │ Sale FIFO Deduction
                                  ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────────┐
│     Sale     │    │   SaleItem   │    │  SaleItemBatch   │
│──────────────│    │──────────────│    │──────────────────│
│ invoice_no   │◄───│ sale_id      │◄───│ sale_item_id     │
│ customer_id  │    │ product_id   │    │ product_batch_id │
│ user_id      │    │ qty_base     │    │ qty_base         │
│ total        │    │ unit_label   │    │ harga_beli/unit  │
│ total_paid   │    │ unit_multi   │    └──────────────────┘
│ payment_meth │    │ harga_jual   │         ↑ Rekam batch mana
│ status       │    │ subtotal     │         │ yg dipotong (audit)
│ cash_rcvd    │    └──────────────┘
│ cash_change  │
└──────────────┘
        │
        │ 1:1 (jika hutang)           ┌─────────────┐
        ▼                             │  Customer    │
┌──────────────┐    ┌──────────────┐  │─────────────│
│     Debt     │    │ DebtPayment  │  │ nama        │
│──────────────│    │──────────────│  │ no_hp       │
│ sale_id      │    │ debt_id      │  │ alamat      │
│ customer_id  │───►│ customer_id  │◄─│ total_debt  │
│ jaminan      │    │ user_id      │  └─────────────┘
│ amount       │    │ amount       │
│ status       │    │ payment_meth │
└──────────────┘    │ paid_at      │
                    └──────────────┘
```

### Tabel Pendukung

| Tabel | Fungsi |
|---|---|
| `product_logs` | Audit trail perubahan master produk |
| `product_category_logs` | Audit trail perubahan kategori |
| `product_batch_logs` | Audit trail perubahan stok batch (create, sale, adjustment, update) |
| `stock_adjustments` | Header stock opname/koreksi |
| `stock_adjustment_items` | Detail item yang dikoreksi |
| `shift_reports` | Laporan shift kasir (buka/tutup kas) |
| `sale_payments` | Catatan individual pembayaran |
| `backup_histories` | Riwayat backup database |

---

## 🔄 Alur Bisnis Utama

### 1. 📥 Alur Stok Masuk (Procurement)

```
┌─────────────────────────────────────────────────────────┐
│  ADMIN: Halaman Stok Masuk (/stok-masuk)                │
│                                                          │
│  1. Cari produk (ketik nama/barcode, min 3 karakter)     │
│     ├── Produk ditemukan → Pilih dari dropdown           │
│     └── Tidak ditemukan → "Tambah Master Baru" (Quick)   │
│                                                          │
│  2. Isi detail batch:                                    │
│     ├── Batch Code: Auto-generate (BCH-YYYYMMDD-001)     │
│     ├── Tanggal Masuk                                    │
│     ├── Harga Beli (per base unit)                       │
│     ├── Margin (nominal Rp)                              │
│     ├── Harga Jual = Harga Beli + Margin                 │
│     ├── Kuantitas Masuk                                  │
│     └── Satuan: Base Unit (Pcs) / Unit Lain (Pack, Box)  │
│              ↓                                           │
│         Konversi ke base unit:                           │
│         qty_masuk_base = qty_input × multiplier          │
│         Simpan: input_unit_name + qty_masuk_original     │
│                                                          │
│  3. Jika harga berbeda dari Master:                      │
│     └── Opsi: "Update Harga Master" (dengan konfirmasi)  │
│                                                          │
│  4. Simpan → ProductBatch created + Log dicatat          │
└─────────────────────────────────────────────────────────┘
```

**Contoh Konversi Satuan:**
```
Produk: Aqua 600ml
├── Base Unit: Pcs
├── Unit Tambahan: Pack (multiplier: 12), Dus (multiplier: 48)
│
│ Input: 2 Dus
│ ├── qty_masuk_original = 2
│ ├── input_unit_name = "Dus"
│ └── qty_masuk_base = 2 × 48 = 96 Pcs
│
│ Tampilan di Tabel Stok Masuk:
│ │ Unit Masuk │ Stok (Sisa/Masuk) │
│ │   2 Dus    │   96 dari 96 Pcs  │
```

### 2. 🛒 Alur Penjualan (Transaksi POS)

```
┌──────────────────────────────────────────────────────────┐
│  KASIR: Halaman Transaksi (/transaksi)                   │
│                                                           │
│  1. DRAFT SALE (Keranjang)                                │
│     ├── Scan barcode / cari produk                        │
│     ├── addToCart() → Tambah ke SaleItem                  │
│     ├── Bisa ubah qty, ganti satuan (Pcs → Pack)          │
│     └── Harga otomatis dari harga_jual_default produk     │
│                                                           │
│  2. FINALIZE (Checkout)                                   │
│     ├── Step 1: validateSale()                            │
│     │   └── Cek stok setiap item mencukupi                │
│     │                                                     │
│     ├── Step 2: processStockFIFO() ← INTI SISTEM          │
│     │   │                                                 │
│     │   │  Untuk setiap SaleItem:                         │
│     │   │  ├── Ambil batches (ORDER BY tanggal_masuk ASC) │
│     │   │  ├── Loop: potong qty_sisa dari batch terlama   │
│     │   │  ├── Catat di SaleItemBatch (audit trail)       │
│     │   │  └── Log di ProductBatchLog (action: 'sale')    │
│     │   │                                                 │
│     │   │  Contoh FIFO:                                   │
│     │   │  Jual 10 Pcs Aqua                               │
│     │   │  ├── Batch A (masuk 1 Jan, sisa 6) → potong 6  │
│     │   │  └── Batch B (masuk 5 Jan, sisa 20) → potong 4 │
│     │   │      Sisa Batch B = 16                          │
│     │   │                                                 │
│     │   └── Update last_sold_at pada Product              │
│     │                                                     │
│     └── Step 3: finalizePayment()                         │
│         ├── Tunai: Cash received → cash change → completed│
│         └── Hutang: Buat Customer + Debt + DebtPayment    │
│                     (bisa bayar sebagian/partial)          │
└──────────────────────────────────────────────────────────┘
```

### 3. 💰 Alur Pembayaran Hutang

```
┌──────────────────────────────────────────────────────────┐
│  KASIR: Halaman Hutang (/kasir/hutang)                   │
│                                                           │
│  1. Cari customer (nama/no HP)                            │
│  2. Lihat daftar hutang aktif (OPEN / PARTIAL)            │
│  3. Bayar:                                                │
│     ├── Input nominal bayar                               │
│     ├── DebtPayment created                               │
│     ├── Customer.total_debt dikurangi                     │
│     └── Status Debt:                                      │
│         ├── Lunas → PAID                                  │
│         └── Belum lunas → PARTIAL                         │
└──────────────────────────────────────────────────────────┘
```

### 4. 📊 Alur Stock Adjustment

```
┌──────────────────────────────────────────────────────────┐
│  ADMIN: Stock Adjustment (/stock-adjustments)            │
│                                                           │
│  Digunakan untuk:                                         │
│  ├── Barang rusak/expired                                 │
│  ├── Stok opname (koreksi selisih)                        │
│  └── Pengembalian barang                                  │
│                                                           │
│  Alur:                                                    │
│  1. Pilih batch tertentu                                  │
│  2. Input qty perubahan (+/-)                             │
│  3. Isi alasan                                            │
│  4. qty_sisa_base di batch diupdate                       │
│  5. Dicatat di ProductBatchLog (action: 'adjustment')     │
└──────────────────────────────────────────────────────────┘
```

### 5. 🏪 Alur Shift Kasir

```
┌──────────────────────────────────────────────────────────┐
│  KASIR: Dashboard (/kasir/dashboard)                     │
│                                                           │
│  Buka Shift:                                              │
│  1. Input modal awal (opening_cash)                       │
│  2. Mulai transaksi sepanjang hari                        │
│                                                           │
│  Tutup Shift:                                             │
│  1. Input uang di laci (cash_in_drawer)                   │
│  2. Sistem hitung:                                        │
│     ├── Expected = opening_cash + total cash sales         │
│     └── Difference = cash_in_drawer - expected            │
│  3. ShiftReport disimpan                                  │
└──────────────────────────────────────────────────────────┘
```

---

## 📐 Aturan Bisnis Kritis

### FIFO (First In, First Out)

| Aturan | Implementasi |
|---|---|
| Stok terlama dijual duluan | `ORDER BY tanggal_masuk ASC` di `processStockFIFO()` |
| Batch yang sudah terjual tidak bisa diedit/dihapus | `qty_sisa < qty_masuk` → **Locked (FIFO)** |
| Setiap penjualan dicatat per-batch | `SaleItemBatch` table (sale_item ↔ batch mapping) |
| Audit trail lengkap | `ProductBatchLog` mencatat setiap perubahan qty |

### Konversi Satuan

| Konsep | Detail |
|---|---|
| **Base Unit** | Satuan terkecil (selalu Pcs) → semua perhitungan internal |
| **Multi Unit** | `ProductUnit` dengan `multiplier` (Pack=12, Dus=48) |
| **Input Stok** | Bisa input dalam unit apapun, auto-convert ke base |
| **Penjualan** | Kasir bisa pilih unit, auto-convert ke base saat checkout |

### Harga

| Konsep | Detail |
|---|---|
| **Harga Master** | Default di `Product.harga_beli_default` / `harga_jual_default` |
| **Harga Batch** | Setiap batch bisa punya harga berbeda (fleksibel per pengiriman) |
| **Update Master** | Dari form Stok Masuk, ada opsi update harga master jika beda |

---

## 🖥️ Peta Halaman & Routing

### Kasir (Public - Auto Login)

| Route | Component | Fungsi |
|---|---|---|
| `/` | Redirect | → `/kasir/dashboard` |
| `/kasir/dashboard` | `KasirDashboard` | Dashboard kasir, buka/tutup shift, ringkasan hari |
| `/transaksi` | `PosPage` | Layar transaksi POS (scan, keranjang, bayar) |
| `/kasir/hutang` | `DebtPage` | Pembayaran hutang pelanggan |

### Admin (Protected - Password Modal)

| Route | Component | Fungsi |
|---|---|---|
| `/dashboard` | `DashboardController` → `AdminDashboard` | Dashboard admin, statistik bulanan |
| `/product-categories` | `CategoryList` | CRUD Kategori Produk |
| `/products` | `ProductList` | CRUD Master Produk + Barcode |
| `/stok-masuk` | `ProductBatchList` | Input stok masuk (batch), quick product |
| `/stock-adjustments` | `StockAdjustmentList` | Koreksi stok (opname) |
| `/laporan` | `ReportPage` | Laporan penjualan, profit, stok, shift |
| `/sales` | `SalesPage` | Data transaksi penjualan |
| `/manajemen-user` | `UserManagement` | Kelola user (Owner only) |

---

## 🧩 Service Layer

| Service | Tanggung Jawab |
|---|---|
| `SaleService` | addToCart, updateItemQty, updateItemUnit, finalizeSale (FIFO), cancelSale |
| `ProductBatchService` | createBatch (+ unit conversion), updateBatch (FIFO lock), deleteBatch, generateBatchCode |
| `ProductService` | CRUD master produk, unit management, barcode generation |
| `ProductCategoryService` | CRUD kategori, find-or-create logic |
| `StockAdjustmentService` | Stock opname, koreksi qty batch |
| `ReportService` | Statistik bulanan, dashboard data, laporan shift |
| `DebtPaymentService` | Proses pembayaran hutang, update saldo customer |
| `PrinterService` | Generate format struk/invoice untuk print |
| `BackupService` | Backup database MySQL (mysqldump) |

---

## 📝 Log & Audit Trail

Sistem mencatat **semua perubahan penting** untuk transparansi:

```
ProductBatchLog   → Setiap perubahan stok batch (created, sale, adjustment, updated)
ProductLog        → Setiap perubahan master produk (harga, nama, status)
ProductCategoryLog → Setiap perubahan kategori (create, update, delete)
SaleItemBatch     → Mapping batch mana yang dipotong per item penjualan (FIFO audit)
DebtPayment       → Riwayat pembayaran hutang per customer
ShiftReport       → Laporan buka/tutup shift kasir
BackupHistory     → Riwayat backup database
```

---

## 🔑 Ringkasan Fitur

| # | Fitur | Status |
|---|---|---|
| 1 | POS Transaction (scan, cart, checkout) | ✅ |
| 2 | FIFO Stock Deduction | ✅ |
| 3 | Multi-Unit Support (Pcs, Pack, Box, Dus) | ✅ |
| 4 | Cash & Debt Payment | ✅ |
| 5 | Debt Management & Payment | ✅ |
| 6 | Stock Intake (Batch-based) | ✅ |
| 7 | Stock Adjustment / Opname | ✅ |
| 8 | Input Unit Tracking (saat stok masuk) | ✅ |
| 9 | Category Find-or-Create | ✅ |
| 10 | Quick Product Creation (dari Stok Masuk) | ✅ |
| 11 | Master Price Update (dari batch form) | ✅ |
| 12 | Shift Report (Buka/Tutup Kas) | ✅ |
| 13 | Sales & Profit Reporting | ✅ |
| 14 | Barcode Generation & Print | ✅ |
| 15 | User Management (Owner) | ✅ |
| 16 | Database Backup | ✅ |
| 17 | Full Audit Trail | ✅ |
| 18 | Admin Password Guard (Session-based) | ✅ |
| 19 | Auto-Login Kasir (No login page) | ✅ |
| 20 | Toast Notification System (Success/Error/Info) | ✅ |
