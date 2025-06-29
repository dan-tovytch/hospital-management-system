<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NurseRequest extends FormRequest
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
            "user_id" => "required|string",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "specialtie" => "required|numeric",
            "cpf" => "required|string|",
            "coren" => "required|string",
            "phone_number" => "required|string",
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O ID do usuário é obrigatório.',
            'first_name.required' => 'O nome é obrigatório.',
            'last_name.required' => 'O sobrenome é obrigatório.',
            'specialtie.required' => 'A especialidade é obrigatória.',
            'cpf.required' => 'O CPF é obrigatório.',
            'coren.required' => 'O COREN é obrigatório.',
            'phone_number.required' => 'O telefone é obrigatório.',
        ];
    }
}
