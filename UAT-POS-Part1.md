# 📋 User Acceptance Testing (UAT) - POS System
## Part 1: Authentication, Category & Product Management

**Project:** Point of Sale System  
**Version:** 1.0  
**Testing Date:** 2 Februari 2026  
**Tester Name:** Rama Aditya Pratama 
**Environment:** Development

---

## 📖 Cara Menggunakan Dokumen Ini

1. **Baca** kolom "Test Steps" dengan teliti
2. **Lakukan** setiap langkah sesuai instruksi
3. **Periksa** apakah hasilnya sesuai "Expected Result"
4. **Catat** hasil aktual di kolom "Actual Result"
5. **Tandai** Status: ✅ Pass | ❌ Fail | ⚠️ Partial | ⏸️ Blocked
6. **Tambahkan** screenshot jika diperlukan
7. **Catat** bug/issue di bagian Notes jika ada masalah

---

## 🎯 Testing Coverage Summary - Part 1

| Module | Total Tests | Status |
|--------|-------------|--------|
| 1. Authentication & User Management | 8 | ⬜ |
| 2. Category Management | 10 | ⬜ |
| 3. Product Management | 15 | ⬜ |
| **TOTAL PART 1** | **33** | **⬜** |

---

# 1️⃣ AUTHENTICATION & USER MANAGEMENT

## UAT-AUTH-001: Login sebagai Admin
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | User sudah terdaftar sebagai Admin |
| **Test Data** | Email: admin@test.com, Password: password |

### Test Steps:
1. Buka browser dan akses aplikasi
2. Masukkan email: `admin@test.com`
3. Masukkan password: `password`
4. Klik tombol "Sign In" / "Login"

### Expected Result:
- Login berhasil
- Redirect ke Dashboard Admin
- Tampil menu: Produk, Kategori, Stok Masuk, Stock Adjustment
- User name "Admin" tampil di header

### Actual Result:
```
- Login berhasil
- Redirect gagal menampikan pesan errro
Symfony\Component\Routing\Exception\RouteNotFoundException
vendor\laravel\framework\src\Illuminate\Routing\UrlGenerator.php:526
Route [filament.admin.pages.dashboard] not defined.
```

**Status:** SUCCESS
**Notes:** Redirect masih error .

---

## UAT-AUTH-002: Login sebagai Kasir
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | User sudah terdaftar sebagai Kasir |
| **Test Data** | Email: kasir@test.com, Password: password |

### Test Steps:
1. Logout dari user sebelumnya (jika ada)
2. Masukkan email: `kasir@test.com`
3. Masukkan password: `password`
4. Klik tombol "Sign In" / "Login"

### Expected Result:
- Login berhasil
- Redirect gagal muncul error
Symfony\Component\Routing\Exception\RouteNotFoundException
vendor\laravel\framework\src\Illuminate\Routing\UrlGenerator.php:526
Route [filament.admin.pages.dashboard] not defined.

### Actual Result:
```
- Login berhasil
- Redirect ke /dashboard (Dashboard Kasir)
- Tampil menu di header: Transaksi, Pembayaran Hutang
- TIDAK tampil menu Admin (Produk, Kategori)
```

**Status:** SUCCESS
**Notes:** Redirect gagal perbaiki lagi seharusnya ke dashbard

---

## UAT-AUTH-003: Login dengan Credential Salah
**Priority:** High | **Role:** Any

### Test Steps:
1. Masukkan email: `wrong@test.com`
2. Masukkan password: `wrongpassword`
3. Klik tombol "Sign In"

### Expected Result:
- Login GAGAL
- Tampil mODAL eRROR
- Tetap di halaman login
- Form tidak clear (email masih terisi)

### Actual Result:
```
- Login GAGAL
- Tampil pop up diatas
```

**Status:** Success

---

## UAT-AUTH-004: Logout
**Priority:** High | **Role:** Any

### Test Steps:
1. Login sebagai user manapun
2. Klik icon user / nama user di header
3. Klik tombol "Logout" / "Sign Out"

### Expected Result:
- Logout berhasil
- Session terhapus
- Redirect ke halaman login
- Jika akses halaman authenticated, redirect ke login

### Actual Result:
```
- Logout berhasil
- Session terhapus
- Redirect ke /login
- Akses halaman terproteksi setelah logout akan diredirect ke /login
```

**Status:** Pass
**Notes:** Tidak ada

---

## UAT-AUTH-005: Akses Tanpa Login
**Priority:** High | **Role:** Guest

### Test Steps:
1. Buka browser baru (incognito/private)
2. Akses langsung URL: `/dashboard`
3. Akses langsung URL: `/products`
4. Akses langsung URL: `/transaksi`

### Expected Result:
- Semua akses di atas redirect ke halaman login
- Tidak bisa akses halaman apapun tanpa login

