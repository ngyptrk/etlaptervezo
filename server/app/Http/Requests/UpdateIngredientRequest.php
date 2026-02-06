<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
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
                'nullable',
                'integer',
                'exists:recipes,id',
            ],

            'raw_ingredient_id' => [
                'nullable',
                'integer',
                'exists:raw_ingredients,id',
                Rule::unique('ingredients', 'raw_ingredient_id')
                    ->where(fn($q) => $q->where('recipe_id', request('recipe_id'))),
            ],

            'amount' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recipe_id.integer' => 'A recept azonosítónak számnak kell lennie.',
            'recipe_id.exists' => 'A megadott recept nem létezik.',

            'raw_ingredient_id.integer' => 'Az alapanyag azonosítónak számnak kell lennie.',
            'raw_ingredient_id.exists' => 'A megadott alapanyag nem létezik.',
            'raw_ingredient_id.unique' => 'Ez az alapanyag már hozzá van adva ehhez a recepthez.',

            'amount.integer' => 'Az mennyiségnek számnak kell lennie.',
            'amount.min' => 'A mennyiség legalább 1 kell legyen.',

            'unit_id.integer' => 'A mértékegység azonosítónak számnak kell lennie.',
            'unit_id.exists' => 'A megadott mértékegység nem létezik.',
        ];
    }
}
