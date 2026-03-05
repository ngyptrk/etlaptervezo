<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['nullable', 'string', 'max:125', Rule::unique('recipes', 'name')->ignore($id)],
            'description' => ['nullable', 'string'],
            'picture' => ['nullable', 'string', 'max:125', Rule::unique('recipes', 'picture')->ignore($id)],
            'person' => ['nullable', 'integer', 'min:1', 'max:255'],
            'meal_id' => ['nullable', 'integer', 'exists:meals,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'A név csak szöveg lehet.',
            'name.max' => 'A név legfeljebb 125 karakter hosszú lehet.',
            'name.unique' => 'Ez a receptnév már létezik.',

            'description.string' => 'A leírás csak szöveg lehet.',

            'picture.string' => 'A kép mező csak szöveg lehet.',
            'picture.max' => 'A kép mező legfeljebb 125 karakter hosszú lehet.',
            'picture.unique' => 'Ez a képútvonal már foglalt.',

            'person.integer' => 'Az adagszám csak egész szám lehet.',
            'person.min' => 'Az adagszám legalább 1 kell legyen.',
            'person.max' => 'Az adagszám legfeljebb 255 lehet.',

            'meal_id.integer' => 'Az étel azonosítónak számnak kell lennie.',
            'meal_id.exists' => 'A megadott étel nem létezik.',
        ];
    }
}