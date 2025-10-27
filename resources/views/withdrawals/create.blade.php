<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penarikan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('withdrawals.store') }}" x-data="withdrawalForm()">
                        @csrf

                        <!-- Customer Selection -->
                        <div class="mb-6">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Nasabah <span class="text-red-500">*</span>
                            </label>
                            <select name="customer_id" id="customer_id" x-model="customerId"
                                @change="loadCustomerBalance()"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('customer_id') border-red-500 @enderror"
                                required>
                                <option value="">-- Pilih Nasabah --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer['id'] }}" data-balance="{{ $customer['balance'] }}"
                                        {{ old('customer_id') == $customer['id'] ? 'selected' : '' }}>
                                        {{ $customer['code'] }} - {{ $customer['name'] }} (Saldo: Rp
                                        {{ number_format($customer['balance'], 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Balance Display -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg" x-show="balance !== null">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Saldo Tersedia:</span>
                                <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(balance)"></span>
                            </div>
                        </div>

                        <!-- Transaction Date -->
                        <div class="mb-6">
                            <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Penarikan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="transaction_date" id="transaction_date"
                                value="{{ old('transaction_date', date('Y-m-d')) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('transaction_date') border-red-500 @enderror"
                                required>
                            @error('transaction_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Nominal Penarikan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    Rp
                                </span>
                                <input type="number" name="amount" id="amount" x-model.number="amount"
                                    min="1" step="1" value="{{ old('amount') }}"
                                    class="block w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                                    placeholder="Masukkan nominal" required>
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Warning if amount exceeds balance -->
                            <template x-if="amount > 0 && balance !== null && amount > balance">
                                <p class="mt-1 text-sm text-red-600">
                                    ⚠️ Nominal melebihi saldo tersedia!
                                </p>
                            </template>
                        </div>

                        <!-- Note -->
                        <div class="mb-6">
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea name="note" id="note" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('note') border-red-500 @enderror"
                                placeholder="Tambahkan catatan jika diperlukan">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- General Error -->
                        @if ($errors->has('error'))
                            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                {{ $errors->first('error') }}
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Penarikan
                            </button>
                            <a href="{{ route('withdrawals.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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
            const tomSelect = new TomSelect('#customer_id', {
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
