<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        // $user = User::factory()->create();
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // $this->assertAuthenticated();
        // $response->assertNoContent();

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'name',
                    'email',
                ],
            ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        // $this->post('/api/login', ['email' => $user->email, 'password' => 'wrong-password',]);
        // $this->assertGuest();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken; // new

        // $response = $this->actingAs($user)->post('/api/logout');

        // $this->assertGuest();
        // $response->assertNoContent();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertNoContent();
        $this->assertEmpty($user->fresh()->tokens); // Проверяем, что токены удалены
    }
}
