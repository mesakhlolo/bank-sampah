<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Data Nasabah" description="Kelola data anggota Bank Sampah.">
            <x-slot name="actions">
                @can('create', App\Models\Customer::class)
                    <x-button :href="route('customers.create')" size="sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Nasabah
                    </x-button>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash />

            <div class="rounded-xl border border-slate-200 bg-white shadow-xs">
                <div class="border-b border-slate-100 p-4">
                    <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col gap-2 sm:flex-row">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, kode anggota, atau telepon..."
                            class="w-full rounded-lg border-slate-300 shadow-xs focus:border-green-500 focus:ring-green-500 sm:flex-1">
                        <x-button type="submit">Cari</x-button>
                        @if (request('search'))
                            <x-button :href="route('customers.index')" variant="secondary">Reset</x-button>
                        @endif
                    </form>
                </div>

                @if ($customers->isEmpty())
                    <x-empty-state
                        title="Belum ada nasabah"
                        description="Tambahkan nasabah pertama untuk mulai mencatat setoran sampah."
                    >
                        <x-slot name="action">
                            @can('create', App\Models\Customer::class)
                                <x-button :href="route('customers.create')" size="sm">Tambah Nasabah</x-button>
                            @endcan
                        </x-slot>
                    </x-empty-state>
                @else
                    <x-table :headers="[
                        'Kode Anggota',
                        'Kode Portal',
                        'Nama',
                        'Telepon',
                        'Tanggal Bergabung',
                        ['label' => 'Saldo', 'align' => 'right'],
                        ['label' => 'Aksi', 'align' => 'right'],
                    ]">
                        @foreach ($customers as $customer)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-slate-900">
                                    {{ $customer->member_code }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-500">
                                    {{ $customer->unique_code }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-900">
                                    {{ $customer->name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $customer->phone ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">
                                    {{ $customer->joined_at->format('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-slate-900">
                                    Rp {{ number_format($customer->balance, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('customers.show', $customer) }}"
                                            class="text-green-700 hover:text-green-900">Detail</a>
                                        @can('update', $customer)
                                            <a href="{{ route('customers.edit', $customer) }}"
                                                class="text-green-700 hover:text-green-900">Edit</a>
                                        @endcan
                                        @can('delete', $customer)
                                            <form action="{{ route('customers.destroy', $customer) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus nasabah ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>

                    <div class="border-t border-slate-100 px-4 py-3">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
