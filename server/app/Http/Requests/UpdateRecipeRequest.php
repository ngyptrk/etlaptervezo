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
            'picture' => ['nullable', 'file', 'mimes:png', 'max:5120'],
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

            'picture.file' => 'A képnek fájlnak kell lennie.',
            'picture.mimes' => 'Csak PNG képet lehet feltölteni.',
            'picture.max' => 'A kép mérete legfeljebb 5 MB lehet.',

            'person.integer' => 'Az adagszám csak egész szám lehet.',
            'person.min' => 'Az adagszám legalább 1 kell legyen.',
            'person.max' => 'Az adagszám legfeljebb 255 lehet.',

            'meal_id.integer' => 'Az étel azonosítónak számnak kell lennie.',
            'meal_id.exists' => 'A megadott étel nem létezik.',
        ];
    }
}