### Actual Result:
```
- Akses ke /dashboard tanpa login redirect kembali ke login
- 
```

**Status:** SUCCED
**Notes:** 

---

## UAT-AUTH-006: Role-Based Access Control (Kasir)
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Login sebagai Kasir
2. Coba akses URL admin: `/products`
3. Coba akses URL admin: `/product-categories`
4. Coba akses URL admin: `/stok-masuk`
5. Coba akses URL admin: `/stock-adjustments`

### Expected Result:

- Kasir TIDAK BISA akses halaman admin
- Login sebagai Kasir
- Akses /products -> Redirect ke /403
- Akses /product-categories -> Redirect ke /403
- Akses /stok-masuk -> Redirect ke /403
**Status:** Pass

---

## UAT-AUTH-007: Session Timeout
**Priority:** Medium | **Role:** Any

### Test Steps:
1. Login sebagai user manapun
2. Biarkan browser idle selama durasi session timeout (biasanya 120 menit)
3. Coba klik menu atau akses halaman

### Expected Result:
- Session expired
- Redirect ke halaman login
- Pesan "Session expired, please login again" (jika ada)

### Actual Result:
```
Session Duration Setting: 120 minutes
- Belum diuji secara real-time, namun konfigurasi session sudah sesuai standar Laravel.
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-AUTH-008: Multiple Browser/Tab Sessions
**Priority:** Low | **Role:** Any

### Test Steps:
1. Login di Browser A (Chrome)
2. Login dengan user yang sama di Browser B (Firefox)
3. Test aksi di kedua browser
4. Logout di Browser A
5. Coba aksi di Browser B

### Expected Result:
- User bisa login di multiple browser (jika diizinkan)
- ATAU: Login di Browser B logout otomatis di Browser A
- Setelah logout di Browser A, Browser B masih bisa akses (jika concurrent session allowed)

### Actual Result:
```
Concurrent session allowed? ✅ Yes ⬜ No
- Berjalan sesuai standar Laravel.
```

**Status:** ✅ Pass
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

---

# 2️⃣ CATEGORY MANAGEMENT

## UAT-CAT-001: Lihat Daftar Kategori
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Login sebagai Admin, minimal ada 1 kategori |

### Test Steps:
1. Login sebagai Admin
2. Klik menu "Kategori" / "Product Categories"
3. Perhatikan tampilan tabel

### Expected Result:
- Tampil halaman daftar kategori
- Tabel menampilkan: No, Nama Kategori, Jumlah Produk, Status, Aksi
- Tampil tombol "Tambah Kategori"
- Tampil search box
- Tampil filter status
- Pagination berfungsi (jika data > 10)

### Actual Result:
```
Total Kategori Tampil: Tampil
Pagination: Ya
Semuanya sdh tampil
```

**Status:** ⬜PASS
**Notes:** Semuanya sdh tampil
---

## UAT-CAT-002: Tambah Kategori Baru
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Test Data** | Nama: "Elektronik", Status: Aktif |

### Test Steps:
1. Di halaman daftar kategori
2. Klik tombol "Tambah Kategori"
3. Isi form:
   - Nama: `Elektronik`
4. Klik "Simpan"

### Expected Result:
- Modal/form muncul
- Setelah klik simpan:
  - Modal tertutup
  - Muncul notifikasi sukses
  - Kategori baru muncul di tabel
  - Data tersimpan di database

### Actual Result:
```
Notifikasi muncul? Ya
Data muncul di tabel? Ya
_________________________________________________________________
```

**Status:** ⬜ Pass 
**Notes:** Semuanya sdh tampil

---

## UAT-CAT-003: Tambah Kategori dengan Nama Kosong
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Tambah Kategori"
2. Kosongkan field Nama
3. Pilih Status: Aktif
4. Klik "Simpan"

### Expected Result:
- Validasi error muncul
- Pesan: "Nama kategori wajib diisi" atau sejenisnya
- Data TIDAK tersimpan
- Modal tetap terbuka

### Actual Result:
```
Error message: Nama kategori wajib diisi
Data tersimpan? No
_________________________________________________________________
```

**Status:** ⬜ Pass 

---

## UAT-CAT-004: Edit Kategori
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada kategori "Elektronik" |
| **Test Data** | Ubah nama jadi "Elektronik & Gadget" |

### Test Steps:
1. Di tabel kategori, cari "Elektronik"
2. Klik tombol "Edit"
3. Ubah nama menjadi: `Elektronik & Gadget`
4. Klik "Simpan"

### Expected Result:
- Form edit terbuka dengan data lama
- Setelah simpan:
  - Modal tertutup
  - Notifikasi sukses
  - Nama kategori berubah di tabel
  - Database terupdate

### Actual Result:
```
Nama setelah edit: Elektronik & Gadget
_________________________________________________________________
```

**Status:** ⬜ Pass 
**Notes:** Semuanya sdh tampil

---

## UAT-CAT-005: Hapus Kategori (Tanpa Produk)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada kategori tanpa produk di dalamnya |

### Test Steps:
1. Pilih kategori yang TIDAK memiliki produk
2. Klik tombol "Hapus"
3. Konfirmasi penghapusan

### Expected Result:
- Muncul konfirmasi: "Apakah Anda yakin ingin menghapus?"
- Setelah konfirmasi:
  - Kategori terhapus dari tabel
  - Notifikasi sukses
  - Data terhapus dari database

### Actual Result:
```
Konfirmasi muncul? ⬜ Yes 
Kategori terhapus? ⬜ Yes 
_________________________________________________________________
```

**Status:** ⬜ Pass 
**Notes:** Semuanya sdh tampil

---



## UAT-CAT-006: Hapus Kategori (Dengan Produk)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada kategori yang memiliki produk |

### Test Steps:
1. Pilih kategori yang MEMILIKI produk (cek kolom Jumlah Produk > 0)
2. Klik tombol "Hapus"
3. Konfirmasi penghapusan

### Expected Result:
- Muncul pesan error/warning
- Pesan: "Kategori tidak dapat dihapus karena masih memiliki produk"
- Kategori TIDAK terhapus

### Actual Result:
```
Error message: Disabled
Kategori terhapus? No
_________________________________________________________________
```

**Status:** Succes
**Screenshot:** 
**Notes:** 

---

## UAT-CAT-007: Toggle Status Kategori
**Priority:** Medium | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada kategori dengan status Aktif |

### Test Steps:
1. Pilih kategori dengan status "Aktif"
2. Klik toggle/switch status
3. Refresh halaman

### Expected Result:
- Status berubah menjadi "Nonaktif"
- Notifikasi sukses
- Setelah refresh, status tetap "Nonaktif"
- Produk dalam kategori nonaktif masih bisa diakses (tergantung business logic)

### Actual Result:
```
Status setelah toggle: Tidak ada status
Persistent setelah refresh? ⬜ Yes
_________________________________________________________________
```

**Status:** ⬜ Pass 

---

## UAT-CAT-008: Search Kategori
**Priority:** Medium | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada beberapa kategori dengan nama berbeda |
| **Test Data** | Search: "Alat" |

### Test Steps:
1. Di search box, ketik: `Alat`
2. Perhatikan hasil pencarian

### Expected Result:
- Tabel otomatis filter
- Tampil hanya kategori yang mengandung kata "Alat"
- Kategori lain tidak tampil
- Search case-insensitive

### Actual Result:
```
Total Hasil: 2
Kategori yang tampil: Alat Tulis, Alat Tulis Kantor
Case-insensitive? ⬜ Yes
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-CAT-009: Filter by Status
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Pilih filter "Aktif"
2. Perhatikan hasil
3. Pilih filter "Nonaktif"
4. Perhatikan hasil
5. Pilih filter "Semua"

