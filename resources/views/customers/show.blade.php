<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Detail Nasabah" description="Informasi anggota, saldo, dan riwayat transaksi.">
            <x-slot name="actions">
                <x-button :href="route('customers.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <!-- Customer Info Card -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-xs">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Left Column -->
                        <div>
                            <h3 class="mb-4 text-base font-semibold text-gray-900">Informasi Nasabah</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600">Kode Anggota</p>
                                    <p class="text-lg font-semibold">{{ $customer->member_code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kode Unik Portal</p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <p class="inline-block rounded-lg bg-gray-100 px-3 py-1 font-mono text-lg font-bold text-gray-900">
                                            {{ $customer->unique_code }}
                                        </p>
                                        <button type="button"
                                            onclick="copyToClipboard('{{ $customer->unique_code }}')"
                                            class="text-sm font-medium text-green-700 hover:text-green-900">
                                            Salin
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Kode untuk akses portal nasabah.</p>
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
                            <h3 class="mb-4 text-base font-semibold text-gray-900">Saldo</h3>
                            <div class="rounded-xl bg-linear-to-br from-green-500 to-green-600 p-6 text-white">
                                <p class="mb-2 text-sm opacity-90">Total Saldo</p>
                                <p class="text-4xl font-bold">
                                    Rp {{ number_format($customer->balance, 0, ',', '.') }}
                                </p>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-4 flex gap-2">
                                @can('create', App\Models\Deposit::class)
                                    <x-button :href="route('deposits.create', ['customer_id' => $customer->id])"
                                        class="flex-1">Setor Sampah</x-button>
                                @endcan
                                @can('create', App\Models\Withdrawal::class)
                                    <x-button :href="route('withdrawals.create', ['customer_id' => $customer->id])"
                                        variant="danger" class="flex-1">Tarik Saldo</x-button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Tabs -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-xs" x-data="{ tab: 'ledger' }">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button @click="tab = 'ledger'"
                            :class="tab === 'ledger' ? 'border-green-500 text-green-700' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="border-b-2 px-6 py-4 text-sm font-medium">
                            Buku Besar
                        </button>
                        <button @click="tab = 'deposits'"
                            :class="tab === 'deposits' ? 'border-green-500 text-green-700' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="border-b-2 px-6 py-4 text-sm font-medium">
                            Riwayat Setoran
                        </button>
                        <button @click="tab = 'withdrawals'"
                            :class="tab === 'withdrawals' ? 'border-green-500 text-green-700' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="border-b-2 px-6 py-4 text-sm font-medium">
                            Riwayat Penarikan
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Ledger Tab -->
                    <div x-show="tab === 'ledger'">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Buku Besar (20 Transaksi Terakhir)</h3>
                            <a href="{{ route('ledger.show', $customer) }}"
                                class="text-sm font-medium text-green-700 hover:text-green-900">
                                Lihat Semua Mutasi
                            </a>
                        </div>
                        @if ($entries->isNotEmpty())
                            <x-table :headers="[
                                'Tanggal',
                                'Kode',
                                'Keterangan',
                                ['label' => 'Kredit', 'align' => 'right'],
                                ['label' => 'Debit', 'align' => 'right'],
                                ['label' => 'Saldo', 'align' => 'right'],
                            ]">
                                @foreach ($entries as $entry)
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            {{ $entry->transaction_date ? $entry->transaction_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 font-mono text-sm">
                                            {{ optional($entry->referenceable)->code ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $entry->description }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-green-600">
                                            {{ $entry->direction === 'KREDIT' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-red-600">
                                            {{ $entry->direction === 'DEBIT' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold">
                                            Rp {{ number_format($entry->running_balance, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </x-table>
                        @else
                            <x-empty-state title="Belum ada transaksi" description="Mutasi saldo nasabah akan muncul di sini." />
                        @endif
                    </div>

                    <!-- Deposits Tab -->
                    <div x-show="tab === 'deposits'">
                        <h3 class="mb-4 text-base font-semibold text-gray-900">Riwayat Setoran (10 Terakhir)</h3>
                        @if ($customer->deposits->count() > 0)
                            <div class="space-y-3">
                                @foreach ($customer->deposits as $deposit)
                                    <div class="rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="font-mono text-sm text-gray-600">{{ $deposit->code }}</p>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ $deposit->transaction_date ? $deposit->transaction_date->format('d M Y') : '-' }}
                                                </p>
                                                <p class="mt-2 text-sm text-gray-600">
                                                    {{ $deposit->items->count() }} jenis sampah,
                                                    {{ number_format($deposit->total_weight, 2) }} kg
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-green-600">
                                                    Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}
                                                </p>
                                                <a href="{{ route('deposits.show', $deposit) }}"
                                                    class="mt-2 inline-block text-sm font-medium text-green-700 hover:text-green-900">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <x-empty-state title="Belum ada setoran" description="Setoran sampah nasabah akan muncul di sini." />
                        @endif
                    </div>

                    <!-- Withdrawals Tab -->
                    <div x-show="tab === 'withdrawals'">
                        <h3 class="mb-4 text-base font-semibold text-gray-900">Riwayat Penarikan (10 Terakhir)</h3>
                        @if ($customer->withdrawals->count() > 0)
                            <div class="space-y-3">
                                @foreach ($customer->withdrawals as $withdrawal)
                                    <div class="rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="font-mono text-sm text-gray-600">{{ $withdrawal->code }}</p>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ $withdrawal->transaction_date ? $withdrawal->transaction_date->format('d M Y') : '-' }}
                                                </p>
                                                @if ($withdrawal->note)
                                                    <p class="mt-2 text-sm text-gray-600">{{ $withdrawal->note }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-red-600">
                                                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                                </p>
                                                <a href="{{ route('withdrawals.show', $withdrawal) }}"
                                                    class="mt-2 inline-block text-sm font-medium text-green-700 hover:text-green-900">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <x-empty-state title="Belum ada penarikan" description="Penarikan saldo nasabah akan muncul di sini." />
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
                alert('Kode berhasil disalin.');
            }, function(err) {
                console.error('Gagal menyalin kode.');
            });
        }
    </script>
    @endpush
</x-app-layout>
