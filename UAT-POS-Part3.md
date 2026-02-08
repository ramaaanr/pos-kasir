# 📋 User Acceptance Testing (UAT) - POS System
## Part 3: Stock Adjustment (Lanjutan), POS Debt, Debt Management & Dashboard

**Project:** Point of Sale System  
**Version:** 1.0  
**Testing Date:** _________________  
**Tester Name:** _________________

---

## 🎯 Testing Coverage Summary - Part 3

| Module | Total Tests | Status |
|--------|-------------|--------|
| 5. Stock Adjustment (lanjutan) | 6 | ⬜ |
| 7. POS Transactions - Debt | 12 | ⬜ |
| 8. Debt Management | 10 | ⬜ |
| 9. Dashboard & Reporting | 10 | ⬜ |
| **TOTAL PART 3** | **38** | **⬜** |

---

# 5️⃣ STOCK ADJUSTMENT (Lanjutan)

## UAT-ADJ-003: Buat Adjustment Multi-Product
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Buat Adjustment"
2. **Step 1 - Header:**
   - Reason: `Stock opname bulanan`
   - Tanggal: `(hari ini)`
   - Next
3. **Step 2 - Select Products:**
   - Search dan pilih Produk A
   - Batch 1: qty 100 → ubah jadi 95
   - Search dan pilih Produk B
   - Batch 1: qty 50 → ubah jadi 55
   - Search dan pilih Produk C
   - Batch 1: qty 80 → ubah jadi 80 (no change)
4. Perhatikan Summary
5. Klik "Submit"

### Expected Result:
- Bisa pilih multiple products
- Summary menampilkan:
  - Total Batches Affected: 3
  - Net Change: 0 (-5 + 5 + 0)
- Semua perubahan tersimpan
- Log tercatat untuk setiap batch

### Actual Result:
```
Total Products Selected: _______
Total Batches: _______
Net Change: _______ (expected: 0)
Produk A qty: _______ (expected: 95)
Produk B qty: _______ (expected: 55)
Produk C qty: _______ (expected: 80)
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-ADJ-004: Validasi Reason Required
**Priority:** High | **Role:** Admin

### Test Steps:
1. Klik "Buat Adjustment"
2. **Step 1 - Header:**
   - Kosongkan field "Reason"
   - Tanggal: `(hari ini)`
   - Klik "Next"

### Expected Result:
- Validasi error muncul
- Pesan: "Alasan wajib diisi" atau similar
- Tidak bisa lanjut ke Step 2
- Form tetap di Step 1

### Actual Result:
```
Error message: ________________________________________________
Bisa lanjut ke Step 2? ⬜ Yes ⬜ No (harus No)
_________________________________________________________________
```

**Status:** ⬜ Pass
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-ADJ-005: View Adjustment Summary
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Buat adjustment dengan:
   - Batch 1: qty 100 → 90 (decrease 10)
   - Batch 2: qty 50 → 60 (increase 10)
   - Batch 3: qty 30 → 25 (decrease 5)
2. Perhatikan Summary sebelum submit

### Expected Result:
- Summary menampilkan:
  - Total Batches: 3
  - Total Decrease: -15
  - Total Increase: +10
  - Net Change: -5
- Perhitungan otomatis dan real-time

### Actual Result:
```
Total Batches: _______
Total Decrease: _______
Total Increase: _______
Net Change: _______ (expected: -5)
Real-time calculation? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-ADJ-006: Adjust Qty - Increase dan Decrease
**Priority:** High | **Role:** Admin

### Test Steps:
1. Buat adjustment baru
2. Pilih batch dengan qty_sisa = 100
3. Test increase: ubah qty jadi 120 (+20)
4. Submit
5. Buat adjustment lagi
6. Pilih batch yang sama (sekarang 120)
7. Test decrease: ubah qty jadi 110 (-10)
8. Submit

### Expected Result:
- Increase works: 100 → 120
- Decrease works: 120 → 110
- Final qty: 110
- Semua log tercatat

### Actual Result:
```
After increase: _______ (expected: 120)
After decrease: _______ (expected: 110)
Logs recorded: ⬜ Yes ⬜ No
_________________________________________________________________
```

**Status:** ⬜ Pass s
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-ADJ-007: View Adjustment Detail
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Di halaman daftar Stock Adjustment
2. Pilih adjustment yang sudah dibuat
3. Klik "Detail" / "Lihat Detail"
4. Perhatikan informasi yang ditampilkan

