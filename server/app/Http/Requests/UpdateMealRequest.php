<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMealRequest extends FormRequest
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
            'meal' => ['required', 'string', 'max:255', 'unique:meals,meal'],

        ];
    }
    public function messages(): array
    {
        return [
            'meal.required' => 'Az étel megnevezése kötelező.',
            'meal.string' => 'Az étel megnevezése csak szöveg lehet.',
            'meal.max' => 'Az étel megnevezése legfeljebb 255 karakter hosszú lehet.',
            'meal.unique' => 'Ez az étel már létezik.',
        ];
    }
}
