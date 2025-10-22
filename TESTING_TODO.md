# 📋 TESTING TODO LIST - Bank Sampah

## ✅ Bug Fixes yang Sudah Diterapkan

1. ✅ **Bug #1** - Total Penarikan text putih (dashboard) - _Sebenarnya tidak ada bug, mungkin cache browser_
2. ✅ **Bug #2** - Kode Anggota auto-generate
3. ✅ **Bug #3** - Filter nasabah di transaksi - _Code sudah benar, test dengan data baru_
4. ✅ **Bug #4** - Total setoran 0 kg - Fixed: `total_weight` sekarang di-update
5. ✅ **Bug #5** - Reset button di reports - Added reset button

---

## 🧪 TESTING CHECKLIST

### **A. DASHBOARD** (`/dashboard`)

-   [ ] 1. Total Setoran hari ini tampil dengan benar (warna hijau, text putih)
-   [ ] 2. Total Penarikan hari ini tampil dengan benar (warna merah, text putih)
-   [ ] 3. Jumlah Transaksi hari ini tampil dengan benar (warna biru, text putih)
-   [ ] 4. Top 3 Kategori hari ini tampil (jika ada transaksi hari ini)
-   [ ] 5. Semua quick action buttons berfungsi

**Expected**: Semua metrics tampil dengan jelas, text putih pada background berwarna

---

### **B. KELOLA NASABAH** (`/customers`)

#### B.1. List Nasabah

-   [ ] 1. Semua nasabah tampil dalam tabel
-   [ ] 2. Search berfungsi (cari by nama/kode/telepon)
-   [ ] 3. Pagination berfungsi
-   [ ] 4. Button "Tambah Nasabah" visible

#### B.2. Tambah Nasabah (`/customers/create`)

-   [ ] 5. **Kode Anggota tampil otomatis** (readonly, background abu-abu)
-   [ ] 6. Format kode: `NSB-0001`, `NSB-0002`, dst
-   [ ] 7. Field nama required
-   [ ] 8. Tanggal bergabung default hari ini
-   [ ] 9. Bisa simpan nasabah baru
-   [ ] 10. Redirect ke list setelah simpan
-   [ ] 11. Kode anggota tidak duplicate

#### B.3. Detail Nasabah (`/customers/{id}`)

-   [ ] 12. Saldo tampil dengan benar
-   [ ] 13. Riwayat setoran tampil (10 terakhir)
-   [ ] 14. Riwayat penarikan tampil (10 terakhir)
-   [ ] 15. Buku besar tampil (20 terakhir)
-   [ ] 16. Link "Lihat Semua Mutasi" berfungsi

#### B.4. Edit & Delete

-   [ ] 17. Admin bisa edit nasabah
-   [ ] 18. Admin bisa delete nasabah
-   [ ] 19. Operator tidak bisa delete nasabah

---

### **C. TRANSAKSI SETORAN** (`/deposits`)

#### C.1. List Transaksi

-   [ ] 1. Tampil gabungan setoran + penarikan
-   [ ] 2. **Filter by Tipe Transaksi** (Semua/Setoran/Penarikan) berfungsi
-   [ ] 3. **Filter by Nasabah** berfungsi dengan benar
-   [ ] 4. **Filter by Date Range** berfungsi
-   [ ] 5. **Search by Code** berfungsi
-   [ ] 6. Button "Reset" clear semua filter
-   [ ] 7. Pagination membawa query string filter
-   [ ] 8. Color coding: Setoran (hijau), Penarikan (merah)

#### C.2. Buat Setoran (`/deposits/create`)

-   [ ] 9. Dropdown nasabah tampil semua
-   [ ] 10. Dropdown kategori sampah tampil (yang aktif saja)
-   [ ] 11. Harga per kg tampil otomatis saat pilih kategori
-   [ ] 12. Bisa tambah multiple items (+ button)
-   [ ] 13. Bisa hapus item (- button)
-   [ ] 14. **Auto calculation subtotal** berfungsi (berat × harga)
-   [ ] 15. **Auto calculation total** berfungsi (sum semua subtotal)
-   [ ] 16. Tanggal transaksi default hari ini
-   [ ] 17. Validasi: minimal 1 item
-   [ ] 18. Validasi: berat harus > 0
-   [ ] 19. Simpan berhasil, redirect ke detail
-   [ ] 20. **Saldo nasabah bertambah** (cek di profil)

#### C.3. Detail Setoran (`/deposits/{id}`)

-   [ ] 21. Info nasabah tampil
-   [ ] 22. Info operator (creator) tampil
-   [ ] 23. Semua items tampil dengan detail
-   [ ] 24. **Total berat tampil dengan benar** (sum semua weight_kg)
-   [ ] 25. Total nilai tampil dengan benar
-   [ ] 26. Button "Download PDF" berfungsi
-   [ ] 27. PDF generate dengan layout yang baik

---

### **D. TRANSAKSI PENARIKAN** (`/withdrawals`)

