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
Konfirmasi muncul? ⬜ Yes 
Produk terhapus? ⬜ Yes 
Masih ada di DB? ⬜ Yes with deleted_at filled
```

**Status:** ⬜ Pass
**Screenshot:** ⬜ Attached  
**Notes:** Terdapat Modal konfirmasi dengan teks: Data yang dihapus masih dapat dipulihkan oleh administrator. Tolong dihapus cukup sisahkan teks konfirmasi saja 

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
Error message: Erro di bagian stok masin
Produk terhapus? ⬜ Yes 
```

**Status:** Pass
**Screenshot:** ⬜ Attached  
**Notes:** Cek Jika Produk sudah ada transaksi/batch tidak bisa dihapus

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
Status berubah? ⬜ Yes 
Muncul di POS? ⬜ No (harus No)
Admin bisa lihat? ⬜ Yes 
```

**Status:** ⬜ Pass 
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

**Status:** ⬜ Pass 
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

**Status:** ⬜ Pass 
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

**Status:** ⬜ Pass
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
Info yang tampil: semua terkeciali totalk stok
_________________________________________________________________
Stok total correct? ⬜ No
```

**Status:** ⬜ Pass
**Screenshot:** ⬜ Attached  
**Notes:** Tamabhakn total stok pada modal detail produk

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

**Status:** Pass
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

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---


## UAT-PROD-016: View Product Action
**Priority:** Low | **Role:** Admin

### Test Steps:
1. Tekan Button Aksi

### Expected Result:
- Tampil Detail Produk
Edit Produk
Riwayat
Nonaktifkan
Cetak Barcode
Hapus

### Actual Result:
```
List tampil sempurna
```

**Status:** ⬜ Pass
**Screenshot:** ⬜ Attached  
**Notes:** Ada bugs dimana step sebagai berikut
1. path /products diakses 
2. ketika aksi ditekan maka list aksi akan muncul
3. search products
4. data terfilter
5. pada table ketika user menekan aksi maka list aksi tidak akan muncul terkecuali data paling atas

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
  - Aksi
- Tampil tombol "Tambah Stok Masuk"
- Filter & search berfungsi

### Actual Result:
```
Total Batch tampil: _______
Kolom yang ada: _______________________________________________
_________________________________________________________________
```

**Status:** ⬜ Pass
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

**Status:** ⬜ Pass 
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

**Status:** ⬜ Blocked  
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
Error message: The qty masuk field must be at least 1.
Data tersimpan? No (harus No)
```

**Status:** Pass
**Screenshot:** ⬜ Attached  
**Notes:** sesuaikan pesan error jadi bahasa indo seperti "Qty harus lebih dari 0"

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
Field yang bisa diubah: Harga dan Tanggal Masuk
Harga ter-update? ⬜ Yes
```

**Status:** Pass
**Screenshot:** ⬜ Attached  
**Notes:** Ketika harga berubah seharusnya juga tercatat di product bathc logs tapi ternyata table dri product bathc logs tidak bisa menyimpan perubahan harga dant anggal untuk product batch logs malah menyimpan perubahan quantity. patut diketahui bahwa batch log tidak boleh merubah stok managemetn jadi bugsnya sperti ini:
Case 1:
1.  Terdapat Produk Indomie Goreng Dengan harga beli 2400 dan harga jual 3500 serta stok 100/100
2.  Diubah Harga beli jadi 2500 dan harga Jual 3500 (tetep)
3. Perbarui
4. harga akan berubah sesuai dengan perubahan
5. Di Master Data Product Batch Juga berubah dan Tercatat di Product Batch Log pada kolom changes 
{"new": {"harga_beli": 2500}, "old": {"harga_beli": 2400}}
6. Lihat IRwyat Batch akan tercata perubahan yang da
SUCCESS

