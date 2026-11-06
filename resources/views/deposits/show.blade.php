<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Detail Setoran" description="Struk transaksi setoran sampah.">
            <x-slot name="actions">
                <x-button :href="route('deposits.download-pdf', $deposit)" size="sm">Unduh PDF</x-button>
                <x-button :href="route('deposits.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-xs">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">Bank Sampah</h1>
                    <p class="mt-1 text-gray-600">Struk Transaksi Setoran</p>
                    <div class="mt-4 font-mono text-xl font-bold text-green-700">{{ $deposit->code }}</div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Transaction Info -->
                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Informasi Nasabah</h3>
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
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Informasi Transaksi</h3>
                        <div class="space-y-2">
                            <div class="flex">
                                <span class="w-32 text-sm text-gray-600">Tanggal:</span>
                                <span class="font-medium">{{ $deposit->transaction_date->format('d F Y') }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-sm text-gray-600">Operator:</span>
                                <span class="font-medium">{{ $deposit->creator->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-sm text-gray-600">Status:</span>
                                <x-badge variant="green">{{ strtoupper($deposit->status) }}</x-badge>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Deposit Items -->
                <div class="mb-6">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Rincian Setoran</h3>
                    <x-table :headers="[
                        'Kategori Sampah',
                        ['label' => 'Berat (kg)', 'align' => 'right'],
                        ['label' => 'Harga/kg', 'align' => 'right'],
                        ['label' => 'Subtotal', 'align' => 'right'],
                    ]">
                        @foreach ($deposit->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $item->wasteCategory->name }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    {{ number_format($item->weight_kg, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-sm">Rp
                                    {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium">Rp
                                    {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        <x-slot name="footer">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right text-sm font-bold text-gray-700">Total Setoran:</td>
                                <td class="px-4 py-4 text-right text-lg font-bold text-green-700">
                                    Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </x-slot>
                    </x-table>
                </div>

                @if ($deposit->notes)
                    <div class="mb-6">
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</h3>
                        <p class="rounded-lg bg-gray-50 p-3 text-sm text-gray-700">{{ $deposit->notes }}</p>
                    </div>
                @endif

                <hr class="my-6 border-gray-200">

                <!-- Footer -->
                <div class="text-center text-xs text-gray-500">
                    <p>Terima kasih atas kontribusi Anda untuk lingkungan yang lebih bersih.</p>
                    <p class="mt-2">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
