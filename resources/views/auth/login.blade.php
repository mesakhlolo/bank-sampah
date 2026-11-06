<x-guest-layout>
    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Masuk ke Akun Anda</h1>
    <p class="mt-1 text-sm text-slate-500">Gunakan email dan kata sandi yang terdaftar.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember_me" class="inline-flex items-center gap-2">
            <x-checkbox id="remember_me" name="remember" />
            <span class="text-sm text-slate-600">{{ __('Ingat saya') }}</span>
        </label>

        <div class="flex items-center justify-between gap-3">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-green-700 hover:text-green-800" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
