<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDayRequest extends FormRequest
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
            'day' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'meal_of_days_id' => ['required', 'integer', 'exists:meal_of_days,id'],
            'recipe_id' => ['required', 'integer', 'exists:recipes,id'],
            'meal_id' => ['required', 'integer', 'exists:meals,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'day.required' => 'A nap megadása kötelező.',
            'day.string'   => 'A nap csak szöveg lehet.',
            'day.max'      => 'A nap legfeljebb 255 karakter lehet.',

            'user_id.required' => 'A felhasználó megadása kötelező.',
            'user_id.integer'  => 'A felhasználó azonosítója érvénytelen.',
            'user_id.exists'   => 'A kiválasztott felhasználó nem létezik.',

            'meal_of_days_id.required' => 'Az étkezés időpontjának megadása kötelező.',
            'meal_of_days_id.integer'  => 'Az étkezés időpontja érvénytelen.',
            'meal_of_days_id.exists'   => 'A kiválasztott étkezés időpontja nem létezik.',

            'recipe_id.required' => 'A recept kiválasztása kötelező.',
            'recipe_id.integer'  => 'A recept azonosítója érvénytelen.',
            'recipe_id.exists'   => 'A kiválasztott recept nem létezik.',

            'meal_id.required' => 'Az étkezés kiválasztása kötelező.',
            'meal_id.integer'  => 'Az étkezés azonosítója érvénytelen.',
            'meal_id.exists'   => 'A kiválasztott étkezés nem létezik.',
        ];
    }
}
