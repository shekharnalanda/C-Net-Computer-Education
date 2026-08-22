<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityAndMobileTest extends TestCase
{
    public function test_public_response_has_security_headers(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_admin_pages_are_marked_private(): void
    {
        $this->get('/admin/login')->assertHeader('Cache-Control');
    }

    public function test_homepage_contains_accessible_mobile_navigation(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('mobileMenuToggle', false)
            ->assertSee('aria-expanded="false"', false)
            ->assertSee('siteNavigation', false);
    }

    public function test_admission_page_contains_mobile_navigation_and_csrf_protection(): void
    {
        $response = $this->get('/apply-online');

        $response->assertOk()
            ->assertSee('mobileMenuToggle', false)
            ->assertSee('_token', false);
    }
}
