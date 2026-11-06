<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bank Sampah Bersih Sejahtera')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased">
    <!-- Navigation -->
    <nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <!-- Logo & Brand -->
                <a href="{{ route('landing.index') }}" class="flex items-center gap-2.5">
                    <x-application-logo class="h-9 w-9 text-green-600" />
                    <span class="text-base font-semibold tracking-tight text-slate-900">Bank Sampah</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden items-center gap-2 md:flex">
                    <a href="{{ route('landing.index') }}"
                       class="{{ request()->routeIs('landing.index') ? 'text-slate-900' : 'text-slate-600 hover:text-slate-900' }} px-3 py-2 text-sm font-medium transition">
                        Beranda
                    </a>
                    <a href="#cara-kerja" class="px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-slate-900">
                        Cara Kerja
                    </a>
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-slate-900">
                        Masuk Admin
                    </a>
                    <x-button :href="route('customer-portal.check-code')" size="sm" class="ms-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Cek Saldo
                    </x-button>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="open = ! open"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-cloak x-show="open" x-transition class="border-t border-slate-200 md:hidden">
            <div class="space-y-1 px-4 pb-3 pt-2">
                <a href="{{ route('landing.index') }}"
                   class="{{ request()->routeIs('landing.index') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} block rounded-lg px-3 py-2 text-sm font-medium">
                    Beranda
                </a>
                <a href="#cara-kerja" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    Cara Kerja
                </a>
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    Masuk Admin
                </a>
                <a href="{{ route('customer-portal.check-code') }}" class="mt-1 block rounded-lg bg-green-600 px-3 py-2 text-center text-sm font-medium text-white">
                    Cek Saldo
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-800 bg-slate-900 text-slate-300">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- About -->
                <div>
                    <div class="flex items-center gap-2.5">
                        <x-application-logo class="h-9 w-9 text-green-500" />
                        <span class="text-base font-semibold tracking-tight text-white">Bank Sampah</span>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-slate-400">
                        Bersama mewujudkan lingkungan bersih dan ekonomi sirkular melalui pengelolaan sampah yang bertanggung jawab.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-white">Menu Cepat</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="{{ route('landing.index') }}" class="text-slate-400 transition hover:text-white">Beranda</a></li>
                        <li><a href="{{ route('customer-portal.check-code') }}" class="text-slate-400 transition hover:text-white">Cek Saldo</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 transition hover:text-white">Masuk Admin</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-sm font-semibold text-white">Kontak</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            0812-3456-7890
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            info@banksampah.test
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            Jl. Bersih Raya No. 123, Jakarta Selatan
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 border-t border-slate-800 pt-8 text-center text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} Bank Sampah Bersih Sejahtera.</p>
            </div>
        </div>
    </footer>
</body>
</html>
