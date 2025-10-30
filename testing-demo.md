# 🧪 PANDUAN TESTING SIMULASI - Bank Sampah VibeCODE

> **Dibuat:** 30 Oktober 2025  
> **Target:** Testing Fitur Baru (Portal Nasabah + Landing Page)

---

## 🚀 PERSIAPAN AWAL

### 1. Jalankan Migration & Seeder

```bash
# Pastikan XAMPP MySQL sudah running
php artisan migrate:fresh --seed
```

### 2. Generate Unique Codes untuk Customers

```bash
# Jalankan script untuk generate kode unik
php generate-unique-codes.php
```

**Expected Output:**

```
✓ Budi Setiawan (CUST-0001) -> BSB-LFHR4GB0HNJ6
✓ Siti Aminah (CUST-0002) -> BSB-YXN1SX3WC6XN
...
✅ Done! Generated unique codes for 50 customers.
```

### 3. Ambil Kode Unik Nasabah untuk Testing

Pilih salah satu dari output di atas, contoh:

-   **Budi Setiawan:** `BSB-LFHR4GB0HNJ6`
-   **Siti Aminah:** `BSB-YXN1SX3WC6XN`

Atau cek via database:

```bash
php artisan tinker --execute="print_r(DB::select('SELECT name, member_code, unique_code FROM customers LIMIT 5'));"
```

### 4. Jalankan Server

```bash
php artisan serve
```

Buka browser: `http://127.0.0.1:8000`

---

## 📝 SKENARIO TESTING SISTEM

### 🏠 Skenario 1: Testing Landing Page (5 menit)

1. Buka `http://127.0.0.1:8000`
2. Pastikan tampil landing page (bukan default Laravel)
3. Scroll dari atas ke bawah, cek semua section
4. Klik button "Cek Saldo Saya" → harus ke `/nasabah/cek`
5. Back ke homepage, klik "Masuk" → harus ke `/login`

**Catatan:** Jika ada gambar tidak muncul (404), layout rusak, atau link tidak berfungsi → catat sebagai bug

---

### 🔐 Skenario 2: Testing Portal Nasabah - Login (5 menit)

1. Buka `http://127.0.0.1:8000/nasabah/cek`
2. **Test dengan kode valid:**

    - Paste kode unik dari persiapan awal (contoh: `BSB-LFHR4GB0HNJ6`)
    - Klik "Lihat Saldo"
    - Harus redirect ke dashboard nasabah

3. **Test dengan kode invalid:**

    - Input kode random: `BSB-INVALID00000`
    - Klik "Lihat Saldo"
    - Harus muncul error "Kode tidak ditemukan"

4. **Test dengan member code (CUST-0001):**
    - Harus DITOLAK (security test penting!)
    - Member code sequential = tidak aman

**Catatan:** Jika login berhasil dengan member code → **BUG KRITIS!**

---

### 📊 Skenario 3: Testing Portal Nasabah - Dashboard (5 menit)

Setelah berhasil login:

1. **Cek Dashboard:**

    - Apakah nama nasabah tampil?
    - Apakah saldo tampil dengan format Rp?
    - Apakah ada card statistik (total setoran, transaksi)?

2. **Test Navigation:**

    - Klik "Riwayat Transaksi" → cek apakah table muncul
    - Klik "Info & Bantuan" → cek apakah halaman info muncul
    - Klik "Dashboard" → kembali ke dashboard

3. **Test Logout:**
    - Klik button "Keluar"
    - Harus kembali ke halaman check code
    - Try akses `/nasabah/dashboard` langsung → harus redirect ke check code

**Catatan:** Jika ada page 404, layout error, atau data tidak tampil → catat sebagai bug

---

### 📜 Skenario 4: Testing Riwayat Transaksi (5 menit)

1. Login ke portal, klik "Riwayat Transaksi"
2. **Cek Table:**

    - Apakah ada data transaksi?
    - Apakah kolom Tanggal, Jenis, Debit/Kredit, Saldo ada?
    - Apakah running balance benar?

3. **Test Detail:**

    - Klik salah satu row setoran
    - Harus tampil detail: kode setoran, rincian barang, total berat, total nilai

4. **Test Pagination:**
    - Jika ada > 20 transaksi, cek apakah pagination works

