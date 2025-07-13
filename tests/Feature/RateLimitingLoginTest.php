<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\ProfileSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Profiler\Profile;

class RateLimitingLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProfileSeeder::class);
    }

    public function test_rate_limiting_login(): void
    {
        $this->seed(ProfileSeeder::class);

        $email = "teste@teste.com";

        User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Teste@123'),
        ]);

        $loginRoute = '/api/login';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson($loginRoute, [
                "email" => $email,
                "password" => "Teste@123"
            ]);

            // espera 200 OK para sucesso ou 401 para falha dependendo da sua app
            $response->assertStatus(200);
        }

        // agora a 6ª tentativa deve bater o rate limit (429)
        $response = $this->postJson($loginRoute, [
            'email' => $email,
            "password" => "Teste@123"
        ]);

        $response->assertStatus(429);
    }
}
