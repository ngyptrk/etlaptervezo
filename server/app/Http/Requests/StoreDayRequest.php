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
}
