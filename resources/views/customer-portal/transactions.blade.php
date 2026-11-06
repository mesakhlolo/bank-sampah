@extends('layouts.portal')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Transaksi</h1>
        <p class="mt-1 text-sm text-gray-600">Lihat semua setoran dan penarikan Anda.</p>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xs">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('customer-portal.transactions', ['type' => 'all']) }}"
                   class="{{ $type === 'all' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} flex-1 whitespace-nowrap border-b-2 px-1 py-3 text-center text-xs font-medium transition">
                    Semua Transaksi
                </a>
                <a href="{{ route('customer-portal.transactions', ['type' => 'deposits']) }}"
                   class="{{ $type === 'deposits' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} flex-1 whitespace-nowrap border-b-2 px-1 py-3 text-center text-xs font-medium transition">
                    Setoran
                </a>
                <a href="{{ route('customer-portal.transactions', ['type' => 'withdrawals']) }}"
                   class="{{ $type === 'withdrawals' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} flex-1 whitespace-nowrap border-b-2 px-1 py-3 text-center text-xs font-medium transition">
                    Penarikan
                </a>
            </nav>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xs">
        <x-table :headers="[
            'Tanggal',
            'Jenis',
            'Keterangan',
            ['label' => 'Debit', 'align' => 'right'],
            ['label' => 'Kredit', 'align' => 'right'],
            ['label' => 'Saldo', 'align' => 'right'],
        ]">
            @forelse($ledgerEntries as $entry)
                <tr class="transition hover:bg-gray-50">
                    <!-- Date -->
                    <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-900">
                        <div>{{ $entry->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->created_at->format('H:i') }}</div>
                    </td>

                    <!-- Type -->
                    <td class="whitespace-nowrap px-4 py-3">
                        @if($entry->direction === 'KREDIT')
                            <x-badge variant="green">Setoran</x-badge>
                        @else
                            <x-badge variant="red">Penarikan</x-badge>
                        @endif
                    </td>

                    <!-- Description -->
                    <td class="px-4 py-3 text-xs text-gray-900">
                        <div>{{ $entry->description }}</div>
                        @if($entry->ref_type === 'App\Models\Deposit' && $entry->referenceable)
                            <a href="{{ route('customer-portal.deposit-detail', $entry->referenceable->id) }}"
                               class="mt-0.5 inline-flex items-center text-xs text-green-700 hover:text-green-900">
                                Lihat Detail
                                <svg class="ml-0.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </td>

                    <!-- Debit (Expense - Penarikan) -->
                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs">
                        @if($entry->direction === 'DEBIT')
                            <span class="font-semibold text-red-600">
                                - Rp {{ number_format($entry->amount, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <!-- Kredit (Income - Setoran) -->
                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs">
                        @if($entry->direction === 'KREDIT')
                            <span class="font-semibold text-green-600">
                                + Rp {{ number_format($entry->amount, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <!-- Running Balance -->
                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-bold text-gray-900">
                        Rp {{ number_format($entry->running_balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Tidak ada transaksi" description="Setoran dan penarikan Anda akan muncul di sini." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <!-- Pagination -->
        @if($ledgerEntries->hasPages())
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
            {{ $ledgerEntries->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Card -->
    <div class="mt-8 rounded-xl border border-green-200 bg-green-50 p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="text-center">
                <p class="mb-1 text-sm text-gray-600">Saldo Saat Ini</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($customer->balance, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="mb-1 text-sm text-gray-600">Total Setoran</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ $customer->deposits()->count() }} transaksi
                </p>
            </div>
            <div class="text-center">
                <p class="mb-1 text-sm text-gray-600">Total Penarikan</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ $customer->withdrawals()->count() }} transaksi
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
