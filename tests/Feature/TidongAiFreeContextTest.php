<?php

namespace Tests\Feature;

use App\Services\Customer\AiRestaurantAssistantService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TidongAiFreeContextTest extends TestCase
{
    public function test_ca_follow_up_remembers_customer_topic_without_paid_api(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        Http::fake();
        $history = [
            ['role' => 'user', 'content' => 'मुझे व्यवसाय के लिए चार्टर्ड अकाउंटेंट चाहिए'],
            ['role' => 'assistant', 'content' => 'किस काम के लिए?'],
        ];
        $reply = app(AiRestaurantAssistantService::class)->answer('उसकी फीस कितनी होगी?', [], $history);
        $this->assertStringContainsString('चार्टर्ड अकाउंटेंट', $reply['text']);
        $this->assertStringNotContainsString('रेस्टोरेंट', $reply['text']);
        Http::assertNothingSent();
    }

    public function test_explicit_topic_switch_does_not_reuse_old_ca_topic(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $history = [['role' => 'user', 'content' => 'चार्टर्ड अकाउंटेंट चाहिए']];
        $reply = app(AiRestaurantAssistantService::class)->answer('अब मुझे शादी के लिए मैरिज गार्डन चाहिए', [], $history);
        $this->assertStringContainsString('शादी', $reply['text']);
        $this->assertStringNotContainsString('चार्टर्ड अकाउंटेंट', $reply['text']);
    }

    public function test_unrelated_follow_up_does_not_invent_context(): void
    {
        config()->set('tidong_ai_restaurant.paid_provider_enabled', false);
        $reply = app(AiRestaurantAssistantService::class)->answer('उसकी फीस कितनी होगी?', []);
        $this->assertStringNotContainsString('चार्टर्ड अकाउंटेंट', $reply['text']);
    }
}
