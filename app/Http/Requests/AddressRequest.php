<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'street'        => 'required|string|max:255',
            'number'        => 'required|numeric|min:1',
            'city'          => 'required|string|max:255',
            'neighborhood'  => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'cep'           => 'required|string|max:9',
        ];
    }

    public function messages()
    {
        return [
            'street.required'       => 'O campo rua é obrigatório.',
            'street.string'         => 'O campo rua deve ser um texto.',
            'street.max'            => 'O campo rua não pode ter mais que 255 caracteres.',

            'number.required'       => 'O campo número é obrigatório.',
            'number.numeric'        => 'O campo número deve ser numérico.',
            'number.max'            => 'O campo número não pode ser maior que 10.',

            'city.required'         => 'O campo cidade é obrigatório.',
            'city.string'           => 'O campo cidade deve ser um texto.',
            'city.max'              => 'O campo cidade não pode ter mais que 255 caracteres.',

            'neighborhood.required' => 'O campo bairro é obrigatório.',
            'neighborhood.string'   => 'O campo bairro deve ser um texto.',
            'neighborhood.max'      => 'O campo bairro não pode ter mais que 255 caracteres.',

            'state.required'        => 'O campo estado é obrigatório.',
            'state.string'          => 'O campo estado deve ser um texto.',
            'state.max'             => 'O campo estado não pode ter mais que 255 caracteres.',

            'cep.required'          => 'O campo CEP é obrigatório.',
            'cep.string'            => 'O campo CEP deve ser um texto.',
            'cep.max'               => 'O campo CEP não pode ter mais que 9 caracteres.',
        ];
    }

}