### Expected Result:
- Modal/halaman detail terbuka
- Tampil info:
  - Tanggal adjustment
  - User yang membuat
  - Reason
  - Daftar items dengan:
    - Nama Produk
    - Batch Code
    - Qty Before
    - Qty After
    - Difference
  - Summary total

### Actual Result:
```
Info yang tampil: _____________________________________________
_________________________________________________________________
Total items shown: _______
Summary correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-ADJ-008: Adjustment Log in Batch History
**Priority:** High | **Role:** Admin

### Test Steps:
1. Buat stock adjustment yang mengubah batch tertentu
2. Kembali ke halaman "Stok Masuk" / Batches
3. Pilih batch yang di-adjust
4. Klik "History" / "Riwayat"
5. Perhatikan log entries

### Expected Result:
- Log adjustment tercatat di batch history
- Entry menampilkan:
  - Type: "Stock Adjustment"
  - Tanggal
  - User
  - Qty Before
  - Qty After
  - Reason
  - Reference ke adjustment ID

### Actual Result:
```
Log tercatat? ⬜ Yes ⬜ No
Info lengkap? ⬜ Yes ⬜ No
Type shown: ___________________________________________________
Reason shown: _________________________________________________
```

**Status:** ⬜ Pass 
**Notes:** _____________________________________________________

---

# 6️⃣ POS TRANSACTIONS - CASH (Lanjutan)

## UAT-POS-CASH-011: FIFO Stock Deduction - Multiple Batches
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Setup 2 batches untuk produk yang sama |

### Test Steps:
**Setup:**
1. Sebagai Admin, buat 2 batches untuk "Indomie Kari Ayam":
   - Batch A: Tanggal Masuk = 5 hari lalu, Qty = 50
   - Batch B: Tanggal Masuk = hari ini, Qty = 50
2. Logout, login sebagai Kasir

**Test:**
3. Buka halaman Transaksi (POS)
4. Search "Indomie Kari Ayam"
5. Tambah ke cart
6. Set qty = 70 (lebih dari Batch A)
7. Checkout dengan cash
8. Bayar dan complete
9. Cek qty masing-masing batch

### Expected Result:
- Batch A (oldest) habis duluan: 50 → 0
- Batch B tersisa: 50 → 30 (ambil 20)
- FIFO priority berdasarkan tanggal masuk
- Total deduction: 70 (50 + 20)

### Actual Result:
```
Batch A qty sisa: _______ (expected: 0)
Batch B qty sisa: _______ (expected: 30)
FIFO working correctly? ⬜ Yes ⬜ No
Total deducted: _______ (expected: 70)
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-CASH-012: Total Calculation Accuracy
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Tambah ke cart:
   - Produk A: 3 pcs @ Rp 3.000 = Rp 9.000
   - Produk B: 2 pcs @ Rp 5.500 = Rp 11.000
   - Produk C: 1 Dus (40 pcs) @ Rp 2.500/pcs = Rp 100.000
2. Perhatikan total

### Expected Result:
- Subtotal Produk A: Rp 9.000
- Subtotal Produk B: Rp 11.000
- Subtotal Produk C: Rp 100.000
- **Grand Total: Rp 120.000**
- Perhitungan update real-time

### Actual Result:
```
Produk A subtotal: Rp _____________
Produk B subtotal: Rp _____________
Produk C subtotal: Rp _____________
Grand Total: Rp _____________ (expected: Rp 120.000)
Match? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

# 7️⃣ POS TRANSACTIONS - DEBT

## UAT-POS-DEBT-001: Create Debt Transaction (Basic)
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Ada minimal 1 customer di database |

### Test Steps:
1. Login sebagai Kasir
2. Buka halaman Transaksi
3. Tambah produk ke cart (total: Rp 50.000)
4. Klik "Checkout"
5. Pilih metode pembayaran: **Hutang**
6. Search dan pilih customer: "Budi Santoso"
7. Input jaminan: `KTP`
8. Kosongkan field "Partial Payment" (full debt)
9. Klik "Confirm"

### Expected Result:
- Transaksi berhasil dibuat
- Sale record tersimpan dengan:
  - payment_method = 'hutang'
  - total = 50000
  - total_paid = 0
  - status = 'completed'
- Debt record dibuat:
  - amount = 50000
  - status = 'OPEN'
  - jaminan = 'KTP'
- Customer total_debt bertambah 50000
- Stock tetap terpotong (FIFO)
- Invoice generated

### Actual Result:
```
Sale ID: _______
Payment method: _______________________________________________
Debt amount: Rp _____________ (expected: Rp 50.000)
Debt status: _____________ (expected: OPEN)
Customer total_debt: Rp _____________
Stock deducted? ⬜ Yes ⬜ No (harus Yes)
Invoice generated? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-002: Select Customer dari List
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Proses checkout dengan metode Hutang
2. Klik field "Pilih Customer"
3. Perhatikan dropdown/modal customer
4. Search customer by nama: `Budi`
5. Pilih customer

