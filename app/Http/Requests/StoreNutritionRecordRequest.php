<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNutritionRecordRequest extends FormRequest
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
            'child_id' => 'required|string|exists:children,id',
            'user_id' => 'required|exists:users,id',
            'height_cm' => 'required|numeric|min:0',
            'weight_kg' => 'required|numeric|min:0',
            'nutrition_status' => 'required|string',
        ];
    }
}
