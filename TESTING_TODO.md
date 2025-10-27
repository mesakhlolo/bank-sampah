# 📋 TESTING TODO LIST - Bank Sampah

## 🧪 TESTING CHECKLIST

### **A. DASHBOARD** (`/dashboard`)

-   [x] 1. Total Setoran hari ini tampil dengan benar (warna hijau, text putih)
-   [-] 2. Total Penarikan hari ini tampil dengan benar (warna merah, text putih)
    bug: background warna merah tidak tampil
-   [x] 3. Jumlah Transaksi hari ini tampil dengan benar (warna biru, text putih)
-   [x] 4. Top 3 Kategori hari ini tampil (jika ada transaksi hari ini)
-   [x] 5. Semua quick action buttons berfungsi

---

### **B. KELOLA NASABAH** (`/customers`)

#### B.1. List Nasabah

-   [x] 1. Semua nasabah tampil dalam tabel
-   [x] 2. Search berfungsi (cari by nama/kode/telepon)
-   [-] 3. Pagination berfungsi
    note: kemungkinan berfungsi, data belum banyak sampai pagination muncul
-   [x] 4. Button "Tambah Nasabah" visible

#### B.2. Tambah Nasabah (`/customers/create`)

-   [x] 5. **Kode Anggota tampil otomatis** (readonly, background abu-abu)
-   [x] 6. Format kode: `NSB-0001`, `NSB-0002`, dst
-   [x] 7. Field nama required
-   [x] 8. Tanggal bergabung default hari ini
-   [x] 9. Bisa simpan nasabah baru
-   [x] 10. Redirect ke list setelah simpan
-   [x] 11. Kode anggota tidak duplicate
        note: berhasil, kode nasabah dari NSB--6766 ke NSB--6765, berkurang 1

#### B.3. Detail Nasabah (`/customers/{id}`)

-   [-] 12. Saldo tampil dengan benar
    bug: text tidak terlihat, background putih, text juga putih
-   [x] 13. Riwayat setoran tampil (10 terakhir)
-   [x] 14. Riwayat penarikan tampil (10 terakhir)
-   [x] 15. Buku besar tampil (20 terakhir)
-   [x] 16. Link "Lihat Semua Mutasi" berfungsi

#### B.4. Edit & Delete

-   [x] 17. Admin bisa edit nasabah
-   [x] 18. Admin bisa delete nasabah
-   [x] 19. Operator tidak bisa delete nasabah
        note: apakah logika seperti ini oke? operator bisa add new nasabah, tapi tidak bisa delete

---

### **C. TRANSAKSI SETORAN** (`/deposits`)

#### C.1. List Transaksi

-   [x] 1. Tampil gabungan setoran + penarikan
-   [x] 2. **Filter by Tipe Transaksi** (Semua/Setoran/Penarikan) berfungsi
-   [x] 3. **Filter by Nasabah** berfungsi dengan benar
-   [-] 4. **Filter by Date Range** berfungsi
    bug: tidak bisa filter by date, tidak ada transaksi yg tampil
-   [-] 5. **Search by Code** berfungsi
    bug: tidak bisa search filer by code, tidak ada transaksi yg tampil
-   [x] 6. Button "Reset" clear semua filter
-   [-] 7. Pagination membawa query string filter
    note: data hanya 15, pagination belum muncul
-   [x] 8. Color coding: Setoran (hijau), Penarikan (merah)

#### C.2. Buat Setoran (`/deposits/create`)

-   [x] 9. Dropdown nasabah tampil semua
       note: tidak bisa search ketik key huruf, jadi harus select manual, tidak seperti saat filter nasabah di riwayat transaksi
-   [x] 10. Dropdown kategori sampah tampil (yang aktif saja)
-   [x] 11. Harga per kg tampil otomatis saat pilih kategori
-   [x] 12. Bisa tambah multiple items (+ button)
-   [x] 13. Bisa hapus item (- button)
-   [x] 14. **Auto calculation subtotal** berfungsi (berat × harga)
-   [x] 15. **Auto calculation total** berfungsi (sum semua subtotal)
-   [x] 16. Tanggal transaksi default hari ini
-   [x] 17. Validasi: minimal 1 item
-   [x] 18. Validasi: berat harus > 0
-   [x] 19. Simpan berhasil, redirect ke detail
-   [x] 20. **Saldo nasabah bertambah** (cek di profil)

#### C.3. Detail Setoran (`/deposits/{id}`)

-   [x] 21. Info nasabah tampil
-   [x] 22. Info operator (creator) tampil
-   [x] 23. Semua items tampil dengan detail
-   [x] 24. **Total berat tampil dengan benar** (sum semua weight_kg)
-   [x] 25. Total nilai tampil dengan benar
-   [x] 26. Button "Download PDF" berfungsi
-   [x] 27. PDF generate dengan layout yang baik

