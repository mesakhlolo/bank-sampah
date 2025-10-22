# REQUIREMENTS.md — Sistem “Bank Sampah” (MVP Siap Presentasi)

> **Tujuan:** Menyediakan aplikasi sederhana namun utuh untuk operasional dasar Bank Sampah: registrasi nasabah, setoran sampah (ditimbang & dihargakan), perhitungan saldo tabungan, penarikan saldo, dan laporan ringkas. Fokus ke alur yang lancar saat demo/presentasi.

---

## 1 Ringkasan Proyek

- **Nama kerja:** BankSampah-MVP
- **Stakeholder utama:**

  - Pengurus/Operator Bank Sampah (Admin/Kasir)
  - Nasabah (warga penyetor)
  - Pimpinan/Koordinator (lihat laporan)

- **Nilai utama:** Transparansi (bukti/riwayat transaksi), kemudahan operasional (satu layar untuk setoran), dan laporan cepat.

---

## 2 Sasaran & Cakupan

### Sasaran

- Mengelola **setoran** nasabah berbasis **timbangan** & **harga per kategori**.
- Mencatat dan menghitung **saldo tabungan nasabah** (dalam rupiah).
- Memfasilitasi **penarikan saldo** (tunai/transfer).
- Menyediakan **laporan ringkas** harian/mingguan/bulanan.

### Cakupan (In-Scope, MVP)

- Manajemen nasabah (CRUD sederhana).
- Master data kategori jenis sampah & harga aktif.
- Proses setoran: pilih nasabah → pilih jenis → input berat → auto-hitungan nilai → simpan → terbitkan bukti.
- Saldo nasabah (mutasi debit/kredit).
- Penarikan saldo (kasir) + bukti.
- Laporan transaksi & ringkasan pemasukan/penarikan.
- Login/otorisasi dasar (Admin vs Operator).
- Cetak/unduh bukti setoran (PDF) **opsional**.

### Di luar Cakupan (Out-of-Scope, untuk versi lanjutan)

- Manajemen stok fisik & penjualan ke pengepul (Vendor) mendetail.
- Integrasi pembayaran (gateway).
- Aplikasi mobile terpisah.
- Multi-cabang/warung (multi-tenant).
- Notifikasi WhatsApp/SMS/Email otomatis.

---

## 3 Peran & Hak Akses

- **Admin**

  - Kelola master data (kategori, harga).
  - Kelola nasabah & pengguna (operator).
  - Melihat semua laporan.
  - Melakukan setoran & penarikan.

- **Operator/Kasir**

  - Melakukan setoran & penarikan.
  - Lihat riwayat transaksi.
  - **Tidak** bisa ubah master harga & hapus transaksi final.

- **Nasabah (opsional portal)**

  - Lihat saldo & riwayat sendiri (jika portal/akun nasabah diaktifkan).

---

## 4 Persona Singkat

- **Ibu Rina (Operator):** butuh form setoran cepat, minimal klik, cetak bukti.
- **Pak Andi (Nasabah):** ingin saldo transparan & bukti setoran/penarikan.
- **Pak RT (Koordinator):** ingin laporan total setoran, nilai rupiah, dan jumlah nasabah aktif per periode.

---

## 5 Alur Utama Aplikasi (Flow)

### 5.1. Alur Login

1. Pengguna masukkan email/username & password.
2. Sistem cek peran → arahkan ke **Dashboard**.

### 5.2. Alur Setoran Sampah

1. Operator buka **Setoran Baru**.
2. Cari & pilih **Nasabah** (by nama/ID/telepon).
3. Tambah item setoran:

   - Pilih **Kategori** (misal: Plastik PET, Kertas, Kaleng).
   - Input **Berat (kg)** → sistem hitung **Subtotal = Berat × Harga Aktif**.
   - Tambah beberapa item jika perlu.

4. Lihat **Total Nilai Setoran** → **Simpan**.
5. Sistem:

   - Membuat **Transaksi Setoran** (kode unik, tanggal/jam).
   - Menambah **Saldo Nasabah** sebesar total.
   - Menyimpan **Mutasi** (DEBIT).

6. (Opsional) **Cetak/Unduh Bukti** setoran untuk nasabah.

