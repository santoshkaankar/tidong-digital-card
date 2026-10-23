<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PublicPasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_forgot_password_page(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_email_and_new_password_work(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => Hash::make('OldPass#123456'),
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect()->assertSessionHas('success');

        $token = null;

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) use (&$token) {
                $token = $notification->token;
                return !empty($token);
            }
        );

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->assertOk();

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPass#123456',
            'password_confirmation' => 'NewPass#123456',
        ])->assertRedirect(route('login'));

        $this->assertTrue(
            Hash::check('NewPass#123456', $user->fresh()->password)
        );

        $this->assertFalse(
            Hash::check('OldPass#123456', $user->fresh()->password)
        );
    }

    public function test_invalid_token_cannot_reset_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPass#123456'),
        ]);

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'NewPass#123456',
            'password_confirmation' => 'NewPass#123456',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(
            Hash::check('OldPass#123456', $user->fresh()->password)
        );
    }
}