---

### **D. TRANSAKSI PENARIKAN** (`/withdrawals`)

#### D.1. Buat Penarikan (`/withdrawals/create`)

-   [x] 1. Dropdown nasabah tampil semua
-   [x] 2. **Saldo tersedia tampil** saat pilih nasabah
-   [x] 3. Input nominal
-   [x] 4. Validasi: nominal tidak boleh > saldo
-   [x] 5. Tanggal transaksi default hari ini
-   [x] 6. Catatan (optional)
-   [x] 7. Simpan berhasil, redirect ke detail
-   [x] 8. **Saldo nasabah berkurang** (cek di profil)

#### D.2. Detail Penarikan (`/withdrawals/{id}`)

-   [x] 9. Info nasabah tampil
-   [x] 10. Info operator tampil
-   [x] 11. Nominal tampil
-   [x] 12. Catatan tampil (jika ada)
-   [x] 13. Button "Download PDF" berfungsi
-   [x] 14. PDF generate dengan layout yang baik

---

### **E. LAPORAN** (`/reports/summary`)

#### E.1. Filter & Display

-   [x] 1. Default tampil data bulan ini
-   [-] 2. **Filter by date range** berfungsi
    bug: tidak ada data yg ditampilkan, full kosong
-   [x] 3. Button "Tampilkan" apply filter
-   [x] 4. **Button "Reset"** kembali ke default (bulan ini)
-   [x] 5. URL berubah saat filter (query string)

#### E.2. Metrics

-   [x] 6. **Total Setoran (Rp)** tampil dengan benar
-   [x] 7. **Total Setoran (kg)** tampil dengan benar (BUKAN 0.00)
-   [x] 8. Jumlah transaksi setoran tampil
-   [x] 9. Total Penarikan (Rp) tampil
-   [x] 10. Jumlah transaksi penarikan tampil
-   [x] 11. Perubahan Saldo Bersih tampil (setoran - penarikan)

#### E.3. Breakdown per Kategori

-   [x] 12. Tabel kategori tampil (jika ada data)
-   [x] 13. Setiap kategori menampilkan:
    -   [x] Nama kategori
    -   [x] Total berat (kg)
    -   [x] Total nilai (Rp)
    -   [x] Jumlah transaksi
-   [x] 14. Footer tabel menampilkan TOTAL semua kategori
-   [x] 15. Sort by total_amount descending

#### E.4. Top 10 Customers

-   [x] 16. Tabel top customers tampil (jika ada data)
-   [x] 17. Setiap customer menampilkan:
    -   [x] Nama
    -   [x] Telepon
    -   [x] Total nilai setoran
    -   [-] Total berat
        bug: beratnya 0 semua
    -   [x] Jumlah transaksi
-   [x] 18. Sort by total_amount descending
        note: dari total nilai rp dari paling tinggi
-   [x] 19. Maksimal 10 customers

#### E.5. Export PDF

-   [x] 20. Button "Download PDF" berfungsi
-   [x] 21. PDF menampilkan semua data yang sama dengan web
-   [x] 22. Nama file: `Laporan-Ringkasan-{start}-{end}.pdf`
-   [x] 23. Layout PDF rapih dan readable

---

### **F. BUKU BESAR (LEDGER)** (`/customers/{id}/ledger`)

