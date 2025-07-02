<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Nurses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DailyAgendaRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function dailyAgenda(DailyAgendaRequest $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'error' => true,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $nurse = Nurses::where('user_id', $user->id)->first();

            if (! $nurse) {
                return response()->json([
                    'error' => true,
                    'message' => 'Enfermeiro não encontrado.'
                ], 404);
            }

            $alreadyHasAgenda = Agenda::where('nurses_id', $nurse->id)->exists();
            if ($alreadyHasAgenda) {
                return response()->json([
                    'error' => true,
                    'message' => 'Este profissional já possui uma agenda cadastrada.'
                ], 422);
            }

            $validated = $request->validated();
            $daysWeek = is_array($validated['days_week'] ?? null) ? $validated['days_week'] : [];
            DB::transaction(function () use ($daysWeek, $nurse) {
                foreach ($daysWeek as $schedule) {
                    $dayNumber  = $schedule["day"];
                    $start      = $schedule["start"];
                    $end        = $schedule["end"];

                    Agenda::create([
                        "nurses_id" => $nurse?->id,
                        "days_week" => $dayNumber,
                        "start_time" => $start,
                        "end_time" => $end,
                        "active" => true,
                    ]);
                }
            });

            Log::info("[AgendaController][dailyAgenda] Usuário {$nurse?->id} criou sua agenda");
            return response()->json([
                "error" => false,
                "message" => "Agenda criada com sucesso!"
            ], 201);
        } catch (Exception $e) {
            Log::error("Erro ao criar a agenda: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Erro ao criar a agenda. Tente novamente!"
            ], 500);
        }
    }

    public function update(DailyAgendaRequest $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'error' => true,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $nurse = Nurses::where('user_id', $user->id)->first();

            if (! $nurse) {
                return response()->json([
                    'error' => true,
                    'message' => 'Enfermeiro não encontrado.'
                ], 404);
            }

            if (!Agenda::where('nurses_id', $nurse->id)->exists()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Nenhuma agenda cadastrada ainda, cadastre primeiro.'
                ], 422);
            }
            $validated = $request->validated();
            $daysWeek = is_array($validated['days_week'] ?? null) ? $validated['days_week'] : [];
            DB::transaction(function () use ($daysWeek, $nurse) {
                foreach ($daysWeek as $item) {
                    Agenda::where("id", $item["id"])
                        ->where('nurses_id', $nurse->id)
                        ->update([
                            'days_week' => $item['day'],
                            'start_time' => $item['start'],
                            'end_time' => $item['end'],
                            'active' => $item['active'],
                        ]);
                }
            });

            Log::info("[AgendaController][update] Enfermeiro {$nurse->id} atualizou sua agenda");

            return response()->json([
                'error' => false,
                'message' => 'Agenda atualizada com sucesso!'
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao atualizar agenda: {$e->getMessage()}");

            return response()->json([
                'error' => true,
                'message' => 'Erro ao atualizar a agenda. Tente novamente!'
            ], 500);
        }
    }
}