#### D.1. Buat Penarikan (`/withdrawals/create`)

-   [ ] 1. Dropdown nasabah tampil semua
-   [ ] 2. **Saldo tersedia tampil** saat pilih nasabah
-   [ ] 3. Input nominal
-   [ ] 4. Validasi: nominal tidak boleh > saldo
-   [ ] 5. Tanggal transaksi default hari ini
-   [ ] 6. Catatan (optional)
-   [ ] 7. Simpan berhasil, redirect ke detail
-   [ ] 8. **Saldo nasabah berkurang** (cek di profil)

#### D.2. Detail Penarikan (`/withdrawals/{id}`)

-   [ ] 9. Info nasabah tampil
-   [ ] 10. Info operator tampil
-   [ ] 11. Nominal tampil
-   [ ] 12. Catatan tampil (jika ada)
-   [ ] 13. Button "Download PDF" berfungsi
-   [ ] 14. PDF generate dengan layout yang baik

---

### **E. LAPORAN** (`/reports/summary`)

#### E.1. Filter & Display

-   [ ] 1. Default tampil data bulan ini
-   [ ] 2. **Filter by date range** berfungsi
-   [ ] 3. Button "Tampilkan" apply filter
-   [ ] 4. **Button "Reset"** kembali ke default (bulan ini)
-   [ ] 5. URL berubah saat filter (query string)

#### E.2. Metrics

-   [ ] 6. **Total Setoran (Rp)** tampil dengan benar
-   [ ] 7. **Total Setoran (kg)** tampil dengan benar (BUKAN 0.00)
-   [ ] 8. Jumlah transaksi setoran tampil
-   [ ] 9. Total Penarikan (Rp) tampil
-   [ ] 10. Jumlah transaksi penarikan tampil
-   [ ] 11. Perubahan Saldo Bersih tampil (setoran - penarikan)

#### E.3. Breakdown per Kategori

-   [ ] 12. Tabel kategori tampil (jika ada data)
-   [ ] 13. Setiap kategori menampilkan:
    -   [ ] Nama kategori
    -   [ ] Total berat (kg)
    -   [ ] Total nilai (Rp)
    -   [ ] Jumlah transaksi
-   [ ] 14. Footer tabel menampilkan TOTAL semua kategori
-   [ ] 15. Sort by total_amount descending

#### E.4. Top 10 Customers

-   [ ] 16. Tabel top customers tampil (jika ada data)
-   [ ] 17. Setiap customer menampilkan:
    -   [ ] Nama
    -   [ ] Telepon
    -   [ ] Total nilai setoran
    -   [ ] Total berat
    -   [ ] Jumlah transaksi
-   [ ] 18. Sort by total_amount descending
-   [ ] 19. Maksimal 10 customers

#### E.5. Export PDF

-   [ ] 20. Button "Download PDF" berfungsi
-   [ ] 21. PDF menampilkan semua data yang sama dengan web
-   [ ] 22. Nama file: `Laporan-Ringkasan-{start}-{end}.pdf`
-   [ ] 23. Layout PDF rapih dan readable

---

### **F. BUKU BESAR (LEDGER)** (`/customers/{id}/ledger`)