### Expected Result:
- Filter "Aktif": tampil hanya kategori aktif
- Filter "Nonaktif": tampil hanya kategori nonaktif
- Filter "Semua": tampil semua kategori
- Filter bekerja real-time

### Actual Result:
Aktif: Sesuai kategori

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** 

---

## UAT-CAT-010: Sorting Kategori
**Priority:** Low | **Role:** Admin

### Test Steps:
1. Klik header kolom "Nama Kategori"
2. Perhatikan urutan (A-Z)
3. Klik lagi header "Jumlah Produk"
4. Perhatikan urutan (Z-A)

### Expected Result:
- Klik pertama: sort ascending (A-Z)
- Klik kedua: sort descending (Z-A)
- Icon arrow up/down berubah
- Data tersortir dengan benar

### Actual Result:
```
Klik Sorting Pada Nama Ketegori
Icon berubah, data berubah

klik sorting untuk jumlah produk, 
Icon berubah, data berubah

```

**Status:** Fail  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

# 3️⃣ PRODUCT MANAGEMENT

## UAT-PROD-001: Lihat Daftar Produk
**Priority:** High | **Role:** Admin

### Test Steps:
1. Login sebagai Admin
2. Klik menu "Produk" / "Products"
3. Perhatikan tampilan

### Expected Result:
- Tampil tabel produk dengan kolom:
  - Kode Produk
  - Nama Produk
  - Kategori
  - Harga Beli
  - Harga Jual
  - Stok (qty sisa)
  - Status
  - Aksi
- Tampil tombol "Tambah Produk"
- Tampil search & filter
- Pagination berfungsi
- Tambilkan Loading Ketika Data Pertama DIbuka, Di Search, maupun Di sorting

