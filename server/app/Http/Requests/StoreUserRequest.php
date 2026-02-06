<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A név megadása kötelező.',
            'name.string'   => 'A név csak szöveg lehet.',

            'email.required' => 'Az e-mail cím megadása kötelező.',
            'email.email'    => 'Az e-mail cím formátuma nem megfelelő.',

            'password.required' => 'A jelszó megadása kötelező.',
        ];
    }
}
