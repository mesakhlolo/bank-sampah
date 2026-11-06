<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Tambah Kategori Sampah" description="Buat kategori beserta harga awalnya.">
            <x-slot name="actions">
                <x-button :href="route('waste-categories.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs">
                <form method="POST" action="{{ route('waste-categories.store') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" value="Nama Kategori *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name')" required autofocus />
                        <x-input-error class="mt-1" :messages="$errors->get('name')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-green-500 focus:ring-green-500">{{ old('description') }}</textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('description')" />
                    </div>

                    <!-- Is Active -->
                    <div>
                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded-sm border-gray-300 text-green-600 shadow-xs focus:border-green-500 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Kategori Aktif</span>
                        </label>
                    </div>

                    <!-- Harga Awal -->
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h3 class="mb-3 text-sm font-semibold text-gray-700">Harga Awal</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="price_per_kg" value="Harga per Kg (Rp) *" />
                                <input type="number" name="price_per_kg" id="price_per_kg"
                                    value="{{ old('price_per_kg') }}" step="0.01" min="0" required
                                    placeholder="Contoh: 3000"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-green-500 focus:ring-green-500">
                                <x-input-error class="mt-1" :messages="$errors->get('price_per_kg')" />
                            </div>

                            <div>
                                <x-input-label for="effective_from" value="Berlaku Dari *" />
                                <input type="date" name="effective_from" id="effective_from"
                                    value="{{ old('effective_from', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-green-500 focus:ring-green-500">
                                <x-input-error class="mt-1" :messages="$errors->get('effective_from')" />
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            Harga ini akan menjadi harga awal untuk kategori sampah yang baru dibuat.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <x-button :href="route('waste-categories.index')" variant="secondary">Batal</x-button>
                        <x-button type="submit">Simpan</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
