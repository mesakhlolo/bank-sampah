<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Penarikan') }}
            </h2>
            <a href="{{ route('withdrawals.download-pdf', $withdrawal) }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Download PDF
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Receipt Display -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Header -->
                    <div class="text-center mb-8 pb-4 border-b-2 border-gray-300">
                        <h1 class="text-3xl font-bold text-gray-900">BUKTI PENARIKAN</h1>
                        <p class="text-lg text-gray-600 mt-2">Bank Sampah</p>
                    </div>

                    <!-- Transaction Info -->
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-sm text-gray-600">Kode Penarikan</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $withdrawal->code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Transaksi</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $withdrawal->transaction_date->format('d F Y') }}</p>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Informasi Nasabah</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600">Kode Nasabah</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->customer->code }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Nama</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->customer->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">No. Telepon</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->customer->phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Alamat</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->customer->address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Info -->
                    <div class="mb-8 p-6 bg-red-50 border-2 border-red-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-700">Nominal Penarikan:</span>
                            <span class="text-3xl font-bold text-red-600">
                                Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Note -->
                    @if ($withdrawal->note)
                        <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 mb-1">Catatan:</p>
                            <p class="text-gray-900">{{ $withdrawal->note }}</p>
                        </div>
                    @endif

                    <!-- Creator Info -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600">Dibuat Oleh</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->creator->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Waktu Dibuat</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->created_at->format('d F Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 pt-4 border-t border-gray-300">
                        <div class="flex justify-between items-start">
                            <div class="text-center" style="width: 45%">
                                <p class="text-sm text-gray-600 mb-16">Nasabah</p>
                                <div class="border-t border-gray-400 pt-2">
                                    <p class="font-medium">{{ $withdrawal->customer->name }}</p>
                                </div>
                            </div>
                            <div class="text-center" style="width: 45%">
                                <p class="text-sm text-gray-600 mb-16">Petugas</p>
                                <div class="border-t border-gray-400 pt-2">
                                    <p class="font-medium">{{ $withdrawal->creator->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-4">
                <a href="{{ route('withdrawals.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
                <a href="{{ route('withdrawals.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Penarikan Baru
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
