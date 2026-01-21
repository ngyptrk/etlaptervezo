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
}
