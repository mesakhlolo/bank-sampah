<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Laporan Ringkasan" description="Rekap setoran, penarikan, dan performa nasabah.">
            <x-slot name="actions">
                <x-button :href="route('reports.download-pdf', ['start_date' => $start, 'end_date' => $end])"
                    variant="secondary" size="sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Unduh PDF
                </x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <!-- Filter -->
            <x-card title="Periode" :padding="false">
                <form method="GET" action="{{ route('reports.summary') }}"
                    class="grid grid-cols-1 items-end gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <x-input-label for="start_date" value="Dari Tanggal" />
                        <x-text-input type="date" name="start_date" id="start_date"
                            :value="request('start_date', $start)" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="Sampai Tanggal" />
                        <x-text-input type="date" name="end_date" id="end_date"
                            :value="request('end_date', $end)" class="mt-1 block w-full" />
                    </div>
                    <div class="flex gap-2">
                        <x-button type="submit" size="sm">Tampilkan</x-button>
                        <x-button :href="route('reports.summary')" variant="secondary" size="sm">Reset</x-button>
                    </div>
                </form>
            </x-card>

            <!-- Summary Metrics -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-stat-card
                    label="Total Setoran"
                    :value="'Rp '.number_format($totalDeposits->total_amount ?? 0, 0, ',', '.')"
                    :description="number_format($totalDeposits->total_weight ?? 0, 2, ',', '.').' kg | '.($totalDeposits->count ?? 0).' transaksi'"
                    variant="green"
                />

                <x-stat-card
                    label="Total Penarikan"
                    :value="'Rp '.number_format($totalWithdrawals->total_amount ?? 0, 0, ',', '.')"
                    :description="($totalWithdrawals->count ?? 0).' transaksi'"
                    variant="red"
                />

                <x-stat-card
                    label="Perubahan Saldo Bersih"
                    :value="'Rp '.number_format($netBalanceChange, 0, ',', '.')"
                    description="Setoran dikurangi penarikan"
                    variant="gray"
                />
            </div>

            <!-- Category Breakdown -->
            <x-card title="Breakdown per Kategori Sampah" :padding="false">
                @if ($categorySummary->isEmpty())
                    <x-empty-state title="Tidak ada data" description="Tidak ada transaksi pada periode ini." />
                @else
                    <x-table :headers="[
                        'Kategori',
                        ['label' => 'Total Berat (kg)', 'align' => 'right'],
                        ['label' => 'Total Nilai (Rp)', 'align' => 'right'],
                        ['label' => 'Jml Transaksi', 'align' => 'right'],
                    ]">
                        @foreach ($categorySummary as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ number_format($category->total_weight, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ number_format($category->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ $category->transaction_count }}
                                </td>
                            </tr>
                        @endforeach

                        <x-slot name="footer">
                            <tr>
                                <td class="px-4 py-3 text-sm">TOTAL</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    {{ number_format($categorySummary->sum('total_weight'), 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    {{ number_format($categorySummary->sum('total_amount'), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    {{ $categorySummary->sum('transaction_count') }}
                                </td>
                            </tr>
                        </x-slot>
                    </x-table>
                @endif
            </x-card>

            <!-- Top 10 Customers -->
            <x-card title="Top 10 Nasabah" description="Berdasarkan total setoran pada periode ini." :padding="false">
                @if ($topCustomers->isEmpty())
                    <x-empty-state title="Tidak ada data" description="Tidak ada transaksi pada periode ini." />
                @else
                    <x-table :headers="[
                        '#',
                        'Nama Nasabah',
                        'Telepon',
                        ['label' => 'Total Nilai (Rp)', 'align' => 'right'],
                        ['label' => 'Total Berat (kg)', 'align' => 'right'],
                        ['label' => 'Jml Transaksi', 'align' => 'right'],
                    ]">
                        @foreach ($topCustomers as $index => $customer)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $customer->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $customer->phone }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ number_format($customer->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ number_format($customer->total_weight, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ $customer->transaction_count }}
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
