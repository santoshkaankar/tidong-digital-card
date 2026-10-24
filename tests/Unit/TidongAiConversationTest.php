<?php

namespace Tests\Unit;

use App\Support\TidongAiConversation;
use PHPUnit\Framework\TestCase;

final class TidongAiConversationTest extends TestCase
{
    public function test_welcome_is_only_for_new_chat(): void
    {
        self::assertStringStartsWith('नमस्ते', TidongAiConversation::welcomeIfNew(false));
        self::assertNull(TidongAiConversation::welcomeIfNew(true));
    }

    public function test_identity_and_greetings(): void
    {
        self::assertStringContainsString('TIDONG AI', TidongAiConversation::reply('तुम्हारा नाम क्या है?'));
        self::assertSame('हैलो!', TidongAiConversation::reply('हैलो'));
        self::assertSame('आपका स्वागत है!', TidongAiConversation::reply('धन्यवाद'));
    }

    public function test_unknown_question_is_not_misclassified(): void
    {
        self::assertNull(TidongAiConversation::reply('पास में RO वाटर प्लांट है?'));
        self::assertNull(TidongAiConversation::reply('मुझे चार्टर्ड अकाउंटेंट चाहिए'));
    }
}