### Expected Result:
- List customer tampil
- Bisa search by nama
- Bisa search by no_hp
- Customer terpilih auto-fill info (nama, no_hp, alamat)
- Bisa lihat total_debt customer (if any)

### Actual Result:
```
Customer list muncul? ⬜ Yes ⬜ No
Search working? ⬜ Yes ⬜ No
Customer info auto-fill? ⬜ Yes ⬜ No
Total debt shown? ⬜ Yes ⬜ No
Current debt customer: Rp _____________
```

**Status:**  ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-003: Add New Customer On-the-Fly
**Priority:** Medium | **Role:** Kasir

### Test Steps:
1. Proses checkout dengan metode Hutang
2. Klik "Tambah Customer Baru" / similar button
3. Isi form customer:
   - Nama: `Customer Baru`
   - No HP: `081234567890`
   - Alamat: `Jl. Test No. 123`
4. Save customer
5. Customer otomatis terpilih
6. Lanjutkan transaksi

### Expected Result:
- Modal/form tambah customer muncul
- Customer baru tersimpan
- Auto-select customer yang baru dibuat
- Bisa lanjut transaksi tanpa refresh

### Actual Result:
```
Form add customer: ⬜ Available ⬜ Not Available
Customer tersimpan? ⬜ Yes ⬜ No
Auto-selected? ⬜ Yes ⬜ No
Customer ID: _______
```

**Status:**  ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-004: Input Jaminan (Collateral)
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Checkout dengan Hutang
2. Pilih customer
3. Test berbagai jaminan:
   - Test 1: `KTP`
   - Test 2: `SIM`
   - Test 3: `BPKB Motor`
   - Test 4: `Emas 5 gram`
4. Complete transaksi

### Expected Result:
- Field jaminan bisa diisi free text
- Jaminan tersimpan di debt record
- Bisa input jaminan panjang (limit ±200 char)
- Jaminan tampil di detail debt

### Actual Result:
```
Jaminan tersimpan? ⬜ Yes ⬜ No
Character limit: _______ chars
Tampil di detail? ⬜ Yes ⬜ No
Jaminan yang tersimpan: _______________________________________
```

**Status:** ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-005: Partial Payment (DP) saat Create Debt
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Test Data** | Total: Rp 100.000, DP: Rp 30.000 |

### Test Steps:
1. Buat transaksi total Rp 100.000
2. Checkout dengan Hutang
3. Pilih customer
4. Input jaminan: `KTP`
5. Input Partial Payment: `30000`
6. Confirm

### Expected Result:
- Transaksi berhasil
- Sale total_paid = 30000
- Debt amount = 100000
- Debt status = 'PARTIAL'
- Debt payment record dibuat untuk DP 30000
- Sisa debt = 70000
- Customer total_debt = 70000

### Actual Result:
```
Sale total_paid: Rp _____________ (expected: Rp 30.000)
Debt amount: Rp _____________ (expected: Rp 100.000)
Debt status: _____________ (expected: PARTIAL)
Debt payment created? ⬜ Yes ⬜ No
Remaining debt: Rp _____________ (expected: Rp 70.000)
Customer total_debt: Rp _____________ (expected: Rp 70.000)
```

**Status:** ⬜ Pass   
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-006: Zero Partial Payment (Full Debt)
**Priority:** Medium | **Role:** Kasir

### Test Steps:
1. Buat transaksi total Rp 50.000
2. Checkout dengan Hutang
3. Pilih customer
4. Kosongkan atau isi 0 di "Partial Payment"
5. Confirm

### Expected Result:
- Transaksi berhasil
- Sale total_paid = 0
- Debt amount = 50000
- Debt status = 'OPEN' (bukan PARTIAL)
- Tidak ada debt payment record
- Customer total_debt = 50000

