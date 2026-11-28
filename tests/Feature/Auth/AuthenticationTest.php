<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_and_use_a_bearer_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure(['token', 'user' => ['id', 'role', 'email']])
            ->assertJsonMissingPath('user.password_hash');

        $this->withToken($response->json('token'))
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_inactive_and_invalid_credentials_are_rejected_generically(): void
    {
        $user = User::factory()->inactive()->create();

        $inactiveResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $invalidResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $missingResponse = $this->postJson('/api/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ]);

        $inactiveResponse->assertUnprocessable()->assertExactJson($invalidResponse->json());
        $missingResponse->assertUnprocessable()->assertExactJson($invalidResponse->json());
    }

    public function test_protected_endpoint_rejects_missing_and_invalid_tokens(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
        $this->withToken('invalid-token')->getJson('/api/user')->assertUnauthorized();
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = User::factory()->create();
        $currentToken = $user->createToken('current')->plainTextToken;
        $otherToken = $user->createToken('other')->plainTextToken;

        $this->withToken($currentToken)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => (int) explode('|', $currentToken, 2)[0],
        ]);
        Auth::forgetGuards();
        $this->withToken($currentToken)->getJson('/api/user')->assertUnauthorized();
        $this->withToken($otherToken)->getJson('/api/user')->assertOk();
    }

    public function test_deactivating_a_user_revokes_existing_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('existing')->plainTextToken;

        $user->update(['is_active' => false]);

        $this->withToken($token)->getJson('/api/user')->assertUnauthorized();
    }
}
