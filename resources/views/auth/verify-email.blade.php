<x-guest-layout>
    <h1 class="mb-2 text-lg font-semibold text-gray-900">Verifikasi Email</h1>

    <div class="mb-4 text-sm text-gray-600">
        Terima kasih telah mendaftar. Sebelum memulai, verifikasi alamat email Anda dengan mengklik tautan yang baru kami kirimkan. Jika Anda tidak menerima email tersebut, kami akan mengirimkannya kembali.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-green-600">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-hidden focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
