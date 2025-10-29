# MVP Demo Flow - Bank Sampah VibeCODE

## 🎯 Overview

Aplikasi manajemen bank sampah dengan sistem double-entry bookkeeping untuk tracking deposit sampah dan penarikan saldo nasabah.

---

## 🔐 1. Authentication

-   **Login** sebagai Admin/Staff
-   Role-based access control (Admin & Staff)

---

## 👥 2. Customer Management

### Fitur:

-   ✅ **Lihat daftar nasabah** (index)
-   ✅ **Tambah nasabah baru** (create/store)
-   ✅ **Edit data nasabah** (edit/update)
-   ✅ **Hapus nasabah** (delete)
-   ✅ **Lihat detail & riwayat transaksi** nasabah

**Demo:** Tambah nasabah → Lihat daftar → Detail nasabah

---

## ♻️ 3. Waste Category Management

### Fitur:

-   ✅ **Kelola kategori sampah** (Plastik, Kertas, Logam, dll)
-   ✅ **Set harga per kategori** (per kg)
-   ✅ **Aktifkan/Non-aktifkan** kategori

**Demo:** Tambah kategori "Plastik" → Set harga Rp 2.000/kg

---

## 💰 4. Deposit (Setor Sampah)

### Flow:

1. Pilih nasabah
2. Input multiple items (kategori + berat)
3. Sistem kalkulasi otomatis: **Total = Σ(harga × berat)**
4. Catat transaksi + update saldo nasabah

### Accounting:

```
Debit:  Waste Inventory (asset)
Credit: Customer Balance (liability)
```

**Demo:** Nasabah setor 5kg Plastik @ Rp 2.000 = Rp 10.000 masuk saldo

---

## 💸 5. Withdrawal (Tarik Saldo)

### Flow:

1. Pilih nasabah
2. Input jumlah penarikan
3. Validasi saldo cukup
4. Proses penarikan + update saldo

### Accounting:

```
Debit:  Customer Balance (liability)
Credit: Cash (asset)
```

**Demo:** Nasabah tarik Rp 50.000 dari saldo

---

## 📊 6. Ledger System

### Fitur:

-   ✅ **Double-entry bookkeeping** otomatis
-   ✅ **Audit trail** semua transaksi
-   ✅ **Balance tracking** real-time per nasabah

**Demo:** Lihat ledger entries untuk validasi debit = credit

---

## 🔍 7. Reports & Analytics

### Data yang bisa dilihat:

-   Total saldo semua nasabah
-   Total deposit per kategori sampah
-   Riwayat transaksi lengkap
-   Customer balance history

---

## 🎬 Demo Scenario (5 menit)

### Skenario Complete Flow:

1. **Login** sebagai Admin
2. **Tambah Nasabah** → "Ibu Siti"
3. **Tambah Kategori** → "Botol Plastik" (Rp 3.000/kg)
4. **Deposit Pertama:**
    - Ibu Siti setor 10kg Botol Plastik
    - Saldo: Rp 30.000
5. **Deposit Kedua:**
    - Setor 5kg Kertas @ Rp 1.500/kg
    - Saldo: Rp 30.000 + Rp 7.500 = **Rp 37.500**
6. **Withdrawal:**
    - Ibu Siti tarik Rp 20.000
    - Sisa saldo: **Rp 17.500**
7. **Lihat Ledger** → Validasi semua entry balance

---

## ✨ Key Features Highlight

| Feature             | Status | Description                    |
| ------------------- | ------ | ------------------------------ |
| Customer CRUD       | ✅     | Full management nasabah        |
| Waste Category      | ✅     | Kelola jenis & harga sampah    |
| Multi-item Deposit  | ✅     | Setor berbagai jenis sekaligus |
| Balance Tracking    | ✅     | Real-time per customer         |
| Double-entry Ledger | ✅     | Accounting yang benar          |
| Transaction History | ✅     | Audit trail lengkap            |
| Authorization       | ✅     | Policy-based access control    |

---

## 🚀 Tech Stack

-   **Backend:** Laravel 11
-   **Frontend:** Blade + Alpine.js + Tailwind CSS
-   **Testing:** Pest PHP (TDD approach)
-   **Database:** MySQL (double-entry bookkeeping)
-   **Auth:** Laravel Breeze

---

## 📝 Testing Coverage

✅ Feature tests untuk semua CRUD operations  
✅ Service layer tests (DepositService, WithdrawalService)  
✅ Policy authorization tests  
✅ Database transaction integrity tests

---

**Ready to Demo!** 🎉
