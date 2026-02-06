<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealOfDayRequest extends FormRequest
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
            'meal_of_day' => ['required', 'string', 'max:255', 'unique:meal_of_days,meal_of_day'],
        ];
    }

    public function messages(): array
    {
        return [
            'meal_of_day.required' => 'A napszak megadása kötelező.',
            'meal_of_day.string'   => 'A napszak csak szöveg lehet.',
            'meal_of_day.max'      => 'A napszak legfeljebb 255 karakter lehet.',
            'meal_of_day.unique'   => 'Ez a napszak már létezik.',
        ];
    }
}
