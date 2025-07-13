<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalRecordRequest extends FormRequest
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
            "querie_id" => "required|integer",
            "patient_id" => "required|integer",
            "diagnosis" => "required|string",
            "prescriptions" => "required|string",
            "obs" => "nullable|string",
        ];
    }

    public function messages(): array
    {
        return [
            'querie_id.required' => 'O campo consulta é obrigatório.',
            'patient_id.required' => 'O campo paciente é obrigatório.',
            'diagnosis.required' => 'O campo diagnóstico é obrigatório.',
            'prescriptions.required' => 'O campo prescrições é obrigatório.',
        ];
    }
}
