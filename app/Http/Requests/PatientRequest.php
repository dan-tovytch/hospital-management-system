<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
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
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "cpf" => "required|string",
            "phone_number" => "required|string",
            "date_birth" => "required|date",
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'O nome é obrigatório.',
            'first_name.string' => 'O nome deve ser uma letra.',
            'first_name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'last_name.required' => 'O sobrenome é obrigatório.',
            'last_name.string' => 'O sobrenome deve ser uma letra.',
            'last_name.max' => 'O sobrenome não pode ter mais que 255 caracteres.',
            'cpf.required' => 'O CPF é obrigatório.',
            'phone_number.required' => 'O telefone é obrigatório.',
            'date_birth.required' => 'A data de nascimento é obrigatória.',
            'date_birth.date' => 'A data de nascimento deve ser uma data válida.',
        ];
    }
}
