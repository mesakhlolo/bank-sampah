1. d ibagian http://127.0.0.1:8000/nasabah/info untuk section faq font tidak terstruktur dan tidak rapi, dari kecil lalu membesar makin ke bawah

2. di bagian http://127.0.0.1:8000/nasabah/transaksi?type=deposits yang mana adalah tombol setoran, yang muncul malah penarikan dan di http://127.0.0.1:8000/nasabah/transaksi?type=withdrawals yang mana tombol penarikan yang muncul malah setoran.

3. dibagian lihat detail di penarikan terjadi error. http://127.0.0.1:8000/nasabah/setoran/19 ketika akses halaman ini terjadi error `Illuminate\Database\Eloquent\RelationNotFoundException
Call to undefined relationship [createdBy] on model [App\Models\Deposit].`

4. tidak tahu ini bug, tapi agak aneh segi logic, pada bagian setoran tidak ada keterangan untuk lihat detail, berbeda dengan penarikan yang punya lihat detail.

5. total setoran dan total penarikan sepertinya malah kebalik infonya
