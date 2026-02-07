## UAT-BUGS-001: Modal Tambah Produk Matikan Enter

**Priority:** High | **Role:** Admin

### Test Steps:

1. Saat barcode scan kode maka diakan melaukan typing dengan cepet dan kemudian otomatis enter

### Expected Result:

- Input Barcode langsun menyimpan angka dan Liste Keyboard Enter dihiraukan

### Actual Result:

```
Batch 1 Date: _______ | Batch 2 Date: _______
Urutan correct? ⬜ Yes ⬜ No
```

**Status:** ⬜ Pass
**Screenshot:** ⬜ Attached  
**Notes:** **************************\_**************************

---

## UAT-BUGS-002: Modal Tambah Produk batch Matikan Enter

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
**Notes:** **************************\_**************************

---

## UAT-BUGS-003: Product Code Scan terlalu Cepat di sales

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
**Notes:** **************************\_**************************

---