Case 2 Yang memiliki product units dan create pertama kali:
Notes: case ini ketika user pertama create batch dan lngsung edit
1. user create produk BCH-20260207-002	Indomie Goreng Jumbo	harga beli Rp 3.900 jual	Rp 4.400 stok 20, saat di create dia diinput sebagai Product Unit "Per10" dengan multiplier 10 dengan mengisi 2.  jadi stoknya akan jadi 20
2. User mencoba untuk edit product tanpa reload halaman, Semua input terkecuiali tanggal, harga beli, harga jual, dan margin tidak dapat diisi. coba kita ubah harga beli jadi 4000, perlu di notice kuantitas tadi tidak di conver ke base unti sperti biasanya tapi masih sperti di awal "Product Unit "Per10" dengan multiplier 10 dengan mengisi 2" saat disimpan ternyata stoknya berubha jadi di kali 10 lagi jadi 200, jika diedit dn dsimpan akan dikali lagi 10. tapi saat di reload ini aman saja, saat edit tidak akan mengali berdasarkan product unitsnya. di Database dengan id product bathc logs 29 dan 28 tidak ada terisi kolom changes tapi beubah di qty change dan tambah deskripsi Batch quantity updated via Edit form seharusnya form edit tidak boleh ubah stok terkecuali di sales karena pengurangan disebabkan oleh penjualan also stock adjustment ketika stok ada bermasalah
FAILED

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

**Status:** Pass
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
- Tidak Ada pilihan Menghapus batch

### Actual Result:
```
Error message: ________________________________________________
Batch terhapus? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass 
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

**Status:** ⬜ Pass
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

**Status:** Fail
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
Info yang ditampilkan:Lengkap sesuai Expected Result
```

**Status:** Partial
**Screenshot:** ⬜ Attached  
**Notes:** Saya ingin menambahkan Product Code di bawah tulisan nama produk agar mendapatkan informasi kode produknya

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

**Status:** ⬜ Pass 
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

**Status:** ⬜ Pass
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
 
  - Tanggal
  - User
  - Reason/Alasan
  - Total Items
  - Aksi
- Tampil tombol "Buat Adjustment"

### Actual Result:
```
Total Adjustment: _______
Kolom yang tampil: ____________________________________________
```

**Status:** ⬜ Pass 
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

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________


# 6️⃣ POS TRANSACTIONS - CASH

## UAT-POS-CASH-001: Search dan Add Product to Cart
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Login sebagai Kasir
2. Klik menu "Transaksi" / "POS"
3. Di search box, ketik: `Nice living Food Safe` atau kode 8992759114348
4. Perhatikan hasil search
5. Klik produk atau tombol "Tambah" untuk add to cart

### Expected Result:
- Search box tersedia dan responsive
- Autocomplete/dropdown muncul saat mengetik
- Tampil produk yang mengandung "Indomie"
- Klik produk → masuk ke cart
- Cart menampilkan:
  - Nama produk
  - Qty: 1 (default)
  - Harga satuan
  - Subtotal
- Total transaksi update otomatis

### Actual Result:
```
Search working? ⬜ Yes 
Autocomplete? ⬜ Yes 
Product added to cart? ⬜ Yes 
Default qty: 1
Subtotal shown: Rp 8000
Total updated? ⬜ Yes 
```

**Status:** Pass
**Screenshot:** ⬜ Attached  
**Notes:** Barcode Scanner akan mengisi kode misalka 8992759114348 dan akan menekan Enter setelah angka tersebut berhasil sehingga tidak akan tersimpan ke cart karean data Produk yag dicari tidak ada, sedangkan sistem akan mengquery dulu dan menampilkan item baru bisa dienter agar masuk ke cart. Coba buatkan agar ketika user menekan input search maka jika barcode scanner mengisi angak terus enter, enter tadi dsimpan dlu bahwa dia pernah melakukan enter, ketika system sdh menemukan product sesuai dengan kode terus tambahkan lgnsugng ke cart

---

## UAT-POS-CASH-002: Adjust Quantity (+/-)
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Tambah produk ke cart (qty default: 1)
2. Klik tombol **+** (plus) beberapa kali
3. Perhatikan qty dan subtotal
4. Klik tombol **-** (minus)
5. Perhatikan perubahan

