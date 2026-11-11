<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'design_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'design_notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'design_photo.mimes' => 'File desain hanya boleh berformat JPG, PNG, atau PDF.',
            'design_photo.max' => 'Ukuran file maksimal 2MB.',
            'design_notes.max' => 'Catatan desain maksimal 2000 karakter.',
        ];
    }
}
