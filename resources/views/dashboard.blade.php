<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Bank Sampah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600">Anda login sebagai: <span
                            class="font-bold">{{ ucfirst(Auth::user()->role) }}</span></p>
                </div>
            </div>

            <!-- Today's Metrics -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4">Transaksi Hari Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-500 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="text-3xl font-bold">Rp {{ number_format($todayDeposits, 0, ',', '.') }}</div>
                            <div class="text-sm">Total Setoran</div>
                        </div>
                    </div>
                    <div class="bg-red-500 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="text-3xl font-bold">Rp {{ number_format($todayWithdrawals, 0, ',', '.') }}
                            </div>
                            <div class="text-sm">Total Penarikan</div>
                        </div>
                    </div>
                    <div class="bg-blue-500 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="text-3xl font-bold">{{ $todayTransactions }}</div>
                            <div class="text-sm">Jumlah Transaksi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 3 Categories Today -->
            @if ($topCategoriesToday->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Top 3 Kategori Hari Ini</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total Berat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($topCategoriesToday as $category)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ number_format($category->total_weight, 2, ',', '.') }} kg</td>
                                            <td class="px-6 py-4 whitespace-nowrap">Rp
                                                {{ number_format($category->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Menu Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('deposits.create') }}"
                            class="block p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition">
                            <div class="font-semibold text-purple-700">🔄 Setoran Baru</div>
                            <div class="text-sm text-gray-600 mt-1">Catat setoran sampah nasabah</div>
                        </a>
                        <a href="{{ route('withdrawals.create') }}"
                            class="block p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg border border-yellow-200 transition">
                            <div class="font-semibold text-yellow-700">💰 Penarikan Saldo</div>
                            <div class="text-sm text-gray-600 mt-1">Proses penarikan saldo nasabah</div>
                        </a>
                        <a href="{{ route('customers.index') }}"
                            class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition">
                            <div class="font-semibold text-blue-700">👥 Kelola Nasabah</div>
                            <div class="text-sm text-gray-600 mt-1">Tambah, edit, dan lihat data nasabah</div>
                        </a>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('waste-categories.index') }}"
                                class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition">
                                <div class="font-semibold text-green-700">📦 Kelola Kategori Sampah</div>
                                <div class="text-sm text-gray-600 mt-1">Atur kategori dan harga sampah</div>
                            </a>
                        @endif
                        <a href="{{ route('reports.summary') }}"
                            class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition">
                            <div class="font-semibold text-red-700">📊 Laporan</div>
                            <div class="text-sm text-gray-600 mt-1">Lihat laporan dan statistik</div>
                        </a>
                        <a href="{{ route('deposits.index') }}"
                            class="block p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition">
                            <div class="font-semibold text-indigo-700">📋 Riwayat Transaksi</div>
                            <div class="text-sm text-gray-600 mt-1">Lihat semua transaksi</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