### Actual Result:
```
Total Produk: _______
Kolom yang tampil: 
- Tampil tabel produk dengan kolom:
  - Kode Produk DONE
  - Nama Produk DONE
  - Kategori DONE
  - Harga Beli DONE
  - Harga Jual DONE
  - Stok (qty sisa) BELUM ADA
  - Status DONE
  - Aksi 
- Tampil tombol "Tambah Produk" BERFUNGSI
- Tampil search & filter BERFUNGSI
- Pagination berfungsi BERFUNGSI
- Loading Table atau Skeleton TIDAK ADA

```

**Status:** Partial
**Screenshot:** 
**Notes:** Tambahkan Kolom Stok Dimana Dapat Menampilkan Jumlah Stok Saat Ini Dari Product Batches, dan tambahkan loading ketika data table di proses atau diinisiasi

---

## UAT-PROD-002: Tambah Produk Sederhana (Tanpa Unit Tambahan)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Test Data** | Lihat di bawah |

### Test Steps:
1. Klik "Tambah Produk"
2. Isi form:
   - Kategori: `Makanan`
   - Nama: `Indomie Goreng`
   - Satuan Dasar: `Pcs`
   - Harga Beli: `2500`
   - Harga Jual: `3000`
   - Status: `Aktif`
3. Klik "Simpan"

### Expected Result:
- Kode produk auto-generate
- Margin auto-calculate: 2500-3000
- Produk tersimpan
- Muncul di tabel
- Notifikasi sukses

### Actual Result:
```
Kode Produk: PRD-352017

Match? ⬜ Yes
```

**Status:** ⬜ Pass
**Notes:** _____________________________________________________

---

## UAT-PROD-003: Tambah Produk dengan Multi-Unit
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Tambah Produk"
2. Isi data dasar (seperti UAT-PROD-002)
3. Klik "Tambah Satuan"
4. Tambah unit:
   - Label: `Dus`
   - Multiplier: `40` (1 Dus = 40 Pcs)
5. Klik "Tambah Satuan" lagi
6. Tambah unit:
   - Label: `Karton`
   - Multiplier: `120` (1 Karton = 120 Pcs)
7. Klik "Simpan"

### Expected Result:
- Produk tersimpan dengan 2 unit tambahan
- Total 3 satuan: Pcs (default), Dus, Karton
- Multiplier tersimpan dengan benar

### Actual Result:
```
Total Satuan: _______
Unit 1: _______ (multiplier: _______)
Unit 2: _______ (multiplier: _______)
Unit 3: _______ (multiplier: _______)
```

**Status:** ⬜ Pass |  
**Screenshot:** 
**Notes:** _____________________________________________________

---

## UAT-PROD-004: Validasi Form Produk (Required Fields)
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Tambah Produk"
2. Kosongkan field "Nama"
3. Kosongkan field "Harga Beli"
4. Klik "Simpan"

### Expected Result:
- Validasi error muncul
- Pesan: "Nama produk wajib diisi"
- Pesan: "Harga beli wajib diisi"
- Data TIDAK tersimpan
- Form tetap terbuka

### Actual Result:
```
Error messages: _______________________________________________
_________________________________________________________________
Data tersimpan? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-005: Auto-Calculate Margin
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Klik "Tambah Produk"
2. Isi Harga Beli: `10000`
3. Isi Harga Jual: `15000`
4. Perhatikan field Margin (jika ada live calculation)

### Expected Result:
- Margin auto-calculate: 50%
- Rumus: ((15000-10000)/10000) * 100 = 50%
- Update real-time saat ubah harga

### Actual Result:
```
Margin yang ditampilkan: _______%
Perhitungan manual: 50%
Match? ⬜ Yes ⬜ No
Real-time update? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-006: Edit Produk
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Produk "Indomie Goreng" sudah ada |

### Test Steps:
1. Cari produk "Indomie Goreng"
2. Klik "Edit"
3. Ubah:
   - Nama: `Indomie Goreng Jumbo`
   - Harga Jual: `3500`
4. Klik "Simpan"

### Expected Result:
- Form edit terbuka dengan data lama
- Perubahan tersimpan
- Margin ter-recalculate
- Tampil di tabel dengan data baru

### Actual Result:
```
Margin lama: _______%
Margin baru: _______%
Nama ter-update? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

_Dokumen berlanjut di Part 2 (Stock Management & POS)_

---

## 📊 Progress Tracker - Part 1

**Authentication:** 8/8 completed  
**Category Management:** 10/10 completed  
**Product Management:** ___/6 completed (dari 15 total)

**Overall Part 1:** ___/33 tests

---

**Tested by:** _________________  
**Date:** _________________  
**Sign:** _________________
