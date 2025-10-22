<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Mutasi - ') }}{{ $customer->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Customer Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama Nasabah</h3>
                            <p class="text-lg font-semibold">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Telepon</h3>
                            <p class="text-lg font-semibold">{{ $customer->phone }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Saldo Saat Ini</h3>
                            <p class="text-lg font-semibold text-green-600">Rp
                                {{ number_format($customer->balance, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                            ← Kembali ke Profil Nasabah
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ledger Entries -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Mutasi</h3>
                    @if ($ledgerWithBalance->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Keterangan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tipe</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Debit</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Kredit</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Saldo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($ledgerWithBalance as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $item['entry']->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if ($item['entry']->referenceable instanceof \App\Models\Deposit)
                                                    Setoran #{{ $item['entry']->referenceable->code }}
                                                @elseif ($item['entry']->referenceable instanceof \App\Models\Withdrawal)
                                                    Penarikan #{{ $item['entry']->referenceable->code }}
                                                @else
                                                    {{ class_basename($item['entry']->ref_type) }}
                                                    #{{ $item['entry']->ref_id }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if ($item['entry']->direction === 'DEBIT')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Debit
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Kredit
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                @if ($item['entry']->direction === 'DEBIT')
                                                    <span class="text-green-600 font-medium">
                                                        {{ number_format($item['entry']->amount, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                @if ($item['entry']->direction === 'KREDIT')
                                                    <span class="text-red-600 font-medium">
                                                        {{ number_format($item['entry']->amount, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold">
                                                {{ number_format($item['running_balance'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Belum ada transaksi untuk nasabah ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
