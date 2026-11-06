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
            <x-card>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">Nama Nasabah</h3>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">Telepon</h3>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900">{{ $customer->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">Saldo Saat Ini</h3>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900">Rp
                            {{ number_format($customer->balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Ledger Entries -->
            <x-card title="Riwayat Mutasi" :padding="false">
                @if ($ledgerWithBalance->isNotEmpty())
                    <x-table :headers="[
                        'Tanggal',
                        'Keterangan',
                        'Tipe',
                        ['label' => 'Debit', 'align' => 'right'],
                        ['label' => 'Kredit', 'align' => 'right'],
                        ['label' => 'Saldo', 'align' => 'right'],
                    ]">
                        @foreach ($ledgerWithBalance as $entry)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $entry->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-900">
                                    @if ($entry->referenceable instanceof \App\Models\Deposit)
                                        Setoran #{{ $entry->referenceable->code }}
                                    @elseif ($entry->referenceable instanceof \App\Models\Withdrawal)
                                        Penarikan #{{ $entry->referenceable->code }}
                                    @else
                                        {{ class_basename($entry->ref_type) }}
                                        #{{ $entry->ref_id }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
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
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    @if ($entry->direction === 'KREDIT')
                                        <span class="font-medium text-green-700">
                                            {{ number_format($entry->amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-slate-900">
                                    {{ number_format($entry->running_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </x-table>

                    @if ($ledgerWithBalance->hasPages())
                        <div class="border-t border-slate-100 px-4 py-3">
                            {{ $ledgerWithBalance->links() }}
                        </div>
                    @endif
                @else
                    <x-empty-state title="Belum ada transaksi" description="Mutasi saldo nasabah ini akan muncul di sini." />
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
