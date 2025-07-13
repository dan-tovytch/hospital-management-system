<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Admin;
use App\Models\Nurses;
use App\Models\Address;
use App\Models\Patients;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
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

            $address = Address::paginate(10);

            Log::info("[AddressController][index] O usuário {$user->id} listou os endreços");
            return response()->json([
                "error" => false,
                "message" => "sucesso!",
                "data" => $address,
            ]);

        } catch (Exception $e) {
            Log::error("Erro ao puxar a lista de endereço. ERROR: {$e->getMessage()}");
            return response()->json([
                "error" => true,
                "message" => "Erro interno. Tente novamente",
            ], 500);
        }
    }

    public function store(AddressRequest $request)
    {
        try {
            $user = Auth::user();
            $address = Address::create([
                'user_id'       => $user->id,
                'street'        => $request->street,
                'number'        => $request->number,
                'city'          => $request->city,
                'neighborhood'  => $request->neighborhood,
                'state'         => $request->state,
                'cep'           => $request->cep,
            ]);

            $registers = [Admin::class, Nurses::class, Patients::class];

            foreach ($registers as $register) {
                $entity = $register::where("user_id", $user->id)->first();
                if ($entity) {
                    $entity->address_id = $address->id;
                    $entity->save();
                    break;
                }
            }

            Log::info("Endereço registrado com sucesso para o usuário ID: {$user->id}", [
                'user_id'       => $user->id,
                'address_id'    => $address->id,
            ]);
            return response()->json([
                "error"     => false,
                "message"   => "Endereço registrado com sucesso!"
            ], 201);
        } catch (Exception $e) {
            Log::error("Erro ao registrar o endereço para o usuário {$user->id}");
            return response()->json([
                "error"     => true,
                "message"   => 'Erro ao registrar o endereço. Tente novamente'
            ], 500);
        }
    }

    public function update(AddressRequest $request)
    {
        try {

            $user = Auth::user();

            $address = Address::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $address) {
                return response()->json([
                    "error"     => true,
                    "message"   => "Não há endereço registrado"
                ], 404);
            }

            $address->update([
                'street'        => $request->street,
                'number'        => $request->number,
                'city'          => $request->city,
                'neighborhood'  => $request->neighborhood,
                'state'         => $request->state,
                'cep'           => $request->cep,
            ]);

            Log::info("Endereço atualizado com sucesso para o usuário ID: {$user->id}");
            return response()->json([
                "error"     => false,
                "message"   => "Endereço atualizado com sucesso."
            ], 200);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar o endereço', [
                'user_id'   => Auth::id(),
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
                'ip'        => request()->ip(),
            ]);
            return response()->json([
                "error"     => true,
                'message'   => "Erro ao atualizar o endereço. Tente novamente",
            ], 500);
        }
    }
}
