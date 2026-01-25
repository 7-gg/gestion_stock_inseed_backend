<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $resp = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $resp->assertStatus(200)
            ->assertJsonStructure(['status', 'message', 'data' => ['token', 'token_type', 'expires_in', 'user']]);

        $this->assertArrayHasKey('token', $resp->json('data'));
    }

    public function test_logout_revokes_current_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $login = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $login->json('data.token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/logout')
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // refresh user and assert no active tokens
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_me_requires_authentication()
    {
        $this->getJson('/me')->assertStatus(401);
    }

    public function test_me_returns_user_when_authenticated()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $login = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $login->json('data.token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/me')
            ->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    }
}
