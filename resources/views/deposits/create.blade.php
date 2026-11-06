<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Transaksi Setoran Baru" description="Catat setoran sampah dari nasabah.">
            <x-slot name="actions">
                <x-button :href="route('deposits.index')" variant="secondary" size="sm">Kembali</x-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <x-flash />

            <x-card>
                <form method="POST" action="{{ route('deposits.store') }}" x-data="depositForm()" class="space-y-5">
                    @csrf

                    <!-- Customer Selection -->
                    <div>
                        <x-input-label for="customer_id" value="Nasabah *" />
                        <x-select name="customer_id" id="customer_id" required x-model="customerId" class="mt-1">
                            <option value="">Pilih Nasabah</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->member_code }} - {{ $customer->name }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error class="mt-1" :messages="$errors->get('customer_id')" />
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <x-input-label for="transaction_date" value="Tanggal Transaksi" />
                        <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full"
                            :value="old('transaction_date', date('Y-m-d'))" max="{{ date('Y-m-d') }}" />
                        <x-input-error class="mt-1" :messages="$errors->get('transaction_date')" />
                    </div>

                    <!-- Deposit Items -->
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <x-input-label value="Item Sampah *" />
                            <x-button type="button" variant="secondary" size="sm" x-on:click="addItem">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah Item
                            </x-button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex flex-col gap-3 rounded-lg border border-slate-200 p-3 md:flex-row md:items-start">
                                    <!-- Waste Category -->
                                    <div class="flex-1">
                                        <label class="mb-1 block text-xs font-medium text-slate-600">Kategori</label>
                                        <select :name="'items[' + index + '][waste_category_id]'"
                                            x-model="item.waste_category_id" @change="updatePrice(index)" required
                                            class="block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-green-500 focus:ring-green-500">
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
                                    <div class="md:w-32">
                                        <label class="mb-1 block text-xs font-medium text-slate-600">Berat (kg)</label>
                                        <input type="number" :name="'items[' + index + '][weight_kg]'"
                                            x-model="item.weight_kg" @input="calculateSubtotal(index)"
                                            step="0.01" min="0.01" required placeholder="0.00"
                                            class="block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-green-500 focus:ring-green-500">
                                    </div>

                                    <!-- Subtotal (readonly) -->
                                    <div class="md:w-40">
                                        <label class="mb-1 block text-xs font-medium text-slate-600">Subtotal</label>
                                        <input type="text" :value="formatCurrency(item.subtotal)" readonly
                                            class="block w-full rounded-lg border-slate-200 bg-slate-50 text-right text-sm font-medium">
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="md:pt-6">
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                            class="text-sm font-medium text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <x-input-error class="mt-1" :messages="$errors->get('items')" />
                    </div>

                    <!-- Total Amount Display -->
                    <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700">Total Setoran</span>
                            <span class="text-2xl font-semibold tracking-tight text-green-700" x-text="formatCurrency(total)"></span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" value="Catatan" />
                        <x-textarea name="notes" id="notes" class="mt-1">{{ old('notes') }}</x-textarea>
                        <x-input-error class="mt-1" :messages="$errors->get('notes')" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <x-button :href="route('deposits.index')" variant="secondary">Batal</x-button>
                        <x-button type="submit">Simpan Transaksi</x-button>
                    </div>
                </form>
            </x-card>
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
