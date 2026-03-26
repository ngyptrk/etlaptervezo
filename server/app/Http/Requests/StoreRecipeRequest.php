<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:125', Rule::unique('recipes', 'name')],
            'description' => ['required', 'string'],
            'picture' => ['required', 'file', 'mimes:png', 'max:5120'],
            'person' => ['required', 'integer', 'min:1', 'max:255'],
            'meal_id' => ['required', 'integer', 'exists:meals,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A recept neve kötelező.',
            'name.string' => 'A recept neve csak szöveg lehet.',
            'name.max' => 'A recept neve legfeljebb 125 karakter lehet.',
            'name.unique' => 'Ez a receptnév már létezik.',

            'description.required' => 'A leírás megadása kötelező.',
            'description.string' => 'A leírás csak szöveg lehet.',

            'picture.required' => 'A kép megadása kötelező.',
            'picture.file' => 'A képnek fájlnak kell lennie.',
            'picture.mimes' => 'Csak PNG képet lehet feltölteni.',
            'picture.max' => 'A kép mérete legfeljebb 5 MB lehet.',

            'person.required' => 'Az adagszám megadása kötelező.',
            'person.integer' => 'Az adagszám csak egész szám lehet.',
            'person.min' => 'Az adagszám legalább 1 legyen.',
            'person.max' => 'Az adagszám legfeljebb 255 lehet.',

            'meal_id.required' => 'Az étkezés kiválasztása kötelező.',
            'meal_id.integer' => 'Az étkezés azonosítója érvénytelen.',
            'meal_id.exists' => 'A kiválasztott étkezés nem létezik.',
        ];
    }
}