### Actual Result:
```
Sale total_paid: Rp _____________ (expected: Rp 0)
Debt status: _____________ (expected: OPEN)
Debt payments count: _______ (expected: 0)
Customer total_debt: Rp _____________ (expected: Rp 50.000)
```

**Status:** ⬜ Pass
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-007: Debt Status Logic - OPEN
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Buat debt transaction tanpa DP (full debt)
2. Check debt status di database atau UI

### Expected Result:
- Debt status = 'OPEN'
- Indicator: belum ada pembayaran sama sekali

### Actual Result:
```
Debt status: _____________ (expected: OPEN)
Total paid: Rp _____________ (expected: Rp 0)
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-008: Debt Status Logic - PARTIAL
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Buat debt transaction dengan DP (partial payment)
2. Check debt status

### Expected Result:
- Debt status = 'PARTIAL'
- Indicator: ada pembayaran tapi belum lunas
- total_paid < amount

### Actual Result:
```
Debt status: _____________ (expected: PARTIAL)
Amount: Rp _____________
Total paid: Rp _____________
Remaining: Rp _____________
```

**Status:** ⬜ Pass   
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-009: Stock Deduction pada Debt Transaction
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Batch dengan qty_sisa = 100 |

### Test Steps:
1. Catat qty_sisa batch sebelum transaksi
2. Buat debt transaction:
   - Produk dari batch tersebut
   - Qty = 10 pcs
3. Complete transaksi
4. Check qty_sisa batch setelah transaksi

### Expected Result:
- Stock TETAP terpotong meski hutang
- Qty before: 100
- Qty after: 90
- Stock deduction TIDAK menunggu pembayaran
- FIFO tetap berlaku

### Actual Result:
```
Qty before: _______
Qty after: _______
Deducted: _______ (expected: 10)
Stock terpotong? ⬜ Yes ⬜ No (harus Yes)
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-010: Customer Total_Debt Update
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Check total_debt customer sebelum transaksi
2. Buat debt transaction Rp 50.000 (tanpa DP)
3. Check total_debt customer setelah transaksi
4. Buat debt transaction lagi Rp 30.000
5. Check total_debt customer

### Expected Result:
- Before: Rp 0 (assume fresh customer)
- After debt 1: Rp 50.000
- After debt 2: Rp 80.000 (50.000 + 30.000)
- Customer total_debt = sum of all unpaid debts

### Actual Result:
```
Total debt before: Rp _____________
After debt 1: Rp _____________ (expected: Rp 50.000)
After debt 2: Rp _____________ (expected: Rp 80.000)
Calculation correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-011: Validation - Customer Required
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Buat transaksi dan checkout
2. Pilih metode "Hutang"
3. JANGAN pilih customer
4. Langsung klik "Confirm"

### Expected Result:
- Validasi error muncul
- Pesan: "Customer wajib dipilih untuk transaksi hutang"
- Transaksi TIDAK tersimpan
- Tetap di halaman checkout

### Actual Result:
```
Error message: ________________________________________________
Transaksi tersimpan? ⬜ Yes ⬜ No (harus No)
_________________________________________________________________
```

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-POS-DEBT-012: Invoice Generated
**Priority:** Medium | **Role:** Kasir

### Test Steps:
1. Complete debt transaction
2. Perhatikan setelah confirm
3. Cek apakah ada tombol "Print Invoice" atau auto-print

### Expected Result:
- Invoice number auto-generate (format: INV-YYYYMMDD-XXXX)
- Tampil notifikasi sukses dengan invoice number
- Ada opsi print/download invoice
- Invoice contains:
  - Invoice number
  - Date
  - Customer info
  - Items & qty
  - Total amount
  - Payment method: Hutang
  - Jaminan
  - Partial payment (if any)
  - Remaining debt

### Actual Result:
```
Invoice generated? ⬜ Yes ⬜ No
Invoice number: _______________________________________________
Print option available? ⬜ Yes ⬜ No
Invoice content complete? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

# 8️⃣ DEBT MANAGEMENT

## UAT-DEBT-001: View Customer List (With Debt)
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Login sebagai Kasir
2. Klik menu "Pembayaran Hutang" / "Debt Management"
3. Perhatikan daftar customer

### Expected Result:
- Tampil list customer yang memiliki hutang
- Kolom yang ditampilkan:
  - Nama Customer
  - No HP
  - Total Hutang (Rp)
  - Jumlah Transaksi Hutang
  - Aksi (Pilih/Detail)
