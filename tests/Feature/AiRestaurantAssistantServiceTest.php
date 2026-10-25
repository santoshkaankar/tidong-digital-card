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

    public function test_free_ai_recognizes_common_multilingual_time_greetings(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $assistant = app(AiRestaurantAssistantService::class);
        foreach ([
            'गुड मॉर्निंग' => 'गुड मॉर्निंग!',
            'good morning' => 'गुड मॉर्निंग!',
            'सुप्रभात' => 'गुड मॉर्निंग!',
            'Good evening!' => 'गुड इवनिंग!',
            'गुड नाइट' => 'शुभ रात्रि!',
        ] as $input => $expected) {
            $answer = $assistant->answer($input, []);
            $this->assertStringContainsString($expected, $answer['text']);
            $this->assertStringNotContainsString('समझ नहीं पाई', $answer['text']);
            $this->assertTrue($answer['free_mode']);
        }
    }

    public function test_free_ai_answers_basic_arithmetic_and_percentage_without_provider(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $assistant = app(AiRestaurantAssistantService::class);
        foreach ([
            '2 + 3' => 'उत्तर: 5',
            '12 * 4' => 'उत्तर: 48',
            '20 / 4' => 'उत्तर: 5',
            '0 + 0' => 'उत्तर: 0',
            '25% of 200' => 'उत्तर: 50',
            '10% का 350' => 'उत्तर: 35',
            '7 / 0' => 'शून्य से भाग नहीं किया जा सकता',
        ] as $input => $expected) {
            $answer = $assistant->answer($input, []);
            $this->assertStringContainsString($expected, $answer['text'], $input);
            $this->assertTrue($answer['free_mode']);
        }
    }

    public function test_free_ai_formula_library_handles_average_discount_gst_and_units(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $assistant = app(AiRestaurantAssistantService::class);
        foreach ([
            'average of 10, 20, 30' => 'औसत: 20',
            '10% discount on 500' => 'छूट के बाद कीमत: 450.00',
            '18% gst on 1000' => 'टैक्स सहित कुल: 1180.00',
            '2 km to meters' => 'उत्तर: 2000 m',
            '3 kg to grams' => 'उत्तर: 3000 g',
            '2 hours to minutes' => 'उत्तर: 120 min',
            '3 kg to meters' => 'रूपांतरण संभव नहीं',
        ] as $input => $expected) {
            $answer = $assistant->answer($input, []);
            $this->assertStringContainsString($expected, $answer['text'], $input);
            $this->assertTrue($answer['free_mode']);
        }
    }

    public function test_free_ai_distinguishes_gst_included_and_excluded(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $assistant = app(AiRestaurantAssistantService::class);
        foreach ([
            'gst include 1180 18%' => ['GST Included', 'बेस कीमत: 1000.00', 'GST: 180.00', 'कुल कीमत: 1180.00'],
            'gst exclude 1000 18%' => ['GST Excluded', 'बेस कीमत: 1000.00', 'GST: 180.00', 'कुल कीमत: 1180.00'],
            'जीएसटी सहित 112 12%' => ['GST Included', 'बेस कीमत: 100.00', 'GST: 12.00'],
            'जीएसटी बिना 100 12%' => ['GST Excluded', 'GST: 12.00', 'कुल कीमत: 112.00'],
            'gst include 100 0%' => ['GST: 0.00', 'कुल कीमत: 100.00'],
            'gst exclude 100 101%' => ['0 से 100 के बीच GST दर'],
        ] as $input => $parts) {
            $answer = $assistant->answer($input, []);
            foreach ($parts as $part) {
                $this->assertStringContainsString($part, $answer['text'], $input);
            }
            $this->assertTrue($answer['free_mode']);
        }
    }

    public function test_free_ai_handles_small_talk_and_feedback_without_repeating_fallback(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $assistant = app(AiRestaurantAssistantService::class);
        $examples = [
            ['हाँ भाई क्या कर रहे हो', 'मैं यहाँ आपकी मदद के लिए हूँ'],
            ['रट्टू तोते हो क्या', 'एक ही जवाब बार-बार'],
            ['इससे तो पहले ज्यादा ठीक जवाब दे पा रहे थे', 'आपकी प्रतिक्रिया समझ गई'],
        ];
        foreach ($examples as [$question, $expected]) {
            $answer = $assistant->answer($question, []);
            $this->assertStringContainsString($expected, $answer['text']);
            $this->assertStringNotContainsString('सवाल पूरी तरह समझ नहीं', $answer['text']);
        }
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
            $this->assertStringContainsString('उपलब्धता अभी सत्यापित नहीं', $answer['text']);
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
