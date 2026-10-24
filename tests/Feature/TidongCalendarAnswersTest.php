<?php

namespace Tests\Feature;

use App\Services\Customer\TidongCalendarAnswers;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class TidongCalendarAnswersTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_today_and_four_days_later_in_india(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-24 23:00:00', 'Asia/Kolkata'));
        $this->assertStringContainsString('शनिवार', TidongCalendarAnswers::answer('आज कौन सा वार है?'));
        $this->assertStringContainsString('28 अक्टूबर 2026, बुधवार', TidongCalendarAnswers::answer('चार दिन बाद क्या है?'));
    }

    public function test_fixed_national_days_are_computed_for_next_occurrence(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-24 12:00:00', 'Asia/Kolkata'));
        $this->assertStringContainsString('15 अगस्त', TidongCalendarAnswers::answer('स्वतंत्रता दिवस कब है?'));
        $this->assertStringContainsString('26 जनवरी', TidongCalendarAnswers::answer('गणतंत्र दिवस कब है?'));
        $this->assertStringContainsString('2 अक्टूबर', TidongCalendarAnswers::answer('गांधी जयंती कब है?'));
    }

    public function test_lunar_festivals_do_not_invent_dates(): void
    {
        $this->assertStringContainsString('पंचांग', TidongCalendarAnswers::answer('गुरु नानक जयंती कब है?'));
        $this->assertNull(TidongCalendarAnswers::answer('मुझे रेस्टोरेंट चाहिए'));
    }
}
