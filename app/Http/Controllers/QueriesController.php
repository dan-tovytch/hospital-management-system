<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Agenda;
use App\Models\Nurses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

            $patient = Patients::where("user_id", $user->id)->first();

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

            $days_week = date("N", strtotime($request->date));

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
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro no schedule: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Ocorreu um erro interno."
            ], 500);
        }
    }

    public function cancel()
    {
        //
    }

    public function postpone()
    {
        //
    }

    public function view()
    {
        //
    }
}
