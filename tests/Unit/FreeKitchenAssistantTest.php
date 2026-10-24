<?php

namespace Tests\Unit;

use App\Services\Kds\FreeKitchenAssistant;
use PHPUnit\Framework\TestCase;

final class FreeKitchenAssistantTest extends TestCase
{
    private function data(): array
    {
        return ['table'=>'Table 5','items'=>[
            ['name'=>'Chapati','ordered'=>6,'added'=>2,'ready'=>4,'served'=>2,'remaining_to_prepare'=>2],
            ['name'=>'Dal Makhani','ordered'=>1,'added'=>0,'ready'=>1,'served'=>0,'remaining_to_prepare'=>0],
        ]];
    }

    public function test_served_count_uses_actual_record(): void
    {
        $reply=(new FreeKitchenAssistant)->answer('टेबल 5 की कितनी चपाती पहुंची?', $this->data());
        $this->assertTrue($reply['resolved']);
        $this->assertStringContainsString('परोसे 2', $reply['reply']);
        $this->assertStringNotContainsString('Dal Makhani', $reply['reply']);
    }

    public function test_added_is_not_total(): void
    {
        $ai=new FreeKitchenAssistant;
        $this->assertStringContainsString('कुल ऑर्डर 6', $ai->answer('कुल रोटी कितनी?', $this->data())['reply']);
        $this->assertStringContainsString('अतिरिक्त 2', $ai->answer('अतिरिक्त चपाती कितनी?', $this->data())['reply']);
    }

    public function test_kitchen_ready_is_not_waiter_served(): void
    {
        $reply=(new FreeKitchenAssistant)->answer('कितनी रोटी तैयार?', $this->data());
        $this->assertStringContainsString('तैयार 4', $reply['reply']);
    }

    public function test_spoken_instruction_never_executes_without_confirmation(): void
    {
        $snapshot=$this->data();
        $reply=(new FreeKitchenAssistant)->answer('दो चपाती और भेजो', $snapshot);
        $this->assertTrue($reply['needs_confirmation']);
        $this->assertSame(6, $snapshot['items'][0]['ordered']);
    }

    public function test_other_table_is_refused(): void
    {
        $reply=(new FreeKitchenAssistant)->answer('टेबल 8 की कितनी चपाती?', $this->data());
        $this->assertFalse($reply['resolved']);
    }

    public function test_complex_complaint_is_not_guessed_in_free_mode(): void
    {
        $reply=(new FreeKitchenAssistant)->answer('रोटी कच्ची थी, अच्छी पका दो', $this->data());
        $this->assertFalse($reply['resolved']);
    }
}