### Expected Result:
- Tombol + berfungsi: qty bertambah 1
- Tombol - berfungsi: qty berkurang 1
- Subtotal update otomatis setiap perubahan qty
- Total transaksi update otomatis
- Tombol - disabled atau qty tidak bisa < 1

### Actual Result:
```
Tombol + working? ⬜ Yes ⬜ No
Tombol - working? ⬜ Yes ⬜ No
After 3x click +:
  Qty: _______ (expected: 4)
  Subtotal update? ⬜ Yes ⬜ No
Minimum qty = 1? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-003: Input Quantity Manual
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Tambah produk ke cart
2. Klik langsung pada field qty
3. Hapus angka dan ketik: `25`
4. Tab/Enter atau klik di luar field
5. Perhatikan perubahan

### Expected Result:
- Field qty bisa diklik dan di-edit
- Input manual langsung terupdate
- Subtotal recalculate: qty x harga
- Total transaksi update
- Validasi: qty harus angka positif

### Actual Result:
```
Field editable? ⬜ Yes ⬜ No
Input 25 accepted? ⬜ Yes ⬜ No
Subtotal: Rp _____________ (verify: 25 x harga)
Total updated? ⬜ Yes ⬜ No
Validation working? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-004: Change Product Unit
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Produk memiliki multi-unit (Pcs, Dus, Karton) |
| **Test Data** | Indomie: Pcs (1x), Dus (40x), Karton (120x), Harga: Rp 3.000/pcs |

### Test Steps:
1. Tambah produk "Indomie" ke cart
2. Default: 1 Pcs, Subtotal: Rp 3.000
3. Klik dropdown unit
4. Pilih "Dus"
5. Perhatikan subtotal
6. Pilih "Karton"
7. Perhatikan subtotal

### Expected Result:
- Dropdown unit menampilkan: Pcs, Dus, Karton
- Ganti ke Dus:
  - Subtotal = 1 Dus x (Rp 3.000 x 40) = Rp 120.000
- Ganti ke Karton:
  - Subtotal = 1 Karton x (Rp 3.000 x 120) = Rp 360.000
- Qty tetap 1, yang berubah multiplier
- Total transaksi update

### Actual Result:
```
Units available: ______________________________________________
Select Dus:
  Subtotal: Rp _____________ (expected: Rp 120.000)
Select Karton:
  Subtotal: Rp _____________ (expected: Rp 360.000)
Calculation correct? ⬜ Yes ⬜ No
```

**Status:** Pass
**Screenshot:** ⬜ Attached  
**Notes:** Seharusnya ketika Pindah Product Units value qty harus di set ke 1
---

## UAT-POS-CASH-005: Remove Item from Cart
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Tambah 3 produk berbeda ke cart
2. Perhatikan total
3. Klik tombol "Hapus" / "Remove" / icon X pada item ke-2
4. Perhatikan perubahan

### Expected Result:
- Item terhapus dari cart
- Cart sekarang hanya 2 item
- Total transaksi berkurang (recalculate)
- UI update smooth tanpa reload

