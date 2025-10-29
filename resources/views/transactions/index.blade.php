<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Transaksi') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('deposits.create') }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    + Setoran Baru
                </a>
                <a href="{{ route('withdrawals.create') }}"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    + Penarikan Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('transactions.index') }}"
                        class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search by code -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Cari Kode</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="DEP-... / WD-..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Filter by type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Transaksi</label>
                            <select name="type" id="type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Transaksi</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Setoran
                                </option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>
                                    Penarikan</option>
                            </select>
                        </div>

                        <!-- Filter by customer -->
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">Nasabah</label>
                            <select name="customer_id" id="customer_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Nasabah</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date from -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Date to -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Buttons -->
                        <div class="md:col-span-5 flex gap-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            <a href="{{ route('transactions.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($transactions->isEmpty())
                        <p class="text-center text-gray-500 py-8">Belum ada transaksi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kode
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nasabah
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Operator
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nominal
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($transactions as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($transaction->type === 'deposit')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Setoran
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Penarikan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $transaction->code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->customer->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->creator->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                                @if ($transaction->type === 'deposit')
                                                    <span class="text-green-600">+ Rp
                                                        {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-red-600">- Rp
                                                        {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                @if ($transaction->type === 'deposit')
                                                    <a href="{{ route('deposits.show', $transaction->id) }}"
                                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                                        Lihat
                                                    </a>
                                                    <a href="{{ route('deposits.download-pdf', $transaction->id) }}"
                                                        class="text-green-600 hover:text-green-900">
                                                        PDF
                                                    </a>
                                                @else
                                                    <a href="{{ route('withdrawals.show', $transaction->id) }}"
                                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                                        Lihat
                                                    </a>
                                                    <a href="{{ route('withdrawals.download-pdf', $transaction->id) }}"
                                                        class="text-green-600 hover:text-green-900">
                                                        PDF
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
