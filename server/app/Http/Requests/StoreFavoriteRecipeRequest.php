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
            'recipe_id.required' => 'A recept kivĂˇlasztĂˇsa kĂ¶telezĹ‘.',
            'recipe_id.integer' => 'A recept azonosĂ­tĂłja Ă©rvĂ©nytelen.',
            'recipe_id.exists' => 'A kivĂˇlasztott recept nem lĂ©tezik.',
        ];
    }
}
