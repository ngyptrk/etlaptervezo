<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
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
            'unit' => ['required', 'string', 'max:255', 'unique:units,unit'],
        ];
    }

    public function messages(): array
    {
        return [
            'unit.required' => 'A mértékegység megadása kötelező.',
            'unit.string'   => 'A mértékegység csak szöveg lehet.',
            'unit.max'      => 'A mértékegység legfeljebb 255 karakter lehet.',
            'unit.unique'   => 'Ez a mértékegység már létezik.',
        ];
    }
}