- HANYA customer dengan total_debt > 0 yang tampil
- Sorting by total_debt (terbesar ke terkecil)

### Actual Result:
```
Total customers shown: _______
All have debt > 0? ⬜ Yes ⬜ No (harus Yes)
Sorted correctly? ⬜ Yes ⬜ No
Kolom yang tampil: ____________________________________________
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-002: Search Customer by Name
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Di halaman Debt Management
2. Ketik di search box: `Budi`
3. Perhatikan hasil

### Expected Result:
- Filter real-time
- Tampil hanya customer dengan nama mengandung "Budi"
- Case-insensitive
- Customer lain tidak tampil

### Actual Result:
```
Search term: Budi
Results: _______ customer(s)
Customer names shown: _________________________________________
Case-insensitive working? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-003: Search Customer by Phone
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Di search box, ketik no HP: `0812`
2. Perhatikan hasil

### Expected Result:
- Tampil customer dengan no HP mengandung "0812"
- Search works for partial phone number
- Results accurate

### Actual Result:
```
Search term: 0812
Results: _______ customer(s)
Phone numbers shown: __________________________________________
Partial search working? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-004: Select Customer → View Debts
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Pilih/klik customer yang memiliki hutang
2. Perhatikan detail yang muncul

### Expected Result:
- Area detail customer expand/muncul
- Tampil info customer:
  - Nama, No HP, Alamat
  - Total Hutang keseluruhan
- Tampil list semua debt yang OPEN atau PARTIAL:
  - Invoice Number
  - Tanggal
  - Total Amount
  - Paid Amount
  - Remaining
  - Status (OPEN/PARTIAL)
  - Jaminan
  - Aksi (Bayar)
- Debt yang sudah PAID tidak tampil (atau beda section)

### Actual Result:
```
Customer info shown? ⬜ Yes ⬜ No
Total debt shown: Rp _____________
Number of debts: _______
All debts OPEN/PARTIAL? ⬜ Yes ⬜ No
Paid debts hidden? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-005: Make Full Payment
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Customer "Budi" memiliki debt Rp 50.000 (status OPEN) |

### Test Steps:
1. Pilih customer "Budi"
2. Pilih debt Rp 50.000
3. Klik "Bayar" / "Pay Debt"
4. Modal payment muncul
5. Input amount: `50000` (full)
6. Select payment method: `Cash`
7. Opsional: Input note: `Lunas`
8. Klik "Confirm Payment"

### Expected Result:
- Payment berhasil
- Debt payment record created (amount: 50000)
- Debt status berubah: OPEN → PAID
- Customer total_debt berkurang 50000
- Debt hilang dari list (moved to paid section)
- Notifikasi: "Pembayaran berhasil. Hutang lunas."

### Actual Result:
```
Payment amount: Rp _____________ (expected: Rp 50.000)
Debt status: _____________ (expected: PAID)
Customer total_debt after: Rp _____________
Debt moved to paid? ⬜ Yes ⬜ No
Notification shown? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass   
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-006: Make Partial Payment
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Customer memiliki debt Rp 100.000 (status OPEN) |

### Test Steps:
1. Pilih customer dengan debt Rp 100.000
2. Klik "Bayar"
3. Input amount: `40000` (partial, sisa 60000)
4. Payment method: `Cash`
5. Note: `Cicilan 1`
6. Confirm

### Expected Result:
- Payment berhasil
- Debt payment record created (amount: 40000)
- Debt status: OPEN → PARTIAL
- Remaining debt: 60000
- Customer total_debt berkurang 40000
- Debt masih tampil di list dengan sisa 60000
- Notifikasi: "Pembayaran berhasil. Sisa hutang: Rp 60.000"

### Actual Result:
```
Payment amount: Rp _____________ (expected: Rp 40.000)
Debt status: _____________ (expected: PARTIAL)
Remaining: Rp _____________ (expected: Rp 60.000)
Customer total_debt after: Rp _____________
Still in debt list? ⬜ Yes ⬜ No (harus Yes)
```

**Status:** ⬜ Pass 
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-007: Validation - Payment Cannot Exceed Remaining
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Debt dengan remaining Rp 50.000 |

### Test Steps:
1. Pilih debt dengan sisa Rp 50.000
2. Klik "Bayar"
3. Input amount: `70000` (lebih dari sisa)
4. Klik "Confirm"

### Expected Result:
- Validasi error muncul
- Pesan: "Jumlah pembayaran tidak boleh melebihi sisa hutang (Rp 50.000)"
- Payment TIDAK tersimpan
- Modal tetap terbuka

### Actual Result:
```
Error message: ________________________________________________
_________________________________________________________________
Payment tersimpan? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-008: Multiple Partial Payments → PAID
**Priority:** High | **Role:** Kasir

