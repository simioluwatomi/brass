<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_users_can_not_log_in()
    {
        $this->expectException(AuthorizationException::class);

        Sanctum::actingAs($this->user);

        $this->postJson(route('api.login'));
    }

    /** @test */
    public function registered_guest_users_can_log_in()
    {
        $this->postJson(route('api.login'), [
            'email'    => $this->user->email,
            'password' => 'password',
        ])
            ->assertCreated()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'user',
                    'token',
                    'expires_in'
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => $this->user->getMorphClass(),
            'tokenable_id' => $this->user->id
        ]);
    }

    /** @test */
    public function guest_users_can_not_log_out()
    {
        $this->expectException(AuthenticationException::class);

        $this->postJson(route('api.logout'));
    }

    /** @test */
    public function authenticated_users_can_log_out()
    {
        $this->user->createToken(config('auth.token.name'))->accessToken;

        Sanctum::actingAs($this->user);

        $this->postJson(route('api.logout'))
            ->assertNoContent();
    }
}
