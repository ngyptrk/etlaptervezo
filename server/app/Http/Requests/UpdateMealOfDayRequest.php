<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMealOfDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'meal_of_day' => ['required', 'string', 'max:255', Rule::unique('meal_of_days', 'meal_of_day')->ignore($id)],
        ];
    }

    public function messages(): array
    {
        return [
            'meal_of_day.required' => 'Az étkezés megnevezése kötelező.',
            'meal_of_day.string' => 'Az étkezés megnevezése csak szöveg lehet.',
            'meal_of_day.max' => 'Az étkezés megnevezése legfeljebb 255 karakter hosszú lehet.',
            'meal_of_day.unique' => 'Ez az étkezés már létezik.',
        ];
    }
}