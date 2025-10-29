<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kategori Sampah & Harga') }}
            </h2>
            @can('create', App\Models\WasteCategory::class)
                <a href="{{ route('waste-categories.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah Kategori
                </a>
            @endcan
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($categories->isEmpty())
                        <p class="text-center text-gray-500 py-8">Belum ada kategori sampah. Silakan tambahkan kategori
                            baru.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Deskripsi
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Harga Aktif
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Berlaku Sejak
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($categories as $category)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-600">
                                                    {{ $category->description ?: '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $activePrice = $category->getActivePriceOn(now());
                                                @endphp
                                                @if ($activePrice)
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        Rp {{ number_format($activePrice->price_per_kg, 0, ',', '.') }}
                                                        <span class="text-xs text-gray-500 font-normal">/kg</span>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400">Belum ada harga</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($activePrice)
                                                    <div class="text-sm text-gray-500">
                                                        {{ $activePrice->effective_from->format('d M Y') }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($category->is_active)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                @can('update', $category)
                                                    <a href="{{ route('waste-categories.edit', $category) }}"
                                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('delete', $category)
                                                    <form action="{{ route('waste-categories.destroy', $category) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>

                                        <!-- Price History Row (Collapsible) -->
                                        @php
                                            $futurePrices = $category->prices->filter(function ($price) {
                                                return $price->effective_from->isFuture();
                                            });
                                        @endphp
                                        @if ($futurePrices->count() > 0)
                                            <tr class="bg-blue-50">
                                                <td colspan="6" class="px-6 py-3">
                                                    <div class="text-xs font-semibold text-blue-700 mb-2">
                                                        🔔 Harga Yang Akan Datang:
                                                    </div>
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                        @foreach ($futurePrices as $price)
                                                            <div
                                                                class="bg-white border border-blue-200 rounded px-3 py-2">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    Rp
                                                                    {{ number_format($price->price_per_kg, 0, ',', '.') }}
                                                                    <span class="text-xs text-gray-500">/kg</span>
                                                                </div>
                                                                <div class="text-xs text-gray-600">
                                                                    Mulai:
                                                                    {{ $price->effective_from->format('d M Y') }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
