---
name: bank-sampah-domain
description: Aturan domain Bank Sampah — ledger KREDIT/DEBIT, perhitungan saldo nasabah, format kode transaksi (DEP/WD/CUST/BSB), harga efektif per tanggal, dan relasi model. Gunakan skill ini setiap kali menyentuh model Customer/Deposit/Withdrawal/LedgerEntry/WastePrice, service transaksi, seeder, factory, atau view yang menampilkan saldo dan mutasi.
---

# Domain Bank Sampah

Aplikasi pencatatan setoran sampah warga: timbang → harga per kg → akumulasi saldo → penarikan.

Referensi lengkap: `docs/PROJECT.md`.

## Saldo — aturan paling penting

**Saldo TIDAK disimpan di kolom.** Selalu dihitung dari agregat `ledger_entries`.

```
saldo = SUM(amount WHERE direction='KREDIT') − SUM(amount WHERE direction='DEBIT')
```

Implementasi saat ini: accessor `Customer::getBalanceAttribute()`.

❌ **Jangan** menambah kolom `customers.balance` — itu menciptakan dua sumber kebenaran yang bisa divergen.
❌ **Jangan** `->get()->sum()` di PHP — pakai agregat SQL:

```php
$this->ledgerEntries()
    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'KREDIT' THEN amount ELSE -amount END), 0) as balance")
    ->value('balance');
```

## ⚠️ Arah `direction` terbalik dari intuisi perbankan

Ini sumber bug paling umum. `direction` mengikuti sudut pandang **buku besar bank sampah**, bukan rekening nasabah.

| Kejadian | `direction` | Efek saldo |
|---|---|---|
| Nasabah **setor** sampah | `KREDIT` | **naik** |
| Nasabah **tarik** saldo | `DEBIT` | **turun** |

Jangan "membetulkan" ini saat refactor — test yang ada mengunci perilaku ini.

## Format kode

| Entitas | Format | Sifat |
|---|---|---|
| `Deposit.code` | `DEP-YYYYMMDD-####` | Sekuensial per hari |
| `Withdrawal.code` | `WD-YYYYMMDD-####` | Sekuensial per hari |
| `Customer.member_code` | `CUST-####` | Internal admin, sekuensial |
| `Customer.unique_code` | `BSB-` + 12 char | Portal publik, **cryptographically secure** |

**Generate kode sekuensial punya risiko race condition.** Pola `orderBy('code','desc')` lalu `+1` gagal saat dua request bersamaan. `lockForUpdate()` **tidak menolong** saat query mengembalikan 0 baris (transaksi pertama hari itu — tidak ada row untuk dikunci).

Pola yang benar: `DB::transaction` + `lockForUpdate()` + **retry saat `QueryException` unique**:

```php
for ($attempt = 1; $attempt <= 3; $attempt++) {
    try {
        return Deposit::create(['code' => $code, ...]);
    } catch (\Illuminate\Database\QueryException $e) {
        if ($attempt === 3 || ! $this->isUniqueViolation($e)) {
            throw $e;
        }
        $code = $this->generateDepositCode($date);
    }
}
```

Cocokkan **`UNIQUE constraint failed`** (SQLite) **dan** `Duplicate entry` (MySQL) agar portabel.

## Harga efektif per tanggal

Harga tidak disimpan di kategori — ada tabel riwayat `waste_prices` dengan `effective_from`.

```php
$category->getActivePriceOn($transactionDate);   // WastePrice|null
```

Aturan:
- Ambil record dengan `effective_from <= tanggal transaksi`, urut terbaru
- Jika tidak ada → **lempar exception**, jangan fallback ke harga apa pun
- `DepositItem` **menyimpan salinan** `price_per_kg` & `subtotal` → transaksi final terkunci meski master harga berubah

## Relasi model

```
Customer 1─┬─< Deposit 1─< DepositItem >─ WasteCategory 1─< WastePrice
           ├─< Withdrawal
           └─< LedgerEntry  (morph: ref_type + ref_id → Deposit|Withdrawal)
```

⚠️ `WasteCategory::prices()` dan `wastePrices()` adalah **relasi yang sama** — `wastePrices()` hanya alias untuk backward compatibility. Jangan duplikasi logikanya.

`LedgerEntry::referenceable()` adalah morphTo dengan kolom kustom `ref_type` / `ref_id`.

## Status deposit

`deposits.status` = `draft` | `final`.

Saat ini **selalu `final`** (belum ada dukungan draft). Semua query laporan & dashboard **wajib** memfilter `where('status', 'final')` — jika tidak, angka bisa salah saat draft diimplementasi nanti.

## Operasi transaksional

Setiap operasi yang menulis lebih dari satu tabel **wajib** `DB::transaction`:

- Setoran: `deposits` + `deposit_items` (n) + `ledger_entries`
- Penarikan: `withdrawals` + `ledger_entries`

Validasi saldo dilakukan **di dalam** transaksi, setelah `Customer::findOrFail()` — bukan di controller.

## Seeder & factory

Saat menulis seeder/factory yang membuat `Customer`, `Deposit`, atau `Withdrawal`:
- `member_code` & `unique_code` auto-generate di event `creating` — jangan set manual kecuali untuk test spesifik
- Karena generasi kode butuh transaksi, bungkus pembuatan banyak record dalam `DB::transaction` atau andalkan retry
