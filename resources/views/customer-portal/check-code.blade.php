@extends('layouts.portal')

@section('title', 'Cek Saldo')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-16rem)] px-4 py-8">
    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-3">
                <span class="text-3xl">♻️</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Portal Nasabah</h1>
            <p class="text-sm text-gray-600">Bank Sampah Bersih Sejahtera</p>
        </div>

        <!-- Check Code Card -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-5">
                <p class="text-sm text-green-100 mb-1">Masukkan kode unik Anda untuk melihat saldo</p>
            </div>

            <div class="px-6 py-6">
                <!-- Error Messages -->
                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <label for="unique_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Unik Anda
                        </label>
                        <input type="text" 
                               name="unique_code" 
                               id="unique_code" 
                               value="{{ old('unique_code') }}"
                               placeholder="BSB-X7K9M2P4N8Q1" 
                               maxlength="16"
                               class="block w-full px-4 py-2.5 text-center text-base font-mono uppercase tracking-wide border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('unique_code') !border-red-400 @enderror"
                               required
                               autofocus>
                        <p class="mt-1.5 text-xs text-gray-500 text-center">
                            Format: BSB- diikuti 12 karakter
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lihat Saldo Saya
                    </button>
                </form>

                <!-- Help Text -->
                <div class="mt-5 pt-5 border-t border-gray-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="ml-2.5">
                            <h3 class="text-xs font-semibold text-blue-800">Keamanan</h3>
                            <p class="mt-0.5 text-xs text-blue-700">
                                Jangan bagikan kode unik kepada siapapun. Kode ini bersifat pribadi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home Link -->
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-green-600 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
