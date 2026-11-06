<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Nasabah') - {{ config('app.name', 'Bank Sampah') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Portal Navigation (different from admin) -->
        @if(isset($portalCustomer))
        <nav class="border-b border-green-200 bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <!-- Logo & Brand -->
                    <div class="flex items-center">
                        <a href="{{ route('customer-portal.dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="h-9 w-9 text-green-600" />
                            <span class="text-lg font-bold text-gray-900">Bank Sampah</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-6">
                        <a href="{{ route('customer-portal.dashboard') }}"
                           class="{{ request()->routeIs('customer-portal.dashboard') ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-600 hover:text-green-600' }} px-3 py-2 text-sm font-medium transition">
                            Dashboard
                        </a>
                        <a href="{{ route('customer-portal.transactions') }}"
                           class="{{ request()->routeIs('customer-portal.transactions') ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-600 hover:text-green-600' }} px-3 py-2 text-sm font-medium transition">
                            Riwayat
                        </a>
                        <a href="{{ route('customer-portal.info') }}"
                           class="{{ request()->routeIs('customer-portal.info') ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-600 hover:text-green-600' }} px-3 py-2 text-sm font-medium transition">
                            Info & Bantuan
                        </a>

                        <!-- Customer Info & Logout -->
                        <div class="ml-6 flex items-center space-x-4 border-l border-gray-200 pl-6">
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-700">{{ $portalCustomer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $portalCustomer->member_code }}</div>
                            </div>
                            <form method="POST" action="{{ route('customer-portal.logout') }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center rounded-lg border border-transparent bg-red-600 px-3 py-2 text-sm font-medium leading-4 text-white transition hover:bg-red-700 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button id="mobile-menu-btn" type="button" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-green-50 hover:text-green-600 focus:outline-hidden focus:ring-2 focus:ring-green-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden border-t border-green-100 sm:hidden">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <a href="{{ route('customer-portal.dashboard') }}"
                       class="{{ request()->routeIs('customer-portal.dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }} block rounded-lg px-3 py-2 text-base font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('customer-portal.transactions') }}"
                       class="{{ request()->routeIs('customer-portal.transactions') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }} block rounded-lg px-3 py-2 text-base font-medium">
                        Riwayat
                    </a>
                    <a href="{{ route('customer-portal.info') }}"
                       class="{{ request()->routeIs('customer-portal.info') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }} block rounded-lg px-3 py-2 text-base font-medium">
                        Info & Bantuan
                    </a>
                </div>
                <div class="border-t border-green-100 pb-3 pt-4">
                    <div class="px-4">
                        <div class="text-base font-semibold text-gray-700">{{ $portalCustomer->name }}</div>
                        <div class="text-sm text-gray-500">{{ $portalCustomer->member_code }}</div>
                    </div>
                    <div class="mt-3 px-2">
                        <form method="POST" action="{{ route('customer-portal.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-lg px-3 py-2 text-left text-base font-medium text-red-600 hover:bg-red-50">
                                Keluar
                            </button>
                        </form>
                    </div>
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
        <footer class="mt-12 border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} Bank Sampah Bersih Sejahtera.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
