<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use Exception;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function store(AddressRequest $request)
    {
        try {
            $user = Auth::user();
            Address::create([
                'user_id'       => $user->id,
                'street'        => $request->street,
                'number'        => $request->number,
                'city'          => $request->city,
                'neighborhood'  => $request->neighborhood,
                'state'         => $request->state,
                'cep'           => $request->cep,
            ]);

            Log::info('Success: ' . "O usuário " . $user->id . ' registrou seu endereço');
            return response()->json(["message" => "Endereço registrado com sucesso!"], 201);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json([
                "message" => 'Erro ao registrar o endereço. Tente novamente'
            ], 500);
        }
    }

    public function update(AddressRequest $request) {
        try {

            $user = Auth::user();

            Address::where('user_id', $user->id)->update([
                'street'        => $request->street,
                'number'        => $request->number,
                'city'          => $request->city,
                'neighborhood'  => $request->neighborhood,
                'state'         => $request->state,
                'cep'           => $request->cep,
            ]);

            Log::info('Usuário ' . $user->id . " Atualizou o endereço");
            return response()->json([
                "message" => "Endereço atualizado com sucesso."
            ], 200);
        } catch (Exception $e) {
            Log::error("Erro ao atualizar o endereço: " . $e->getMessage());
            return response()->json([
                'message' => "Erro ao atualizar o endereço. Tente novamente",
            ], 500);
        }
    }
}
