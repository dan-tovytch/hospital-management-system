<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(AuthRequest $request)
    {
        try {
            User::create([
                'id' => Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_id' => 1,
            ]);
            Log::info('Novo registro', ['message' => 'Novo usuario registrado']);
            return response()->json(['message' => 'Conta criada com sucesso!'], 201);
        } catch (Exception $e) {
            Log::error('Erro no registro', ['erro' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao criar a conta. Tente novamente'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
                ],
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => [' As credenciais fornecidas estão incorretas.'],
                ]);
            }

            if (! Hash::check($request->password, $user?->password)) {
                throw ValidationException::withMessages([
                    'password' => [' As credenciais fornecidas estão incorretas.'],
                ]);
            }

            $user->update(['last_login' => now()]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "error" => false,
                'message' => 'Logado com sucesso',
                'token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                "error" => true,
                "message" => $e->getMessage(),
                "errors" => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error("Erro no login", ["erro" => $e->getMessage()]);
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente"
            ], 500);
        }
    }
}
