<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreWithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'amount' => ['required', 'numeric', 'min:1', function ($attribute, $value, $fail) {
                $customer = Customer::find($this->customer_id);
                if ($customer && $customer->balance < $value) {
                    $fail('Saldo nasabah tidak mencukupi. Saldo tersedia: Rp '.number_format($customer->balance, 0, ',', '.'));
                }
            }],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
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
            'customer_id.required' => 'Nasabah harus dipilih.',
            'customer_id.exists' => 'Nasabah tidak ditemukan.',
            'amount.required' => 'Nominal penarikan harus diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal penarikan minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi harus diisi.',
            'transaction_date.date' => 'Format tanggal tidak valid.',
            'note.max' => 'Catatan maksimal 500 karakter.',
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
            'amount' => 'nominal',
            'transaction_date' => 'tanggal transaksi',
            'note' => 'catatan',
        ];
    }
}
