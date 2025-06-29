<?php

namespace App\Http\Controllers;

use App\Http\Requests\NurseRequest;
use Exception;
use App\Models\Address;
use App\Models\Nurses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NursesController extends Controller
{
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
            $profile = $this->getUserProfile($user->id);

            if($profile?->profile_id !== 3) {
                return response()->json([
                    "error" => true,
                    "message" => "Você não tem permissão para realizar o registro"
                ], 500);
            }

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

            Log::info("O usuario" . $user->id . " se registrou com enfermeiro");
            return response()->json([
                "error" => false,
                "message" => "Conta criada com sucesso!",
            ], 200);
        } catch (Exception $e) {
            Log::error("Error" . $e->getMessage());
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getUserAddress($user_id)
    {
        return Address::where("user_id", $user_id)->first();
    }
    private function getUserProfile($user_id)
    {
        return User::where("id", $user_id)->first();
    }
}
