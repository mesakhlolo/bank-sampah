<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Setoran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('deposits.store') }}" x-data="depositForm()">
                        @csrf

                        <!-- Customer Selection -->
                        <div class="mb-4">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">Nasabah *</label>
                            <select name="customer_id" id="customer_id" required x-model="customerId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Nasabah</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Transaction Date -->
                        <div class="mb-4">
                            <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal
                                Transaksi</label>
                            <input type="date" name="transaction_date" id="transaction_date"
                                value="{{ old('transaction_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('transaction_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deposit Items -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700">Item Sampah *</label>
                                <button type="button" @click="addItem"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    + Tambah Item
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex gap-3 items-start border border-gray-200 p-3 rounded">
                                        <!-- Waste Category -->
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                                            <select :name="'items[' + index + '][waste_category_id]'"
                                                x-model="item.waste_category_id" @change="updatePrice(index)" required
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($wasteCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        data-price="{{ $category->current_price ?? 0 }}">
                                                        {{ $category->name }} (Rp
                                                        {{ number_format($category->current_price ?? 0, 0, ',', '.') }}/kg)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Weight -->
                                        <div class="w-32">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Berat
                                                (kg)</label>
                                            <input type="number" :name="'items[' + index + '][weight_kg]'"
                                                x-model="item.weight_kg" @input="calculateSubtotal(index)"
                                                step="0.01" min="0.01" required placeholder="0.00"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>

                                        <!-- Subtotal (readonly) -->
                                        <div class="w-40">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Subtotal</label>
                                            <input type="text" :value="formatCurrency(item.subtotal)" readonly
                                                class="block w-full rounded-md border-gray-200 bg-gray-50 text-sm text-right font-medium">
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="pt-6">
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                class="text-red-600 hover:text-red-900 font-bold">
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            @error('items')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Amount Display -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 p-4 rounded">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-700">Total Setoran:</span>
                                <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(total)"></span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('deposits.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function depositForm() {
            return {
                customerId: '{{ old('customer_id') }}',
                items: [{
                    waste_category_id: '',
                    weight_kg: '',
                    price_per_kg: 0,
                    subtotal: 0
                }],

                get total() {
                    return this.items.reduce((sum, item) => sum + item.subtotal, 0);
                },

                addItem() {
                    this.items.push({
                        waste_category_id: '',
                        weight_kg: '',
                        price_per_kg: 0,
                        subtotal: 0
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updatePrice(index) {
                    const select = document.querySelector(`select[name="items[${index}][waste_category_id]"]`);
                    const selectedOption = select.options[select.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price || 0);

                    this.items[index].price_per_kg = price;
                    this.calculateSubtotal(index);
                },

                calculateSubtotal(index) {
                    const weight = parseFloat(this.items[index].weight_kg) || 0;
                    const price = this.items[index].price_per_kg || 0;
                    this.items[index].subtotal = weight * price;
                },

                formatCurrency(amount) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
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
                maxOptions: null
            });
        });
    </script>
</x-app-layout>
