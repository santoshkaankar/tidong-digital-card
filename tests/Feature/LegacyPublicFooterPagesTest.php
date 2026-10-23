<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyPublicFooterPagesTest extends TestCase
{
    /**
     * Visitors and crawlers need access to every information topic in the
     * archived homepage footer without registration or authentication.
     */
    public function test_all_eight_legacy_footer_pages_are_public(): void
    {
        foreach ([
            '/services' => 'Services',
            '/features' => 'Features',
            '/user-guides' => 'User Guides',
            '/sponsored-ads' => 'Sponsored Ads',
            '/about-us' => 'About TIDONG',
            '/terms-overview' => 'Terms overview',
            '/privacy-overview' => 'Privacy overview',
            '/contact' => 'Contact Us',
        ] as $path => $heading) {
            $this->get($path)->assertOk()->assertSee($heading);
        }
    }

    public function test_legacy_help_url_remains_usable_as_user_guide_alias(): void
    {
        $this->get('/help')->assertOk()->assertSee('User Guides');
    }

    public function test_public_pages_have_links_to_original_footer_topics(): void
    {
        $this->get('/about-us')->assertOk()
            ->assertSee('/privacy-overview', false)
            ->assertSee('/terms-overview', false)
            ->assertSee('/contact', false);
    }
}
