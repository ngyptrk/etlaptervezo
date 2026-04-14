<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => ['required', 'integer', 'exists:recipes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipe_id.required' => 'A recept kiválasztása kötelező.',
            'recipe_id.integer' => 'A recept azonosítója érvénytelen.',
            'recipe_id.exists' => 'A kiválasztott recept nem létezik.',
        ];
    }
}
