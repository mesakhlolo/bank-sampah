<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Nasabah') - Bank Sampah</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-green-50 to-blue-50">
    <div class="min-h-screen">
        <!-- Portal Navigation (different from admin) -->
        @if(isset($portalCustomer))
        <nav class="bg-white shadow-md border-b border-green-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center">
                        <a href="{{ route('customer-portal.dashboard') }}" class="flex items-center">
                            <span class="text-2xl">♻️</span>
                            <span class="ml-2 text-xl font-bold text-green-600">Bank Sampah</span>
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
                        <div class="flex items-center ml-6 space-x-4 border-l pl-6">
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-700">{{ $portalCustomer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $portalCustomer->member_code }}</div>
                            </div>
                            <form method="POST" action="{{ route('customer-portal.logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button id="mobile-menu-btn" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden sm:hidden border-t border-green-100">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('customer-portal.dashboard') }}" 
                       class="{{ request()->routeIs('customer-portal.dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }} block px-3 py-2 rounded-md text-base font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('customer-portal.transactions') }}" 
                       class="{{ request()->routeIs('customer-portal.transactions') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }} block px-3 py-2 rounded-md text-base font-medium">
                        Riwayat
                    </a>
                    <a href="{{ route('customer-portal.info') }}" 
                       class="{{ request()->routeIs('customer-portal.info') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }} block px-3 py-2 rounded-md text-base font-medium">
                        Info & Bantuan
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-green-100">
                    <div class="px-4">
                        <div class="text-base font-semibold text-gray-700">{{ $portalCustomer->name }}</div>
                        <div class="text-sm text-gray-500">{{ $portalCustomer->member_code }}</div>
                    </div>
                    <div class="mt-3 px-2">
                        <form method="POST" action="{{ route('customer-portal.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        @endif

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Page Content -->
        <main class="py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} Bank Sampah Bersih Sejahtera. All rights reserved.</p>
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
</body>
</html>
