<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWeekdayRequest extends FormRequest
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
            'day' => ['required', 'string', 'max:25', 'unique:weekdays,day'],
        ];
    }
    public function messages(): array
    {
        return [
            'day.required' => 'A nap megadása kötelező.',
            'day.string' => 'A nap csak szöveg lehet.',
            'day.max' => 'A nap legfeljebb 25 karakter hosszú lehet.',
            'day.unique' => 'Ez a nap már létezik.',
        ];
    }
}
