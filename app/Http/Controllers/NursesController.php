<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Nurses;
use App\Models\Address;
use App\Models\Queries;
use App\Models\Patients;
use Illuminate\Http\Request;
use App\Models\MedicalRecords;
use App\Http\Requests\NurseRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\MedicalRecordRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NursesController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $user = Auth::user();

            $this->authorize('manage', Nurses::class);

            $nurses = Nurses::all();

            Log::info("[AdminController][listNurses] O usuário {$user->id} listou os enfermeiros registrados");
            return response()->json([
                "error" => false,
                "message" => "Sucesso!",
                "data" => $nurses,
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao listar os enfermeiros ERROR: {$e->getMessage()}");
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente"
            ]);
        }
    }


    /**
     * Cria um novo registro de enfermeiro.
     *
     * @param \App\Http\Requests\NurseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NurseRequest $request)
    {
        try {
            $user = Auth::user();

            $this->authorize('manage', Nurses::class);

            $cpf = $request->cpf;
            $cpfHash = hash('sha256', $cpf);

            if (Nurses::where('cpf_hash', $cpfHash)->exists()) {
                return response()->json([
                    "error" => true,
                    "message" => "Já existe um enfermeiro cadastrado com esse CPF."
                ], 422);
            }

            $encryptedCpf = Crypt::encryptString($cpf);

            Nurses::create([
                "user_id" => $request->user_id,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "specialtie_id" => $request->specialtie,
                "cpf" => $encryptedCpf,
                "cpf_hash" => $cpfHash,
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
     * Atualiza os dados de um registro de enfermeiro existente.
     *
     * @param \App\Http\Requests\NurseRequest $request
     * @return \Illuminate\Http\JsonResponse
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
     * Desativa (desliga) um registro de enfermeiro, marcando-o
     * como inativo e registrando a data de desligamento.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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


    public function medicalRecord(MedicalRecordRequest $request)
    {
        try {
            $user = Auth::user();
            $nurse = $this->getNurse($user->id);

            if (! $nurse) {
                return response()->json([
                    "error" => true,
                    "message" => "Enfermeiro não encontrado."
                ], 404);
            }

            $patient = Patients::find($request->patient_id);
            if (! $patient) {
                return response()->json([
                    "error" => true,
                    "message" => "Paciente não encontrado."
                ], 404);
            }

            $querie = Queries::find($request->querie_id);
            if (! $querie) {
                return response()->json([
                    "error" => true,
                    "message" => "Consulta não encontrada nos registros."
                ], 404);
            }

            $medicalRecord = MedicalRecords::create([
                "querie_id" => $querie->id,
                "patient_id" => $patient->id,
                "nurse_id" => $nurse->id,
                "diagnosis" => $request->diagnosis,
                "prescriptions" => $request->prescriptions,
                "obs" => $request->obs,
            ]);

            Log::info("[NursesController][medicalRecord] O usuário {$user->id} criou um prontuário {$medicalRecord->id} para o paciente {$patient->user_id}");

            return response()->json([
                "error" => false,
                "message" => "Prontuário criado com sucesso.",
                "data" => $medicalRecord,
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao criar o prontuário do usuário {$user->id}: {$e->getMessage()}");
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

    private function getNurse($user_id)
    {
        return Nurses::where("user_id", $user_id)->first();
    }
}
