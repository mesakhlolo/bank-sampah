<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Riwayat Penarikan Saldo" description="Daftar transaksi penarikan yang telah tercatat.">
            <x-slot name="actions">
                @can('create', App\Models\Withdrawal::class)
                    <x-button :href="route('withdrawals.create')" size="sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Penarikan Baru
                    </x-button>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <!-- Filters -->
            <x-card title="Filter" :padding="false">
                <form method="GET" action="{{ route('withdrawals.index') }}"
                    class="grid grid-cols-1 gap-4 p-5 md:grid-cols-4">
                    <div>
                        <x-input-label for="search" value="Cari Kode" />
                        <x-text-input type="text" name="search" id="search" :value="request('search')"
                            placeholder="WD-..." class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="customer_id" value="Nasabah" />
                        <x-select name="customer_id" id="customer_id" class="mt-1">
                            <option value="">Semua Nasabah</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-input-label for="start_date" value="Dari Tanggal" />
                        <x-text-input type="date" name="start_date" id="start_date" :value="request('start_date')"
                            class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="end_date" value="Sampai Tanggal" />
                        <x-text-input type="date" name="end_date" id="end_date" :value="request('end_date')"
                            class="mt-1 block w-full" />
                    </div>

                    <div class="flex gap-2 md:col-span-4">
                        <x-button type="submit" size="sm">Filter</x-button>
                        <x-button :href="route('withdrawals.index')" variant="secondary" size="sm">Reset</x-button>
                    </div>
                </form>
            </x-card>

            <!-- Withdrawals Table -->
            <x-card :padding="false">
                @if ($withdrawals->isEmpty())
                    <x-empty-state
                        title="Belum ada penarikan"
                        description="Transaksi penarikan saldo yang dicatat akan muncul di sini."
                    />
                @else
                    <x-table :headers="[
                        'Kode',
                        'Tanggal',
                        'Nasabah',
                        'Operator',
                        ['label' => 'Nominal', 'align' => 'right'],
                        ['label' => 'Aksi', 'align' => 'center'],
                    ]">
                        @foreach ($withdrawals as $withdrawal)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-slate-900">
                                    {{ $withdrawal->code }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $withdrawal->transaction_date ? $withdrawal->transaction_date->format('d M Y') : $withdrawal->created_at->format('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $withdrawal->customer->name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $withdrawal->creator->name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-red-600">
                                    - Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium">
                                    <a href="{{ route('withdrawals.show', $withdrawal) }}"
                                        class="text-green-700 hover:text-green-900">Lihat</a>
                                    <a href="{{ route('withdrawals.download-pdf', $withdrawal) }}"
                                        class="ml-3 text-slate-500 hover:text-slate-700">PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>

                    <div class="border-t border-slate-100 px-4 py-3">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
