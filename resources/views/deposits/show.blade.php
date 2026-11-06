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
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <x-card :padding="false">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <h1 class="text-base font-semibold text-slate-900">Bukti Setoran</h1>
                        <p class="mt-0.5 text-sm text-slate-500">Bank Sampah Bersih Sejahtera</p>
                    </div>
                    <x-badge variant="green">{{ ucfirst($deposit->status) }}</x-badge>
                </div>

                <!-- Transaction Info -->
                <div class="grid grid-cols-1 gap-5 border-b border-slate-200 bg-slate-50 px-6 py-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Kode Setoran</p>
                        <p class="mt-1 font-mono text-sm font-medium text-slate-900">{{ $deposit->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ $deposit->transaction_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Nasabah</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $deposit->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $deposit->customer->member_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Diterima oleh</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $deposit->creator->name }}</p>
                        <p class="text-xs text-slate-500">{{ $deposit->creator->role === 'admin' ? 'Admin' : 'Operator' }}</p>
                    </div>
                </div>

                <!-- Deposit Items -->
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-slate-900">Rincian setoran</h2>

                    <div class="mt-3">
                        <x-table :headers="[
                            'Kategori Sampah',
                            ['label' => 'Berat (kg)', 'align' => 'right'],
                            ['label' => 'Harga/kg', 'align' => 'right'],
                            ['label' => 'Subtotal', 'align' => 'right'],
                        ]">
                            @foreach ($deposit->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-900">{{ $item->wasteCategory->name }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-slate-600">
                                        {{ number_format($item->weight_kg, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-slate-600">Rp
                                        {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-slate-900">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            <x-slot name="footer">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-slate-600">
                                        Total Setoran</td>
                                    <td class="px-4 py-3 text-right text-base font-semibold text-slate-900">
                                        Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                </div>

                @if ($deposit->notes)
                    <div class="border-t border-slate-200 px-6 py-5">
                        <h2 class="text-sm font-semibold text-slate-900">Catatan</h2>
                        <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-700">{{ $deposit->notes }}</p>
                    </div>
                @endif

                <!-- Footer -->
                <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 text-xs text-slate-500">
                    <p>Terima kasih atas kontribusi Anda untuk lingkungan yang lebih bersih.</p>
                    <p class="mt-1">Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>