### Actual Result:
```
Before remove:
  Items count: _______
  Total: Rp _____________

After remove:
  Items count: _______ (expected: 2)
  Total: Rp _____________
  Calculation correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-006: Complete Cash Transaction
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Test Data** | Total: Rp 50.000, Cash: Rp 100.000 |

### Test Steps:
1. Tambah produk ke cart (total: Rp 50.000)
2. Klik "Checkout" / "Bayar"
3. Modal/halaman payment muncul
4. Pilih metode: **Cash/Tunai**
5. Input jumlah uang: `100000`
6. Klik "Confirm" / "Proses"

### Expected Result:
- Payment modal muncul
- Tampil total: Rp 50.000
- Input cash: Rp 100.000
- Auto-calculate kembalian: Rp 50.000
- Transaksi berhasil tersimpan:
  - Sale record created
  - payment_method = 'cash'
  - total_paid = 50000
  - status = 'completed'
- Stock terpotong (FIFO)
- Invoice generated
- Modal close, cart kosong
- Notifikasi sukses

### Actual Result:
```
Payment modal shown? ⬜ Yes 
Total: Rp _____________ (expected: Rp 50.000)
Cash: Rp 100.000
Kembalian: Rp _____________ (expected: Rp 50.000)
Transaction saved? ⬜ Yes ⬜ No
Stock deducted? ⬜ Yes ⬜ No
Cart cleared? ⬜ Yes ⬜ No
Invoice generated? ⬜ Yes ⬜ No
```

**Status:** Pass
**Screenshot:** ⬜ Attached  
**Notes:** Saya ingin print invoice buat dengan detail berdsarkan sistem fifo

---

## UAT-POS-CASH-007: Calculate Change Correctly
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Buat transaksi dengan berbagai total:
   - Test 1: Total Rp 15.000, Cash Rp 20.000
   - Test 2: Total Rp 47.500, Cash Rp 50.000
   - Test 3: Total Rp 100.000, Cash Rp 100.000 (exact)

### Expected Result:
- Test 1: Kembalian = Rp 5.000
- Test 2: Kembalian = Rp 2.500
- Test 3: Kembalian = Rp 0
- Perhitungan akurat
- Tampil dengan format Rupiah

### Actual Result:
```
Test 1:
  Total: Rp 15.000 | Cash: Rp 20.000
  Kembalian: Rp 5000 (expected: Rp 5.000)

Test 2:
  Total: Rp 47.500 | Cash: Rp 50.000
  Kembalian: Rp _____________ (expected: Rp 2.500)

Test 3:
  Total: Rp 100.000 | Cash: Rp 100.000
  Kembalian: Rp _____________ (expected: Rp 0)

All correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-008: Stock Deduction (FIFO - Single Batch)
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada 1 batch untuk produk dengan qty_sisa = 100 |

### Test Steps:
1. Catat qty_sisa batch sebelum transaksi
2. Buat transaksi POS:
   - Produk tersebut, qty = 15 pcs
3. Complete dengan cash
4. Cek qty_sisa batch setelah transaksi

### Expected Result:
- Qty before: 100
- Qty after: 85 (100 - 15)
- Stock terpotong otomatis saat transaksi complete
- Log tercatat di product_batch_logs

### Actual Result:
```
Batch ID: _______
Qty before: _______ (should be 100)
Qty after transaction: _______ (expected: 85)
Deducted: _______ (expected: 15)
Log recorded? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-009: Insufficient Stock Validation
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Produk hanya punya stok 5 pcs |

### Test Steps:
1. Tambah produk ke cart
2. Set qty = 10 (lebih dari stok tersedia)
3. Klik "Checkout"

### Expected Result:
- Validasi error muncul
- Pesan: "Stok tidak mencukupi. Tersedia: 5 pcs"
- Atau: Tidak bisa input qty > stok
- Atau: Tombol checkout disabled
- Transaksi TIDAK bisa diproses

### Actual Result:
```
Validation working? ⬜ Yes ⬜ No
Error message: ________________________________________________
_________________________________________________________________
Transaction blocked? ⬜ Yes ⬜ No (harus Yes)
Stock available shown? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-010: Cancel Transaction
**Priority:** Medium | **Role:** Kasir

### Test Steps:
1. Tambah beberapa produk ke cart
2. Perhatikan tombol "Batal" / "Cancel"
3. Klik tombol tersebut
4. Konfirmasi pembatalan (jika ada)

### Expected Result:
- Tombol Cancel tersedia
- Konfirmasi muncul: "Yakin ingin membatalkan transaksi?"
- Setelah confirm:
  - Cart dikosongkan
  - Total = Rp 0
  - Tidak ada perubahan stock
  - Tidak ada sale record tersimpan
  - Kembali ke state awal (empty cart)

### Actual Result:
```
Cancel button available? ⬜ Yes ⬜ No
Confirmation shown? ⬜ Yes ⬜ No
After cancel:
  Cart empty? ⬜ Yes ⬜ No
  Total: Rp _____________ (expected: Rp 0)
  Stock unchanged? ⬜ Yes ⬜ No
  No sale record? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---
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