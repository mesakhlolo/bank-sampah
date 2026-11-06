<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Kategori Sampah & Harga" description="Atur kategori dan harga sampah yang berlaku.">
            <x-slot name="actions">
                @can('create', App\Models\WasteCategory::class)
                    <x-button :href="route('waste-categories.create')" size="sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Kategori
                    </x-button>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash />

            <div class="rounded-xl border border-gray-200 bg-white shadow-xs">
                @if ($categories->isEmpty())
                    <x-empty-state
                        title="Belum ada kategori sampah"
                        description="Tambahkan kategori dan harga awal untuk mulai mencatat setoran."
                    >
                        <x-slot name="action">
                            @can('create', App\Models\WasteCategory::class)
                                <x-button :href="route('waste-categories.create')" size="sm">Tambah Kategori</x-button>
                            @endcan
                        </x-slot>
                    </x-empty-state>
                @else
                    <x-table :headers="[
                        'Kategori',
                        'Deskripsi',
                        ['label' => 'Harga Aktif', 'align' => 'right'],
                        'Berlaku Sejak',
                        ['label' => 'Status', 'align' => 'center'],
                        ['label' => 'Aksi', 'align' => 'center'],
                    ]">
                        @foreach ($categories as $category)
                            @php
                                // Prices are eager-loaded newest-first, so the first one that has
                                // already taken effect is the active price. Avoids a query per row.
                                $activePrice = $category->prices->first(
                                    fn ($price) => $price->effective_from->lessThanOrEqualTo(now())
                                );
                                $futurePrices = $category->prices->filter(
                                    fn ($price) => $price->effective_from->isFuture()
                                );
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $category->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $category->description ?: '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                    @if ($activePrice)
                                        Rp {{ number_format($activePrice->price_per_kg, 0, ',', '.') }}
                                        <span class="text-xs font-normal text-gray-500">/kg</span>
                                    @else
                                        <span class="font-normal text-gray-400">Belum ada harga</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                    {{ $activePrice ? $activePrice->effective_from->format('d M Y') : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    @if ($category->is_active)
                                        <x-badge variant="green">Aktif</x-badge>
                                    @else
                                        <x-badge variant="red">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium">
                                    @can('update', $category)
                                        <a href="{{ route('waste-categories.edit', $category) }}"
                                            class="text-green-700 hover:text-green-900">Edit</a>
                                    @endcan
                                    @can('delete', $category)
                                        <form action="{{ route('waste-categories.destroy', $category) }}"
                                            method="POST" class="ml-3 inline"
                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>

                            @if ($futurePrices->isNotEmpty())
                                <tr class="bg-green-50">
                                    <td colspan="6" class="px-4 py-3">
                                        <p class="mb-2 text-xs font-semibold text-green-800">Harga yang akan datang</p>
                                        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                                            @foreach ($futurePrices as $price)
                                                <div class="rounded-lg border border-green-200 bg-white px-3 py-2">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        Rp {{ number_format($price->price_per_kg, 0, ',', '.') }}
                                                        <span class="text-xs text-gray-500">/kg</span>
                                                    </p>
                                                    <p class="text-xs text-gray-600">
                                                        Mulai {{ $price->effective_from->format('d M Y') }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </x-table>

                    <div class="border-t border-gray-100 px-4 py-3">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
