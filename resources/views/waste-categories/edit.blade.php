<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kategori Sampah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('waste-categories.update', $wasteCategory) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori
                                *</label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $wasteCategory->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $wasteCategory->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Active -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $wasteCategory->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Kategori Aktif</span>
                            </label>
                        </div>

                        <!-- Current Price Info -->
                        @php
                            $activePrice = $wasteCategory->getActivePriceOn(now());
                        @endphp
                        @if ($activePrice)
                            <div class="mb-4 p-3 bg-gray-50 rounded border border-gray-200">
                                <div class="text-sm text-gray-600">
                                    Harga saat ini:
                                    <span class="font-semibold text-gray-900">
                                        Rp {{ number_format($activePrice->price_per_kg, 0, ',', '.') }}/kg
                                    </span>
                                    <span class="text-gray-500">
                                        (sejak {{ $activePrice->effective_from->format('d M Y') }})
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-6"></div>

                        <!-- Update Price Section -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Update Harga (Opsional)</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="price_per_kg" class="block text-sm font-medium text-gray-700">Harga
                                        Baru (Rp/kg)</label>
                                    <input type="number" name="price_per_kg" id="price_per_kg"
                                        value="{{ old('price_per_kg') }}" step="0.01" min="0"
                                        placeholder="Contoh: 3000"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('price_per_kg')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="effective_from" class="block text-sm font-medium text-gray-700">Berlaku
                                        Dari</label>
                                    <input type="date" name="effective_from" id="effective_from"
                                        value="{{ old('effective_from', date('Y-m-d')) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('effective_from')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 mt-2">
                                Kosongkan jika tidak ingin mengubah harga
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('waste-categories.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
