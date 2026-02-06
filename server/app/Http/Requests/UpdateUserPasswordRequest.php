<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Mivel a bejelentkezett user a saját jelszavát módosítja, ez true
        return true; 
    }

    public function rules(): array
    {
        return [
            // Ellenőrzi, hogy a megadott 'oldpassword' azonos-e a DB-ben lévővel
            'oldpassword' => ['required', 'current_password'], 
            'newpassword' => ['required', 'string', Password::min(3), 'confirmed'],
        ];
    }
    public function messages(): array
    {
        return [
            'oldpassword.required' => 'A jelenlegi jelszó megadása kötelező.',
            'oldpassword.current_password' => 'A megadott jelenlegi jelszó helytelen.',

            'newpassword.required' => 'Az új jelszó megadása kötelező.',
            'newpassword.string' => 'Az új jelszó csak szöveg lehet.',
            'newpassword.min' => 'Az új jelszónak legalább :min karakter hosszúnak kell lennie.',
            'newpassword.confirmed' => 'Az új jelszó megerősítése nem egyezik.',
        ];
    }
}