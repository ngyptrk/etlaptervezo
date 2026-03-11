<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:2',
                Rule::unique('users', 'name'),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                Password::min(3),
                'confirmed',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A nev megadasa kotelezo.',
            'name.string'   => 'A nev csak szoveg lehet.',
            'name.min'      => 'A nev legalabb 2 karakter legyen.',
            'name.unique'   => 'Ez a nev mar foglalt.',

            'email.required' => 'Az email cim megadasa kotelezo.',
            'email.email'    => 'Az email cim formatuma nem megfelelo.',
            'email.unique'   => 'Ez az email cim mar foglalt.',

            'password.required' => 'A jelszo megadasa kotelezo.',
            'password.min'      => 'A jelszonak legalabb :min karakternek kell lennie.',
            'password.confirmed' => 'A ket jelszo nem egyezik.',
        ];
    }
}
