<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserSelfRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                Rule::unique('users', 'name')->ignore($this->user()->id),
                'required_without_all:email'
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'required_without_all:name',  
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'role' => 'prohibited',
            'password' => 'prohibited',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'A név megadása kötelező, ha az e-mail nincs megadva.',
            'name.string' => 'A név csak szöveg lehet.',
            'name.unique' => 'Már van ilyen nevű felhasználó.',
            'name.required_without_all' => 'A név megadása kötelező, ha nincs e-mail.',

            'email.required' => 'Az e-mail megadása kötelező, ha a név nincs megadva.',
            'email.email' => 'Az e-mail cím formátuma nem megfelelő.',
            'email.required_without_all' => 'Az e-mail megadása kötelező, ha nincs név.',
            'email.unique' => 'Ez az e-mail cím már foglalt.',

            'role.prohibited' => 'A szerepkör mező nem módosítható.',
            'password.prohibited' => 'A jelszó mező nem módosítható ezen a kérésen keresztül.',
        ];
    }
}
