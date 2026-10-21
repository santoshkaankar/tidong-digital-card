<?php

namespace Tests\Feature;

use Tests\TestCase;

class TidongHomepageTest extends TestCase
{
    public function test_guest_can_open_homepage_with_real_login_route(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('One platform.', false)
            ->assertSee(route('login'), false)
            ->assertSee('Restaurant POS', false)
            ->assertSee('Kitchen Display', false)
            ->assertDontSee('Game routes working!', false);
    }
}
