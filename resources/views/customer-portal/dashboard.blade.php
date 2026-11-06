@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Welcome -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Selamat datang, {{ $customer->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">Lihat saldo dan riwayat transaksi Anda.</p>
    </div>

    <!-- Balance -->
    <div class="mb-6 rounded-2xl bg-slate-900 p-6 text-white">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-widest text-slate-400">Saldo tersedia</p>
                <p class="mt-2 text-4xl font-semibold tracking-tight">Rp {{ number_format($customer->balance, 0, ',', '.') }}</p>
                <p class="mt-3 text-xs text-slate-400">
                    Member <span class="font-medium text-slate-200">{{ $customer->member_code }}</span>
                    &middot; Bergabung {{ $customer->joined_at->format('d M Y') }}
                </p>
            </div>

            <div class="flex shrink-0 gap-2">
                <a href="{{ route('customer-portal.transactions') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-2 text-xs font-medium text-white transition hover:bg-white/20">
                    Lihat Riwayat
                </a>
                <a href="{{ route('customer-portal.info') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-2 text-xs font-medium text-white transition hover:bg-white/20">
                    Info &amp; Harga
                </a>
            </div>
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
    <x-card title="Transaksi terbaru" :padding="false">
        <x-slot name="actions">
            <a href="{{ route('customer-portal.transactions') }}" class="text-sm font-medium text-green-700 hover:text-green-800">
                Lihat semua
            </a>
        </x-slot>

        <div class="divide-y divide-slate-100">
            @forelse ($customer->ledgerEntries->take(5) as $entry)
                <div class="flex items-center justify-between gap-4 px-5 py-3.5">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-900">{{ $entry->description }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $entry->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div class="shrink-0 text-right">
                        <p class="text-sm font-semibold {{ $entry->direction === 'KREDIT' ? 'text-green-700' : 'text-red-600' }}">
                            {{ $entry->direction === 'KREDIT' ? '+' : '-' }} Rp {{ number_format($entry->amount, 0, ',', '.') }}
                        </p>
                        @if ($entry->ref_type === 'App\Models\Deposit' && $entry->referenceable)
                            <a href="{{ route('customer-portal.deposit-detail', $entry->referenceable->id) }}"
                               class="mt-0.5 inline-block text-xs text-slate-500 hover:text-green-700">
                                Lihat detail
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <x-empty-state title="Belum ada transaksi" description="Setoran dan penarikan Anda akan muncul di sini." />
            @endforelse
        </div>
    </x-card>

    <!-- Info -->
    <div class="mt-6 flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <div>
            <h3 class="text-xs font-semibold text-slate-900">Cara penarikan saldo</h3>
            <p class="mt-1 text-xs text-slate-600">
                Datang ke Bank Sampah pada jam operasional. Operator akan membantu proses penarikan Anda.
            </p>
        </div>
    </div>
</div>
@endsection
