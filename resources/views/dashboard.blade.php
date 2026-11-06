<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Dasbor" description="Ringkasan operasional Bank Sampah hari ini.">
            <x-slot name="actions">
                <x-button :href="route('deposits.create')" size="sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Setoran Baru
                </x-button>
                <x-button :href="route('withdrawals.create')" variant="secondary" size="sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14" />
                    </svg>
                    Penarikan Baru
                </x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <!-- Welcome -->
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-xs">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Selamat datang, {{ Auth::user()->name }}</p>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Anda masuk sebagai
                        <span class="font-medium text-green-700">{{ Auth::user()->role === 'admin' ? 'Admin' : 'Operator' }}</span>.
                    </p>
                </div>
                <x-badge variant="green">{{ now()->translatedFormat('l, d F Y') }}</x-badge>
            </div>

            <!-- Today's Metrics -->
            <div>
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Transaksi hari ini</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-stat-card
                        label="Total Setoran"
                        :value="'Rp '.number_format($todayDeposits, 0, ',', '.')"
                        description="Nilai setoran sampah hari ini"
                        variant="green"
                    >
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </x-slot>
                    </x-stat-card>

                    <x-stat-card
                        label="Total Penarikan"
                        :value="'Rp '.number_format($todayWithdrawals, 0, ',', '.')"
                        description="Nilai penarikan saldo hari ini"
                        variant="red"
                    >
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </x-slot>
                    </x-stat-card>

                    <x-stat-card
                        label="Jumlah Transaksi"
                        :value="number_format($todayTransactions, 0, ',', '.')"
                        description="Setoran dan penarikan hari ini"
                        variant="gray"
                    >
                        <x-slot name="icon">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </x-slot>
                    </x-stat-card>
                </div>
            </div>

            <!-- Top 3 Categories Today -->
            @if ($topCategoriesToday->isNotEmpty())
                <x-card title="Top 3 kategori hari ini" description="Kategori dengan berat sampah terbanyak." :padding="false">
                    <x-table :headers="[
                        'Kategori',
                        ['label' => 'Total Berat', 'align' => 'right'],
                        ['label' => 'Total Nilai', 'align' => 'right'],
                    ]">
                        @foreach ($topCategoriesToday as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">
                                    {{ number_format($category->total_weight, 2, ',', '.') }} kg
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-slate-900">
                                    Rp {{ number_format($category->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                </x-card>
            @endif

            <!-- Quick Actions -->
            <div>
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Menu cepat</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('customers.index') }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-green-300 hover:shadow-sm">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <p class="mt-4 text-sm font-semibold text-slate-900 group-hover:text-green-700">Kelola Nasabah</p>
                        <p class="mt-1 text-xs text-slate-500">Tambah, ubah, dan lihat data nasabah.</p>
                    </a>

                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('waste-categories.index') }}"
                            class="group rounded-xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-green-300 hover:shadow-sm">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </span>
                            <p class="mt-4 text-sm font-semibold text-slate-900 group-hover:text-green-700">Kategori Sampah</p>
                            <p class="mt-1 text-xs text-slate-500">Atur kategori dan harga sampah.</p>
                        </a>
                    @endif

                    <a href="{{ route('reports.summary') }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-green-300 hover:shadow-sm">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </span>
                        <p class="mt-4 text-sm font-semibold text-slate-900 group-hover:text-green-700">Laporan</p>
                        <p class="mt-1 text-xs text-slate-500">Lihat ringkasan dan unduh PDF.</p>
                    </a>

                    <a href="{{ route('transactions.index') }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-green-300 hover:shadow-sm">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <p class="mt-4 text-sm font-semibold text-slate-900 group-hover:text-green-700">Riwayat Transaksi</p>
                        <p class="mt-1 text-xs text-slate-500">Lihat seluruh setoran dan penarikan.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
