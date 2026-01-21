<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'], // TEXT
            'picture' => ['required', 'string', 'max:255'], // vagy 'image' ha fájl
            'person' => ['required', 'integer', 'min:1', 'max:255'], // TINYINT
            'meal_id' => ['required', 'integer', 'exists:meals,id'],
        ];
    }
}
