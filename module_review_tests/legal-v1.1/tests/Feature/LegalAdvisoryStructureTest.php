<?php
namespace Tests\Feature;

use App\Support\LegalAdvisory\Feature;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegalAdvisoryStructureTest extends TestCase
{
    public function test_unknown_or_unconfigured_features_are_disabled(): void
    {
        $this->assertFalse(Feature::enabled('invalid-option'));
        $this->assertFalse(Feature::enabled('module'));
        $this->assertFalse(Feature::enabled('hiring'));
    }

    public function test_guests_cannot_manage_vendor_engagements(): void
    {
        $this->post(route('vendor.legal.invite'), [])->assertRedirect(route('login'));
    }

    public function test_professional_portal_requires_authentication(): void
    {
        $this->get(route('legal.professional.index'))->assertRedirect(route('login'));
    }
}
