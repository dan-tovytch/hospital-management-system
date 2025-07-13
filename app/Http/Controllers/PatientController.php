<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Nurses;
use App\Models\Address;
use App\Models\Patients;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PatientRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Crypt;

class PatientController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    "error" => true,
                    "message" => "Usuário não autenticado."
                ], 401);
            }

            $this->authorize("manage", Nurses::class);

            $patients = Patients::paginate(10);

            Log::info("[AddressController][index] O usuário {$user->id} listou os pacientes.");
            return response()->json([
                "error" => false,
                "message" => "sucesso!",
                "data" => $patients,
            ]);
        } catch (Exception $e) {
            Log::error("Erro ao puxar a lista de pacientes. ERROR: {$e->getMessage()}");
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente",
            ], 500);
        }
    }

    public function store(PatientRequest $request)
    {
        try {
            $user = Auth::user();
            $address_user = $this->getUserAddress($user->id);

            if (Patients::where('user_id', $user->id)->exists()) {
                return response()->json([
                    "error" => true,
                    "message" => "Paciente já cadastrado."
                ], 409);
            }

            $cpf = $request->cpf;
            $cpfHash = hash('sha256', $cpf);

            if (Patients::where('cpf_hash', $cpfHash)->exists()) {
                return response()->json([
                    "error" => true,
                    "message" => "Já existe um enfermeiro cadastrado com esse CPF."
                ], 422);
            }

            $encryptedCpf = Crypt::encryptString($cpf);

            Patients::create([
                "user_id"       => $user->id,
                "first_name"    => $request->first_name,
                "last_name"     => $request->last_name,
                "cpf" => $encryptedCpf,
                "cpf_hash" => $cpfHash,
                "phone_number"  => $request->phone_number,
                "address_id"    => $address_user?->id ?: null,
                "date_birth"    => $request->date_birth,
            ]);
            Log::info("Paciente registrado com sucesso para o usuário ID: {$user->id}");
            return response()->json([
                "error" => false,
                "message" => "Conta criada com sucesso!"
            ], 201);
        } catch (Exception $e) {
            Log::error("Erro ao registrar o paciente: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Erro ao se registrar. Tente novamente",
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
                    "error" => true,
                    "message" => "Endereço não encontrado para este usuário."
                ], 404);
            }

            $patients = Patients::where("user_id", $user->id)
                ->lockForUpdate()
                ->first();

            if (! $patients) {
                return response()->json([
                    "error"   => true,
                    "message" => "Paciente não encontrado."
                ], 404);
            }

            $patients->update([
                "first_name"    => $request->first_name,
                "last_name"     => $request->last_name,
                "cpf"           => $request->cpf,
                "phone_number"  => $request->phone_number,
                "address_id"    => $address_user?->id,
                "date_birth"    => $request->date_birth,
            ]);

            Log::info("O usuário {$user->id} atualizou seus dados de paciente");

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