| Field | Details |
|-------|---------|
| **Pre-condition** | Debt Rp 100.000 (OPEN) |

### Test Steps:
1. Payment 1: Bayar Rp 30.000
   - Check status: should be PARTIAL
   - Remaining: Rp 70.000
2. Payment 2: Bayar Rp 30.000
   - Check status: should be PARTIAL
   - Remaining: Rp 40.000
3. Payment 3: Bayar Rp 40.000 (sisa)
   - Check status: should be PAID
   - Remaining: Rp 0

### Expected Result:
- After payment 1: PARTIAL, remaining 70000
- After payment 2: PARTIAL, remaining 40000
- After payment 3: PAID, remaining 0
- Customer total_debt = 0
- Debt hilang dari active list
- 3 payment records tersimpan

### Actual Result:
```
After Payment 1:
  Status: _____________ (expected: PARTIAL)
  Remaining: Rp _____________ (expected: Rp 70.000)

After Payment 2:
  Status: _____________ (expected: PARTIAL)
  Remaining: Rp _____________ (expected: Rp 40.000)

After Payment 3:
  Status: _____________ (expected: PAID)
  Remaining: Rp _____________ (expected: Rp 0)

Total payments recorded: _______ (expected: 3)
Customer total_debt: Rp _____________ (expected: Rp 0)
```

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-009: Customer Total_Debt Sync
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Customer "Budi" punya 2 debts:
   - Debt A: Rp 50.000 (OPEN)
   - Debt B: Rp 30.000 (OPEN)
2. Check customer total_debt: should be 80000
3. Bayar Debt A lunas (Rp 50.000)
4. Check customer total_debt: should be 30000
5. Bayar Debt B sebagian (Rp 10.000)
6. Check customer total_debt: should be 20000

### Expected Result:
- Initial total_debt: Rp 80.000
- After full payment Debt A: Rp 30.000
- After partial payment Debt B: Rp 20.000
- Customer total_debt always = sum of remaining debts
- Auto-sync after every payment

### Actual Result:
```
Initial total_debt: Rp _____________ (expected: Rp 80.000)
After payment A: Rp _____________ (expected: Rp 30.000)
After payment B: Rp _____________ (expected: Rp 20.000)
Sync working? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DEBT-010: View Payment History
**Priority:** Medium | **Role:** Kasir

### Test Steps:
1. Pilih debt yang sudah ada beberapa payments
2. Klik "History" / "Riwayat Pembayaran"
3. Perhatikan detail

### Expected Result:
- Modal/section history muncul
- Tampil semua payment records untuk debt ini:
  - Tanggal pembayaran
  - Amount
  - Payment method
  - Kasir yang terima
  - Note (jika ada)
- Urut dari terbaru
- Total paid amount = sum of all payments

### Actual Result:
```
History accessible? ⬜ Yes ⬜ No
Number of payments shown: _______
Info lengkap per payment? ⬜ Yes ⬜ No
Sorted by date (newest first)? ⬜ Yes ⬜ No
Total calculation correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

# 9️⃣ DASHBOARD & REPORTING

## UAT-DASH-001: Admin Can Access Admin Dashboard
**Priority:** High | **Role:** Admin

### Test Steps:
1. Login sebagai Admin
2. Redirect/navigate ke dashboard
3. Perhatikan tampilan

### Expected Result:
- URL: /dashboard
- Tampil "Dashboard Admin" atau similar title
- Tampil widget/cards statistik
- Tampil menu navigasi Admin (Produk, Kategori, Stok, dll)
- Tidak tampil menu Kasir

