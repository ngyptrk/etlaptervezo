<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateMealRequirementRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'meal_of_day_id' => [
                'nullable',
                'integer',
            ],

            'meal_id' => [
                'nullable',
                'integer',
                Rule::unique('meal_requirements', 'meal_id')
                    ->ignore($id)
                    ->where(fn($q) => $q->where('meal_of_day_id', request('meal_of_day_id'))),
            ],

        ];
    }

    public function messages(): array
{
    return [
        'meal_of_day_id.integer' => 'Az étkezés időpontjának azonosítója csak szám lehet.',

        'meal_id.integer' => 'Az étel azonosítónak számnak kell lennie.',
        'meal_id.unique' => 'Ez az étel már hozzá van rendelve ehhez az étkezéshez.',
    ];
}
}
