@extends('layouts.landing')

@section('title', 'Beranda - Bank Sampah Bersih Sejahtera')

@section('content')
<!-- Hero -->
<section class="relative isolate overflow-hidden bg-white">
    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
            <!-- Copy -->
            <div>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">
                    Ubah sampah jadi tabungan yang bernilai
                </h1>

                <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-600">
                    Setor sampah yang sudah dipilah, saldo langsung tercatat di akun Anda.
                    Pantau mutasi dan tarik saldo kapan saja melalui portal nasabah.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-button :href="route('customer-portal.check-code')" size="md">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Cek Saldo Saya
                    </x-button>
                    <x-button href="#cara-kerja" variant="secondary" size="md">
                        Cara Kerja
                    </x-button>
                </div>
            </div>

            <!-- Member card visual -->
            <div class="mx-auto w-full max-w-sm lg:mx-0 lg:ml-auto">
                <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <x-application-logo class="h-8 w-8 text-green-500" />
                            <span class="text-sm font-semibold tracking-tight">Bank Sampah</span>
                        </div>
                        <span class="rounded-md bg-white/10 px-2 py-0.5 text-xs font-medium text-green-300">Nasabah</span>
                    </div>

                    <p class="mt-10 text-xs font-medium uppercase tracking-widest text-slate-400">Kode Anggota</p>
                    <p class="mt-1 font-mono text-lg tracking-wide">CUST-0000</p>

                    <div class="mt-8 flex items-end justify-between border-t border-white/10 pt-4">
                        <div>
                            <p class="text-xs text-slate-400">Nama Nasabah</p>
                            <p class="mt-0.5 text-sm font-medium">Nama Anda</p>
                        </div>
                        <span class="rounded-md bg-green-500/15 px-2 py-0.5 text-xs font-medium text-green-300">Aktif</span>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600 shadow-xs">
                    Punya kode unik dari kartu member? Cek saldo dan mutasi tanpa perlu datang ke lokasi.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Stats -->
<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
            <div>
                <p class="text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_customers']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Nasabah aktif</p>
            </div>
            <div>
                <p class="text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_weight'], 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-slate-500">Kg sampah terkumpul</p>
            </div>
            <div>
                <p class="text-3xl font-semibold tracking-tight text-slate-900">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-slate-500">Rupiah tersalur ke nasabah</p>
            </div>
            <div>
                <p class="text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['trees_equivalent']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Pohon setara</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="cara-kerja" class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Cara kerja</h2>
            <p class="mt-3 text-base text-slate-600">Empat langkah sederhana dari sampah menjadi saldo yang bisa ditarik.</p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $steps = [
                    ['title' => 'Daftar', 'body' => 'Hubungi kami untuk mendaftar sebagai nasabah dan dapatkan kartu member dengan kode unik.'],
                    ['title' => 'Pilah & Setor', 'body' => 'Bawa sampah yang sudah dipilah ke Bank Sampah. Operator akan menimbang dan mencatat.'],
                    ['title' => 'Saldo Bertambah', 'body' => 'Nilai setoran langsung masuk ke akun Anda sesuai harga per kilogram yang berlaku.'],
                    ['title' => 'Tarik atau Tabung', 'body' => 'Saldo bisa ditarik kapan saja atau dibiarkan untuk kebutuhan berikutnya.'],
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-sm font-semibold text-white">
                        {{ $index + 1 }}
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Waste Prices -->
<section class="border-t border-slate-200 bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Harga sampah hari ini</h2>
            <p class="mt-3 text-base text-slate-600">Harga per kilogram yang berlaku untuk setiap kategori sampah.</p>
        </div>

        @if ($wasteCategories->isEmpty())
            <div class="mt-12 rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                Data harga sedang diperbarui. Silakan hubungi operator untuk informasi terkini.
            </div>
        @else
            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($wasteCategories as $category)
                    @php($price = $category->wastePrices->first())
                    <div class="flex flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
                        <h3 class="text-base font-semibold text-slate-900">{{ $category->name }}</h3>
                        <p class="mt-1 flex-1 text-sm leading-relaxed text-slate-600">
                            {{ $category->description ?: 'Kategori sampah yang kami terima.' }}
                        </p>

                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                            <span class="text-sm text-slate-500">Harga per kg</span>
                            @if ($price)
                                <span class="text-lg font-semibold text-slate-900">
                                    Rp {{ number_format($price->price_per_kg, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400">Hubungi operator</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- CTA -->
<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-slate-900 px-6 py-12 sm:px-12">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">Siap mulai menabung sampah?</h2>
                <p class="mt-4 text-base leading-relaxed text-slate-300">
                    Cek saldo Anda sekarang, atau hubungi kami untuk mendaftar sebagai nasabah baru
                    Bank Sampah Bersih Sejahtera.
                </p>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <x-button :href="route('customer-portal.check-code')" size="md">
                    Cek Saldo Sekarang
                </x-button>
                <a href="tel:081234567890"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/20 bg-transparent px-4 py-2 text-sm font-medium text-white transition hover:bg-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Hubungi Kami
                </a>
            </div>

            <dl class="mt-12 grid grid-cols-1 gap-6 border-t border-white/10 pt-8 sm:grid-cols-3">
                <div>
                    <dt class="text-sm text-slate-400">Telepon</dt>
                    <dd class="mt-1 text-sm font-medium text-white">0812-3456-7890</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-400">Email</dt>
                    <dd class="mt-1 text-sm font-medium text-white">info@banksampah.test</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-400">Alamat</dt>
                    <dd class="mt-1 text-sm font-medium text-white">Jl. Bersih Raya No. 123, Jakarta Selatan</dd>
                </div>
            </dl>
        </div>
    </div>
</section>
@endsection
