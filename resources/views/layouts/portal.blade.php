<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Nasabah') - {{ config('app.name', 'Bank Sampah') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased">
    <div class="min-h-screen bg-slate-50">
        <!-- Portal Navigation (different from admin) -->
        @if(isset($portalCustomer))
        <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between gap-4">
                    <!-- Logo & Brand -->
                    <a href="{{ route('customer-portal.dashboard') }}" class="flex items-center gap-2.5">
                        <x-application-logo class="h-9 w-9 text-green-600" />
                        <span class="text-base font-semibold tracking-tight text-slate-900">Bank Sampah</span>
                    </a>

                    <!-- Navigation Links -->
                    <div class="hidden items-center gap-1 sm:flex">
                        <a href="{{ route('customer-portal.dashboard') }}"
                           class="{{ request()->routeIs('customer-portal.dashboard') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition">
                            Dashboard
                        </a>
                        <a href="{{ route('customer-portal.transactions') }}"
                           class="{{ request()->routeIs('customer-portal.transactions') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition">
                            Riwayat
                        </a>
                        <a href="{{ route('customer-portal.info') }}"
                           class="{{ request()->routeIs('customer-portal.info') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition">
                            Info &amp; Bantuan
                        </a>
                    </div>

                    <!-- Customer Info & Logout -->
                    <div class="hidden items-center gap-3 sm:flex">
                        <div class="text-right">
                            <div class="text-sm font-medium text-slate-900">{{ $portalCustomer->name }}</div>
                            <div class="text-xs text-slate-500">{{ $portalCustomer->member_code }}</div>
                        </div>
                        <form method="POST" action="{{ route('customer-portal.logout') }}">
                            @csrf
                            <x-button type="submit" variant="secondary" size="sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Keluar
                            </x-button>
                        </form>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
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
            <div x-cloak x-show="open" x-transition class="border-t border-slate-200 sm:hidden">
                <div class="space-y-1 px-4 pb-3 pt-2">
                    <a href="{{ route('customer-portal.dashboard') }}"
                       class="{{ request()->routeIs('customer-portal.dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} block rounded-lg px-3 py-2 text-sm font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('customer-portal.transactions') }}"
                       class="{{ request()->routeIs('customer-portal.transactions') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} block rounded-lg px-3 py-2 text-sm font-medium">
                        Riwayat
                    </a>
                    <a href="{{ route('customer-portal.info') }}"
                       class="{{ request()->routeIs('customer-portal.info') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} block rounded-lg px-3 py-2 text-sm font-medium">
                        Info &amp; Bantuan
                    </a>
                </div>
                <div class="border-t border-slate-200 px-4 pb-4 pt-4">
                    <div class="text-sm font-medium text-slate-900">{{ $portalCustomer->name }}</div>
                    <div class="text-xs text-slate-500">{{ $portalCustomer->member_code }}</div>
                    <form method="POST" action="{{ route('customer-portal.logout') }}" class="mt-3">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm" class="w-full">
                            Keluar
                        </x-button>
                    </form>
                </div>
            </div>
        </nav>
        @endif

        <!-- Flash Messages -->
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash />
        </div>

        <!-- Page Content -->
        <main class="py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-12 border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-slate-500">
                    <p>&copy; {{ date('Y') }} Bank Sampah Bersih Sejahtera.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
