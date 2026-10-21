<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginRateLimitSecurityTest extends TestCase
{
    public function test_sixth_login_request_for_same_email_and_ip_is_throttled(): void
    {
        $email = 'throttle-one@example.test';
        $key = 'login-account:'.hash('sha256', $email.'|127.0.0.1');
        RateLimiter::clear($key);

        // Missing password fails validation without querying the database.
        // Login throttling must apply even to these rejected requests.
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $email])->assertStatus(302);
        }

        $this->post('/login', ['email' => $email])->assertStatus(429);
    }

    public function test_different_email_is_not_blocked_by_per_account_limit(): void
    {
        $email = 'throttle-two@example.test';
        $key = 'login-account:'.hash('sha256', $email.'|127.0.0.1');
        RateLimiter::clear($key);

        $this->post('/login', ['email' => $email])->assertStatus(302);
    }
}
