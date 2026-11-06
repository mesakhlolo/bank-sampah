@extends('layouts.portal')

@section('title', 'Detail Setoran')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Back -->
    <div class="mb-6">
        <a href="{{ route('customer-portal.transactions') }}"
           class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-green-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <!-- Deposit Receipt -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
        <!-- Header -->
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-base font-semibold text-slate-900">Bukti Setoran</h1>
                    <p class="mt-0.5 text-xs text-slate-500">Bank Sampah Bersih Sejahtera</p>
                </div>
                <x-badge variant="green">{{ ucfirst($deposit->status) }}</x-badge>
            </div>
        </div>

        <!-- Info -->
        <div class="grid grid-cols-1 gap-4 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:grid-cols-2">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Kode Setoran</p>
                <p class="mt-0.5 font-mono text-sm font-medium text-slate-900">{{ $deposit->code }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal &amp; Waktu</p>
                <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $deposit->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Nasabah</p>
                <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $customer->name }}</p>
                <p class="text-xs text-slate-500">{{ $customer->member_code }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Diterima Oleh</p>
                <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $deposit->creator->name }}</p>
                <p class="text-xs text-slate-500">{{ $deposit->creator->role === 'admin' ? 'Admin' : 'Operator' }}</p>
            </div>
        </div>

        <!-- Items -->
        <div class="px-5 py-5">
            <h2 class="text-sm font-semibold text-slate-900">Rincian barang</h2>

            <div class="mt-3 space-y-2">
                @foreach ($deposit->items as $item)
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-900">{{ $item->wasteCategory->name }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ number_format($item->weight_kg, 2) }} kg &times; Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}/kg
                            </p>
                        </div>
                        <p class="shrink-0 text-sm font-semibold text-slate-900">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="mt-5 flex items-end justify-between border-t border-slate-200 pt-4">
                <div>
                    <p class="text-xs text-slate-500">Total berat</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-900">{{ number_format($deposit->total_weight, 2) }} kg</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Total nilai</p>
                    <p class="mt-0.5 text-2xl font-semibold tracking-tight text-slate-900">
                        Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You -->
    <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-5">
        <h3 class="text-sm font-semibold text-slate-900">Terima kasih</h3>
        <p class="mt-1 text-sm text-slate-700">
            Kontribusi Anda membantu menjaga lingkungan tetap bersih dan sehat. Mari terus setor sampah untuk lingkungan yang lebih baik.
        </p>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex flex-wrap gap-3">
        <x-button :href="route('customer-portal.dashboard')">Kembali ke Dashboard</x-button>
        <x-button :href="route('customer-portal.transactions')" variant="secondary">Lihat Riwayat</x-button>
    </div>
</div>
@endsection
