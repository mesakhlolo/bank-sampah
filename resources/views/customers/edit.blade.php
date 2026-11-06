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
            <x-flash />

            <x-card>
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
                        <x-textarea name="address" id="address" class="mt-1">{{ old('address', $customer->address) }}</x-textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('address')" />
                    </div>

                    <!-- Joined At -->
                    <div>
                        <x-input-label for="joined_at" value="Tanggal Bergabung *" />
                        <x-text-input id="joined_at" name="joined_at" type="date" class="mt-1 block w-full"
                            :value="old('joined_at', $customer->joined_at->format('Y-m-d'))" required />
                        <x-input-error class="mt-1" :messages="$errors->get('joined_at')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <x-button :href="route('customers.index')" variant="secondary">Batal</x-button>
                        <x-button type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
