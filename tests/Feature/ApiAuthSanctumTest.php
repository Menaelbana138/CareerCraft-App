<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthSanctumTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user(): void
    {
        $user = User::factory()->create([
            'email' => 'tester@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'type' => 'user',
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'email', 'name']]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertSame($user->email, $response->json('user.email'));
    }

    public function test_protected_route_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/user/profile');

        $response->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated']);
    }

    public function test_protected_route_with_valid_token_returns_200(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('mobile')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/user/profile');

        $response->assertOk();
    }
}
