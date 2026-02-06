<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'], // TEXT
            'picture' => ['required', 'string', 'max:255'], // vagy 'image' ha fájl
            'person' => ['required', 'integer', 'min:1', 'max:255'], // TINYINT
            'meal_id' => ['required', 'integer', 'exists:meals,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A recept neve megadása kötelező.',
            'name.string'   => 'A recept neve csak szöveg lehet.',
            'name.max'      => 'A recept neve legfeljebb 255 karakter lehet.',

            'description.required' => 'A leírás megadása kötelező.',
            'description.string'   => 'A leírás csak szöveg lehet.',

            'picture.required' => 'A kép megadása kötelező.',
            'picture.string'   => 'A kép csak szöveg lehet.',
            'picture.max'      => 'A kép legfeljebb 255 karakter lehet.',

            'person.required' => 'Az adagok száma megadása kötelező.',
            'person.integer'  => 'Az adagok száma csak egész szám lehet.',
            'person.min'      => 'Az adagok számának legalább 1-nek kell lennie.',
            'person.max'      => 'Az adagok száma legfeljebb 255 lehet.',

            'meal_id.required' => 'Az étkezés kiválasztása kötelező.',
            'meal_id.integer'  => 'Az étkezés azonosítója érvénytelen.',
            'meal_id.exists'   => 'A kiválasztott étkezés nem létezik.',
        ];
    }
}
