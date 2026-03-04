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
            'picture' => ['required', 'string', 'max:125', Rule::unique('recipes', 'picture')],
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
            'picture.string' => 'A kép mező csak szöveg lehet.',
            'picture.max' => 'A kép mező legfeljebb 125 karakter lehet.',
            'picture.unique' => 'Ez a képútvonal már foglalt.',

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