# 📋 UAT Summary - Quick Reference Checklist

**Project:** POS System v1.0  
**Tester:** _________________  
**Date:** _________________

---

## ✅ Quick Test Checklist

### 1️⃣ AUTHENTICATION (8 tests)
- [ ] AUTH-001: Login as Admin
- [ ] AUTH-002: Login as Kasir  
- [ ] AUTH-003: Login with wrong credentials
- [ ] AUTH-004: Logout
- [ ] AUTH-005: Access without login (blocked)
- [ ] AUTH-006: Kasir cannot access admin routes
- [ ] AUTH-007: Session timeout
- [ ] AUTH-008: Multiple browser sessions

**Status:** ___/8 Pass | Priority: HIGH

---

### 2️⃣ CATEGORY MANAGEMENT (10 tests)
- [ ] CAT-001: View list
- [ ] CAT-002: Add new category
- [ ] CAT-003: Add with empty name (validation)
- [ ] CAT-004: Edit category
- [ ] CAT-005: Delete (without products)
- [ ] CAT-006: Delete (with products) - should fail
- [ ] CAT-007: Toggle status
- [ ] CAT-008: Search
- [ ] CAT-009: Filter by status
- [ ] CAT-010: Sorting

**Status:** ___/10 Pass | Priority: HIGH

---

### 3️⃣ PRODUCT MANAGEMENT (15 tests)
- [ ] PROD-001: View list
- [ ] PROD-002: Add simple product
- [ ] PROD-003: Add with multi-unit (Dus, Karton)
- [ ] PROD-004: Validation (required fields)
- [ ] PROD-005: Auto-calculate margin
- [ ] PROD-006: Edit product
- [ ] PROD-007: Delete (no transactions)
- [ ] PROD-008: Delete (with transactions) - should fail
- [ ] PROD-009: Toggle status (hide from POS)
- [ ] PROD-010: Search by name/code
- [ ] PROD-011: Filter by category
- [ ] PROD-012: Filter by status
- [ ] PROD-013: View detail
- [ ] PROD-014: Generate barcode
- [ ] PROD-015: View history log

**Status:** ___/15 Pass | Priority: HIGH

---

### 4️⃣ STOCK MANAGEMENT - BATCHES (12 tests)
- [ ] BATCH-001: View list
- [ ] BATCH-002: Add new batch
- [ ] BATCH-003: Add with expiry date
- [ ] BATCH-004: Validation (qty > 0)
- [ ] BATCH-005: Edit batch
- [ ] BATCH-006: Delete (unused batch)
- [ ] BATCH-007: Delete (used batch) - should fail
- [ ] BATCH-008: Update master price from batch
- [ ] BATCH-009: Search by product
- [ ] BATCH-010: View detail
- [ ] BATCH-011: View history log
- [ ] BATCH-012: FIFO ordering (different dates)

**Status:** ___/12 Pass | Priority: HIGH

---

### 5️⃣ STOCK ADJUSTMENT (8 tests)
- [ ] ADJ-001: View list
- [ ] ADJ-002: Create (single product)
- [ ] ADJ-003: Create (multiple products)
- [ ] ADJ-004: Validation (reason required)
- [ ] ADJ-005: View summary (net change)
- [ ] ADJ-006: Adjust qty up/down
- [ ] ADJ-007: View detail
- [ ] ADJ-008: Log recorded in batch history

**Status:** ___/8 Pass | Priority: MEDIUM

---

### 6️⃣ POS - CASH TRANSACTIONS (10 tests)
- [ ] POS-CASH-001: Search and add to cart
- [ ] POS-CASH-002: Adjust qty (+/-)
- [ ] POS-CASH-003: Input qty manually
- [ ] POS-CASH-004: Change unit (Pcs → Dus)
- [ ] POS-CASH-005: Remove item from cart
- [ ] POS-CASH-006: Complete cash payment
- [ ] POS-CASH-007: Calculate change correctly
- [ ] POS-CASH-008: Stock deduction (FIFO)
- [ ] POS-CASH-009: Insufficient stock validation
- [ ] POS-CASH-010: Cancel transaction

**Status:** ___/10 Pass | Priority: HIGH

---

### 7️⃣ POS - DEBT TRANSACTIONS (12 tests)
- [ ] POS-DEBT-001: Create debt transaction
- [ ] POS-DEBT-002: Select customer
- [ ] POS-DEBT-003: Add customer on-the-fly
- [ ] POS-DEBT-004: Input jaminan (collateral)
- [ ] POS-DEBT-005: Partial payment (DP)
- [ ] POS-DEBT-006: Zero partial payment (full debt)
- [ ] POS-DEBT-007: Debt status = OPEN
- [ ] POS-DEBT-008: Debt status = PARTIAL (if DP)
- [ ] POS-DEBT-009: Stock still deducted
- [ ] POS-DEBT-010: Customer total_debt updated
- [ ] POS-DEBT-011: Validation (customer required)
- [ ] POS-DEBT-012: Invoice generated

**Status:** ___/12 Pass | Priority: HIGH

---

