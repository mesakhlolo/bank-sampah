@extends('layouts.landing')

@section('title', 'Beranda - Bank Sampah Bersih Sejahtera')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-linear-to-br from-green-500 via-green-600 to-emerald-700 text-white">
    <div class="pointer-events-none absolute inset-0 bg-black opacity-10"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-8 md:grid-cols-2">
            <!-- Left Content -->
            <div class="text-center md:text-left">
                <h1 class="mb-4 text-3xl font-bold leading-tight md:text-5xl">
                    Ubah Sampah Menjadi Rupiah
                </h1>
                <p class="mb-6 text-base leading-relaxed text-green-100 md:text-lg">
                    Bergabunglah dengan Bank Sampah Bersih Sejahtera. Lingkungan bersih, ekonomi tumbuh, masa depan cerah.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col justify-center gap-3 sm:flex-row md:justify-start">
                    <a href="{{ route('customer-portal.check-code') }}"
                       class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-3 text-sm font-bold text-green-600 shadow-lg transition hover:bg-green-50 hover:shadow-xl">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Cek Saldo Saya
                    </a>
                    <a href="#cara-kerja"
                       class="inline-flex items-center justify-center rounded-lg border-2 border-white bg-transparent px-6 py-3 text-sm font-bold text-white transition hover:bg-white hover:text-green-600">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="hidden md:block">
                <div class="rounded-2xl bg-white/10 p-6 shadow-xl backdrop-blur-xs">
                    <div class="text-center">
                        <x-application-logo class="mx-auto mb-3 h-24 w-24 text-white" />
                        <p class="text-lg font-semibold">Sampah = Berkah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Decoration -->
    <div class="pointer-events-none absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 80C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Statistics Section -->
<section class="bg-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h2 class="mb-2 text-2xl font-bold text-gray-900 md:text-3xl">Dampak Kami Bersama</h2>
            <p class="text-base text-gray-600">Kontribusi nyata untuk lingkungan yang lebih baik.</p>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <!-- Stat 1 -->
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center transition hover:shadow-md">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="mb-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}+</div>
                <p class="text-xs font-medium text-gray-700">Nasabah Aktif</p>
            </div>

            <!-- Stat 2 -->
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-center transition hover:shadow-md">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-green-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="mb-1 text-2xl font-bold text-green-600">{{ number_format($stats['total_weight']) }}</div>
                <p class="text-xs font-medium text-gray-700">Kg Sampah Terkumpul</p>
            </div>

            <!-- Stat 3 -->
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-center transition hover:shadow-md">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-green-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mb-1 text-2xl font-bold text-green-600">{{ number_format($stats['total_amount']/1000000, 1) }}Jt</div>
                <p class="text-xs font-medium text-gray-700">Rupiah Tersalur</p>
            </div>

            <!-- Stat 4 -->
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center transition hover:shadow-md">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="mb-1 text-2xl font-bold text-gray-900">{{ number_format($stats['trees_equivalent']) }}</div>
                <p class="text-xs font-medium text-gray-700">Pohon Setara</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="cara-kerja" class="bg-linear-to-br from-gray-50 to-green-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h2 class="mb-2 text-2xl font-bold text-gray-900 md:text-3xl">Cara Kerja Bank Sampah</h2>
            <p class="text-base text-gray-600">Mudah, cepat, dan menguntungkan.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <!-- Step 1 -->
            <div class="rounded-xl bg-white p-5 text-center shadow-xs transition hover:shadow-md">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">1. Daftar</h3>
                <p class="text-xs leading-relaxed text-gray-600">
                    Hubungi kami untuk mendaftar sebagai nasabah. Anda akan mendapat kartu member dengan kode unik.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="rounded-xl bg-white p-5 text-center shadow-xs transition hover:shadow-md">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">2. Setor</h3>
                <p class="text-xs leading-relaxed text-gray-600">
                    Bawa sampah yang sudah dipilah ke Bank Sampah. Operator kami akan menimbang dan mencatat.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="rounded-xl bg-white p-5 text-center shadow-xs transition hover:shadow-md">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">3. Tabung</h3>
                <p class="text-xs leading-relaxed text-gray-600">
                    Saldo langsung masuk ke akun Anda. Bisa ditarik kapan saja sesuai kebutuhan.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="rounded-xl bg-white p-5 text-center shadow-xs transition hover:shadow-md">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">4. Dampak</h3>
                <p class="text-xs leading-relaxed text-gray-600">
                    Lingkungan bersih, ekonomi naik, masa depan lebih baik untuk generasi mendatang.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Waste Categories & Prices Section -->
<section class="bg-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h2 class="mb-2 text-2xl font-bold text-gray-900 md:text-3xl">Harga Sampah Hari Ini</h2>
            <p class="text-base text-gray-600">Lihat harga terkini untuk berbagai jenis sampah.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($wasteCategories as $category)
            <div class="rounded-lg border-2 border-green-200 bg-linear-to-br from-gray-50 to-green-50 p-4 transition hover:border-green-400 hover:shadow-md">
                <div class="mb-2 flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="mb-1 text-base font-bold text-gray-900">{{ $category->name }}</h3>
                        <p class="text-xs leading-relaxed text-gray-600">{{ $category->description }}</p>
                    </div>
                    <div class="ml-3 flex h-9 w-9 items-center justify-center rounded-full bg-white text-green-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                @if($category->wastePrices->isNotEmpty())
                <div class="mt-4 border-t border-green-200 pt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Harga per kg</span>
                        <span class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($category->wastePrices->first()->price_per_kg, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @else
                <div class="mt-4 border-t border-green-200 pt-4">
                    <p class="text-center text-sm text-gray-500">Hubungi operator untuk harga</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="relative overflow-hidden bg-linear-to-r from-green-600 to-emerald-700 py-20 text-white">
    <div class="pointer-events-none absolute inset-0 bg-black opacity-10"></div>
    <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="mb-6 text-3xl font-bold md:text-5xl">Siap Bergabung dengan Kami?</h2>
        <p class="mx-auto mb-10 max-w-3xl text-xl leading-relaxed text-green-100 md:text-2xl">
            Mari bersama-sama menciptakan lingkungan yang lebih bersih dan sehat. Hubungi kami untuk mendaftar sebagai nasabah Bank Sampah Bersih Sejahtera.
        </p>

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
            <a href="{{ route('customer-portal.check-code') }}"
               class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-4 text-lg font-bold text-green-600 shadow-xl transition hover:bg-green-50 hover:shadow-2xl">
                <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Cek Saldo Sekarang
            </a>
            <a href="tel:081234567890"
               class="inline-flex items-center justify-center rounded-lg border-2 border-white bg-transparent px-8 py-4 text-lg font-bold text-white transition hover:bg-white hover:text-green-600">
                <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Hubungi Kami
            </a>
        </div>

        <!-- Contact Info -->
        <div class="mx-auto mt-12 grid max-w-4xl grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg bg-white/10 p-4 backdrop-blur-xs">
                <svg class="mx-auto mb-2 h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <p class="font-semibold">0812-3456-7890</p>
            </div>
            <div class="rounded-lg bg-white/10 p-4 backdrop-blur-xs">
                <svg class="mx-auto mb-2 h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <p class="font-semibold">info@banksampah.test</p>
            </div>
            <div class="rounded-lg bg-white/10 p-4 backdrop-blur-xs">
                <svg class="mx-auto mb-2 h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="font-semibold">Jl. Bersih Raya No. 123</p>
            </div>
        </div>
    </div>
</section>
@endsection
