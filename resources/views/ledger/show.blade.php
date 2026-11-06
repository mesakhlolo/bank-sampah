<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Riwayat Mutasi" :description="$customer->name">
            <x-slot name="actions">
                <x-button :href="route('customers.show', $customer)" variant="secondary" size="sm">Kembali ke Profil</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <!-- Customer Info -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Nama Nasabah</h3>
                        <p class="text-lg font-semibold">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Telepon</h3>
                        <p class="text-lg font-semibold">{{ $customer->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Saldo Saat Ini</h3>
                        <p class="text-lg font-semibold text-green-600">Rp
                            {{ number_format($customer->balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Ledger Entries -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-xs">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Riwayat Mutasi</h3>
                </div>

                @if ($ledgerWithBalance->isNotEmpty())
                    <div class="p-2">
                        <x-table :headers="[
                            'Tanggal',
                            'Keterangan',
                            'Tipe',
                            ['label' => 'Debit', 'align' => 'right'],
                            ['label' => 'Kredit', 'align' => 'right'],
                            ['label' => 'Saldo', 'align' => 'right'],
                        ]">
                            @foreach ($ledgerWithBalance as $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        {{ $entry->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($entry->referenceable instanceof \App\Models\Deposit)
                                            Setoran #{{ $entry->referenceable->code }}
                                        @elseif ($entry->referenceable instanceof \App\Models\Withdrawal)
                                            Penarikan #{{ $entry->referenceable->code }}
                                        @else
                                            {{ class_basename($entry->ref_type) }}
                                            #{{ $entry->ref_id }}
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        @if ($entry->direction === 'DEBIT')
                                            <x-badge variant="red">Debit</x-badge>
                                        @else
                                            <x-badge variant="green">Kredit</x-badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                        @if ($entry->direction === 'DEBIT')
                                            <span class="font-medium text-red-600">
                                                {{ number_format($entry->amount, 0, ',', '.') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                        @if ($entry->direction === 'KREDIT')
                                            <span class="font-medium text-green-600">
                                                {{ number_format($entry->amount, 0, ',', '.') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold">
                                        {{ number_format($entry->running_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>
                    </div>

                    @if ($ledgerWithBalance->hasPages())
                        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                            {{ $ledgerWithBalance->links() }}
                        </div>
                    @endif
                @else
                    <x-empty-state title="Belum ada transaksi" description="Mutasi saldo nasabah ini akan muncul di sini." />
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
