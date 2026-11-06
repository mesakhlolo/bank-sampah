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
            <x-flash />

            <x-card>
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
                        <x-textarea name="description" id="description" class="mt-1">{{ old('description') }}</x-textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('description')" />
                    </div>

                    <!-- Is Active -->
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <x-checkbox name="is_active" value="1" :checked="old('is_active', true)" />
                            <span class="text-sm text-slate-700">Kategori Aktif</span>
                        </label>
                    </div>

                    <!-- Harga Awal -->
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <h3 class="mb-3 text-sm font-semibold text-slate-700">Harga Awal</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="price_per_kg" value="Harga per Kg (Rp) *" />
                                <x-text-input id="price_per_kg" name="price_per_kg" type="number"
                                    class="mt-1 block w-full" :value="old('price_per_kg')" step="0.01" min="0" required
                                    placeholder="Contoh: 3000" />
                                <x-input-error class="mt-1" :messages="$errors->get('price_per_kg')" />
                            </div>

                            <div>
                                <x-input-label for="effective_from" value="Berlaku Dari *" />
                                <x-text-input id="effective_from" name="effective_from" type="date"
                                    class="mt-1 block w-full" :value="old('effective_from', date('Y-m-d'))" required />
                                <x-input-error class="mt-1" :messages="$errors->get('effective_from')" />
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Harga ini akan menjadi harga awal untuk kategori sampah yang baru dibuat.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <x-button :href="route('waste-categories.index')" variant="secondary">Batal</x-button>
                        <x-button type="submit">Simpan</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
