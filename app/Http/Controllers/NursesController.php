<?php

namespace App\Http\Controllers;

use App\Http\Requests\NurseRequest;
use Exception;
use App\Models\Address;
use App\Models\Nurses;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NursesController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NurseRequest $request)
    {
        try {
            $user = Auth::user();

            $address = $this->getUserAddress($request->user_id);

            if (!$address) {
                return response()->json([
                    "error" => true,
                    "message" => "Endereço não encontrado para o usuário informado."
                ], 404);
            }

            $this->authorize('manage', Nurses::class);

            Nurses::create([
                "user_id" => $request->user_id,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "specialtie_id" => $request->specialtie,
                "cpf" => $request->cpf,
                "address_id" => $address?->id,
                "coren" => $request->coren,
                "phone_number" => $request->phone_number,
                "date_birth" => $request->date_birth,
                "active" => true,
            ]);

            Log::info("O usuário {$user->id} registrou um enfermeiro");
            return response()->json([
                "error" => false,
                "message" => "Conta criada com sucesso!",
            ], 201);
        } catch (Exception $e) {
            Log::error("Erro ao registrar enfermeiro: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Erro ao criar a conta. Tente novamente"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NurseRequest $request)
    {
        try {
            $user = Auth::user();

            $address = $this->getUserAddress($request->user_id);

            if (!$address) {
                return response()->json([
                    "error" => true,
                    "message" => "Endereço não encontrado para o usuário informado."
                ], 404);
            }

            $this->authorize('manage', Nurses::class);


            $nurses = Nurses::where("user_id", $request->user_id)
                ->lockForUpdate()
                ->first();

            if (! $nurses) {
                return response()->json([
                    "error"   => true,
                    "message" => "Enfermeiro não encontrado."
                ], 404);
            }


            $nurses->update([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "specialtie_id" => $request->specialtie,
                "cpf" => $request->cpf,
                "address_id" => $address?->id,
                "coren" => $request->coren,
                "phone_number" => $request->phone_number,
                "date_birth" => $request->date_birth,
            ]);

            Log::info("O usuário {$user->id} atualizou o registro de enfermeiro");
            return response()->json([
                "error" => false,
                "message" => "Conta atualizada com sucesso!",
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao atualizar enfermeiro: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Erro ao atualizar a conta. Tente novamente"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function disable(Request $request)
    {
        try {

            $request->validate(
                [
                    "user_id" => "required",
                    "termination_date" => "required|date",
                ],
                [
                    "user_id.required" => "Precisa informar o id do usuário.",
                    "termination_date.required" => "Precisa informar a data de desligamento.",
                    "termination_date.date" => "Precisa ser uma data válida!",
                ]
            );

            $user = Auth::user();

            $this->authorize('manage', Nurses::class);


            $nurse = Nurses::where("user_id", $request->user_id)
                ->lockForUpdate()
                ->first();

            if (! $nurse) {
                return response()->json([
                    "error" => true,
                    "message" => "Enfermeiro não encontrado."
                ], 404);
            }

            $nurse->update([
                "termination_date" => $request->termination_date,
                "active" => false,
            ]);

            Log::info("O usuário {$user->id} desativou o registro de enfermeiro");
            return response()->json([
                "error" => false,
                "message" => "Enfermeiro desligado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao desativar enfermeiro: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente",
            ], 500);
        }
    }

    private function getUserAddress($user_id)
    {
        return Address::where("user_id", $user_id)->first();
    }
}