### 5.3. Alur Penarikan Saldo

1. Operator buka **Penarikan**.
2. Pilih **Nasabah** → tampilkan **Saldo Tersedia**.
3. Input **Nominal Penarikan** (validasi ≤ saldo).
4. Simpan → sistem:

   - Membuat **Transaksi Penarikan**.
   - Mengurangi **Saldo Nasabah**.
   - Menyimpan **Mutasi** (KREDIT).

5. (Opsional) Cetak bukti penarikan.

### 5.4. Alur Kelola Harga Kategori

1. Admin buka **Master Harga**.
2. Untuk setiap **Kategori**, set **Harga per Kg** & **Tanggal Berlaku (effective_from)**.
3. **Hanya harga aktif** yang dipakai saat setoran (gunakan histori untuk rekam jejak perubahan).

### 5.5. Alur Laporan

- Filter **periode** (tanggal awal–akhir).
- Tampilkan:

  - Total setoran (kg & Rp) per kategori.
  - Total nilai setoran & penarikan.
  - Top 10 nasabah berdasarkan nilai setoran.

- Ekspor PDF/CSV (opsional).

---

## 6 Kebutuhan Fungsional (Functional Requirements)

### 6.1. Autentikasi & Otorisasi

- FR-001: Pengguna dapat login/logout.
- FR-002: Role-based access (Admin, Operator).
- FR-003: Reset password (opsional pada MVP; minimal reset manual oleh Admin).

### 6.2. Manajemen Nasabah

- FR-010: Tambah/Edit/Hapus nasabah (nama, alamat, telepon, nomor anggota, tanggal gabung).
- FR-011: Pencarian cepat nasabah.
- FR-012: Lihat profil nasabah (saldo & riwayat transaksi).

### 6.3. Master Data Kategori & Harga

- FR-020: CRUD kategori (nama, deskripsi singkat, satuan = kg).
- FR-021: Kelola harga per kategori dengan tanggal berlaku (riwayat harga).
- FR-022: Sistem memilih **harga aktif** sesuai tanggal transaksi.

### 6.4. Setoran

- FR-030: Buat transaksi setoran dengan banyak item.
- FR-031: Hitung subtotal item & total otomatis.
- FR-032: Update saldo nasabah (DEBIT) setelah setoran tersimpan.
- FR-033: Generate bukti transaksi (kode, tanggal, nasabah, rincian item, total).
- FR-034: Edit/Hapus **draft** setoran; **final** tidak dapat dihapus (koreksi via **retur/adjustment** opsional).

### 6.5. Penarikan

- FR-040: Buat transaksi penarikan dengan validasi saldo.
- FR-041: Update saldo nasabah (KREDIT).
- FR-042: Cetak bukti penarikan.

### 6.6. Saldo & Mutasi

- FR-050: Tersedia **mutasi saldo** per nasabah (debit/kredit) lengkap dengan referensi transaksi.
- FR-051: Rekap saldo akhir per nasabah.

### 6.7. Laporan

- FR-060: Laporan ringkasan transaksi per periode.
- FR-061: Laporan akumulasi per kategori (kg & Rp).
- FR-062: Ekspor PDF/CSV (opsional).

### 6.8. Audit & Keamanan (Minimum)

- FR-070: Simpan jejak **siapa & kapan** membuat transaksi (created_by, created_at).
- FR-071: Validasi input (angka, berat > 0, nominal > 0).

---

## 7 Kebutuhan Non-Fungsional (NFR)

- NFR-001: **Kemudahan demo**: semua alur inti dapat dilakukan dalam ≤ 5 menit.
- NFR-002: **Kinerja**: Simpan transaksi setoran < 1 detik pada data kecil (MVP).
- NFR-003: **Usability**: Form setoran dapat dioperasikan dengan keyboard (tab/enter).
- NFR-004: **Reliability**: Backup manual mingguan (opsional otomatis).
- NFR-005: **Keamanan**: Password terenkripsi (bcrypt/argon2).
- NFR-006: **Portabilitas**: Dapat dijalankan di environment lokal (PHP/Laravel atau stack sejenis) dan hosting standar.
- NFR-007: **I18N**: Bahasa Indonesia default.