-   [ ] 1. Accessible dari customer profile (link "Lihat Semua Mutasi")
-   [ ] 2. Info customer tampil di atas
-   [ ] 3. Saldo saat ini tampil
-   [ ] 4. Tabel mutasi tampil chronological (ascending)
-   [ ] 5. Setiap entry menampilkan:
    -   [ ] Tanggal & waktu
    -   [ ] Keterangan (Setoran #XXX / Penarikan #XXX)
    -   [ ] Tipe (Badge: Debit hijau / Kredit merah)
    -   [ ] Debit amount (jika ada)
    -   [ ] Kredit amount (jika ada)
    -   [ ] **Running balance** (saldo berjalan)
-   [ ] 6. Running balance dihitung dengan benar:
    -   Debit (+) menambah saldo
    -   Kredit (-) mengurangi saldo
-   [ ] 7. Saldo akhir di tabel = saldo saat ini customer

---

### **G. MASTER DATA (Admin Only)**

#### G.1. Kategori Sampah (`/waste-categories`)

-   [ ] 1. Login sebagai Admin
-   [ ] 2. Menu "Kelola Kategori Sampah" visible
-   [ ] 3. List kategori tampil
-   [ ] 4. Status (Aktif/Nonaktif) tampil
-   [ ] 5. Harga aktif saat ini tampil
-   [ ] 6. Bisa tambah kategori baru
-   [ ] 7. Bisa edit kategori
-   [ ] 8. Bisa toggle active/inactive
-   [ ] 9. Bisa hapus kategori (jika tidak ada transaksi)

#### G.2. Harga Sampah

-   [ ] 10. Bisa tambah harga baru untuk kategori
-   [ ] 11. Input: price_per_kg dan effective_from
-   [ ] 12. History harga tampil
-   [ ] 13. Harga aktif saat ini di-highlight
-   [ ] 14. Validasi: effective_from harus valid date

#### G.3. Authorization (Operator)

-   [ ] 15. Login sebagai Operator
-   [ ] 16. Menu "Kelola Kategori Sampah" **TIDAK VISIBLE**
-   [ ] 17. Akses direct URL `/waste-categories` → **403 Forbidden**
-   [ ] 18. Tidak bisa tambah/edit/delete kategori
-   [ ] 19. Tidak bisa tambah/edit/delete harga

---

### **H. AUTHORIZATION & SECURITY**

#### H.1. Role Access

-   [ ] 1. Admin: Full access ke semua fitur
-   [ ] 2. Operator: Access ke transaksi & view nasabah saja
-   [ ] 3. Operator tidak bisa:
    -   [ ] Delete customer
    -   [ ] Manage waste categories
    -   [ ] Manage waste prices

#### H.2. Session & Login

-   [ ] 4. Logout berfungsi
-   [ ] 5. Session timeout setelah idle (Laravel default: 120 menit)
-   [ ] 6. Redirect ke login jika akses protected route
-   [ ] 7. Remember me checkbox berfungsi

---

### **I. UI/UX & RESPONSIVE**

#### I.1. Responsive Design

-   [ ] 1. Dashboard responsive di mobile
-   [ ] 2. Tables scrollable horizontal di mobile
-   [ ] 3. Forms usable di mobile
-   [ ] 4. Navigation menu collapse di mobile
-   [ ] 5. Buttons size appropriate di mobile

#### I.2. Visual Feedback

-   [ ] 6. Success notification tampil saat save
-   [ ] 7. Error notification tampil saat validation fail
-   [ ] 8. Loading state saat submit form (optional)
-   [ ] 9. Hover states pada buttons
-   [ ] 10. Active states pada navigation

#### I.3. Accessibility

-   [ ] 11. Form labels jelas
-   [ ] 12. Error messages descriptive
-   [ ] 13. Required fields marked (\*)
-   [ ] 14. Color contrast ratio acceptable
-   [ ] 15. Tab navigation berfungsi

---

### **J. DATA INTEGRITY**

#### J.1. Saldo Calculation

-   [ ] 1. Buat 3 setoran untuk nasabah A
-   [ ] 2. Cek saldo di profil = sum semua setoran
-   [ ] 3. Buat 1 penarikan untuk nasabah A
-   [ ] 4. Cek saldo = sum setoran - penarikan
-   [ ] 5. Cek ledger: running balance match dengan saldo akhir

#### J.2. Transaction Flow

-   [ ] 6. Setiap setoran membuat:
    -   [ ] 1 record di `deposits`
    -   [ ] N records di `deposit_items` (sesuai jumlah item)
    -   [ ] 1 record di `ledger_entries` (direction: KREDIT)
-   [ ] 7. Setiap penarikan membuat:
    -   [ ] 1 record di `withdrawals`
    -   [ ] 1 record di `ledger_entries` (direction: DEBIT)

#### J.3. Code Generation

-   [ ] 8. Kode nasabah unique (NSB-xxxx)
-   [ ] 9. Kode setoran unique (DEP-YYYYMMDD-xxxx)
-   [ ] 10. Kode penarikan unique (WDR-YYYYMMDD-xxxx)
-   [ ] 11. Multiple transaksi di hari yang sama: counter increment

---

## 🚨 CRITICAL BUGS TO REPORT

Jika menemukan issue berikut, segera laporkan:

-   [ ] **Data loss**: Transaksi hilang atau data corrupt
-   [ ] **Saldo incorrect**: Saldo tidak match dengan ledger
-   [ ] **Duplicate codes**: Kode transaksi atau kode nasabah duplicate
-   [ ] **Authorization bypass**: Operator bisa akses fitur admin
-   [ ] **PDF generation error**: PDF tidak ter-generate atau error layout
-   [ ] **Database error**: SQL error saat save/retrieve data
-   [ ] **Calculation error**: Total atau subtotal tidak akurat

---

## 📝 FEEDBACK FORMAT

Untuk setiap bug yang ditemukan, berikan info berikut:

```
**Bug #X: [Judul Singkat]**

**Halaman**: [URL]
**User**: [Admin/Operator]
**Steps to Reproduce**:
1. ...
2. ...
3. ...

**Expected**: [Apa yang seharusnya terjadi]
**Actual**: [Apa yang terjadi]

**Screenshot**: [Jika ada]
**Priority**: [Critical / High / Medium / Low]
```

---

## ✅ TESTING SUMMARY

**Total Items**: ~200 test cases
**Estimated Time**: 3-4 jam testing menyeluruh

**Priority Testing Order**:

1. **Critical**: A, C, D, E (Core transactions & reports)
2. **High**: B, F, G (Master data & ledger)
3. **Medium**: H, I (Authorization & UI)
4. **Low**: J (Data integrity validation)

---

**Good luck testing! 🚀**
