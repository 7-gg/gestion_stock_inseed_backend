<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\LoginCodeMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    /**
     * Test: Demander un code de connexion avec un email existant
     */
    public function test_request_login_code_with_existing_email(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        Mail::fake();

        $response = $this->postJson('/api/auth/request-code', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Si votre email est enregistré, vous recevrez un code de connexion.',
        ]);

        // Vérifier que l'email a été envoyé
        Mail::assertSent(LoginCodeMail::class);

        // Vérifier que le code a été généré et stocké
        $user->refresh();
        $this->assertNotNull($user->login_code_expires_at);
        $this->assertEquals(0, $user->login_attempts);
    }

    /**
     * Test: Demander un code avec un email inexistant (ne pas révéler)
     */
    public function test_request_login_code_with_non_existing_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/request-code', [
            'email' => 'nonexistent@example.com',
        ]);

        // La même réponse doit être renvoyée
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Si votre email est enregistré, vous recevrez un code de connexion.',
        ]);

        // Vérifier qu'aucun email n'a été envoyé
        Mail::assertNothingSent();
    }

    /**
     * Test: Vérifier un code valide
     */
    public function test_verify_login_code_success(): void
    {
        $user = User::factory()->create();
        $code = '123456';

        // Simuler un code généré et stocké
        $user->update([
            'password' => Hash::make($code),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
            'login_attempts' => 0,
        ]);

        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => $code,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);

        // Vérifier que le code a été supprimé
        $user->refresh();
        $this->assertNull($user->password);
        $this->assertNull($user->login_code_expires_at);
        $this->assertEquals(0, $user->login_attempts);
    }

    /**
     * Test: Code expiré
     */
    public function test_verify_login_code_expired(): void
    {
        $user = User::factory()->create();
        $code = '123456';

        // Simuler un code expiré
        $user->update([
            'password' => Hash::make($code),
            'login_code_expires_at' => Carbon::now()->subMinutes(5),
            'login_attempts' => 0,
        ]);

        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => $code,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Code expiré. Veuillez demander un nouveau code.',
        ]);

        // Vérifier que le code a été nettoyé
        $user->refresh();
        $this->assertNull($user->password);
    }

    /**
     * Test: Code incorrect
     */
    public function test_verify_login_code_incorrect(): void
    {
        $user = User::factory()->create();

        $user->update([
            'password' => Hash::make('123456'),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
            'login_attempts' => 0,
        ]);

        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => 'wrong_code',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Code incorrect. Il vous reste 2 tentative(s).',
        ]);

        // Vérifier que le compteur de tentatives a été incrémenté
        $user->refresh();
        $this->assertEquals(1, $user->login_attempts);
    }

    /**
     * Test: Trop de tentatives échouées
     */
    public function test_verify_login_code_too_many_attempts(): void
    {
        $user = User::factory()->create();

        $user->update([
            'password' => Hash::make('123456'),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
            'login_attempts' => 3, // Déjà 3 tentatives
        ]);

        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => 'wrong_code',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Trop de tentatives échouées. Veuillez demander un nouveau code.',
        ]);
    }

    /**
     * Test: Validation - email requis
     */
    public function test_request_code_validation_email_required(): void
    {
        $response = $this->postJson('/api/auth/request-code', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Test: Validation - email format valide
     */
    public function test_request_code_validation_email_format(): void
    {
        $response = $this->postJson('/api/auth/request-code', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Test: Validation - code requis
     */
    public function test_verify_code_validation_code_required(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('code');
    }

    /**
     * Test: Validation - code doit être 6 chiffres
     */
    public function test_verify_code_validation_code_digits(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => 'abcdef', // Non-numérique
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('code');

        // Trop court
        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => '12345', // Seulement 5 chiffres
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('code');
    }

    /**
     * Test: Accéder aux ressources protégées avec le token
     */
    public function test_access_protected_endpoint_with_token(): void
    {
        $user = User::factory()->create();
        $code = '123456';

        $user->update([
            'password' => Hash::make($code),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
            'login_attempts' => 0,
        ]);

        // Vérifier le code et obtenir le token
        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => $code,
        ]);

        $token = $response->json('data.token');

        // Utiliser le token pour accéder à une ressource protégée
        $response = $this->getJson('/api/me', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Test: Logout invalide le token
     */
    public function test_logout_invalidates_token(): void
    {
        $user = User::factory()->create();
        $code = '123456';

        $user->update([
            'password' => Hash::make($code),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
            'login_attempts' => 0,
        ]);

        // Vérifier le code et obtenir le token
        $response = $this->postJson('/api/auth/verify-code', [
            'email' => $user->email,
            'code' => $code,
        ]);

        $token = $response->json('data.token');

        // Se déconnecter
        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);

        // Le token ne doit plus fonctionner
        $response = $this->getJson('/api/me', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(401);
    }
}
