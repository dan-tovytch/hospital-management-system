<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'email'    => 'required|unique:users|email|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required'    => "Precisa informar um email válido!",
            'email.unique'      => "Email já cadastrado, por favor tente novamente",
            'password.required' => "Precisa informar uma senha!",
            'password.min'      => "A senha precisa ter no minímo 8 caracters",
            'password.regex'    => 'A senha precisa conter ao menos uma letra maiúscula, uma minúscula, um número e um caractere especial.',
        ];
    }

    public function expectsJson(): bool
    {
        return true;
    }
}
