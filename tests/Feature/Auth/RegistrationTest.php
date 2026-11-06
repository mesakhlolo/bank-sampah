<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_user_creation_form(): void
    {
        $this->get('/register')->assertRedirect('/login');
    }

    public function test_guests_cannot_create_users(): void
    {
        $this->post('/register', $this->validPayload())->assertRedirect('/login');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_operators_cannot_view_the_user_creation_form(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator)->get('/register')->assertForbidden();
    }

    public function test_operators_cannot_create_users(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator)
            ->post('/register', $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_admins_can_view_the_user_creation_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/register')
            ->assertOk()
            ->assertSee('Tambah Pengguna');
    }

    public function test_admins_can_create_a_user_with_an_explicit_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/register', $this->validPayload());

        $response->assertRedirect(route('register'))
            ->assertSessionHas('success');

        $user = User::where('email', 'new-user@example.com')->firstOrFail();

        $this->assertSame('operator', $user->role);

        // The admin stays logged in; the new account is not auto-authenticated.
        $this->assertAuthenticatedAs($admin);
    }

    public function test_role_is_required(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $payload = $this->validPayload();
        unset($payload['role']);

        $this->actingAs($admin)
            ->post('/register', $payload)
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_role_must_be_admin_or_operator(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/register', $this->validPayload(['role' => 'owner']))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_admins_can_create_another_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/register', $this->validPayload(['role' => 'admin']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'new-user@example.com',
            'role' => 'admin',
        ]);
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'new-user@example.com',
            'role' => 'operator',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }
}
