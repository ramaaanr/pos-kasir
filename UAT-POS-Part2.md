# 📋 User Acceptance Testing (UAT) - POS System
## Part 2: Product (Lanjutan), Stock Management & POS Transactions

**Project:** Point of Sale System  
**Version:** 1.0  
**Testing Date:** _________________  
**Tester Name:** _________________

---

## 🎯 Testing Coverage Summary - Part 2

| Module | Total Tests | Status |
|--------|-------------|--------|
| 3. Product Management (lanjutan) | 9 | ⬜ |
| 4. Stock Management (Batches) | 12 | ⬜ |
| 5. Stock Adjustment | 8 | ⬜ |
| 6. POS Transactions - Cash | 10 | ⬜ |
| **TOTAL PART 2** | **39** | **⬜** |

---

# 3️⃣ PRODUCT MANAGEMENT (Lanjutan)

## UAT-PROD-007: Hapus Produk (Tanpa Transaksi)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada produk yang belum pernah ditransaksikan |

### Test Steps:
1. Pilih produk yang belum pernah ada transaksi
2. Klik "Hapus"
3. Konfirmasi

### Expected Result:
- Konfirmasi muncul
- Produk terhapus (soft delete)
- Tidak tampil di tabel produk aktif
- Data masih ada di database (deleted_at terisi)

### Actual Result:
```
Konfirmasi muncul? ⬜ Yes ⬜ No
Produk terhapus? ⬜ Yes ⬜ No
Masih ada di DB? ⬜ Yes ⬜ No (cek dengan admin tools)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-008: Hapus Produk (Sudah Ada Transaksi)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada produk yang sudah pernah ditransaksikan |

### Test Steps:
1. Pilih produk yang SUDAH ADA transaksi/batch
2. Klik "Hapus"
3. Perhatikan respon

### Expected Result:
- Muncul pesan error/warning
- Pesan: "Produk tidak dapat dihapus karena sudah memiliki transaksi"
- Produk TIDAK terhapus

### Actual Result:
```
Error message: ________________________________________________
Produk terhapus? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-009: Toggle Status Produk
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Pilih produk dengan status "Aktif"
2. Klik toggle status
3. Refresh halaman
4. Login sebagai Kasir dan coba search produk di POS

### Expected Result:
- Status berubah "Nonaktif"
- Tersimpan di database
- Produk nonaktif TIDAK MUNCUL di POS search
- Admin masih bisa lihat produk nonaktif dengan filter

### Actual Result:
```
Status berubah? ⬜ Yes ⬜ No
Muncul di POS? ⬜ Yes ⬜ No (harus No)
Admin bisa lihat? ⬜ Yes ⬜ No (harus Yes dengan filter)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-010: Search Produk
**Priority:** High | **Role:** Admin

### Test Steps:
1. Di search box, ketik: `Indo`
2. Perhatikan hasil
3. Ketik kode produk (jika ada)
4. Perhatikan hasil

### Expected Result:
- Search by nama: tampil produk dengan nama mengandung "Indo"
- Search by kode: tampil produk dengan kode tersebut
- Case-insensitive
- Real-time filtering

### Actual Result:
```
Search "Indo": _______ hasil
Produk yang tampil: ___________________________________________
Search by kode working? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-011: Filter by Kategori
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Pilih filter kategori: "Makanan"
2. Perhatikan hasil
3. Pilih kategori lain
4. Pilih "Semua Kategori"

### Expected Result:
- Filter "Makanan": tampil hanya produk kategori Makanan
- Filter kategori lain: tampil sesuai kategori
- Filter "Semua": tampil semua produk

### Actual Result:
```
Filter Makanan: _______ produk
Filter kategori lain: _______ produk
Filter Semua: _______ produk
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-012: Filter by Status
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Pilih filter "Aktif"
2. Pilih filter "Nonaktif"
3. Pilih filter "Semua"

### Expected Result:
- Filter bekerja dengan benar
- Tampil sesuai status yang dipilih

### Actual Result:
```
Aktif: _______ produk
Nonaktif: _______ produk
Semua: _______ produk
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-013: View Detail Produk
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Klik tombol "Detail" pada suatu produk
2. Perhatikan modal/halaman detail

### Expected Result:
- Modal detail terbuka
- Tampil semua info:
  - Kode, Nama, Kategori
  - Harga Beli, Harga Jual, Margin
  - Satuan & Multiplier
  - Status
  - Total Stok (dari semua batch)

