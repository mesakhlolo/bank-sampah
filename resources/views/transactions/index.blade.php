<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Riwayat Transaksi" description="Seluruh setoran dan penarikan dalam satu daftar.">
            <x-slot name="actions">
                <x-button :href="route('deposits.create')" size="sm">Setoran Baru</x-button>
                <x-button :href="route('withdrawals.create')" variant="danger" size="sm">Penarikan Baru</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <!-- Filters -->
            <x-card title="Filter" :padding="false">
                <form method="GET" action="{{ route('transactions.index') }}"
                    class="grid grid-cols-1 gap-4 p-5 md:grid-cols-5">
                    <div>
                        <x-input-label for="search" value="Cari Kode" />
                        <x-text-input type="text" name="search" id="search" :value="request('search')"
                            placeholder="DEP-... / WD-..." class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Tipe Transaksi" />
                        <x-select name="type" id="type" class="mt-1">
                            <option value="">Semua Transaksi</option>
                            <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Setoran</option>
                            <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Penarikan</option>
                        </x-select>
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
                        <x-input-label for="date_from" value="Dari Tanggal" />
                        <x-text-input type="date" name="date_from" id="date_from" :value="request('date_from')"
                            class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="date_to" value="Sampai Tanggal" />
                        <x-text-input type="date" name="date_to" id="date_to" :value="request('date_to')"
                            class="mt-1 block w-full" />
                    </div>

                    <div class="flex gap-2 md:col-span-5">
                        <x-button type="submit" size="sm">Filter</x-button>
                        <x-button :href="route('transactions.index')" variant="secondary" size="sm">Reset</x-button>
                    </div>
                </form>
            </x-card>

            <!-- Transactions Table -->
            <x-card :padding="false">
                @if ($transactions->isEmpty())
                    <x-empty-state
                        title="Belum ada transaksi"
                        description="Setoran dan penarikan yang dicatat akan muncul di sini."
                    />
                @else
                    <x-table :headers="[
                        'Tipe',
                        'Kode',
                        'Tanggal',
                        'Nasabah',
                        'Operator',
                        ['label' => 'Nominal', 'align' => 'right'],
                        ['label' => 'Aksi', 'align' => 'center'],
                    ]">
                        @foreach ($transactions as $transaction)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($transaction->type === 'deposit')
                                        <x-badge variant="green">Setoran</x-badge>
                                    @else
                                        <x-badge variant="red">Penarikan</x-badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-slate-900">
                                    {{ $transaction->code }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $transaction->customer->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $transaction->creator->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold">
                                    @if ($transaction->type === 'deposit')
                                        <span class="text-green-700">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-red-600">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium">
                                    @if ($transaction->type === 'deposit')
                                        <a href="{{ route('deposits.show', $transaction->id) }}"
                                            class="text-green-700 hover:text-green-900">Lihat</a>
                                        <a href="{{ route('deposits.download-pdf', $transaction->id) }}"
                                            class="ml-3 text-slate-500 hover:text-slate-700">PDF</a>
                                    @else
                                        <a href="{{ route('withdrawals.show', $transaction->id) }}"
                                            class="text-green-700 hover:text-green-900">Lihat</a>
                                        <a href="{{ route('withdrawals.download-pdf', $transaction->id) }}"
                                            class="ml-3 text-slate-500 hover:text-slate-700">PDF</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>

                    <div class="border-t border-slate-100 px-4 py-3">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
