@extends('layouts.landing')

@section('title', 'Beranda - Bank Sampah Bersih Sejahtera')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-green-500 via-green-600 to-blue-600 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10 pointer-events-none"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <!-- Left Content -->
            <div class="text-center md:text-left">
                <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
                    Ubah Sampah Menjadi Rupiah
                </h1>
                <p class="text-base md:text-lg text-green-100 mb-6 leading-relaxed">
                    Bergabunglah dengan Bank Sampah Bersih Sejahtera. Lingkungan bersih, ekonomi tumbuh, masa depan cerah.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                    <a href="{{ route('customer-portal.check-code') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-white text-green-600 font-bold rounded-lg hover:bg-green-50 transition shadow-lg hover:shadow-xl text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Cek Saldo Saya
                    </a>
                    <a href="#cara-kerja" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-green-600 transition text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="hidden md:block">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 shadow-xl">
                    <div class="text-center">
                        <div class="text-7xl mb-3">♻️</div>
                        <p class="text-lg font-semibold">Sampah = Berkah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Decoration -->
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 80C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Dampak Kami Bersama</h2>
            <p class="text-base text-gray-600">Kontribusi nyata untuk lingkungan yang lebih baik</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Stat 1 -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 text-center border border-blue-200 hover:shadow-md transition">
                <div class="text-3xl mb-2">🏠</div>
                <div class="text-2xl font-bold text-blue-600 mb-1">{{ number_format($stats['total_customers']) }}+</div>
                <p class="text-xs text-gray-700 font-medium">Nasabah Aktif</p>
            </div>

            <!-- Stat 2 -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 text-center border border-green-200 hover:shadow-md transition">
                <div class="text-3xl mb-2">♻️</div>
                <div class="text-2xl font-bold text-green-600 mb-1">{{ number_format($stats['total_weight']) }}</div>
                <p class="text-xs text-gray-700 font-medium">Kg Sampah Terkumpul</p>
            </div>

            <!-- Stat 3 -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 text-center border border-purple-200 hover:shadow-md transition">
                <div class="text-3xl mb-2">💵</div>
                <div class="text-2xl font-bold text-purple-600 mb-1">{{ number_format($stats['total_amount']/1000000, 1) }}Jt</div>
                <p class="text-xs text-gray-700 font-medium">Rupiah Tersalur</p>
            </div>

            <!-- Stat 4 -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 text-center border border-orange-200 hover:shadow-md transition">
                <div class="text-3xl mb-2">🌳</div>
                <div class="text-2xl font-bold text-orange-600 mb-1">{{ number_format($stats['trees_equivalent']) }}</div>
                <p class="text-xs text-gray-700 font-medium">Pohon Setara</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="cara-kerja" class="py-12 bg-gradient-to-br from-gray-50 to-green-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Cara Kerja Bank Sampah</h2>
            <p class="text-base text-gray-600">Mudah, cepat, dan menguntungkan!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Step 1 -->
            <div class="bg-white rounded-xl p-5 text-center shadow-sm hover:shadow-md transition">
                <div class="bg-blue-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">1. Daftar</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Hubungi kami untuk mendaftar sebagai nasabah. Anda akan mendapat kartu member dengan kode unik.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white rounded-xl p-5 text-center shadow-sm hover:shadow-md transition">
                <div class="bg-green-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">2. Setor</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Bawa sampah yang sudah dipilah ke Bank Sampah. Operator kami akan menimbang dan mencatat.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white rounded-xl p-5 text-center shadow-sm hover:shadow-md transition">
                <div class="bg-purple-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">3. Tabung</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Saldo langsung masuk ke akun Anda. Bisa ditarik kapan saja sesuai kebutuhan.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white rounded-xl p-5 text-center shadow-sm hover:shadow-md transition">
                <div class="bg-orange-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">4. Dampak</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Lingkungan bersih, ekonomi naik, masa depan lebih baik untuk generasi mendatang.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Waste Categories & Prices Section -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Harga Sampah Hari Ini</h2>
            <p class="text-base text-gray-600">Lihat harga terkini untuk berbagai jenis sampah</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($wasteCategories as $category)
            <div class="bg-gradient-to-br from-gray-50 to-green-50 rounded-lg p-4 border-2 border-green-200 hover:border-green-400 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-gray-900 mb-1">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $category->description }}</p>
                    </div>
                    <div class="ml-3 text-2xl">📦</div>
                </div>
                @if($category->wastePrices->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-green-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Harga per kg</span>
                        <span class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($category->wastePrices->first()->price_per_kg, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @else
                <div class="mt-4 pt-4 border-t border-green-200">
                    <p class="text-sm text-gray-500 text-center">Hubungi operator untuk harga</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-20 bg-gradient-to-r from-green-600 to-blue-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10 pointer-events-none"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">Siap Bergabung dengan Kami?</h2>
        <p class="text-xl md:text-2xl text-green-100 mb-10 max-w-3xl mx-auto leading-relaxed">
            Mari bersama-sama menciptakan lingkungan yang lebih bersih dan sehat. Hubungi kami untuk mendaftar sebagai nasabah Bank Sampah Bersih Sejahtera.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('customer-portal.check-code') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-green-600 font-bold rounded-lg hover:bg-green-50 transition shadow-xl hover:shadow-2xl text-lg">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Cek Saldo Sekarang
            </a>
            <a href="tel:081234567890" 
               class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-green-600 transition text-lg">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Hubungi Kami
            </a>
        </div>

        <!-- Contact Info -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <p class="font-semibold">0812-3456-7890</p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <p class="font-semibold">info@banksampah.test</p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="font-semibold">Jl. Bersih Raya No. 123</p>
            </div>
        </div>
    </div>
</section>
@endsection