### Actual Result:
```
Info yang tampil: _____________________________________________
_________________________________________________________________
Stok total correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-014: Generate Barcode
**Priority:** Low | **Role:** Admin

### Test Steps:
1. Pilih produk
2. Klik "Print Barcode" / "Cetak Barcode"
3. Perhatikan output

### Expected Result:
- Halaman baru terbuka (atau PDF download)
- Barcode ter-generate dari kode produk
- Barcode bisa di-scan
- Tampil nama produk dan harga

### Actual Result:
```
Format: ⬜ PDF ⬜ HTML ⬜ Image
Barcode readable: ⬜ Yes ⬜ No
Info lengkap: ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-PROD-015: View Product History Log
**Priority:** Low | **Role:** Admin

### Test Steps:
1. Edit suatu produk beberapa kali (ubah harga, nama, dll)
2. Klik "History" / "Riwayat Perubahan"
3. Perhatikan log

### Expected Result:
- Tampil riwayat perubahan produk
- Info: Tanggal, User, Perubahan (before/after)
- Urut dari terbaru

### Actual Result:
```
Total log entries: _______
Urutan: ⬜ Terbaru ⬜ Terlama
Info lengkap? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

# 4️⃣ STOCK MANAGEMENT (Product Batches)

## UAT-BATCH-001: Lihat Daftar Stok Masuk
**Priority:** High | **Role:** Admin

### Test Steps:
1. Login sebagai Admin
2. Klik menu "Stok Masuk" / "Product Batches"
3. Perhatikan tampilan

### Expected Result:
- Tampil tabel batch dengan kolom:
  - Batch Code
  - Nama Produk
  - Qty Awal
  - Qty Sisa
  - Harga Beli
  - Harga Jual
  - Tanggal Masuk
  - Tanggal Kadaluarsa (jika ada)
  - Aksi
- Tampil tombol "Tambah Stok Masuk"
- Filter & search berfungsi

### Actual Result:
```
Total Batch tampil: _______
Kolom yang ada: _______________________________________________
_________________________________________________________________
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-002: Tambah Stok Masuk (Batch Baru)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Test Data** | Lihat test steps |

### Test Steps:
1. Klik "Tambah Stok Masuk"
2. Search dan pilih produk: `Indomie Goreng`
3. Isi form:
   - Qty Awal: `100` (dalam satuan dasar: Pcs)
   - Harga Beli: `2500`
   - Harga Jual: `3000`
   - Tanggal Masuk: `(hari ini)`
4. Klik "Simpan"

### Expected Result:
- Batch code auto-generate
- Batch tersimpan
- Qty Awal = Qty Sisa = 100
- Muncul di tabel
- Notifikasi sukses

### Actual Result:
```
Batch Code: _______
Qty Awal: _______ | Qty Sisa: _______
Match expected? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-003: Tambah Batch dengan Expiry Date
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Tambah Stok Masuk"
2. Pilih produk yang memiliki expired date (misal: makanan/obat)
3. Isi data batch
4. Isi Tanggal Kadaluarsa: `(6 bulan dari sekarang)`
5. Simpan

### Expected Result:
- Batch tersimpan dengan tanggal kadaluarsa
- Tanggal tersimpan dengan benar

### Actual Result:
```
Tanggal Kadaluarsa tersimpan: _________________________________
Format date correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-004: Validasi Qty Harus Positif
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Tambah Stok Masuk"
2. Pilih produk
3. Isi Qty Awal: `-10` (negatif)
4. Klik "Simpan"

### Expected Result:
- Validasi error muncul
- Pesan: "Qty harus lebih dari 0"
- Data TIDAK tersimpan

### Actual Result:
```
Error message: ________________________________________________
Data tersimpan? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-005: Edit Batch
**Priority:** Medium | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada batch dengan qty_sisa > 0 |

### Test Steps:
1. Pilih batch tertentu
2. Klik "Edit"
3. Ubah Harga Jual: `3500`
4. Simpan

### Expected Result:
- Form edit terbuka
- Hanya field tertentu bisa diubah (biasanya harga, TIDAK qty yang sudah terpakai)
- Perubahan tersimpan

### Actual Result:
```
Field yang bisa diubah: _______________________________________
Harga ter-update? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-006: Hapus Batch (Belum Terpakai)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada batch dengan qty_awal = qty_sisa (belum terpakai) |

### Test Steps:
1. Pilih batch yang belum terpakai sama sekali
2. Klik "Hapus"
3. Konfirmasi

### Expected Result:
- Konfirmasi muncul
- Batch terhapus
- Notifikasi sukses