### Actual Result:
```
URL: ___________________________________________________________
Title shown: __________________________________________________
Stats widgets visible? ⬜ Yes ⬜ No
Admin menu visible? ⬜ Yes ⬜ No
Kasir menu visible? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-002: Admin Dashboard - Product Statistics
**Priority:** High | **Role:** Admin

### Test Steps:
1. Di Admin Dashboard
2. Perhatikan widget/card statistik produk

### Expected Result:
- Tampil card "Total Kategori" dengan jumlah
- Tampil card "Total Produk" dengan jumlah
- Tampil card "Produk Aktif" dengan jumlah
- Tampil card "Total Stok" atau "Low Stock Alert"
- Angka update real-time (saat ada perubahan)

### Actual Result:
```
Total Kategori: _______ (verify with actual count)
Total Produk: _______ (verify with actual count)
Produk Aktif: _______
Other stats shown: ____________________________________________
Accurate? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-003: Admin Dashboard - Quick Links
**Priority:** Medium | **Role:** Admin

### Test Steps:
1. Di Admin Dashboard
2. Cari section "Quick Links" atau "Quick Actions"
3. Klik setiap link dan verify redirect

### Expected Result:
- Link "Manajemen Produk" → /products
- Link "Manajemen Kategori" → /product-categories
- Link "Stok Masuk" → /stok-masuk
- Link "Stock Adjustment" → /stock-adjustments
- Semua link berfungsi
- Redirect benar

### Actual Result:
```
Quick links available? ⬜ Yes ⬜ No
Links tested:
  Produk: ⬜ Working ⬜ Broken
  Kategori: ⬜ Working ⬜ Broken
  Stok Masuk: ⬜ Working ⬜ Broken
  Stock Adjustment: ⬜ Working ⬜ Broken
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-004: Kasir Can Access Kasir Dashboard
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Login sebagai Kasir
2. Redirect/navigate ke dashboard
3. Perhatikan tampilan

### Expected Result:
- URL: /kasir/dashboard
- Tampil "Dashboard Kasir" atau similar
- Tampil widget statistik Kasir (transaksi hari ini, dll)
- Tampil menu Kasir (Transaksi, Hutang)
- TIDAK tampil menu Admin (Produk, Kategori)

### Actual Result:
```
URL: ___________________________________________________________
Title shown: __________________________________________________
Kasir stats visible? ⬜ Yes ⬜ No
Kasir menu visible? ⬜ Yes ⬜ No
Admin menu visible? ⬜ Yes ⬜ No (harus No)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-005: Kasir Dashboard - Today's Transactions
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Di Kasir Dashboard
2. Perhatikan widget "Transaksi Hari Ini"
3. Buat 2 transaksi baru (cash)
4. Refresh dashboard
5. Check angka transaksi

### Expected Result:
- Widget "Transaksi Hari Ini" tampil
- Tampil jumlah transaksi hari ini
- Tampil total sales hari ini (Rp)
- Angka update setelah refresh
- Hanya hitung transaksi hari ini (exclude kemarin)

### Actual Result:
```
Before new transactions:
  Count: _______
  Total: Rp _____________

After 2 new transactions:
  Count: _______ (should increase by 2)
  Total: Rp _____________

Accurate? ⬜ Yes ⬜ No
Only today's data? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-006: Kasir Dashboard - Quick Actions
**Priority:** High | **Role:** Kasir

### Test Steps:
1. Di Kasir Dashboard
2. Cari tombol "Transaksi Baru" atau "New Transaction"
3. Klik tombol tersebut
4. Verify redirect
5. Back to dashboard
6. Cari tombol "Pembayaran Hutang"
7. Klik tombol tersebut
8. Verify redirect

### Expected Result:
- Tombol "Transaksi Baru" → redirect ke /transaksi (POS)
- Tombol "Pembayaran Hutang" → redirect ke /kasir/hutang
- Tombol mudah diakses (prominent)
- Redirect cepat

### Actual Result:
```
"Transaksi Baru" button:
  Available? ⬜ Yes ⬜ No
  Redirect to: __________________________________________________
  Working? ⬜ Yes ⬜ No

"Pembayaran Hutang" button:
  Available? ⬜ Yes ⬜ No
  Redirect to: __________________________________________________
  Working? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-007: Owner Can Access Owner Dashboard
**Priority:** High | **Role:** Owner

### Test Steps:
1. Login sebagai Owner
2. Navigate to dashboard
3. Perhatikan tampilan

### Expected Result:
- URL: /dashboard (or /owner/dashboard)
- Tampil "Dashboard Owner" atau similar
- Tampil comprehensive statistics:
  - Total Sales (all time / period)
  - Total Profit
  - Total Debt Outstanding
  - Product performance
- Charts/graphs jika ada
- Menu Owner (read-only views)

