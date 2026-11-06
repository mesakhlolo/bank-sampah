<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Penarikan Baru" description="Catat penarikan saldo nasabah.">
            <x-slot name="actions">
                <x-button :href="route('withdrawals.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <x-flash />

            <x-card>
                <form method="POST" action="{{ route('withdrawals.store') }}" x-data="withdrawalForm()" class="space-y-5">
                    @csrf

                    <!-- Customer Selection -->
                    <div>
                        <x-input-label for="customer_id" value="Pilih Nasabah *" />
                        <x-select name="customer_id" id="customer_id" x-model="customerId" @change="loadCustomerBalance()" required class="mt-1">
                            <option value="">-- Pilih Nasabah --</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer['id'] }}" data-balance="{{ $customer['balance'] }}"
                                    {{ old('customer_id') == $customer['id'] ? 'selected' : '' }}>
                                    {{ $customer['code'] }} - {{ $customer['name'] }} (Saldo: Rp
                                    {{ number_format($customer['balance'], 0, ',', '.') }})
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error class="mt-1" :messages="$errors->get('customer_id')" />
                    </div>

                    <!-- Current Balance Display -->
                    <div class="rounded-lg border border-green-200 bg-green-50 p-4" x-show="balance !== null" x-cloak>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700">Saldo Tersedia</span>
                            <span class="text-2xl font-semibold tracking-tight text-green-700" x-text="formatCurrency(balance)"></span>
                        </div>
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <x-input-label for="transaction_date" value="Tanggal Penarikan *" />
                        <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full"
                            :value="old('transaction_date', date('Y-m-d'))" required />
                        <x-input-error class="mt-1" :messages="$errors->get('transaction_date')" />
                    </div>

                    <!-- Amount -->
                    <div>
                        <x-input-label for="amount" value="Nominal Penarikan *" />
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">Rp</span>
                            <input type="number" name="amount" id="amount" x-model.number="amount"
                                min="1" step="1" value="{{ old('amount') }}"
                                placeholder="Masukkan nominal" required
                                class="block w-full rounded-lg border-slate-300 pl-12 shadow-xs focus:border-green-500 focus:ring-green-500">
                        </div>
                        <x-input-error class="mt-1" :messages="$errors->get('amount')" />

                        <!-- Warning if amount exceeds balance -->
                        <template x-if="amount > 0 && balance !== null && amount > balance">
                            <p class="mt-1 text-sm text-red-600">Nominal melebihi saldo tersedia.</p>
                        </template>
                    </div>

                    <!-- Note -->
                    <div>
                        <x-input-label for="note" value="Catatan (Opsional)" />
                        <x-textarea name="note" id="note" class="mt-1" placeholder="Tambahkan catatan jika diperlukan">{{ old('note') }}</x-textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('note')" />
                    </div>

                    @if ($errors->has('error'))
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <x-button :href="route('withdrawals.index')" variant="secondary">Batal</x-button>
                        <x-button type="submit" variant="danger">Simpan Penarikan</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function withdrawalForm() {
            return {
                customerId: '{{ old('customer_id') }}',
                balance: null,
                amount: {{ old('amount', 0) }},

                loadCustomerBalance() {
                    if (!this.customerId) {
                        this.balance = null;
                        return;
                    }

                    const select = document.getElementById('customer_id');
                    const selectedOption = select.options[select.selectedIndex];
                    this.balance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;
                },

                formatCurrency(value) {
                    if (value === null) return 'Rp 0';
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                },

                init() {
                    if (this.customerId) {
                        this.loadCustomerBalance();
                    }
                }
            }
        }

        // Initialize TomSelect for customer dropdown
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#customer_id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Pilih atau ketik untuk mencari nasabah...',
                maxOptions: null,
                onChange: function(value) {
                    // Trigger Alpine's loadCustomerBalance when selection changes
                    const event = new Event('change', { bubbles: true });
                    document.getElementById('customer_id').dispatchEvent(event);
                }
            });
        });
    </script>
</x-app-layout>
