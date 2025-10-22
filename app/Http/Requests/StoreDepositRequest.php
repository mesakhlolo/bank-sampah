<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'transaction_date' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.waste_category_id' => ['required', 'exists:waste_categories,id'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0.01', 'max:10000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'nasabah',
            'transaction_date' => 'tanggal transaksi',
            'notes' => 'catatan',
            'items' => 'item sampah',
            'items.*.waste_category_id' => 'kategori sampah',
            'items.*.weight_kg' => 'berat (kg)',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Minimal harus ada 1 item sampah.',
            'items.min' => 'Minimal harus ada 1 item sampah.',
            'items.*.waste_category_id.required' => 'Kategori sampah harus dipilih.',
            'items.*.waste_category_id.exists' => 'Kategori sampah tidak valid.',
            'items.*.weight_kg.required' => 'Berat harus diisi.',
            'items.*.weight_kg.numeric' => 'Berat harus berupa angka.',
            'items.*.weight_kg.min' => 'Berat minimal 0.01 kg.',
            'items.*.weight_kg.max' => 'Berat maksimal 10000 kg.',
        ];
    }
}
