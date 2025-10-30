@extends('layouts.portal')

@section('title', 'Detail Setoran')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('customer-portal.transactions') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-green-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <!-- Deposit Receipt Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-5 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold">BUKTI SETORAN</h1>
                    <p class="text-green-100 text-xs mt-0.5">Bank Sampah Bersih Sejahtera</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Kode Setoran</p>
                    <p class="text-sm font-bold text-gray-900 font-mono">{{ $deposit->deposit_code }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Tanggal & Waktu</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $deposit->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Nasabah</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                    <p class="text-xs text-gray-500">{{ $customer->member_code }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ ucfirst($deposit->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Items Detail -->
        <div class="px-5 py-4">
            <h2 class="text-sm font-bold text-gray-900 mb-3">RINCIAN BARANG:</h2>
            
            <div class="space-y-2">
                @foreach($deposit->items as $item)
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-xs font-semibold text-gray-900 mb-1">{{ $item->wasteCategory->name }}</h3>
                            <div class="text-xs text-gray-600 space-y-0.5">
                                <p>
                                    <span class="font-medium">Berat:</span> 
                                    <span class="text-blue-600 font-bold">{{ number_format($item->weight, 2) }} kg</span>
                                </p>
                                <p>
                                    <span class="font-medium">Harga Satuan:</span> 
                                    Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}/kg
                                </p>
                            </div>
                        </div>
                        <div class="text-right ml-3">
                            <p class="text-xs text-gray-500 mb-0.5">Subtotal</p>
                            <p class="text-base font-bold text-green-600">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total Summary -->
            <div class="mt-4 pt-4 border-t-2 border-gray-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Total Berat</p>
                        <p class="text-lg font-bold text-blue-600">{{ number_format($deposit->total_weight, 2) }} kg</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-600 mb-0.5">TOTAL NILAI</p>
                        <p class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
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
    <div class="mt-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Terima Kasih!</h3>
                <p class="mt-1 text-sm text-gray-700">
                    Kontribusi Anda membantu menjaga lingkungan tetap bersih dan sehat. 
                    Mari terus setor sampah untuk lingkungan yang lebih baik! 🌱
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('customer-portal.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Kembali ke Dashboard
        </a>
        <a href="{{ route('customer-portal.transactions') }}" 
           class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition shadow-md border border-gray-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Lihat Riwayat
        </a>
    </div>
</div>
@endsection
