<?php

namespace Tests\Feature;

use App\Models\DataPlan;
use App\Models\Faq;
use App\Models\NetworkSetting;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SystemSetting::set('platform_name', 'BJ Data Sub');
        SystemSetting::set('primary_color', '#4A0210');
        SystemSetting::set('support_phone', '+2348001234567');
        SystemSetting::set('support_whatsapp', '+2348123456789');
        SystemSetting::set('support_email', 'support@bjdatasub.ng');
        SystemSetting::set('hero_title', 'Instant Airtime, Cheap Data & Everyday Bills in Seconds');
    }

    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('BJ Data Sub');
        $response->assertSee('Instant Airtime, Cheap Data & Everyday Bills in Seconds');
        $response->assertSee('support@bjdatasub.ng');
    }

    public function test_landing_page_updates_dynamically_when_admin_changes_settings(): void
    {
        SystemSetting::set('platform_name', 'SwiftVTU Nigeria');
        SystemSetting::set('hero_title', 'The Ultimate Fintech Telecom Engine');
        SystemSetting::set('support_phone', '+2347011112222');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SwiftVTU Nigeria');
        $response->assertSee('The Ultimate Fintech Telecom Engine');
        $response->assertSee('+2347011112222');
    }

    public function test_landing_page_displays_active_faqs(): void
    {
        Faq::create([
            'question' => 'Is registration free?',
            'answer' => 'Yes, creating an account is completely free.',
            'status' => 'active',
            'order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Is registration free?');
        $response->assertSee('Yes, creating an account is completely free.');
    }

    public function test_privacy_and_terms_pages_load(): void
    {
        $resPrivacy = $this->get('/privacy');
        $resPrivacy->assertStatus(200);
        $resPrivacy->assertSee('Privacy Policy');

        $resTerms = $this->get('/terms');
        $resTerms->assertStatus(200);
        $resTerms->assertSee('Terms of Service');
    }

    public function test_registration_open_allows_signup(): void
    {
        SystemSetting::set('allow_registration', 'open');

        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create your account');

        $postResponse = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'phone' => '08012345678',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => '1',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_registration_closed_blocks_web_and_api_signup(): void
    {
        SystemSetting::set('allow_registration', 'closed');
        SystemSetting::set('registration_closed_message', 'Signups are closed for maintenance.');

        // 1. Web GET shows closed notice
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Registration Temporarily Closed');
        $response->assertSee('Signups are closed for maintenance.');

        // 2. Web POST rejected
        $postResponse = $this->post('/register', [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'phone' => '08099999999',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => '1',
        ]);
        $postResponse->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);

        // 3. API POST rejected with 403
        $apiResponse = $this->postJson('/api/register', [
            'name' => 'API Blocked User',
            'email' => 'apiblocked@example.com',
            'phone' => '08088888888',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $apiResponse->assertStatus(403);
        $apiResponse->assertJsonFragment(['status' => 'error']);
        $this->assertDatabaseMissing('users', ['email' => 'apiblocked@example.com']);
    }
}
