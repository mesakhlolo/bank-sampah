1. halaman menu cepat di dashboard admin yaitu riyawat transaksi tidak ada, dia pindah ke halaman http://127.0.0.1:8000/deposits yang mana sudah kita perbaiki agar fokus ke setoran.

2. ui dari http://127.0.0.1:8000/deposits dan http://127.0.0.1:8000/withdrawals berbeda, tapi saya lebih suka yg di deposits agar lebih konsisten (saya tidak tau mana yg lebih benar) (konsisten menggunakan tailwind).

3. ui di kategory sampah terlihat aneh dan clunky, flow dari logic kategori sampah sudah oke, tapi ui nya agak jelek dan tidak konsisten. buat agar lebih konsisten seperti yg lain.

4. overall verry good. lainnya sudah bagus sekali.

5. halaman menu cepat di dashboard admin yaitu riyawat transaksi tidak ada, dia pindah ke halaman http://127.0.0.1:8000/deposits yang mana sudah kita perbaiki agar fokus ke setoran.

6. ui dari http://127.0.0.1:8000/deposits dan http://127.0.0.1:8000/withdrawals berbeda, tapi saya lebih suka yg di deposits agar lebih konsisten (saya tidak tau mana yg lebih benar) (konsisten menggunakan tailwind).

7. ui di kategory sampah terlihat aneh dan clunky, flow dari logic kategori sampah sudah oke, tapi ui nya agak jelek dan tidak konsisten. buat agar lebih konsisten seperti yg lain.

8. overall verry good. lainnya sudah bagus sekali.

9. perbaiki readme, fokus pada setup aplikasi sampai bisa lancar jalan di local, fokus di sistem windows dan penggunakan xampp. step by step sampai project jalan di local

---

## ✅ FIXED - Bug #6: Checkbox `is_active` Tidak Update Status Kategori Sampah (29 Oktober 2025)

### Deskripsi Bug:

Ketika admin mengedit kategori sampah dan **uncheck** checkbox "Kategori Aktif", status berhasil diubah muncul, tetapi kategori masih berwarna hijau dan statusnya tetap "Aktif" di database.

### Root Cause:

**HTML checkbox behavior**: Ketika checkbox tidak dicentang, browser **tidak mengirimkan field `is_active`** sama sekali dalam HTTP request (bukan mengirimkan `0` atau `false`).

Laravel hanya memvalidasi field yang ada dalam request, sehingga ketika checkbox tidak dicentang:

-   Field `is_active` tidak ada dalam request
-   `$request->validated()` tidak mengandung key `is_active`
-   `$model->update()` tidak mengupdate kolom `is_active` di database
-   Hasilnya: status tetap tidak berubah

### Solusi:

Menambahkan **hidden input** dengan value `0` sebelum checkbox sebagai fallback value:

```blade
<input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
```

**Cara kerja:**

-   Jika checkbox **dicentang**: browser mengirim `is_active=1` (menimpa hidden input)
-   Jika checkbox **tidak dicentang**: browser mengirim `is_active=0` (dari hidden input)

### Files Changed:

1. `resources/views/waste-categories/edit.blade.php` - Tambah hidden input
2. `resources/views/waste-categories/create.blade.php` - Tambah hidden input (preventive fix)
3. `tests/Feature/WasteCategoryManagementTest.php` - Tambah test cases:
    - ✅ `admin can deactivate waste category`
    - ✅ `admin can reactivate waste category`

### Test Results:

```
✓ admin can deactivate waste category    0.02s
✓ admin can reactivate waste category    0.02s
```

**Status: RESOLVED** ✅
