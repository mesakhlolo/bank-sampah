@extends('layouts.portal')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Transaksi</h1>
        <p class="mt-1 text-sm text-gray-600">Lihat semua setoran dan penarikan Anda</p>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('customer-portal.transactions', ['type' => 'all']) }}" 
                   class="{{ $type === 'all' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} flex-1 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-xs text-center transition">
                    Semua Transaksi
                </a>
                <a href="{{ route('customer-portal.transactions', ['type' => 'deposits']) }}" 
                   class="{{ $type === 'deposits' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} flex-1 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-xs text-center transition">
                    Setoran
                </a>
                <a href="{{ route('customer-portal.transactions', ['type' => 'withdrawals']) }}" 
                   class="{{ $type === 'withdrawals' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} flex-1 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-xs text-center transition">
                    Penarikan
                </a>
            </nav>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterangan
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Debit
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kredit
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ledgerEntries as $entry)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- Date -->
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                            <div>{{ $entry->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $entry->created_at->format('H:i') }}</div>
                        </td>

                        <!-- Type -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($entry->direction === 'KREDIT')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Setoran
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                                Penarikan
                            </span>
                            @endif
                        </td>

                        <!-- Description -->
                        <td class="px-4 py-3 text-xs text-gray-900">
                            <div>{{ $entry->description }}</div>
                            @if($entry->ref_type === 'App\Models\Deposit' && $entry->referenceable)
                            <a href="{{ route('customer-portal.deposit-detail', $entry->referenceable->id) }}" 
                               class="text-xs text-blue-600 hover:text-blue-700 inline-flex items-center mt-0.5">
                                Lihat Detail
                                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @endif
                        </td>

                        <!-- Debit (Expense - Penarikan) -->
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-right">
                            @if($entry->direction === 'DEBIT')
                            <span class="text-red-600 font-semibold">
                                - Rp {{ number_format($entry->amount, 0, ',', '.') }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Kredit (Income - Setoran) -->
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-right">
                            @if($entry->direction === 'KREDIT')
                            <span class="text-green-600 font-semibold">
                                + Rp {{ number_format($entry->amount, 0, ',', '.') }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Running Balance -->
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-right font-bold text-gray-900">
                            Rp {{ number_format($entry->running_balance, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-gray-500 text-xs">Tidak ada transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($ledgerEntries->hasPages())
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            {{ $ledgerEntries->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Card -->
    <div class="mt-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Saldo Saat Ini</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($customer->balance, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Total Setoran</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ $customer->deposits()->count() }} transaksi
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Total Penarikan</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ $customer->withdrawals()->count() }} transaksi
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
