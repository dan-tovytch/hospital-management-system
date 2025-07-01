<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function updatePassword(Request $request)
    {
        try {

            $user = User::find(Auth::id());

            $request->validate(
                [
                    'current_password' => "required",
                    'password' => [
                        'required',
                        'string',
                        'min:8',
                        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
                    ],
                ],
                [
                    'password.required' => "Precisa informar uma nova senha!",
                    'password.min'      => "A senha precisa ter no minímo 8 caracteres",
                    'password.regex'    => 'A senha precisa conter ao menos uma letra maiúscula, uma minúscula, um número e um caractere especial.',
                ]
            );

            if (! Hash::check($request->current_password, $user?->password)) {
                return response()->json([
                    "error" => true,
                    "message" => "Senha atual incorreta."
                ], 403);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            Log::info("[UserController][updatePassword] Usuário {$user->id} alterou a senha com sucesso.");
            return response()->json([
                "error" => false,
                "message" => "Senha alterada com sucesso!"
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao alterar a senha do usuário: " . $e->getMessage());
            return response()->json([
                "error" => true,
                "message" => "Erro ao alterar a senha. Tente novamente"
            ], 500);
        }
    }
}
