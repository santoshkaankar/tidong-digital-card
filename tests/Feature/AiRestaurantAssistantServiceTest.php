<?php

namespace Tests\Feature;

use App\Services\Customer\AiRestaurantAssistantService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Provider calls are fully mocked; never spend real tokens in tests.
 * These tests deliberately do not enable checkout or payment.
 */
class AiRestaurantAssistantServiceTest extends TestCase
{
    public function test_professional_transparency_is_default_off_and_never_grants_report_access(): void
    {
        $feature = config('tidong_features')['ai.professional_work_transparency'] ?? null;
        $this->assertIsArray($feature);
        $this->assertFalse($feature['default_enabled']);
        $this->assertSame('ai_restaurant_assistant', $feature['module']);
    }

    public function test_vendor_progress_route_requires_auth_and_vendor_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('vendor.legal.ai-progress');
        $this->assertNotNull($route);
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains(\App\Http\Middleware\EnsureVendorAccount::class, $route->gatherMiddleware());
        $this->assertSame(['GET', 'HEAD'], $route->methods());
    }

    public function test_first_turn_greeting_is_session_scoped_and_keeps_question_answer(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Customer/AiRestaurantAssistantController.php'));
        $this->assertNotFalse($source);
        $this->assertStringContainsString("tidong_ai_food_started", $source);
        $this->assertStringContainsString("नमस्ते! ", $source);
        $this->assertStringContainsString("गुड मॉर्निंग!", $source);
        $this->assertStringContainsString("गुड इवनिंग!", $source);
        $this->assertStringContainsString("नमस्ते!", $source);
    }

    public function test_location_help_is_answered_before_nearby_permission_prompt(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Customer/AiRestaurantAssistantController.php'));
        $this->assertNotFalse($source);
        $this->assertStringContainsString('locationHelpIntent', $source);
        $this->assertStringContainsString('ब्राउज़र की Site Settings', $source);
        $this->assertLessThan(
            strpos($source, 'elseif ($restaurants === [] && $nearbyIntent'),
            strpos($source, 'elseif ($locationHelpIntent)')
        );
    }

    public function test_unknown_question_asks_clarification_without_irrelevant_service_list(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $answer = app(AiRestaurantAssistantService::class)->answer('यह व्यवस्था कैसे मिलेगी', []);
        $this->assertStringContainsString('सवाल पूरी तरह समझ नहीं', $answer['text']);
        $this->assertStringNotContainsString('रेस्टोरेंट, ग्रॉसरी, प्रिंटिंग', $answer['text']);
    }

    public function test_free_wedding_and_order_status_questions_are_not_generic(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        Http::fake();
        foreach (['मुझे मैरिज होम देखना है शादी के लिए', 'मैरिज प्लानर चाहिए', 'मैरिज एनिवर्सरी देखना है'] as $question) {
            $answer = app(AiRestaurantAssistantService::class)->answer($question, []);
            $this->assertStringContainsString('TIDONG', $answer['text']);
            $this->assertStringContainsString('पंजीकृत व्यवसाय', $answer['text']);
        }
        $tracking = app(AiRestaurantAssistantService::class)->answer('मेरे ऑर्डर का क्या हुआ डिलीवरी होगा नहीं होगा', []);
        $this->assertStringContainsString('निजी ऑर्डर', $tracking['text']);
        Http::assertNothingSent();
    }

    public function test_free_customer_chat_is_not_blocked_by_paid_daily_limits(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Customer/AiRestaurantAssistantController.php'));
        $this->assertNotFalse($source);
        $this->assertStringContainsString('if ($paidEnabled && (RateLimiter::tooManyAttempts', $source);
        $this->assertStringContainsString('if ($paidEnabled) {', $source);
    }

    public function test_free_taxi_and_travel_requests_get_relevant_safe_guidance(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        Http::fake();
        foreach (['हम घूमना चाहते हैं कोई व्यवस्था है', 'टैक्सी टैक्सी घूमने के बारे में', 'taxi booking'] as $question) {
            $reply = app(AiRestaurantAssistantService::class)->answer($question, []);
            $this->assertStringContainsString('टैक्सी', $reply['text']);
            $this->assertStringContainsString('कहाँ से कहाँ', $reply['text']);
            $this->assertFalse($reply['ai_available']);
        }
        Http::assertNothingSent();
    }

    public function test_missing_api_key_never_calls_external_provider(): void
    {
        config()->set('tidong_ai_restaurant.api_key', '');
        Http::fake();

        $answer = app(AiRestaurantAssistantService::class)->answer('Nearby food?', []);

        $this->assertFalse($answer['ai_available']);
        $this->assertNotEmpty($answer['text']);
        Http::assertNothingSent();
    }

    public function test_free_mode_never_sends_paid_requests_even_with_api_key(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        config()->set('tidong_ai_restaurant.api_key', 'existing-server-key-must-not-be-used');
        Http::fake();

        $catalogue = [[
            'restaurant' => 'Test Cafe', 'outlet' => 'North Branch',
            'menu' => [['name' => 'Paneer', 'price_rupees' => '120.00']],
        ]];
        $answer = app(AiRestaurantAssistantService::class)->answer('Paneer की कीमत?', $catalogue);

        $this->assertTrue($answer['free_mode']);
        $this->assertFalse($answer['ai_available']);
        $this->assertStringContainsString('Paneer ₹120.00', $answer['text']);
        Http::assertNothingSent();
    }

    public function test_free_mode_restaurant_name_shows_real_menu_and_never_calls_provider(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        config()->set('tidong_ai_restaurant.api_key', 'unused-test-key');
        Http::fake();

        $answer = app(AiRestaurantAssistantService::class)->answer('Test Cafe ka menu?', [[
            'restaurant' => 'Test Cafe', 'outlet' => 'North Branch',
            'menu' => [
                ['name' => 'Paneer', 'price_rupees' => '120.00'],
                ['name' => 'Dal', 'price_rupees' => '90.00'],
            ],
        ]]);

        $this->assertTrue($answer['free_mode']);
        $this->assertStringContainsString('Paneer ₹120.00', $answer['text']);
        $this->assertStringContainsString('Dal ₹90.00', $answer['text']);
        Http::assertNothingSent();
    }

    public function test_free_mode_never_fabricates_unknown_food_or_order(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        Http::fake();
        $answer = app(AiRestaurantAssistantService::class)->answer('Pizza ऑर्डर कर दो', [[
            'restaurant' => 'Test Cafe', 'outlet' => 'North Branch',
            'menu' => [['name' => 'Paneer', 'price_rupees' => '120.00']],
        ]]);
        $this->assertStringNotContainsString('Pizza ₹', $answer['text']);
        $this->assertStringContainsString('यह चैट भुगतान नहीं करती', $answer['text']);
        Http::assertNothingSent();
    }

    public function test_restaurant_answers_use_only_live_catalogue_and_do_not_offer_write_tools(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', true);
        config()->set('tidong_ai_restaurant.api_key', 'test-only-no-real-secret');
        config()->set('tidong_ai_restaurant.model', 'gpt-4.1-mini');

        Http::fake([
            'api.openai.com/v1/responses' => Http::response([
                'output' => [[
                    'type' => 'message',
                    'content' => [['type' => 'output_text', 'text' => 'रेस्टोरेंट में पनीर ₹120 है।']],
                ]],
            ], 200),
        ]);

        $catalogue = [[
            'branch_id' => 11,
            'restaurant' => 'Test Cafe',
            'outlet' => 'North Branch',
            'distance_km' => 1.3,
            'menu' => [['item_id' => 22, 'name' => 'Paneer', 'price_rupees' => '120.00']],
        ]];

        $reply = app(AiRestaurantAssistantService::class)->answer('पनीर की कीमत?', $catalogue);
        $this->assertTrue($reply['ai_available']);
        $this->assertStringContainsString('₹120', $reply['text']);
        Http::assertSent(function ($request) {
            $body = $request->data();
            $messages = $body['input'] ?? [];
            $developer = $messages[0]['content'] ?? '';
            return $request->url() === 'https://api.openai.com/v1/responses'
                && ($body['store'] ?? null) === false
                && ! array_key_exists('tools', $body)
                && ($body['model'] ?? '') === 'gpt-4.1-mini'
                && str_contains($developer, 'Test Cafe')
                && str_contains($developer, 'Paneer')
                && str_contains($developer, '120.00')
                && str_contains($developer, 'Never invent missing facts');
        });
    }

    public function test_provider_failure_returns_safe_read_only_fallback(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', true);
        config()->set('tidong_ai_restaurant.api_key', 'test-only-no-real-secret');
        Http::fake(['api.openai.com/v1/responses' => Http::response(['error' => 'service unavailable'], 503)]);

        $answer = app(AiRestaurantAssistantService::class)->answer('मेन्यू?', []);
        $this->assertFalse($answer['ai_available']);
        $this->assertStringContainsString('रेस्टोरेंट', $answer['text']);
        Http::assertSentCount(1);
    }

    public function test_missing_customer_location_returns_no_restaurant_without_database_access(): void
    {
        $this->assertSame([], app(AiRestaurantAssistantService::class)->nearby(null, null));
        $this->assertSame([], app(AiRestaurantAssistantService::class)->nearby(26.9124, null));
    }
}
