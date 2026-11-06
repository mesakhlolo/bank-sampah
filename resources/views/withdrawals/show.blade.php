<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Detail Penarikan" description="Bukti transaksi penarikan saldo.">
            <x-slot name="actions">
                <x-button :href="route('withdrawals.download-pdf', $withdrawal)" size="sm">Unduh PDF</x-button>
                <x-button :href="route('withdrawals.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-flash />

            <x-card :padding="false">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <h1 class="text-base font-semibold text-slate-900">Bukti Penarikan</h1>
                        <p class="mt-0.5 text-sm text-slate-500">Bank Sampah Bersih Sejahtera</p>
                    </div>
                    <p class="font-mono text-sm text-slate-500">{{ $withdrawal->code }}</p>
                </div>

                <!-- Transaction Info -->
                <div class="grid grid-cols-1 gap-5 border-b border-slate-200 bg-slate-50 px-6 py-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Kode Penarikan</p>
                        <p class="mt-1 font-mono text-sm font-medium text-slate-900">{{ $withdrawal->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal Transaksi</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ $withdrawal->transaction_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Nasabah</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $withdrawal->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $withdrawal->customer->member_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Dibuat oleh</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $withdrawal->creator->name }}</p>
                        <p class="text-xs text-slate-500">{{ $withdrawal->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>

                <!-- Amount -->
                <div class="px-6 py-6">
                    <div class="flex items-end justify-between gap-4 rounded-lg border border-slate-200 p-5">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Nominal penarikan</p>
                            <p class="mt-1 text-xs text-slate-500">Saldo nasabah telah dikurangi sebesar nominal ini.</p>
                        </div>
                        <p class="text-right text-3xl font-semibold tracking-tight text-slate-900">
                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    @if ($withdrawal->note)
                        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Catatan</p>
                            <p class="mt-1 text-sm text-slate-800">{{ $withdrawal->note }}</p>
                        </div>
                    @endif
                </div>

                <!-- Signatures -->
                <div class="border-t border-slate-200 px-6 py-6">
                    <div class="flex items-start justify-between gap-6">
                        <div class="w-2/5 text-center">
                            <p class="text-sm text-slate-500">Nasabah</p>
                            <div class="mt-16 border-t border-slate-300 pt-2">
                                <p class="text-sm font-medium text-slate-900">{{ $withdrawal->customer->name }}</p>
                            </div>
                        </div>
                        <div class="w-2/5 text-center">
                            <p class="text-sm text-slate-500">Petugas</p>
                            <div class="mt-16 border-t border-slate-300 pt-2">
                                <p class="text-sm font-medium text-slate-900">{{ $withdrawal->creator->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3">
                <x-button :href="route('withdrawals.create')">Penarikan Baru</x-button>
                <x-button :href="route('withdrawals.index')" variant="secondary">Kembali ke Daftar</x-button>
            </div>
        </div>
    </div>
</x-app-layout>