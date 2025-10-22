<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Setoran') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('deposits.download-pdf', $deposit) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Download PDF
                </a>
                <a href="{{ route('deposits.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">BANK SAMPAH</h1>
                        <p class="text-gray-600">Struk Transaksi Setoran</p>
                        <div class="mt-4 text-2xl font-bold text-blue-600">{{ $deposit->code }}</div>
                    </div>

                    <hr class="my-6">

                    <!-- Transaction Info -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 mb-3">INFORMASI NASABAH</h3>
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="w-32 text-sm text-gray-600">Kode Nasabah:</span>
                                    <span class="font-medium">{{ $deposit->customer->member_code }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm text-gray-600">Nama:</span>
                                    <span class="font-medium">{{ $deposit->customer->name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm text-gray-600">Telepon:</span>
                                    <span class="font-medium">{{ $deposit->customer->phone ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 mb-3">INFORMASI TRANSAKSI</h3>
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="w-32 text-sm text-gray-600">Tanggal:</span>
                                    <span class="font-medium">{{ $deposit->transaction_date->format('d F Y') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm text-gray-600">creator:</span>
                                    <span class="font-medium">{{ $deposit->creator->name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm text-gray-600">Status:</span>
                                    <span class="font-medium">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">
                                            {{ strtoupper($deposit->status) }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Deposit Items -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-600 mb-3">RINCIAN SETORAN</h3>
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori
                                        Sampah</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Berat
                                        (kg)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                        Harga/kg</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                        Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($deposit->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $item->wasteCategory->name }}</td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            {{ number_format($item->weight_kg, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-right">Rp
                                            {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium">Rp
                                            {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-right text-sm font-bold text-gray-700">
                                        TOTAL SETORAN:</td>
                                    <td class="px-4 py-4 text-right text-lg font-bold text-blue-600">
                                        Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($deposit->notes)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">CATATAN</h3>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">{{ $deposit->notes }}</p>
                        </div>
                    @endif

                    <hr class="my-6">

                    <!-- Footer -->
                    <div class="text-center text-xs text-gray-500">
                        <p>Terima kasih atas kontribusi Anda untuk lingkungan yang lebih bersih!</p>
                        <p class="mt-2">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
