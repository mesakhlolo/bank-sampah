@extends('layouts.portal')

@section('title', 'Info & Bantuan')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Info &amp; Bantuan</h1>
        <p class="mt-1 text-sm text-slate-500">Informasi penting dan harga sampah terkini.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Prices -->
            <x-card title="Harga sampah terkini" description="Berlaku per hari ini" :padding="false">
                <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2">
                    @forelse ($wastePrices as $category)
                        <div class="rounded-lg border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-sm font-medium text-slate-900">{{ $category->name }}</h3>
                                @if ($category->wastePrices->isNotEmpty())
                                    <span class="shrink-0 text-sm font-semibold text-slate-900">
                                        Rp {{ number_format($category->wastePrices->first()->price_per_kg, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="shrink-0 text-xs text-slate-400">Hubungi operator</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $category->description }}</p>
                            @if ($category->wastePrices->isNotEmpty())
                                <p class="mt-1 text-xs text-slate-400">per kilogram</p>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 py-6 text-center text-sm text-slate-500">
                            Tidak ada data harga saat ini.
                        </div>
                    @endforelse
                </div>
            </x-card>

            <!-- FAQ -->
            <x-card title="Pertanyaan yang sering diajukan" :padding="false">
                <div class="divide-y divide-slate-100">
                    <div class="px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-900">Bagaimana cara menyetor sampah?</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            Pilah sampah Anda terlebih dahulu berdasarkan jenisnya (plastik, kertas, kaleng, dll).
                            Kemudian bawa ke Bank Sampah pada jam operasional. Operator kami akan menimbang dan
                            mencatat setoran Anda, saldo akan langsung masuk ke akun Anda.
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-900">Kapan saya bisa menarik saldo?</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            Anda dapat menarik saldo kapan saja pada jam operasional Bank Sampah.
                            Silakan datang langsung ke lokasi dan operator akan membantu proses penarikan Anda.
                            Saat ini penarikan hanya bisa dilakukan secara langsung, tidak bisa melalui portal online.
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-900">Berapa lama saldo masuk setelah setor?</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            Saldo akan langsung masuk ke akun Anda setelah operator menyelesaikan pencatatan setoran.
                            Biasanya proses ini memakan waktu kurang dari 5 menit. Anda bisa langsung mengecek
                            saldo melalui portal ini setelah menyetor.
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-900">Kode unik saya hilang, bagaimana?</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            Jika kode unik Anda hilang, silakan hubungi operator Bank Sampah dengan menyebutkan
                            nama dan member code Anda. Operator akan membantu memberikan kode unik Anda kembali.
                            Untuk keamanan, jangan bagikan kode unik Anda kepada orang lain.
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-900">Jenis sampah apa saja yang diterima?</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            Kami menerima berbagai jenis sampah yang dapat didaur ulang seperti plastik (PET, HDPE, PP),
                            kertas (koran, kardus, majalah), kaleng, botol kaca, dan lainnya.
                            Lihat daftar harga di atas untuk jenis sampah yang kami terima dan harganya.
                        </p>
                    </div>
                </div>
            </x-card>

            <!-- Tips -->
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <h2 class="text-base font-semibold text-slate-900">Tips memilah sampah</h2>
                <ul class="mt-3 space-y-2">
                    @foreach ([
                        'Bersihkan botol plastik dan kaleng dari sisa makanan/minuman',
                        'Pisahkan plastik berdasarkan jenisnya (PET, HDPE, PP)',
                        'Lipat dan rapikan kardus agar mudah ditimbang',
                        'Kumpulkan sampah dalam kondisi kering, tidak basah',
                    ] as $tip)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span class="text-sm text-slate-700">{{ $tip }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contact -->
            <x-card title="Hubungi kami">
                <dl class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <dt class="text-xs text-slate-500">Telepon</dt>
                            <dd class="text-sm font-medium text-slate-900">0812-3456-7890</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <div>
                            <dt class="text-xs text-slate-500">Email</dt>
                            <dd class="text-sm font-medium text-slate-900">info@banksampah.test</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <div>
                            <dt class="text-xs text-slate-500">Alamat</dt>
                            <dd class="text-sm font-medium text-slate-900">Jl. Bersih Raya No. 123, Jakarta Selatan</dd>
                        </div>
                    </div>
                </dl>
            </x-card>

            <!-- Operating Hours -->
            <x-card title="Jam operasional">
                <dl class="space-y-3">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-slate-600">Senin - Jumat</dt>
                        <dd class="text-sm font-medium text-slate-900">08:00 - 16:00</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-slate-600">Sabtu</dt>
                        <dd class="text-sm font-medium text-slate-900">08:00 - 12:00</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-slate-600">Minggu</dt>
                        <dd class="text-sm font-medium text-red-600">Tutup</dd>
                    </div>
                </dl>
                <p class="mt-3 border-t border-slate-100 pt-3 text-xs text-slate-500">
                    Pada hari libur nasional, Bank Sampah tutup.
                </p>
            </x-card>

            <!-- Quick Links -->
            <x-card title="Akses cepat">
                <div class="space-y-2">
                    <a href="{{ route('customer-portal.dashboard') }}"
                       class="block rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-green-300 hover:text-green-700">
                        Dashboard
                    </a>
                    <a href="{{ route('customer-portal.transactions') }}"
                       class="block rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-green-300 hover:text-green-700">
                        Riwayat Transaksi
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
