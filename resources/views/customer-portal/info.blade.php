@extends('layouts.portal')

@section('title', 'Info & Bantuan')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Info & Bantuan</h1>
        <p class="mt-1 text-sm text-gray-600">Informasi penting dan harga sampah terkini.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Current Waste Prices -->
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xs">
                <div class="bg-linear-to-r from-green-500 to-green-600 px-4 py-3">
                    <h2 class="flex items-center text-base font-bold text-white">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Harga Sampah Terkini
                    </h2>
                    <p class="mt-0.5 text-xs text-green-100">Berlaku per hari ini</p>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @forelse($wastePrices as $category)
                        <div class="rounded-lg border border-gray-200 p-3 transition hover:shadow-xs">
                            <div class="mb-1.5 flex items-center justify-between">
                                <h3 class="text-xs font-semibold text-gray-900">{{ $category->name }}</h3>
                                @if($category->wastePrices->isNotEmpty())
                                <span class="text-base font-bold text-green-600">
                                    Rp {{ number_format($category->wastePrices->first()->price_per_kg, 0, ',', '.') }}
                                </span>
                                @else
                                <span class="text-xs text-gray-400">Hubungi operator</span>
                                @endif
                            </div>
                            <p class="mb-1 text-xs text-gray-500">{{ $category->description }}</p>
                            @if($category->wastePrices->isNotEmpty())
                            <p class="text-xs text-gray-400">per kilogram</p>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-2 py-6 text-center text-xs text-gray-500">
                            Tidak ada data harga saat ini
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xs">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="flex items-center text-base font-bold text-gray-900">
                        <svg class="mr-2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pertanyaan yang Sering Diajukan (FAQ)
                    </h2>
                </div>
                <div class="space-y-4 p-4">
                    <!-- FAQ Item 1 -->
                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                        <h3 class="mb-1.5 text-sm font-semibold text-gray-900">
                            Bagaimana cara menyetor sampah?
                        </h3>
                        <p class="text-xs leading-relaxed text-gray-700">
                            Pilah sampah Anda terlebih dahulu berdasarkan jenisnya (plastik, kertas, kaleng, dll).
                            Kemudian bawa ke Bank Sampah pada jam operasional. Operator kami akan menimbang dan
                            mencatat setoran Anda, saldo akan langsung masuk ke akun Anda.
                        </p>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                        <h3 class="mb-1.5 text-sm font-semibold text-gray-900">
                            Kapan saya bisa menarik saldo?
                        </h3>
                        <p class="text-xs leading-relaxed text-gray-700">
                            Anda dapat menarik saldo kapan saja pada jam operasional Bank Sampah.
                            Silakan datang langsung ke lokasi dan operator akan membantu proses penarikan Anda.
                            Saat ini penarikan hanya bisa dilakukan secara langsung, tidak bisa melalui portal online.
                        </p>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                        <h3 class="mb-1.5 text-sm font-semibold text-gray-900">
                            Berapa lama saldo masuk setelah setor?
                        </h3>
                        <p class="text-xs leading-relaxed text-gray-700">
                            Saldo akan langsung masuk ke akun Anda setelah operator menyelesaikan pencatatan setoran.
                            Biasanya proses ini memakan waktu kurang dari 5 menit. Anda bisa langsung mengecek
                            saldo melalui portal ini setelah menyetor.
                        </p>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                        <h3 class="mb-1.5 text-sm font-semibold text-gray-900">
                            Kode unik saya hilang, bagaimana?
                        </h3>
                        <p class="text-xs leading-relaxed text-gray-700">
                            Jika kode unik Anda hilang, silakan hubungi operator Bank Sampah dengan menyebutkan
                            nama dan member code Anda. Operator akan membantu memberikan kode unik Anda kembali.
                            Untuk keamanan, jangan bagikan kode unik Anda kepada orang lain.
                        </p>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div>
                        <h3 class="mb-1.5 text-sm font-semibold text-gray-900">
                            Jenis sampah apa saja yang diterima?
                        </h3>
                        <p class="text-xs leading-relaxed text-gray-700">
                            Kami menerima berbagai jenis sampah yang dapat didaur ulang seperti plastik (PET, HDPE, PP),
                            kertas (koran, kardus, majalah), kaleng, botol kaca, dan lainnya.
                            Lihat daftar harga di atas untuk jenis sampah yang kami terima dan harganya.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tips Section -->
            <div class="rounded-xl border border-green-200 bg-green-50 p-6">
                <h2 class="mb-4 flex items-center text-xl font-bold text-gray-900">
                    <svg class="mr-2 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    Tips Memilah Sampah
                </h2>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="mr-2 mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-700">Bersihkan botol plastik dan kaleng dari sisa makanan/minuman</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="mr-2 mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-700">Pisahkan plastik berdasarkan jenisnya (PET, HDPE, PP)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="mr-2 mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-700">Lipat dan rapikan kardus agar mudah ditimbang</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="mr-2 mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-700">Kumpulkan sampah dalam kondisi kering, tidak basah</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xs">
                <div class="bg-linear-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="flex items-center text-lg font-bold text-white">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Hubungi Kami
                    </h2>
                </div>
                <div class="space-y-4 p-6">
                    <div class="flex items-start">
                        <svg class="mr-3 mt-0.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Telepon</p>
                            <p class="font-semibold text-gray-900">0812-3456-7890</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="mr-3 mt-0.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">info@banksampah.test</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="mr-3 mt-0.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Alamat</p>
                            <p class="font-semibold text-gray-900">Jl. Bersih Raya No. 123</p>
                            <p class="text-sm text-gray-600">Jakarta Selatan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xs">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="flex items-center text-lg font-bold text-gray-900">
                        <svg class="mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Jam Operasional
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Senin - Jumat</span>
                            <span class="font-semibold text-gray-900">08:00 - 16:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sabtu</span>
                            <span class="font-semibold text-gray-900">08:00 - 12:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Minggu</span>
                            <span class="font-semibold text-red-600">Tutup</span>
                        </div>
                    </div>
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500">
                            <span class="font-semibold">Catatan:</span> Pada hari libur nasional, Bank Sampah tutup.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="rounded-xl bg-linear-to-br from-green-500 to-green-600 p-6 text-white shadow-xs">
                <h3 class="mb-4 font-bold">Akses Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('customer-portal.dashboard') }}"
                       class="block rounded-lg bg-white/20 px-4 py-2 text-sm transition hover:bg-white/30">
                        Dashboard
                    </a>
                    <a href="{{ route('customer-portal.transactions') }}"
                       class="block rounded-lg bg-white/20 px-4 py-2 text-sm transition hover:bg-white/30">
                        Riwayat Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
