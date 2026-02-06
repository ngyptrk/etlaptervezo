<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDayRequest extends FormRequest
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
            'day' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'meal_of_days_id' => ['nullable', 'integer', 'exists:meal_of_days,id'],
            'recipe_id' => ['nullable', 'integer', 'exists:recipes,id'],
            'meal_id' => ['nullable', 'integer', 'exists:meals,id'],
        ];
    }
    public function messages(): array
{
    return [
        'day.string' => 'A nap mező csak szöveg lehet.',
        'day.max' => 'A nap mező legfeljebb 255 karakter hosszú lehet.',

        'user_id.integer' => 'A felhasználó azonosítónak számnak kell lennie.',
        'user_id.exists' => 'A megadott felhasználó nem létezik.',

        'meal_of_days_id.integer' => 'Az étkezés napjának azonosítója csak szám lehet.',
        'meal_of_days_id.exists' => 'A megadott étkezési nap nem létezik.',

        'recipe_id.integer' => 'A recept azonosítónak számnak kell lennie.',
        'recipe_id.exists' => 'A megadott recept nem létezik.',

        'meal_id.integer' => 'Az étel azonosítónak számnak kell lennie.',
        'meal_id.exists' => 'A megadott étel nem létezik.',
    ];
}
}