---

## 8 Model Data (Sketsa Minimal)

> **Catatan:** nama kolom disederhanakan; sesuaikan dengan konvensi framework.

**users**

- id, name, email (unik), password_hash, role (admin/operator), created_at

**customers (nasabah)**

- id, member_code (unik), name, phone, address, joined_at, created_at

**waste_categories**

- id, name, description, is_active, created_at

**waste_prices**

- id, waste_category_id (FK), price_per_kg (decimal), effective_from (date), created_at

> **Aturan:** 1 kategori bisa punya banyak harga (riwayat). Harga aktif = yang `effective_from` ≤ tanggal transaksi dan paling baru.

**deposits (setoran_header)**

- id, code (unik), customer_id, total_amount, total_weight, status (draft/final), created_at, created_by (user_id)

**deposit_items**

- id, deposit_id, waste_category_id, weight_kg, price_per_kg, subtotal_amount

**withdrawals (penarikan)**

- id, code (unik), customer_id, amount, note, created_at, created_by

**ledger_entries (mutasi_saldo)**

- id, customer_id, ref_type (‘deposit’/‘withdrawal’), ref_id, direction (‘DEBIT’/‘KREDIT’), amount, created_at

> **Saldo nasabah** dihitung dari sum(mutasi) atau disimpan di kolom `customers.balance` yang terjaga konsistensinya via trigger/service (opsional).

---

## 9 Aturan Bisnis (Business Rules)

- BR-001: **Harga** yang dipakai pada item setoran diambil dari `waste_prices` **aktif** pada tanggal setoran.
- BR-002: Berat minimal 0.01 kg; nominal penarikan ≤ saldo tersedia.
- BR-003: Transaksi **final** mengunci harga & subtotal (tidak berubah walau harga master berganti).
- BR-004: Koreksi kesalahan dilakukan via transaksi penyesuaian (opsional di MVP; atau batalkan & buat ulang jika belum final).
- BR-005: Kode transaksi unik, format bebas (mis. `DEP-YYYYMMDD-####`).

---

## 10 Antarmuka & Layar (Wireframe Tekstual)

- **Dashboard (Operator)**

  - Shortcut: Setoran Baru, Penarikan, Cari Nasabah, Laporan.
  - Kartu ringkas: Total setoran hari ini, jumlah transaksi, top kategori hari ini.

- **Setoran Baru**

  - Cari Nasabah (autocomplete).
  - Tabel Item: [Kategori] [Berat (kg)] [Harga/kg (auto)] [Subtotal] [+ Tambah baris].
  - Ringkasan: Total Berat, Total Rupiah.
  - Tombol: **Simpan & Final**, Cetak (opsional).

- **Penarikan**

  - Pilih Nasabah → tampilkan Saldo.
  - Input Nominal → Simpan → Cetak (opsional).

- **Nasabah**

  - Daftar + Cari.
  - Detail: Profil, Saldo, Riwayat Mutasi.

- **Master Kategori & Harga**

  - Daftar kategori (aktif/nonaktif).
  - Kelola Harga (riwayat + tambah harga baru dengan effective_from).

- **Laporan**

  - Filter tanggal.
  - Ringkasan: total setoran (kg & Rp), total penarikan, tabel per kategori, ekspor.

---

## 11 API (Opsional, Jika Butuh)

Contoh endpoint (REST-ish):

- `POST /auth/login`
- `GET /customers?search=...`
- `POST /deposits` (header + items)
- `POST /withdrawals`
- `GET /customers/{id}/ledger`
- `GET /reports/summary?start=YYYY-MM-DD&end=YYYY-MM-DD`

---

## 12 Kriteria Penerimaan (Acceptance Criteria)

- AC-001: Operator dapat membuat **setoran** dengan ≥ 2 item berbeda, sistem menghitung subtotal & total otomatis, saldo nasabah bertambah sesuai total.
- AC-002: Operator dapat membuat **penarikan**, sistem menolak jika nominal > saldo, dan saldo berkurang setelah transaksi valid.
- AC-003: **Riwayat mutasi** nasabah menampilkan urutan transaksi dengan arah debit/kredit yang benar.
- AC-004: **Laporan** menampilkan total setoran (kg & Rp) per kategori untuk periode tertentu.
- AC-005: **Harga aktif** berubah sesuai `effective_from` tanpa memengaruhi transaksi final yang lampau.
- AC-006: **Role**: Operator tidak bisa mengubah master harga/kategori.

