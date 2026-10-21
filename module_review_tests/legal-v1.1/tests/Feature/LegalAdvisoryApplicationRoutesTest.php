<?php
namespace Tests\Feature;

use Tests\TestCase;

class LegalAdvisoryApplicationRoutesTest extends TestCase
{
    public function test_application_routes_are_private(): void
    {
        $this->get(route('legal.application.index'))->assertRedirect(route('login'));
        $this->post(route('legal.application.store'), [])->assertRedirect(route('login'));
    }

    public function test_verification_reviews_require_admin_login(): void
    {
        $this->get(route('admin.legal.qualifications.index'))->assertRedirect(route('login'));
        $this->post(route('admin.legal.qualifications.decide', 1), [])->assertRedirect(route('login'));
    }
}
