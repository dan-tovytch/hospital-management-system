<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Admin;
use App\Models\Nurses;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
{

    use AuthorizesRequests;

    public function store(AdminRequest $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    "error" => true,
                    "message" => "Usuário não autenticado."
                ], 401);
            }

            $this->authorize('manage', Nurses::class);

            $cpf = $request->cpf;
            $cpfHash = hash('sha256', $cpf);

            if (Admin::where('cpf_hash', $cpfHash)->exists()) {
                return response()->json([
                    "error" => true,
                    "message" => "Já existe um Administrador cadastrado com esse CPF."
                ], 422);
            }

            $encryptedCpf = Crypt::encryptString($cpf);

            if (Admin::where('user_id', $request->user_id)->exists()) {
                return response()->json([
                    "error" => true,
                    "message" => "Usuário já é um administrador"
                ], 409);
            }

            $adminData = $request->only(
                'user_id',
                'first_name',
                'last_name'
            );
            $adminData['cpf'] = $encryptedCpf;
            $adminData['cpf_hash'] = $cpfHash;

            $admin = Admin::create($adminData);

            Log::info(sprintf(
                "[AdminController][store] Novo administrador registrado com sucesso.",
                [
                    'executado_por' => [
                        'user_id' => $user->id,
                    ],
                    'admin_registrado' => [
                        'admin_id' => $admin->id,
                        'user_id' => $admin->user_id,
                        'nome_completo' => "{$admin->first_name} {$admin->last_name}",
                    ]
                ]
            ));
            return response()->json([
                "error" => false,
                "message" => "Usuário foi registrado como administrador com sucesso!"
            ], 200);
        } catch (Exception $e) {
            Log::error("[AdminController][store] Erro ao registrar administrador.", [
                'error_message' => $e->getMessage(),
                'executado_por' => Auth::user() ? [
                    'user_id' => Auth::user()->id,
                ] : null,
                'request_data' => $request->only('user_id', 'first_name', 'last_name'),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente"
            ]);
        }
    }
}
