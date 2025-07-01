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
            "days_week.*.day" => "required|integer|min:1|max:7",
            "days_week.*.start" => "required|date_format:H:i",
            'days_week.*.end'   => 'required|date_format:H:i|after:days_week.*.start',
        ];
    }
}
