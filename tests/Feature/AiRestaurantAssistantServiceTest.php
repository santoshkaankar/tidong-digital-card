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

    public function test_free_mode_never_fabricates_unknown_food_or_order(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        Http::fake();
        $answer = app(AiRestaurantAssistantService::class)->answer('Pizza ऑर्डर कर दो', [[
            'restaurant' => 'Test Cafe', 'outlet' => 'North Branch',
            'menu' => [['name' => 'Paneer', 'price_rupees' => '120.00']],
        ]]);
        $this->assertStringNotContainsString('Pizza ₹', $answer['text']);
        $this->assertStringContainsString('ऑर्डर या भुगतान नहीं किया गया', $answer['text']);
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
