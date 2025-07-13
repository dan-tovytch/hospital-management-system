<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\ProfileSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProfileSeeder::class);
    }

    public function test_register_user_in_database(): void
    {

        $response = $this->postJson('/api/register', [
            'email' => 'teste@teste.com',
            'password' => 'Senha123!',
        ]);

        $response->assertStatus(201)
            ->assertJson(["message" => "Conta criada com sucesso!"]);
    }

    public function test_user_does_not_log()
    {

        $this->seed(ProfileSeeder::class);

        User::factory()->create([
            'email' => 'teste@teste.com',
            'password' => Hash::make('Senha123!'),
            'profile_id' => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'teste@teste.com',
            'password' => 'Errada123!',
        ]);

        $response->assertStatus(422);
    }
}
