<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWeekdayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'day' => ['required', 'string', 'max:25', Rule::unique('weekdays', 'day')->ignore($id)],
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