---

## 13 Skenario Uji (Test Scenarios Ringkas)

1. **Setoran Tunggal**

   - Buat nasabah A → setoran 1 kg Plastik @3.000 → total 3.000 → saldo A = 3.000.

2. **Setoran Multi-Kategori**

   - Tambah 2 item (Plastik 1.2 kg, Kertas 0.8 kg) → periksa subtotal & total → saldo bertambah tepat.

3. **Penarikan Valid & Invalid**

   - Tarik 2.000 dari saldo 3.000 (OK) → sisa 1.000.
   - Coba tarik 2.000 lagi (harus **gagal**).

4. **Perubahan Harga**

   - Set harga Plastik@3.000 berlaku 2025-10-01, ubah ke 3.500 berlaku 2025-10-20.
   - Transaksi 2025-10-19 pakai 3.000; transaksi 2025-10-21 pakai 3.500.

5. **Laporan Periode**

   - Input beberapa transaksi di bulan berjalan → laporan total kg & Rp sesuai.

---

## 14 Data Dummy (untuk Demo)

- **Kategori & Harga (effective_from 2025-10-01)**

  - Plastik PET @ 3.000/kg
  - Kertas Campur @ 1.500/kg
  - Kaleng @ 5.000/kg

- **Nasabah**

  - CUST-001 — “Budi Setiawan”, 0812…, alamat A
  - CUST-002 — “Siti Aminah”, 0821…, alamat B

- **Transaksi Contoh**

  - 2025-10-21: Budi setor 1.2 kg PET + 0.5 kg Kertas → total: 1.2×3000 + 0.5×1500 = 3.600 + 750 = **4.350**
  - 2025-10-21: Siti tarik **2.000** (saldo cukup)

---

## 15 Persyaratan Teknis (Rekomendasi untuk Implementasi Cepat)

> **Opsional, menyesuaikan tools yang dikuasai; contoh untuk Laravel.**

- **Backend:** PHP 8.2+, Laravel 11, Eloquent ORM.
- **DB:** MySQL/MariaDB (decimal untuk uang).
- **Auth:** Laravel Breeze/Fortify (role via Gate/Policy/Spatie Permission).
- **UI:** Blade + Tailwind/Alpine (cukup untuk MVP).
- **PDF (opsional):** barryvdh/laravel-dompdf.
- **Testing:** Pest/PhpUnit minimal untuk alur inti.
- **Deploy lokal:** Laragon/XAMPP; Produksi: shared hosting/VPS.
- **Backup:** export DB manual mingguan.

---

## 16 Risiko & Mitigasi

- **Human error penimbangan** → tampilkan konfirmasi & pratinjau sebelum final.
- **Salah harga** → gunakan **effective_from** + kunci harga di item transaksi.
- **Data hilang** → sediakan ekspor CSV & backup DB.

---

## 17 Roadmap Lanjutan (Pasca-MVP)

- Portal nasabah (login lihat saldo/riwayat).
- Penjualan ke pengepul (pencatatan stok keluar & margin).
- Grafik tren kategori & performa nasabah.
- Notifikasi WA/Email bukti transaksi.
- Multi-cabang & peran auditor.

---

## 18 Skrip Demo (≤ 5 menit)

1. Login sebagai **Operator**.
2. Buka **Setoran Baru** → pilih “Budi” → tambah 2 item → **Simpan & Final** → tampilkan bukti & saldo.
3. Buka **Penarikan** → pilih “Siti” → tarik Rp2.000 → tampilkan bukti.
4. Tunjukkan **Riwayat Mutasi** nasabah & **Laporan** periode hari ini.

---

**Selesai.**
Dokumen ini cukup untuk mulai implementasi & presentasi MVP “Bank Sampah”. Jika mau, saya bisa lanjutkan dengan **skema migrasi database** (SQL/Laravel migration) dan **seed data** sesuai daftar “Data Dummy” di atas.