**Catatan:** Jika detail tidak muncul, running balance salah, atau error → catat sebagai bug

---

### 🔄 Skenario 5: End-to-End Flow (10 menit)

**Complete User Journey:**

1. **Admin Side:**

    - Login ke admin dashboard
    - Tambah nasabah baru "Testing User"
    - Copy unique_code dari detail nasabah
    - Logout

2. **Customer Side:**

    - Login ke portal dengan unique_code nasabah baru
    - Cek saldo (harus Rp 0)
    - Cek riwayat (harus kosong)

3. **Admin Side:**

    - Login admin lagi
    - Buat setoran untuk "Testing User": 5kg Plastik = Rp 15.000
    - Save setoran

4. **Customer Side:**
    - Refresh portal
    - Cek saldo (harus Rp 15.000)
    - Cek riwayat (harus ada 1 transaksi setoran)
    - Klik detail setoran → cek breakdown items

**Catatan:** Jika saldo tidak update, transaksi tidak muncul, atau data tidak match → catat sebagai bug

---

### 🔒 Skenario 6: Testing Security (Optional - 5 menit)

**Test Rate Limiting:**

1. Buka portal di incognito window
2. Input kode salah 5 kali berturut-turut
3. Coba ke-6 kali → harus muncul error "Terlalu banyak percobaan"

**Test Authorization:**

1. Login sebagai Customer 1
2. Copy URL detail setoran milik Customer 2
3. Paste URL di browser
4. Harus muncul error 403 Forbidden

**Catatan:** Jika bisa bypass rate limiting atau akses data customer lain → **BUG KRITIS!**

---

### 📱 Skenario 7: Testing Responsive (Optional - 5 menit)

1. Buka Chrome DevTools (F12)
2. Toggle Device Toolbar (Ctrl+Shift+M)
3. Test di:
    - iPhone SE (375px)
    - iPad (768px)
    - Desktop (1920px)

**Cek:**

-   Apakah layout responsive?
-   Apakah text readable?
-   Apakah button tidak terpotong?
-   Apakah ada horizontal scroll?

**Catatan:** Jika layout rusak di mobile → catat sebagai bug

---

## 🐛 FORMAT LAPORAN BUG (SIMPLE)

Jika menemukan bug, kirim dengan format ini:

```
BUG: [Judul singkat]

Severity: [Critical/High/Medium/Low]

Steps:
1. [Langkah 1]
2. [Langkah 2]
3. [Langkah 3]

Expected: [Yang seharusnya terjadi]
Actual: [Yang sebenarnya terjadi]

Screenshot: [Attach jika ada]
```

**Contoh:**

```
BUG: Saldo tidak update setelah setoran baru

Severity: High

Steps:
1. Admin buat setoran Rp 10.000 untuk Customer 1
2. Refresh portal nasabah (sudah login)
3. Saldo masih nilai lama

Expected: Saldo bertambah Rp 10.000
Actual: Saldo tidak berubah

Screenshot: [attached]
```

---

## 🎯 QUICK CHECKLIST

Centang setelah selesai testing:

-   [ ] Landing page tampil & berfungsi
-   [ ] Portal login dengan kode valid berhasil
-   [ ] Portal login dengan member code ditolak (security)
-   [ ] Dashboard nasabah tampil dengan benar
-   [ ] Riwayat transaksi tampil & detail works
-   [ ] End-to-end flow: admin buat setoran → portal update
-   [ ] Rate limiting works (5x gagal = cooldown)
-   [ ] Authorization works (tidak bisa lihat data customer lain)
-   [ ] Responsive di mobile (optional)
-   [ ] No critical bugs found

---

## 📞 TROUBLESHOOTING CEPAT

**Jika ada error, coba:**

```bash
# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check logs
tail -f storage/logs/laravel.log

# Restart server
php artisan serve
```

---

## 🎉 SELAMAT TESTING!

**Focus:** Gunakan aplikasi seperti user biasa, catat apa yang tidak berfungsi atau aneh.

**Jangan worry tentang detail teknis** - cukup test flow aplikasi dan laporkan jika ada yang error/rusak! 💪

---

**📌 Catatan:** Fitur admin sudah tested sebelumnya dan berjalan dengan baik. File ini fokus pada testing fitur baru saja.
