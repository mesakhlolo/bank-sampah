@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ $customer->name }}!</h1>
        <p class="mt-1 text-sm text-gray-600">Lihat saldo dan riwayat transaksi Anda</p>
    </div>

    <!-- Balance Card (Prominent) -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-xs font-medium mb-1.5">💰 SALDO TERSEDIA</p>
                <h2 class="text-4xl font-bold mb-3">Rp {{ number_format($customer->balance, 0, ',', '.') }}</h2>
                <div class="flex flex-col sm:flex-row sm:items-center text-green-100 text-xs space-y-0.5 sm:space-y-0 sm:space-x-3">
                    <div>
                        <span class="font-semibold">Member:</span> {{ $customer->member_code }}
                    </div>
                    <div>
                        <span class="font-semibold">Bergabung:</span> {{ $customer->joined_at->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="hidden sm:block">
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('customer-portal.transactions') }}" 
               class="inline-flex items-center px-3 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-white font-medium text-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Lihat Semua Riwayat
            </a>
            <a href="{{ route('customer-portal.info') }}" 
               class="inline-flex items-center px-3 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-white font-medium text-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Info & Harga
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total Weight -->
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Setoran</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_weight'], 1) }} kg</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Sampah terkumpul</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['transaction_count'] }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Setoran & penarikan</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Last Deposit -->
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Setoran Terakhir</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">
                        @if($stats['last_deposit'])
                            {{ $stats['last_deposit']->created_at->diffForHumans() }}
                        @else
                            -
                        @endif
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        @if($stats['last_deposit'])
                            {{ $stats['last_deposit']->created_at->format('d M Y') }}
                        @else
                            Belum ada setoran
                        @endif
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Transaksi Terbaru</h3>
            <a href="{{ route('customer-portal.transactions') }}" 
               class="text-xs text-green-600 hover:text-green-700 font-medium inline-flex items-center">
                Lihat Semua
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($customer->ledgerEntries->take(5) as $entry)
            <div class="px-4 py-3 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            @if($entry->direction === 'KREDIT')
                            <div class="bg-green-100 rounded-full p-1.5">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            @else
                            <div class="bg-red-100 rounded-full p-1.5">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        <!-- Details -->
                        <div>
                            <p class="text-xs font-semibold text-gray-900">{{ $entry->description }}</p>
                            <p class="text-xs text-gray-500">{{ $entry->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <!-- Amount -->
                    <div class="text-right">
                        <p class="text-sm font-bold {{ $entry->direction === 'KREDIT' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $entry->direction === 'KREDIT' ? '+' : '-' }} Rp {{ number_format($entry->amount, 0, ',', '.') }}
                        </p>
                        @if($entry->ref_type === 'App\Models\Deposit' && $entry->referenceable)
                        <a href="{{ route('customer-portal.deposit-detail', $entry->referenceable->id) }}" 
                           class="text-xs text-blue-600 hover:text-blue-700 inline-flex items-center mt-0.5">
                            Lihat Detail
                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-gray-500 text-xs">Belum ada transaksi</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-xs font-semibold text-blue-900">Cara Penarikan Saldo</h3>
                <p class="mt-1 text-xs text-blue-700">
                    Datang ke Bank Sampah pada jam operasional. Operator akan membantu proses penarikan Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
