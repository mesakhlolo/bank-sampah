@extends('layouts.portal')

@section('title', 'Cek Saldo')

@section('content')
<div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="mb-6 text-center">
            <div class="mb-3 inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                <x-application-logo class="h-10 w-10" />
            </div>
            <h1 class="mb-1 text-2xl font-bold text-gray-900">Portal Nasabah</h1>
            <p class="text-sm text-gray-600">Bank Sampah Bersih Sejahtera</p>
        </div>

        <!-- Check Code Card -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
            <div class="bg-linear-to-r from-green-500 to-green-600 px-6 py-5">
                <p class="text-sm text-green-100">Masukkan kode unik Anda untuk melihat saldo</p>
            </div>

            <div class="px-6 py-6">
                <!-- Error Messages -->
                @if($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700" role="alert">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">
                                {{ $errors->first('unique_code') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('customer-portal.verify-code') }}">
                    @csrf

                    <!-- Unique Code Input -->
                    <div class="mb-5">
                        <label for="unique_code" class="mb-2 block text-sm font-medium text-gray-700">
                            Kode Unik Anda
                        </label>
                        <input type="text"
                               name="unique_code"
                               id="unique_code"
                               value="{{ old('unique_code') }}"
                               placeholder="BSB-X7K9M2P4N8Q1"
                               maxlength="16"
                               class="block w-full rounded-lg border-2 border-gray-300 px-4 py-2.5 text-center font-mono text-base uppercase tracking-wide transition focus:border-green-500 focus:ring-2 focus:ring-green-500 @error('unique_code') border-red-400! @enderror"
                               required
                               autofocus>
                        <p class="mt-1.5 text-center text-xs text-gray-500">
                            Format: BSB- diikuti 12 karakter
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="flex w-full items-center justify-center rounded-lg border border-transparent bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-green-700 hover:shadow-md focus:outline-hidden focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lihat Saldo Saya
                    </button>
                </form>

                <!-- Help Text -->
                <div class="mt-5 border-t border-gray-200 pt-5">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <svg class="mt-0.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-2.5">
                            <h3 class="text-xs font-semibold text-gray-900">Lupa kode unik Anda?</h3>
                            <p class="mt-1 text-xs text-gray-600">
                                Hubungi operator Bank Sampah atau cek kartu member Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Security Info Box -->
                <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <svg class="mt-0.5 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="ml-2.5">
                            <h3 class="text-xs font-semibold text-gray-900">Keamanan</h3>
                            <p class="mt-0.5 text-xs text-gray-600">
                                Jangan bagikan kode unik kepada siapapun. Kode ini bersifat pribadi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home Link -->
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-600 transition hover:text-green-600">
                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
