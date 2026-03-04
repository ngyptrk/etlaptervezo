<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|integer|in:1,2,3',
            
        ];
    }
    // public function messages(): array
    // {
    //     return [
    //         'name.string' => 'A név csak szöveg lehet.',

    //         'email.email' => 'Az e-mail cím formátuma nem megfelelő.',

    //         'password.string' => 'A jelszó csak szöveg lehet.',

    //         'role.string' => 'A szerepkör csak szöveg lehet.',
    //     ];
    // }
}
