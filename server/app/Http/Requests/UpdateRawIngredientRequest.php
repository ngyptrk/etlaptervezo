<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRawIngredientRequest extends FormRequest
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
            'raw_ingredient' => ['required', 'string', 'max:255', 'unique:raw_ingredients,raw_ingredient'],

        ];
    }
    public function messages(): array
    {
        return [
            'raw_ingredient.required' => 'Az alapanyag megnevezése kötelező.',
            'raw_ingredient.string' => 'Az alapanyag megnevezése csak szöveg lehet.',
            'raw_ingredient.max' => 'Az alapanyag megnevezése legfeljebb 255 karakter hosszú lehet.',
            'raw_ingredient.unique' => 'Ez az alapanyag már létezik.',
        ];
    }
}
