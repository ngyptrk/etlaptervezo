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
}
