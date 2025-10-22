<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWastePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\WastePrice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'waste_category_id' => 'required|exists:waste_categories,id',
            'price_per_kg' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'waste_category_id' => 'kategori sampah',
            'price_per_kg' => 'harga per kg',
            'effective_from' => 'berlaku dari',
        ];
    }
}
