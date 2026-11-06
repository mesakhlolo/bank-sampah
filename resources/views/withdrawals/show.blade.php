<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Detail Penarikan" description="Bukti transaksi penarikan saldo.">
            <x-slot name="actions">
                <x-button :href="route('withdrawals.download-pdf', $withdrawal)" size="sm">Unduh PDF</x-button>
                <x-button :href="route('withdrawals.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <x-flash class="mb-4" />

            <!-- Receipt Display -->
            <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-xs">
                <!-- Header -->
                <div class="mb-8 border-b-2 border-gray-200 pb-4 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">Bukti Penarikan</h1>
                    <p class="mt-1 text-gray-600">Bank Sampah</p>
                </div>

                <!-- Transaction Info -->
                <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-600">Kode Penarikan</p>
                        <p class="font-mono text-lg font-semibold text-gray-900">{{ $withdrawal->code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Transaksi</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $withdrawal->transaction_date->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="mb-8 rounded-lg bg-gray-50 p-4">
                    <h3 class="mb-3 text-sm font-medium text-gray-700">Informasi Nasabah</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-gray-600">Kode Nasabah</p>
                            <p class="font-medium text-gray-900">{{ $withdrawal->customer->member_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Nama</p>
                            <p class="font-medium text-gray-900">{{ $withdrawal->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">No. Telepon</p>
                            <p class="font-medium text-gray-900">{{ $withdrawal->customer->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Alamat</p>
                            <p class="font-medium text-gray-900">{{ $withdrawal->customer->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Amount Info -->
                <div class="mb-8 rounded-lg border-2 border-red-200 bg-red-50 p-6">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-medium text-gray-700">Nominal Penarikan:</span>
                        <span class="text-3xl font-bold text-red-600">
                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Note -->
                @if ($withdrawal->note)
                    <div class="mb-8 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                        <p class="mb-1 text-sm font-medium text-gray-700">Catatan:</p>
                        <p class="text-gray-900">{{ $withdrawal->note }}</p>
                    </div>
                @endif

                <!-- Creator Info -->
                <div class="mb-8 rounded-lg bg-gray-50 p-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-gray-600">Dibuat Oleh</p>
                            <p class="font-medium text-gray-900">{{ $withdrawal->creator->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Waktu Dibuat</p>
                            <p class="font-medium text-gray-900">{{ $withdrawal->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 border-t border-gray-200 pt-4">
                    <div class="flex items-start justify-between">
                        <div class="w-2/5 text-center">
                            <p class="mb-16 text-sm text-gray-600">Nasabah</p>
                            <div class="border-t border-gray-400 pt-2">
                                <p class="font-medium">{{ $withdrawal->customer->name }}</p>
                            </div>
                        </div>
                        <div class="w-2/5 text-center">
                            <p class="mb-16 text-sm text-gray-600">Petugas</p>
                            <div class="border-t border-gray-400 pt-2">
                                <p class="font-medium">{{ $withdrawal->creator->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-wrap gap-3">
                <x-button :href="route('withdrawals.index')" variant="secondary">Kembali ke Daftar</x-button>
                <x-button :href="route('withdrawals.create')">Penarikan Baru</x-button>
            </div>
        </div>
    </div>
</x-app-layout>
