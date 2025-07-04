<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Agenda;
use App\Models\Nurses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QueriesRequest;
use App\Models\Patients;
use App\Models\Queries;
use Carbon\Carbon;

class QueriesController extends Controller
{

    public function schedule(QueriesRequest $request)
    {
        try {
            $user = Auth::user();

            if ($user->profile_id === 2) {
                return response()->json([
                    "error" => true,
                    "message" => "Enfermeiros não podem agendar consultas."
                ], 403);
            }

            $patient = $this->patient($user->id);

            if (! $patient) {
                return response()->json([
                    "error" => true,
                    "message" => "Paciente não encontrado",
                ], 404);
            }

            $nurse = Nurses::find($request->nurses_id);

            if (! $nurse) {
                return response()->json([
                    "error" => true,
                    "message" => "Enfermeiro não encontrado",
                ], 404);
            }

            $days_week = Carbon::parse($request->date)->dayOfWeekIso;
            $getNurseSchedule = Agenda::where("nurses_id", $nurse->id)
                ->where("days_week", $days_week)
                ->where("start_time", $request->hour)
                ->first();

            if (! $getNurseSchedule) {
                return response()->json([
                    "error" => true,
                    "message" => "Este enfermeiro não possui horários disponíveis para o dia e horário informados."
                ], 404);
            }

            $fullDate = Carbon::parse($request->date . ' ' . $request->hour)->format('Y-m-d H:i:s');

            $existingQuery = Queries::where("nurses_id", $nurse->id)
                ->where("date", $fullDate)
                ->first();

            if ($existingQuery) {
                return response()->json([
                    "error" => true,
                    "message" => "Já existe uma consulta agendada para este horário com este enfermeiro."
                ], 409);
            }


            $query = Queries::create([
                "nurses_id" => $nurse->id,
                "patients_id" => $patient->id,
                "date" => $fullDate,
                "query_type" => $request->query_type
            ]);

            return response()->json([
                "success" => true,
                "message" => "Consulta agendada",
                "data" => $query,
            ], 201);
        } catch (Exception $e) {
            Log::error("Erro no schedule: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Ocorreu um erro interno."
            ], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                "id" => "required|integer"
            ]);

            $patient = $this->patient($user->id);

            if (! $patient) {
                return response()->json([
                    "error" => true,
                    "message" => "Paciente não encontrado",
                ], 404);
            }

            $query = Queries::where("id", $request->id)
                ->where("patients_id", $patient->id)
                ->first();

            if (! $query) {
                return response()->json([
                    "error" => true,
                    "message" => "Consulta não encontrada ou não pertence a este paciente."
                ], 404);
            }

            $query->delete();

            Log::info("[QueriesController][cancel] O usuário {$user->id} cancelou a consulta {$query->id}.");
            return response()->json([
                "error" => false,
                "message" => "Consulta cancelada com sucesso!"
            ]);
        } catch (Exception $e) {
            Log::error("[QueriesController][cancel] Erro: {$e->getMessage()}", [
                'exception' => $e
            ]);
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente"
            ]);
        }
    }

    public function postpone(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                "id" => "required|integer|exists:queries,id",
                "date" => "required|date|after_or_equal:today",
                "hour" => "required|date_format:H:i"
            ], [
                "id.exists" => "Consulta não encontrada.",
                "date.after_or_equal" => "A data precisa ser hoje ou futura.",
                "hour.date_format" => "Formato de hora inválido (HH:mm)."
            ]);

            $patient = $this->patient($user->id);

            if (! $patient) {
                return response()->json([
                    "error" => true,
                    "message" => "Paciente não encontrado."
                ], 404);
            }

            $query = Queries::where("id", $request->id)
                ->where("patients_id", $patient->id)
                ->first();

            if (! $query) {
                return response()->json([
                    "error" => true,
                    "message" => "Consulta não encontrada ou não pertence a este paciente."
                ], 404);
            }

            $nurse = Nurses::find($query->nurses_id);

            if (! $nurse) {
                return response()->json([
                    "error" => true,
                    "message" => "Enfermeiro não encontrado."
                ], 404);
            }

            $days_week = Carbon::parse($request->date)->dayOfWeekIso;

            $nurseAgenda = Agenda::where("nurses_id", $nurse->id)
                ->where("days_week", $days_week)
                ->where("start_time", $request->hour)
                ->first();

            if (! $nurseAgenda) {
                return response()->json([
                    "error" => true,
                    "message" => "Este enfermeiro não possui horário disponível nesta data/hora."
                ], 404);
            }

            $fullDate = Carbon::parse($request->date . ' ' . $request->hour)->format('Y-m-d H:i:s');

            $conflict = Queries::where("nurses_id", $nurse->id)
                ->where("date", $fullDate)
                ->where("id", "<>", $query->id)
                ->first();

            if ($conflict) {
                return response()->json([
                    "error" => true,
                    "message" => "Já existe outra consulta neste horário para este enfermeiro."
                ], 409);
            }

            $query->date = $fullDate;
            $query->save();

            Log::info("[QueriesController][postpone] O usuário {$user->id} reagendou a consulta {$query->id} para {$fullDate}");

            return response()->json([
                "error" => false,
                "message" => "Consulta reagendada com sucesso.",
                "data" => $query
            ], 200);
        } catch (Exception $e) {
            Log::error("[QueriesController][postpone] Erro: {$e->getMessage()}", [
                'exception' => $e
            ]);
            return response()->json([
                "error" => true,
                "message" => "Erro interno ao reagendar a consulta."
            ], 500);
        }
    }

    public function list()
    {
        try {

            $user = Auth::user();

            $patient = $this->patient($user->id);

            if (! $patient) {
                return response()->json([
                    "error" => true,
                    "message" => "Paciente não encontrado."
                ], 404);
            }

            $query = Queries::where("patients_id", $patient->id)->get();

            if ($query->isEmpty()) {
                return response()->json([
                    "error" => false,
                    "message" => "Nenhuma consulta encontrada para este paciente.",
                    "data" => [],
                ], 200);
            }

            Log::info("[QueriesController][list] o usuário {$user->id} listou suas consultas marcadas");
            return response()->json([
                "error" => false,
                "message" => "Você tem consulta marcada:",
                "data" => $query,
            ]);
        } catch (Exception $e) {
            Log::error("[QueriesController][list] Erro: {$e->getMessage()}", [
                'exception' => $e
            ]);
            return response()->json([
                "error" => true,
                "message" => "Erro interno. tente novamente"
            ]);
        }
    }

    private function patient($user)
    {
        return Patients::where("user_id", $user)->first();
    }
}
