<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'unit' => ['required', 'string', 'max:255', Rule::unique('units', 'unit')->ignore($id)],
        ];
    }

    public function messages(): array
    {
        return [
            'unit.required' => 'A mértékegység megnevezése kötelező.',
            'unit.string' => 'A mértékegység megnevezése csak szöveg lehet.',
            'unit.max' => 'A mértékegység megnevezése legfeljebb 255 karakter hosszú lehet.',
            'unit.unique' => 'Ez a mértékegység már létezik.',
        ];
    }
}