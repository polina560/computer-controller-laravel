<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1((string) $user->email)]
        );

        // $response = $this->actingAs($user)->get($verificationUrl);
        $response = $this->actingAs($user)->getJson($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        // $response->assertRedirect(config('app.frontend_url').'/dashboard?verified=1');
        $response->assertStatus(200)
            ->assertJson(['status' => 'Успешно подтверждено']);
    }

    public function test_already_verified_email(): void
    {
        $user = User::factory()->create(); // По умолчанию verified

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1((string) $user->email)]
        );

        $response = $this->actingAs($user)->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson(['status' => 'Уже подтверждено']);
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        // $this->actingAs($user)->get($verificationUrl);
        $response = $this->actingAs($user)->getJson($verificationUrl);

        // $this->assertFalse($user->fresh()->hasVerifiedEmail());

        $response->assertStatus(403); // Ожидаем ошибку Forbidden
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
