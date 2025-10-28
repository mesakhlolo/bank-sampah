<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kategori Sampah & Harga') }}
            </h2>
            @can('create', App\Models\WasteCategory::class)
                <a href="{{ route('waste-categories.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Tambah Kategori
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-6">
                        @foreach ($categories as $category)
                            <div class="border rounded-lg p-4 {{ $category->is_active ? 'bg-white' : 'bg-gray-50' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                                            @if ($category->is_active)
                                                <span
                                                    class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Aktif</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Nonaktif</span>
                                            @endif
                                        </div>
                                        @if ($category->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @can('update', $category)
                                            <a href="{{ route('waste-categories.edit', $category) }}"
                                                class="text-blue-600 hover:text-blue-900 font-medium text-sm whitespace-nowrap">
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $category)
                                            <form action="{{ route('waste-categories.destroy', $category) }}" method="POST"
                                                class="inline m-0"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 font-medium text-sm whitespace-nowrap">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>

                                <!-- Price History -->
                                <div class="mt-3 pt-3 border-t">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Riwayat Harga</h4>

                                    <!-- Price List - Only Active & Future -->
                                    @if ($category->prices->count() > 0)
                                        @php
                                            $activePrice = $category->getActivePriceOn(now());
                                            // Filter: hanya tampilkan harga aktif dan masa depan
                                            $relevantPrices = $category->prices->filter(function ($price) use (
                                                $activePrice,
                                            ) {
                                                $isActive = $activePrice && $activePrice->id === $price->id;
                                                $isFuture = $price->effective_from->isFuture();
                                                return $isActive || $isFuture;
                                            });
                                        @endphp
                                        @if ($relevantPrices->count() > 0)
                                            <div class="space-y-1">
                                                @foreach ($relevantPrices as $price)
                                                    @php
                                                        $isActive = $activePrice && $activePrice->id === $price->id;
                                                        $isFuture = $price->effective_from->isFuture();
                                                    @endphp
                                                    <div
                                                        class="flex justify-between items-center text-sm gap-2 p-2 rounded
                                                        {{ $isActive ? 'bg-green-50 font-semibold text-green-800' : 'bg-blue-50 text-blue-700' }}">
                                                        <span>Rp
                                                            {{ number_format($price->price_per_kg, 0, ',', '.') }}/kg</span>
                                                        <div class="flex items-center gap-2">
                                                            <span
                                                                class="text-xs">{{ $price->effective_from->format('d M Y') }}</span>
                                                            @if ($isActive)
                                                                <span
                                                                    class="text-xs bg-green-500 text-white px-2 py-0.5 rounded font-normal">
                                                                    ✓ Aktif Sekarang
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="text-xs bg-blue-500 text-white px-2 py-0.5 rounded font-normal">
                                                                    Akan Datang
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">Belum ada harga yang diatur</p>
                                        @endif
                                    @else
                                        <p class="text-sm text-gray-500 italic">Belum ada harga yang diatur</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if ($categories->count() === 0)
                            <div class="text-center py-8 text-gray-500">
                                Belum ada kategori sampah. Silakan tambahkan kategori baru.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
