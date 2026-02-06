<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'], // TEXT
            'picture' => ['nullable', 'string', 'max:255'], // vagy 'image' ha fájl
            'person' => ['nullable', 'integer', 'min:1', 'max:255'], // TINYINT
            'meal_id' => ['nullable', 'integer', 'exists:meals,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.string' => 'A név csak szöveg lehet.',
            'name.max' => 'A név legfeljebb 255 karakter hosszú lehet.',

            'description.string' => 'A leírás csak szöveg lehet.',

            'picture.string' => 'A kép mező csak szöveg lehet.',
            'picture.max' => 'A kép mező legfeljebb 255 karakter hosszú lehet.',

            'person.integer' => 'Az adagok száma csak egész szám lehet.',
            'person.min' => 'Az adagok száma legalább 1 kell legyen.',
            'person.max' => 'Az adagok száma legfeljebb 255 lehet.',

            'meal_id.integer' => 'Az étel azonosítónak számnak kell lennie.',
            'meal_id.exists' => 'A megadott étel nem létezik.',
        ];
    }
}
