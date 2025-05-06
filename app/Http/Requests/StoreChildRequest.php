<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildRequest extends FormRequest
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
            'kecamatan_id' => 'required|string|exists:kecamatan,id',
            'name' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
        ];
    }
}
