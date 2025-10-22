<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Ringkasan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.summary') }}" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Dari
                                Tanggal</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ request('start_date', $start) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai
                                Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date', $end) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Tampilkan
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('reports.summary') }}"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 inline-block">
                                Reset
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('reports.download-pdf', ['start_date' => $start, 'end_date' => $end]) }}"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 inline-block">
                                Download PDF
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-green-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="text-2xl font-bold">Rp
                            {{ number_format($totalDeposits->total_amount ?? 0, 0, ',', '.') }}</div>
                        <div class="text-sm">Total Setoran</div>
                        <div class="text-xs mt-1">{{ number_format($totalDeposits->total_weight ?? 0, 2, ',', '.') }}
                            kg | {{ $totalDeposits->count ?? 0 }} transaksi</div>
                    </div>
                </div>
                <div class="bg-red-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="text-2xl font-bold">Rp
                            {{ number_format($totalWithdrawals->total_amount ?? 0, 0, ',', '.') }}</div>
                        <div class="text-sm">Total Penarikan</div>
                        <div class="text-xs mt-1">{{ $totalWithdrawals->count ?? 0 }} transaksi</div>
                    </div>
                </div>
                <div class="bg-blue-500 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="text-2xl font-bold">Rp {{ number_format($netBalanceChange, 0, ',', '.') }}</div>
                        <div class="text-sm">Perubahan Saldo Bersih</div>
                        <div class="text-xs mt-1">Setoran - Penarikan</div>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Breakdown per Kategori Sampah</h3>
                    @if ($categorySummary->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total Berat (kg)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total Nilai (Rp)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jml
                                            Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($categorySummary as $category)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $category->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ number_format($category->total_weight, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ number_format($category->total_amount, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $category->transaction_count }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td class="px-6 py-3">TOTAL</td>
                                        <td class="px-6 py-3">
                                            {{ number_format($categorySummary->sum('total_weight'), 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-3">
                                            {{ number_format($categorySummary->sum('total_amount'), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-3">{{ $categorySummary->sum('transaction_count') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada data untuk periode ini.</p>
                    @endif
                </div>
            </div>

            <!-- Top 10 Customers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Top 10 Nasabah (berdasarkan Total Setoran)</h3>
                    @if ($topCustomers->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                            Nasabah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Telepon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total Nilai (Rp)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total Berat (kg)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jml
                                            Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($topCustomers as $index => $customer)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $customer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $customer->phone }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ number_format($customer->total_amount, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ number_format($customer->total_weight, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $customer->transaction_count }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada data untuk periode ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
