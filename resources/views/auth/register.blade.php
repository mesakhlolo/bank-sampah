<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Tambah Pengguna" description="Buat akun admin atau operator baru.">
            <x-slot name="actions">
                <x-button :href="route('dashboard')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <x-flash />

            <x-card>
                <p class="mb-6 text-sm text-slate-600">
                    Hanya admin yang dapat membuat akun baru. Pilih peran pengguna secara eksplisit.
                </p>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama')" />
                        <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" :value="__('Peran')" />
                        <x-select id="role" name="role" class="mt-1" required>
                            <option value="">Pilih peran</option>
                            <option value="operator" @selected(old('role') === 'operator')>Operator</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Active Status -->
                    <div>
                        <input type="hidden" name="is_active" value="0">
                        <label for="is_active" class="inline-flex items-center gap-2">
                            <x-checkbox id="is_active" name="is_active" value="1" :checked="old('is_active', true)" />
                            <span class="text-sm text-slate-600">Akun aktif (dapat masuk)</span>
                        </label>
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Kata Sandi')" />
                        <x-text-input id="password" class="mt-1 block w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                        <x-text-input id="password_confirmation" class="mt-1 block w-full"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <x-button :href="route('dashboard')" variant="secondary">Batal</x-button>
                        <x-primary-button>
                            {{ __('Buat Pengguna') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
