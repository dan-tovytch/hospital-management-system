<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueriesRequest extends FormRequest
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
            "patients_id" => "required|integer|exists:patients,id",
            "nurses_id" => "required|integer|exists:nurses,id",
            "date" => "required|date",
            "hour" => "required|date_format:H:i",
            "query_type" => "required|integer"
        ];
    }

    public function messages(): array
    {
        return [
            'patients_id.required' => 'O campo paciente é obrigatório.',
            'patients_id.exists' => 'O paciente não existe.',
            'nurses_id.required' => 'O campo enfermeiro é obrigatório.',
            'nurses_id.exists' => 'O enfermeiro não existe.',
            'date.required' => 'O campo data é obrigatório.',
            'date.date' => 'O campo data deve ser uma data válida.',
            'hour.required' => 'O campo Horário é obrigatório.',
            'query_type.required' => 'O campo tipo de consulta é obrigatório.',
        ];
    }
}
