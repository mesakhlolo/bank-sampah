<?php

namespace App\Http\Requests;

use App\Models\WasteCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWasteCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('waste_category'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('waste_category');
        $categoryId = $category instanceof WasteCategory ? $category->getKey() : $category;

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'price_per_kg' => 'nullable|numeric|min:0',
            'effective_from' => ['nullable', 'date', 'required_with:price_per_kg'],
        ];

        if ($this->filled('price_per_kg')) {
            $rules['effective_from'][] = Rule::unique('waste_prices', 'effective_from')
                ->where(fn ($query) => $query->where('waste_category_id', $categoryId));
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama kategori',
            'description' => 'deskripsi',
            'is_active' => 'status aktif',
            'price_per_kg' => 'harga per kg',
            'effective_from' => 'berlaku dari',
        ];
    }
}
