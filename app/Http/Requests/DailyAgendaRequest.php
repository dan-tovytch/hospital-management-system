<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyAgendaRequest extends FormRequest
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
            "days_week" => "required|array",
            "days_week.*.id" => "integer",
            "days_week.*.day" => "required|integer|min:1|max:7",
            "days_week.*.start" => "required|date_format:H:i",
            'days_week.*.end'   => 'required|date_format:H:i|after:days_week.*.start',
            'days_week.*.active' => "boolean",
        ];
        }

        public function messages(): array
        {
        return [
            'days_week.required' => 'O campo dias da semana é obrigatório.',
            'days_week.array' => 'O campo dias da semana deve ser enviado em um array.',
            'days_week.*.id.integer' => 'O ID do dia da semana deve ser um número inteiro.',
            'days_week.*.day.required' => 'O campo dia é obrigatório.',
            'days_week.*.day.integer' => 'O campo dia deve ser um número inteiro.',
            'days_week.*.day.min' => 'O campo dia deve ser no mínimo 1.',
            'days_week.*.day.max' => 'O campo dia deve ser no máximo 7.',
            'days_week.*.start.required' => 'O campo horário início é obrigatório.',
            'days_week.*.start.date_format' => 'O campo horário início deve estar no formato HH:MM.',
            'days_week.*.end.required' => 'O campo horário fim é obrigatório.',
            'days_week.*.end.date_format' => 'O campo horário fim deve estar no formato HH:MM.',
            'days_week.*.end.after' => 'O campo fim deve ser uma hora após o início.',
            'days_week.*.active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
