@extends('layouts.portal')

@section('title', 'Cek Saldo')

@section('content')
<div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <x-application-logo class="mx-auto h-14 w-14 text-green-600" />
            <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">Portal Nasabah</h1>
            <p class="mt-1 text-sm text-slate-500">Bank Sampah Bersih Sejahtera</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-base font-semibold text-slate-900">Cek saldo Anda</h2>
            <p class="mt-1 text-sm text-slate-500">Masukkan kode unik dari kartu member Anda.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first('unique_code') }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer-portal.verify-code') }}" class="mt-5 space-y-5">
                @csrf

                <div>
                    <label for="unique_code" class="block text-sm font-medium text-slate-700">Kode Unik</label>
                    <input type="text"
                           name="unique_code"
                           id="unique_code"
                           value="{{ old('unique_code') }}"
                           placeholder="BSB-X7K9M2P4N8Q1"
                           maxlength="16"
                           required
                           autofocus
                           class="mt-1 block w-full rounded-lg border-slate-300 text-center font-mono uppercase tracking-wide shadow-xs focus:border-green-500 focus:ring-green-500">
                    <p class="mt-1.5 text-center text-xs text-slate-500">Format: BSB- diikuti 12 karakter</p>
                </div>

                <x-button type="submit" class="w-full">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Lihat Saldo Saya
                </x-button>
            </form>

            <div class="mt-6 flex items-start gap-2 border-t border-slate-100 pt-5 text-xs text-slate-500">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <p>Jangan bagikan kode unik kepada siapapun. Kode ini bersifat pribadi.</p>
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">
            Lupa kode unik? Hubungi operator atau cek kartu member Anda.
        </p>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-green-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
