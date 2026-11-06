@extends('layouts.portal')

@section('title', 'Detail Setoran')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('customer-portal.transactions') }}"
           class="inline-flex items-center text-sm text-gray-600 transition hover:text-green-600">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <!-- Deposit Receipt Card -->
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xs">
        <!-- Header -->
        <div class="bg-linear-to-r from-green-500 to-green-600 px-5 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold">BUKTI SETORAN</h1>
                    <p class="mt-0.5 text-xs text-green-100">Bank Sampah Bersih Sejahtera</p>
                </div>
                <div class="rounded-full bg-white/20 p-2">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="border-b border-gray-200 bg-gray-50 px-5 py-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">Kode Setoran</p>
                    <p class="font-mono text-sm font-bold text-gray-900">{{ $deposit->code }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">Tanggal & Waktu</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $deposit->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">Nasabah</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                    <p class="text-xs text-gray-500">{{ $customer->member_code }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">Status</p>
                    <x-badge variant="green">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ ucfirst($deposit->status) }}
                    </x-badge>
                </div>
            </div>
        </div>

        <!-- Items Detail -->
        <div class="px-5 py-4">
            <h2 class="mb-3 text-sm font-bold text-gray-900">RINCIAN BARANG</h2>

            <div class="space-y-2">
                @foreach($deposit->items as $item)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="mb-1 text-xs font-semibold text-gray-900">{{ $item->wasteCategory->name }}</h3>
                            <div class="space-y-0.5 text-xs text-gray-600">
                                <p>
                                    <span class="font-medium">Berat:</span>
                                    <span class="font-bold text-green-700">{{ number_format($item->weight_kg, 2) }} kg</span>
                                </p>
                                <p>
                                    <span class="font-medium">Harga Satuan:</span>
                                    Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}/kg
                                </p>
                            </div>
                        </div>
                        <div class="ml-3 text-right">
                            <p class="mb-0.5 text-xs text-gray-500">Subtotal</p>
                            <p class="text-base font-bold text-green-600">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total Summary -->
            <div class="mt-4 border-t-2 border-gray-300 pt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Total Berat</p>
                        <p class="text-lg font-bold text-green-700">{{ number_format($deposit->total_weight, 2) }} kg</p>
                    </div>
                    <div class="text-right">
                        <p class="mb-0.5 text-xs text-gray-600">TOTAL NILAI</p>
                        <p class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="border-t border-gray-200 bg-gray-50 px-5 py-4">
            <div class="flex items-center justify-between text-xs">
                <div>
                    <p class="text-gray-600">Diterima oleh:</p>
                    <p class="font-semibold text-gray-900">{{ $deposit->creator->name }}</p>
                    <p class="text-xs text-gray-500">{{ $deposit->creator->role === 'admin' ? 'Admin' : 'Operator' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600">Dicatat pada:</p>
                    <p class="font-semibold text-gray-900">{{ $deposit->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $deposit->created_at->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You Message -->
    <div class="mt-8 rounded-xl border border-green-200 bg-green-50 p-6">
        <div class="flex items-center">
            <div class="shrink-0">
                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Terima Kasih</h3>
                <p class="mt-1 text-sm text-gray-700">
                    Kontribusi Anda membantu menjaga lingkungan tetap bersih dan sehat. Mari terus setor sampah untuk lingkungan yang lebih baik.
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex flex-wrap gap-3">
        <x-button :href="route('customer-portal.dashboard')">Kembali ke Dashboard</x-button>
        <x-button :href="route('customer-portal.transactions')" variant="secondary">Lihat Riwayat</x-button>
    </div>
</div>
@endsection
