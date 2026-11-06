@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ $customer->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Lihat saldo dan riwayat transaksi Anda.</p>
    </div>

    <!-- Balance Card (Prominent) -->
    <div class="mb-6 rounded-xl bg-linear-to-br from-green-500 to-green-600 p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="mb-1.5 text-xs font-medium text-green-100">SALDO TERSEDIA</p>
                <h2 class="mb-3 text-4xl font-bold">Rp {{ number_format($customer->balance, 0, ',', '.') }}</h2>
                <div class="flex flex-col space-y-0.5 text-xs text-green-100 sm:flex-row sm:items-center sm:space-x-3 sm:space-y-0">
                    <div>
                        <span class="font-semibold">Member:</span> {{ $customer->member_code }}
                    </div>
                    <div>
                        <span class="font-semibold">Bergabung:</span> {{ $customer->joined_at->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="hidden sm:block">
                <div class="rounded-full bg-white/20 p-4">
                    <svg class="h-16 w-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('customer-portal.transactions') }}"
               class="inline-flex items-center rounded-lg bg-white/20 px-3 py-2 text-xs font-medium text-white transition hover:bg-white/30">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Lihat Semua Riwayat
            </a>
            <a href="{{ route('customer-portal.info') }}"
               class="inline-flex items-center rounded-lg bg-white/20 px-3 py-2 text-xs font-medium text-white transition hover:bg-white/30">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Info & Harga
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-stat-card
            label="Total Setoran"
            :value="number_format($stats['total_weight'], 1).' kg'"
            description="Sampah terkumpul"
            variant="green"
        />

        <x-stat-card
            label="Total Transaksi"
            :value="number_format($stats['transaction_count'], 0, ',', '.')"
            description="Setoran dan penarikan"
            variant="gray"
        />

        <x-stat-card
            label="Setoran Terakhir"
            :value="$stats['last_deposit'] ? $stats['last_deposit']->created_at->diffForHumans() : '-'"
            :description="$stats['last_deposit'] ? $stats['last_deposit']->created_at->format('d M Y') : 'Belum ada setoran'"
            variant="gray"
        />
    </div>

    <!-- Recent Transactions -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xs">
        <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h3 class="text-base font-semibold text-gray-900">Transaksi Terbaru</h3>
            <a href="{{ route('customer-portal.transactions') }}"
               class="inline-flex items-center text-xs font-medium text-green-600 hover:text-green-700">
                Lihat Semua
                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($customer->ledgerEntries->take(5) as $entry)
            <div class="px-4 py-3 transition hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Icon -->
                        <div class="shrink-0">
                            @if($entry->direction === 'KREDIT')
                            <div class="rounded-full bg-green-100 p-1.5">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            @else
                            <div class="rounded-full bg-red-100 p-1.5">
                                <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
                           class="mt-0.5 inline-flex items-center text-xs text-green-700 hover:text-green-900">
                            Lihat Detail
                            <svg class="ml-0.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <x-empty-state title="Belum ada transaksi" description="Setoran dan penarikan Anda akan muncul di sini." />
            @endforelse
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
        <div class="flex items-start">
            <div class="shrink-0">
                <svg class="mt-0.5 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-xs font-semibold text-gray-900">Cara Penarikan Saldo</h3>
                <p class="mt-1 text-xs text-gray-600">
                    Datang ke Bank Sampah pada jam operasional. Operator akan membantu proses penarikan Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
