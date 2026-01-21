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
        return [
             'meal_of_days_id' => [
            'required',
            'integer',
            'exists:meal_of_days,id',
        ],

        'meal_id' => [
            'required',
            'integer',
            'exists:meals,id',
            Rule::unique('meal_requirements', 'meal_id')
                ->where(fn ($q) => $q->where('meal_of_days_id', request('meal_of_days_id'))),
        ],

        ];
    }
}
