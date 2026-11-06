<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Nasabah" description="Perbarui data anggota Bank Sampah.">
            <x-slot name="actions">
                <x-button :href="route('customers.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs">
                <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Member Code -->
                    <div>
                        <x-input-label for="member_code" value="Kode Anggota *" />
                        <x-text-input id="member_code" name="member_code" type="text" class="mt-1 block w-full"
                            :value="old('member_code', $customer->member_code)" required />
                        <x-input-error class="mt-1" :messages="$errors->get('member_code')" />
                    </div>

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $customer->name)" required />
                        <x-input-error class="mt-1" :messages="$errors->get('name')" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" value="No. Telepon" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                            :value="old('phone', $customer->phone)" />
                        <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Address -->
                    <div>
                        <x-input-label for="address" value="Alamat" />
                        <textarea name="address" id="address" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-green-500 focus:ring-green-500">{{ old('address', $customer->address) }}</textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('address')" />
                    </div>

                    <!-- Joined At -->
                    <div>
                        <x-input-label for="joined_at" value="Tanggal Bergabung *" />
                        <input type="date" name="joined_at" id="joined_at"
                            value="{{ old('joined_at', $customer->joined_at->format('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-green-500 focus:ring-green-500">
                        <x-input-error class="mt-1" :messages="$errors->get('joined_at')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <x-button :href="route('customers.index')" variant="secondary">Batal</x-button>
                        <x-button type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
