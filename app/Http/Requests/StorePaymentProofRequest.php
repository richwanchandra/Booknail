<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:bank_transfer,qris,ewallet,cash',
            'transaction_ref' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'proof_file.mimes' => 'Bukti transfer hanya boleh berformat JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file maksimal 2MB.',
            'amount.required' => 'Jumlah pembayaran harus diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
        ];
    }
}