-   [x] 1. Accessible dari customer profile (link "Lihat Semua Mutasi")
-   [x] 2. Info customer tampil di atas
-   [x] 3. Saldo saat ini tampil
-   [x] 4. Tabel mutasi tampil chronological (ascending)
-   [x] 5. Setiap entry menampilkan:
    -   [x] Tanggal & waktu
    -   [x] Keterangan (Setoran #XXX / Penarikan #XXX)
    -   [x] Tipe (Badge: Debit hijau / Kredit merah)
    -   [x] Debit amount (jika ada)
    -   [x] Kredit amount (jika ada)
    -   [x] **Running balance** (saldo berjalan)
-   [x] 6. Running balance dihitung dengan benar:
    -   Debit (+) menambah saldo
    -   Kredit (-) mengurangi saldo
-   [x] 7. Saldo akhir di tabel = saldo saat ini customer

---

### **G. MASTER DATA (Admin Only)**

#### G.1. Kategori Sampah (`/waste-categories`)

-   [x] 1. Login sebagai Admin
-   [x] 2. Menu "Kelola Kategori Sampah" visible
-   [x] 3. List kategori tampil
-   [-] 4. Status (Aktif/Nonaktif) tampil
    bug: ketika edit dan discentang (disable) kategori aktif, kategorinya tetap aktif
-   [x] 5. Harga aktif saat ini tampil
-   [x] 6. Bisa tambah kategori baru
-   [x] 7. Bisa edit kategori
-   [-] 8. Bisa toggle active/inactive
    bug: tidak bisa disable dan active
-   [x] 9. Bisa hapus kategori (jika tidak ada transaksi)
       note: tidak ada info jika tidak ada transaksi, belum coba hapus kategoi yang ada transaksi

#### G.2. Harga Sampah

-   [x] 10. Bisa tambah harga baru untuk kategori
        note: bukankah lebih bagus untuk ganti harga saja? kenapa harus tambah harga?
-   [x] 11. Input: price_per_kg dan effective_from
-   [x] 12. History harga tampil
        note: hanya harga terbaru, karena saya prefer begini, kalau bisa tidak perlu tambah harga, tapi lebih ke penggantian harga saja.
-   [x] 13. Harga aktif saat ini di-highlight
-   [x] 14. Validasi: effective_from harus valid date

#### G.3. Authorization (Operator)

-   [x] 15. Login sebagai Operator
-   [x] 16. Menu "Kelola Kategori Sampah" **TIDAK VISIBLE**
-   [-] 17. Akses direct URL `/waste-categories` → **403 Forbidden**
    bug: masih bisa di akses, tapi tidak ada action yg bisa dilakukan
-   [x] 18. Tidak bisa tambah/edit/delete kategori
-   [x] 19. Tidak bisa tambah/edit/delete harga

---

### **H. AUTHORIZATION & SECURITY**

#### H.1. Role Access

-   [x] 1. Admin: Full access ke semua fitur
-   [x] 2. Operator: Access ke transaksi & view nasabah saja
-   [x] 3. Operator tidak bisa:
    -   [x] Delete customer
    -   [x] Manage waste categories
    -   [x] Manage waste prices

#### H.2. Session & Login

-   [x] 4. Logout berfungsi
-   [x] 5. Session timeout setelah idle (Laravel default: 120 menit)
-   [x] 6. Redirect ke login jika akses protected route
-   [x] 7. Remember me checkbox berfungsi

---

### **I. UI/UX & RESPONSIVE**

#### I.1. Responsive Design

-   [x] 1. Dashboard responsive di mobile
-   [x] 2. Tables scrollable horizontal di mobile
-   [x] 3. Forms usable di mobile
-   [x] 4. Navigation menu collapse di mobile
-   [x] 5. Buttons size appropriate di mobile

#### I.2. Visual Feedback

-   [x] 6. Success notification tampil saat save
-   [x] 7. Error notification tampil saat validation fail
-   [x] 8. Loading state saat submit form (optional)
-   [x] 9. Hover states pada buttons
-   [x] 10. Active states pada navigation

#### I.3. Accessibility

-   [x] 11. Form labels jelas
-   [x] 12. Error messages descriptive
-   [x] 13. Required fields marked (\*)
-   [x] 14. Color contrast ratio acceptable
-   [x] 15. Tab navigation berfungsi

---

### **J. DATA INTEGRITY**

#### J.1. Saldo Calculation

-   [x] 1. Buat 3 setoran untuk nasabah A
-   [x] 2. Cek saldo di profil = sum semua setoran
-   [x] 3. Buat 1 penarikan untuk nasabah A
-   [x] 4. Cek saldo = sum setoran - penarikan
-   [x] 5. Cek ledger: running balance match dengan saldo akhir

#### J.2. Transaction Flow

-   [x] 6. Setiap setoran membuat:
    -   [x] 1 record di `deposits`
    -   [x] N records di `deposit_items` (sesuai jumlah item)
    -   [x] 1 record di `ledger_entries` (direction: KREDIT)
-   [x] 7. Setiap penarikan membuat:
    -   [x] 1 record di `withdrawals`
    -   [x] 1 record di `ledger_entries` (direction: DEBIT)

#### J.3. Code Generation

-   [x] 8. Kode nasabah unique (NSB-xxxx)
    note: berkurang dari NSB-6565 ke NSB-6564, apakah nanti bisa habis ini? 
-   [x] 9. Kode setoran unique (DEP-YYYYMMDD-xxxx)
-   [x] 10. Kode penarikan unique (WDR-YYYYMMDD-xxxx)
-   [x] 11. Multiple transaksi di hari yang sama: counter increment

---

## 🚨 CRITICAL BUGS TO REPORT

Jika menemukan issue berikut, segera laporkan:

-   [-] **Data loss**: Transaksi hilang atau data corrupt
-   [-] **Saldo incorrect**: Saldo tidak match dengan ledger
-   [-] **Duplicate codes**: Kode transaksi atau kode nasabah duplicate
-   [-] **Authorization bypass**: Operator bisa akses fitur admin
-   [-] **PDF generation error**: PDF tidak ter-generate atau error layout
-   [-] **Database error**: SQL error saat save/retrieve data
-   [-] **Calculation error**: Total atau subtotal tidak akurat

---