### Actual Result:
```
URL: ___________________________________________________________
Title shown: __________________________________________________
Stats shown: __________________________________________________
_________________________________________________________________
Charts available? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-008: Owner Dashboard - Sales Summary
**Priority:** High | **Role:** Owner

### Test Steps:
1. Di Owner Dashboard
2. Perhatikan sales statistics
3. Verify angka dengan data aktual

### Expected Result:
- Tampil "Total Penjualan" (period: hari ini/minggu/bulan)
- Tampil jumlah transaksi
- Tampil total revenue
- Optional: Tampil profit (revenue - cost)
- Angka akurat sesuai database

### Actual Result:
```
Total Penjualan shown: Rp _____________
Period: ⬜ Today ⬜ This Week ⬜ This Month ⬜ All Time
Jumlah Transaksi: _______
Profit shown? ⬜ Yes ⬜ No
Accurate? ⬜ Yes ⬜ No (verify manually)
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-009: Dashboard Auto-Refresh Data
**Priority:** Medium | **Role:** Any

### Test Steps:
1. Login dan buka dashboard (leave open)
2. Di tab/browser lain, buat transaksi baru
3. Kembali ke dashboard tab
4. Manual refresh (F5)
5. Check apakah data terupdate

### Expected Result:
- Setelah manual refresh, data terupdate
- Angka statistik berubah sesuai transaksi baru
- Optional: Auto-refresh tanpa reload (AJAX/Livewire)

### Actual Result:
```
Data updated after refresh? ⬜ Yes ⬜ No
Auto-refresh (no reload)? ⬜ Yes ⬜ No ⬜ N/A
Stats before transaction: _____________________________________
Stats after transaction: ______________________________________
Match expected? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## UAT-DASH-010: Charts/Graphs Display Correctly
**Priority:** Low | **Role:** Owner/Admin

### Test Steps:
1. Di dashboard yang memiliki charts/graphs
2. Perhatikan tampilan visual
3. Check data accuracy
4. Test responsiveness (resize window)

### Expected Result:
- Charts render correctly (tidak broken)
- Data di chart match dengan angka statistik
- Legend/labels jelas
- Responsive (resize dengan baik)
- Interactive (hover untuk detail) - optional
- Types: Line chart, bar chart, pie chart, dll

### Actual Result:
```
Charts available? ⬜ Yes ⬜ No
Types: ⬜ Line ⬜ Bar ⬜ Pie ⬜ Other: ___________________
Render correctly? ⬜ Yes ⬜ No
Data accurate? ⬜ Yes ⬜ No
Responsive? ⬜ Yes ⬜ No
Interactive? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial | ⬜ Blocked  
**Screenshot:** ⬜ Attached  
**Notes:** _____________________________________________________

---

## 📊 Progress Tracker - Part 3

**Stock Adjustment (cont):** ___/6 completed  
**POS Cash (cont):** ___/2 completed  
**POS Debt:** ___/12 completed  
**Debt Management:** ___/10 completed  
**Dashboard:** ___/10 completed

**Overall Part 3:** ___/40 tests

---

## 🎯 Critical Tests Summary - Part 3

Must pass for production:
- [ ] ADJ-002: Stock adjustment works
- [ ] POS-CASH-011: FIFO with multiple batches
- [ ] POS-DEBT-001: Create debt transaction
- [ ] POS-DEBT-005: Partial payment (DP)
- [ ] POS-DEBT-009: Stock deducted on debt
- [ ] DEBT-005: Full payment works
- [ ] DEBT-006: Partial payment works
- [ ] DEBT-008: Status transition (PARTIAL → PAID)
- [ ] DASH-001: Admin dashboard accessible
- [ ] DASH-004: Kasir dashboard accessible

---

## ✅ Complete Testing Sign-Off

**All 3 Parts Completed:**
- [ ] Part 1: Auth, Category, Product (33 tests)
- [ ] Part 2: Stock, POS Cash (39 tests)  
- [ ] Part 3: Adjustment, POS Debt, Debt Mgmt, Dashboard (40 tests)

**Total Progress: ___/112 tests (___%)** 

**Final Status:**
⬜ APPROVED - Ready for Production  
⬜ APPROVED WITH CONDITIONS - Minor fixes needed  
⬜ NOT APPROVED - Major issues remain

---

**Tested by:** _________________  
**Date:** _________________  
**Sign:** _________________

---

_End of UAT Part 3 - Complete UAT Documentation_