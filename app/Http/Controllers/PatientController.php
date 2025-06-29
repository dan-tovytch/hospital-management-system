<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Address;
use App\Models\Patients;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PatientRequest;

class PatientController extends Controller
{
    public function store(PatientRequest $request)
    {
        try {
            $user = Auth::user();
            $address_user = $this->getUserAddress($user->id);
            Patients::create([
                "user_id"       => $user->id,
                "first_name"    => $request->first_name,
                "last_name"     => $request->last_name,
                "cpf"           => $request->cpf,
                "phone_number"  => $request->phone_number,
                "address_id"    => $address_user?->id,
                "date_birth"    => $request->date_birth,
            ]);
            Log::info("Usuário {$user->id} foi registrado como paciente");
            return response()->json([
                "message" => "Conta criada com sucesso!"
            ], 201);
        } catch (Exception $e) {
            Log::error("Erro ao registrar o paciente: " . $e->getMessage());
            return response()->json([
                "message" => "Erro ao se registrar. Tente novamente"
            ], 500);
        }
    }

    public function update(PatientRequest $request)
    {
        try {
            $user = Auth::user();
            $address_user = $this->getUserAddress($user->id);

            if (!$address_user) {
                return response()->json([
                    "message" => "Endereço não encontrado para este usuário."
                ], 404);
            }

            $patients = Patients::where("user_id", $user->id)
                                ->lockForUpdate()
                                ->first();

            if (! $patients ) {
                return response()->json([
                    "error"   => true,
                    "message" => "Paciente não encontrado."
                ], 404);
            }

            $patients->update([
                "user_id"       => $user->id,
                "first_name"    => $request->first_name,
                "last_name"     => $request->last_name,
                "cpf"           => $request->cpf,
                "phone_number"  => $request->phone_number,
                "address_id"    => $address_user?->id,
                "date_birth"    => $request->date_birth,
            ]);

            Log::info("Usuário {$user->id} atualizou seus dados de paciente");

            return response()->json([
                "error"   => false,
                "message" => "Conta atualizada com sucesso!"
            ], 200);

        } catch (Exception $e) {
            Log::error("Erro ao atualizar o paciente: " . $e->getMessage());
            return response()->json([
                "error"   => true,
                "message" => "Erro ao atualizar o perfil. Tente novamente"
            ], 500);
        }
    }

    private function getUserAddress($user_id)
    {
        return Address::where("user_id", $user_id)->first();
    }
}