### 8️⃣ DEBT MANAGEMENT (10 tests)
- [ ] DEBT-001: View customer list (with debt)
- [ ] DEBT-002: Search by name
- [ ] DEBT-003: Search by phone
- [ ] DEBT-004: Select customer → view debts
- [ ] DEBT-005: Full payment
- [ ] DEBT-006: Partial payment
- [ ] DEBT-007: Payment > remaining (validation)
- [ ] DEBT-008: Status PARTIAL → PAID
- [ ] DEBT-009: Customer total_debt updated
- [ ] DEBT-010: View payment history

**Status:** ___/10 Pass | Priority: HIGH

---

### 9️⃣ DASHBOARD & REPORTING (10 tests)
- [ ] DASH-001: Admin can access admin dashboard
- [ ] DASH-002: Admin stats (products, categories)
- [ ] DASH-003: Admin quick links working
- [ ] DASH-004: Kasir can access kasir dashboard
- [ ] DASH-005: Kasir stats (today's transactions)
- [ ] DASH-006: Kasir quick actions (Transaksi, Hutang)
- [ ] DASH-007: Owner can access owner dashboard
- [ ] DASH-008: Owner stats (sales summary)
- [ ] DASH-009: Dashboard auto-refresh data
- [ ] DASH-010: Charts/graphs display correctly

**Status:** ___/10 Pass | Priority: MEDIUM

---

## 📊 Overall Summary

| Module | Total | Pass | Fail | Partial | Blocked | % |
|--------|-------|------|------|---------|---------|---|
| Authentication | 8 | ___ | ___ | ___ | ___ | ___% |
| Categories | 10 | ___ | ___ | ___ | ___ | ___% |
| Products | 15 | ___ | ___ | ___ | ___ | ___% |
| Stock Batches | 12 | ___ | ___ | ___ | ___ | ___% |
| Stock Adj | 8 | ___ | ___ | ___ | ___ | ___% |
| POS Cash | 10 | ___ | ___ | ___ | ___ | ___% |
| POS Debt | 12 | ___ | ___ | ___ | ___ | ___% |
| Debt Mgmt | 10 | ___ | ___ | ___ | ___ | ___% |
| Dashboard | 10 | ___ | ___ | ___ | ___ | ___% |
| **TOTAL** | **95** | **___** | **___** | **___** | **___** | **___%** |

---

## 🐛 Critical Bugs Found

| # | Test ID | Module | Description | Severity |
|---|---------|--------|-------------|----------|
| 1 | | | | ⬜ High ⬜ Medium ⬜ Low |
| 2 | | | | ⬜ High ⬜ Medium ⬜ Low |
| 3 | | | | ⬜ High ⬜ Medium ⬜ Low |
| 4 | | | | ⬜ High ⬜ Medium ⬜ Low |
| 5 | | | | ⬜ High ⬜ Medium ⬜ Low |

---

## 🎯 Test Environment

**Browser:** ⬜ Chrome ⬜ Firefox ⬜ Edge ⬜ Safari  
**OS:** ⬜ Windows ⬜ Mac ⬜ Linux  
**Screen Resolution:** __________  
**Network:** ⬜ WiFi ⬜ LAN ⬜ Mobile  

---

## ⚡ Critical Path Testing

These tests MUST pass for production:

### Must Pass (Critical):
- [ ] AUTH-001: Admin can login
- [ ] AUTH-002: Kasir can login
- [ ] PROD-002: Can add product
- [ ] BATCH-002: Can add stock
- [ ] POS-CASH-006: Can complete cash transaction
- [ ] POS-CASH-008: FIFO works correctly
- [ ] POS-DEBT-001: Can create debt
- [ ] DEBT-005: Can pay debt

### Should Pass (Important):
- [ ] CAT-002: Can add category
- [ ] PROD-003: Multi-unit works
- [ ] ADJ-002: Can adjust stock
- [ ] POS-DEBT-005: Partial payment works

---

## 📝 Testing Notes

### General Observations:
```
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

### Performance Issues:
```
_________________________________________________________________
_________________________________________________________________
```

### UI/UX Issues:
```
_________________________________________________________________
_________________________________________________________________
```

### Suggestions for Improvement:
```
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

---

## ✅ Final Assessment

**Overall System Quality:**  
⬜ Excellent (95-100% pass)  
⬜ Good (85-94% pass)  
⬜ Acceptable (75-84% pass)  
⬜ Needs Work (<75% pass)

**Recommendation:**  
⬜ APPROVED - Ready for production  
⬜ APPROVED WITH CONDITIONS - Minor fixes needed  
⬜ NOT APPROVED - Major issues must be resolved

**Critical Issues:** _____  
**High Priority Issues:** _____  
**Medium Priority Issues:** _____  
**Low Priority Issues:** _____

---

## ✍️ Sign-Off

**Tested By:**  
Name: _________________  
Role: _________________  
Date: _________________  
Signature: _________________

**Approved By:**  
Name: _________________  
Role: _________________  
Date: _________________  
Signature: _________________

---

## 📎 Attachments

- [ ] Screenshot folder attached
- [ ] Bug report document attached
- [ ] Test data spreadsheet attached
- [ ] Video recordings (if any)

---

**For detailed test cases, refer to:**
- UAT-POS-Part1.md (Auth, Categories, Products)
- UAT-POS-Part2.md (Stock, POS Cash)
- UAT-POS-Part3.md (Stock Adj, POS Debt, Debt Mgmt, Dashboard)

---

_Generated: February 2026_
