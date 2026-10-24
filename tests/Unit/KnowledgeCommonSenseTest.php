<?php

namespace Tests\Unit;

use App\Services\Ai\KnowledgeCommonSense;
use PHPUnit\Framework\TestCase;

final class KnowledgeCommonSenseTest extends TestCase
{
    public function test_hindi_english_and_hinglish_safe_basics(): void
    {
        $engine = new KnowledgeCommonSense();
        $this->assertStringContainsString('नई दिल्ली', $engine->answer('भारत की राजधानी क्या है?')['text']);
        $this->assertStringContainsString('नई दिल्ली', $engine->answer('Capital of India?')['text']);
        $this->assertStringContainsString('H₂O', $engine->answer('water formula kya hai?')['text']);
        $this->assertStringContainsString('चंद्रमा', $engine->answer('पृथ्वी का प्राकृतिक उपग्रह क्या है?')['text']);
    }

    public function test_followup_only_uses_explicit_context(): void
    {
        $engine = new KnowledgeCommonSense();
        $this->assertSame('follow_up', $engine->answer('उसकी राजधानी?', ['last_fact' => 'india_capital'])['intent']);
        $this->assertSame('clarification', $engine->answer('उसकी राजधानी?')['intent']);
    }

    public function test_no_live_fabrication_or_private_data_leak(): void
    {
        $engine = new KnowledgeCommonSense();
        $this->assertSame('needs_live_source', $engine->answer('आज मौसम कैसा है?')['intent']);
        $this->assertSame('needs_authorized_data', $engine->answer('मेरे ऑर्डर का स्टेटस?')['intent']);
        $this->assertFalse($engine->answer('आज मौसम कैसा है?')['verified_live']);
    }

    public function test_empty_unknown_and_oversized_inputs(): void
    {
        $engine = new KnowledgeCommonSense();
        $this->assertSame('clarification', $engine->answer('  ')['intent']);
        $this->assertSame('clarification', $engine->answer('अजीब सवाल')['intent']);
        $this->assertSame('validation', $engine->answer(str_repeat('a', 2001))['intent']);
    }
}
