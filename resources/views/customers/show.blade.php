<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Nasabah
            </h2>
            <a href="{{ route('customers.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Customer Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Informasi Nasabah</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600">Kode Anggota</p>
                                    <p class="font-semibold text-lg">{{ $customer->member_code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kode Unik Portal</p>
                                    <div class="flex items-center gap-2">
                                        <p class="font-mono font-bold text-lg text-blue-600 bg-blue-50 px-3 py-1 rounded">{{ $customer->unique_code }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Kode untuk akses portal nasabah</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Nama Lengkap</p>
                                    <p class="font-semibold">{{ $customer->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">No. Telepon</p>
                                    <p class="font-semibold">{{ $customer->phone ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Alamat</p>
                                    <p class="font-semibold">{{ $customer->address ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tanggal Bergabung</p>
                                    <p class="font-semibold">
                                        {{ $customer->joined_at ? $customer->joined_at->format('d F Y') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Balance -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Saldo</h3>
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                                <p class="text-sm opacity-90 mb-2">Total Saldo</p>
                                <p class="text-4xl font-bold">
                                    Rp {{ number_format($customer->balance, 0, ',', '.') }}
                                </p>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-4 flex gap-2">
                                @can('create', App\Models\Deposit::class)
                                    <a href="{{ route('deposits.create', ['customer_id' => $customer->id]) }}"
                                        class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                        + Setor
                                    </a>
                                @endcan
                                @can('create', App\Models\Withdrawal::class)
                                    <a href="{{ route('withdrawals.create', ['customer_id' => $customer->id]) }}"
                                        class="flex-1 bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded text-center">
                                        - Tarik
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ tab: 'ledger' }">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button @click="tab = 'ledger'"
                            :class="tab === 'ledger' ? 'border-blue-500 text-blue-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                            Buku Besar
                        </button>
                        <button @click="tab = 'deposits'"
                            :class="tab === 'deposits' ? 'border-blue-500 text-blue-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                            Riwayat Setoran
                        </button>
                        <button @click="tab = 'withdrawals'"
                            :class="tab === 'withdrawals' ? 'border-blue-500 text-blue-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                            Riwayat Penarikan
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Ledger Tab -->
                    <div x-show="tab === 'ledger'">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Buku Besar (20 Transaksi Terakhir)</h3>
                            <a href="{{ route('ledger.show', $customer) }}"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua Mutasi →
                            </a>
                        </div>
                        @if ($customer->ledgerEntries->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Kode</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Keterangan</th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                Kredit</th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                Debit</th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            // First, calculate all balances in chronological order (oldest to newest)
                                            $allEntries = $customer->ledgerEntries()->orderBy('created_at', 'asc')->get();
                                            $balances = [];
                                            $runningBalance = 0;
                                            
                                            foreach ($allEntries as $e) {
                                                $runningBalance += $e->direction === 'KREDIT' ? $e->amount : -$e->amount;
                                                $balances[$e->id] = $runningBalance;
                                            }
                                            
                                            // Display entries in reverse chronological order (newest first)
                                            $entries = $customer->ledgerEntries;
                                        @endphp
                                        @foreach ($entries as $entry)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ $entry->transaction_date ? $entry->transaction_date->format('d M Y') : '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm font-mono">
                                                    {{ optional($entry->referenceable)->code ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $entry->description }}</td>
                                                <td class="px-4 py-3 text-sm text-right text-green-600">
                                                    {{ $entry->direction === 'KREDIT' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right text-red-600">
                                                    {{ $entry->direction === 'DEBIT' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold">
                                                    Rp {{ number_format($balances[$entry->id], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Belum ada transaksi</p>
                        @endif
                    </div>

                    <!-- Deposits Tab -->
                    <div x-show="tab === 'deposits'">
                        <h3 class="text-lg font-semibold mb-4">Riwayat Setoran (10 Terakhir)</h3>
                        @if ($customer->deposits->count() > 0)
                            <div class="space-y-3">
                                @foreach ($customer->deposits as $deposit)
                                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-mono text-sm text-gray-600">{{ $deposit->code }}</p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $deposit->transaction_date ? $deposit->transaction_date->format('d M Y') : '-' }}
                                                </p>
                                                <p class="text-sm text-gray-600 mt-2">
                                                    {{ $deposit->items->count() }} jenis sampah •
                                                    {{ number_format($deposit->total_weight, 2) }} kg
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-green-600">
                                                    Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                                                </p>
                                                <a href="{{ route('deposits.show', $deposit) }}"
                                                    class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                    Lihat Detail →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Belum ada setoran</p>
                        @endif
                    </div>

                    <!-- Withdrawals Tab -->
                    <div x-show="tab === 'withdrawals'">
                        <h3 class="text-lg font-semibold mb-4">Riwayat Penarikan (10 Terakhir)</h3>
                        @if ($customer->withdrawals->count() > 0)
                            <div class="space-y-3">
                                @foreach ($customer->withdrawals as $withdrawal)
                                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-mono text-sm text-gray-600">{{ $withdrawal->code }}</p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $withdrawal->transaction_date ? $withdrawal->transaction_date->format('d M Y') : '-' }}
                                                </p>
                                                @if ($withdrawal->note)
                                                    <p class="text-sm text-gray-600 mt-2">{{ $withdrawal->note }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-red-600">
                                                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                                </p>
                                                <a href="{{ route('withdrawals.show', $withdrawal) }}"
                                                    class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                    Lihat Detail →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Belum ada penarikan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Kode berhasil disalin: ' + text);
            }, function(err) {
                console.error('Gagal menyalin: ', err);
            });
        }
    </script>
    @endpush
</x-app-layout>
