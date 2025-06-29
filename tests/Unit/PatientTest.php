<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use Database\Seeders\ProfileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProfileSeeder::class);
    }

    public function test_register_user_patient(): void
    {
        $user = User::factory()->create();

        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $payload = [
            'first_name' => 'Fulano',
            'last_name' => 'Ciclano',
            'cpf' => '12345678900',
            'phone_number' => '11999999999',
            'date_birth' => '2000-01-01',
        ];

        $response = $this->postJson('api/patients/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                "message" => "Conta criada com sucesso!",
            ]);

        $this->assertDatabaseHas('patients', [
            'user_id' => $user->id,
            "first_name" => "Fulano"
        ]);
    }

    public function test_update_user_patient() {
        
    }
}