### Actual Result:
```
Konfirmasi muncul? ⬜ Yes ⬜ No
Batch terhapus? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-007: Hapus Batch (Sudah Terpakai)
**Priority:** High | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada batch dengan qty_sisa < qty_awal (sudah terpakai) |

### Test Steps:
1. Pilih batch yang sudah terpakai (qty_sisa < qty_awal)
2. Klik "Hapus"
3. Perhatikan respon

### Expected Result:
- Muncul pesan error
- Pesan: "Batch tidak bisa dihapus karena sudah terpakai"
- Batch TIDAK terhapus

### Actual Result:
```
Error message: ________________________________________________
Batch terhapus? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-008: Update Harga Master dari Batch
**Priority:** Medium | **Role:** Admin

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada batch dengan harga berbeda dari master product |

### Test Steps:
1. Pilih batch dengan harga: Beli 2500, Jual 3500
2. Klik "Update Harga Master" / similar button
3. Konfirmasi
4. Cek harga di master product

### Expected Result:
- Konfirmasi muncul
- Harga master product terupdate mengikuti batch
- Notifikasi sukses

### Actual Result:
```
Harga Beli Master: _______ (expected: 2500)
Harga Jual Master: _______ (expected: 3500)
Match? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-009: Search Batch by Product
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Di search/filter product
2. Ketik nama produk: `Indomie`
3. Perhatikan hasil

### Expected Result:
- Tampil hanya batch dari produk yang mengandung "Indomie"
- Batch produk lain tidak tampil

### Actual Result:
```
Total batch: _______
Semua batch dari Indomie? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-010: View Batch Detail
**Priority:** Low | **Role:** Admin

### Test Steps:
1. Klik "Detail" pada suatu batch
2. Perhatikan informasi yang ditampilkan

### Expected Result:
- Modal/halaman detail muncul
- Tampil info lengkap batch
- Tampil info produk terkait

### Actual Result:
```
Info yang ditampilkan: ________________________________________
_________________________________________________________________
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-011: View Batch History Log
**Priority:** Low | **Role:** Admin

### Test Steps:
1. Pilih batch
2. Klik "History" / "Riwayat"
3. Perhatikan log

### Expected Result:
- Tampil semua perubahan batch
- Info: Tanggal, User, Qty Before/After, Reason
- Urut dari terbaru

### Actual Result:
```
Total log entries: _______
Info lengkap? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-BATCH-012: Batch dengan Tanggal Masuk Berbeda (untuk FIFO)
**Priority:** High | **Role:** Admin

### Test Steps:
1. Tambah 2 batch untuk produk yang sama
2. Batch 1: Tanggal Masuk = (5 hari lalu), Qty = 50
3. Batch 2: Tanggal Masuk = (hari ini), Qty = 50
4. Perhatikan urutan di tabel

### Expected Result:
- Batch tersimpan dengan tanggal berbeda
- Urut berdasarkan tanggal masuk (oldest first untuk FIFO)

### Actual Result:
```
Batch 1 Date: _______ | Batch 2 Date: _______
Urutan correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

# 5️⃣ STOCK ADJUSTMENT

## UAT-ADJ-001: Lihat Daftar Stock Adjustment
**Priority:** High | **Role:** Admin

### Test Steps:
1. Login sebagai Admin
2. Klik menu "Stock Adjustment"
3. Perhatikan tampilan

### Expected Result:
- Tampil tabel adjustment dengan kolom:
  - No
  - Tanggal
  - User
  - Reason/Alasan
  - Total Items
  - Net Change
  - Aksi
- Tampil tombol "Buat Adjustment"

### Actual Result:
```
Total Adjustment: _______
Kolom yang tampil: ____________________________________________
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-ADJ-002: Buat Stock Adjustment (Single Product)
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Buat Adjustment"
2. **Step 1 - Header:**
   - Reason: `Stok rusak`
   - Tanggal: `(hari ini)`
   - Klik "Next"
3. **Step 2 - Select Product:**
   - Search produk: `Indomie`
   - Pilih produk
   - Pilih batch dengan qty_sisa = 100
   - Ubah qty jadi: `90` (kurangi 10)
4. Klik "Submit"

### Expected Result:
- Wizard 2 step berfungsi
- Adjustment tersimpan
- Qty sisa batch berubah dari 100 → 90
- Log tercatat
- Notifikasi sukses

### Actual Result:
```
Qty Before: _______
Qty After: _______
Difference: _______ (expected: -10)
Log tercatat? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

_Dokumen berlanjut di Part 3 (POS Transactions - Debt & Debt Management)_

---

## 📊 Progress Tracker - Part 2

**Product (lanjutan):** ___/9 completed  
**Stock Batches:** ___/12 completed  
**Stock Adjustment:** ___/2 completed (dari 8 total)  
**POS Cash:** ___/0 completed (dari 10 total)

**Overall Part 2:** ___/39 tests

---

**Tested by:** _________________  
**Date:** _________________