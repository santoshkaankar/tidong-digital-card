<?php

namespace Tests\Unit;

use App\Services\Customer\AiRestaurantAssistantService;
use PHPUnit\Framework\TestCase;

class AiRestaurantAssistantGeometryTest extends TestCase
{
    public function test_same_place_is_zero_kilometres(): void
    {
        $this->assertEqualsWithDelta(0.0,
            AiRestaurantAssistantService::distanceKm(26.9124, 75.7873, 26.9124, 75.7873), 0.000001);
    }

    public function test_nearby_distance_is_symmetric_and_positive(): void
    {
        $a = AiRestaurantAssistantService::distanceKm(26.9124, 75.7873, 26.9324, 75.7973);
        $b = AiRestaurantAssistantService::distanceKm(26.9324, 75.7973, 26.9124, 75.7873);
        $this->assertGreaterThan(2, $a);
        $this->assertLessThan(3, $a);
        $this->assertEqualsWithDelta($a, $b, 0.000001);
    }
}
