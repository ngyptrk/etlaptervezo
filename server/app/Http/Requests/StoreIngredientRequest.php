<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIngredientRequest extends FormRequest
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
            'recipe_id' => [
                'required',
                'integer',
                'exists:recipes,id',
            ],

            'raw_ingredient_id' => [
                'required',
                'integer',
                'exists:raw_ingredients,id',
                Rule::unique('ingredients', 'raw_ingredient_id')
                    ->where(fn($q) => $q->where('recipe_id', request('recipe_id'))),
            ],

            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'unit_id' => [
                'required',
                'integer',
                'exists:units,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recipe_id.required' => 'A recept kiválasztása kötelező.',
            'recipe_id.integer'  => 'A recept azonosítója érvénytelen.',
            'recipe_id.exists'   => 'A kiválasztott recept nem létezik.',

            'raw_ingredient_id.required' => 'Az alapanyag kiválasztása kötelező.',
            'raw_ingredient_id.integer'  => 'Az alapanyag azonosítója érvénytelen.',
            'raw_ingredient_id.exists'   => 'A kiválasztott alapanyag nem létezik.',
            'raw_ingredient_id.unique'   => 'Ez az alapanyag már hozzá van adva ehhez a recepthez.',

            'amount.required' => 'A mennyiség megadása kötelező.',
            'amount.integer'  => 'A mennyiség csak egész szám lehet.',
            'amount.min'      => 'A mennyiségnek legalább 1-nek kell lennie.',

            'unit_id.required' => 'A mértékegység kiválasztása kötelező.',
            'unit_id.integer'  => 'A mértékegység azonosítója érvénytelen.',
            'unit_id.exists'   => 'A kiválasztott mértékegység nem létezik.',
        ];
    }
}
