<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
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
            'meal.required' => 'Az étel megadása kötelező.',
            'meal.string'   => 'Az étel neve csak szöveg lehet.',
            'meal.max'      => 'Az étel neve legfeljebb 255 karakter lehet.',
            'meal.unique'   => 'Ez az étel már létezik.',
        ];
    }
}
