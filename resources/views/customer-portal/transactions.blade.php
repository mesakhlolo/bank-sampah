@extends('layouts.portal')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Riwayat Transaksi</h1>
        <p class="mt-1 text-sm text-slate-500">Lihat semua setoran dan penarikan Anda.</p>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-xs">
        @php
            $tabs = [
                'all' => 'Semua',
                'deposits' => 'Setoran',
                'withdrawals' => 'Penarikan',
            ];
        @endphp
        @foreach ($tabs as $value => $label)
            <a href="{{ route('customer-portal.transactions', ['type' => $value]) }}"
               class="{{ $type === $value ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900' }} rounded-md px-4 py-1.5 text-sm font-medium transition">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Transactions Table -->
    <x-card :padding="false">
        <x-table :headers="[
            'Tanggal',
            'Jenis',
            'Keterangan',
            ['label' => 'Debit', 'align' => 'right'],
            ['label' => 'Kredit', 'align' => 'right'],
            ['label' => 'Saldo', 'align' => 'right'],
        ]">
            @forelse ($ledgerEntries as $entry)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-900">
                        <div>{{ $entry->created_at->format('d M Y') }}</div>
                        <div class="text-slate-500">{{ $entry->created_at->format('H:i') }}</div>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3">
                        @if ($entry->direction === 'KREDIT')
                            <x-badge variant="green">Setoran</x-badge>
                        @else
                            <x-badge variant="red">Penarikan</x-badge>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-xs text-slate-900">
                        <div>{{ $entry->description }}</div>
                        @if ($entry->ref_type === 'App\Models\Deposit' && $entry->referenceable)
                            <a href="{{ route('customer-portal.deposit-detail', $entry->referenceable->id) }}"
                               class="mt-0.5 inline-block text-xs text-slate-500 hover:text-green-700">
                                Lihat detail
                            </a>
                        @endif
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs">
                        @if ($entry->direction === 'DEBIT')
                            <span class="font-semibold text-red-600">- Rp {{ number_format($entry->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs">
                        @if ($entry->direction === 'KREDIT')
                            <span class="font-semibold text-green-700">+ Rp {{ number_format($entry->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold text-slate-900">
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

        @if ($ledgerEntries->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $ledgerEntries->links() }}
            </div>
        @endif
    </x-card>

    <!-- Summary -->
    <x-card class="mt-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div>
                <p class="text-sm text-slate-500">Saldo saat ini</p>
                <p class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Rp {{ number_format($customer->balance, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total setoran</p>
                <p class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $customer->deposits()->count() }} transaksi</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total penarikan</p>
                <p class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $customer->withdrawals()->count() }} transaksi</p>
            </div>
        </div>
    </x-card>
</div>
@endsection
