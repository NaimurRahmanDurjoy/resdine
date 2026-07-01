<?php

namespace Tests\Unit;

use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\RecipeService;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    public function test_it_returns_a_customer_friendly_stock_message(): void
    {
        $service = new OrderService(
            $this->createMock(RecipeService::class),
            $this->createMock(NotificationService::class)
        );

        $message = $service->buildStockUnavailableMessage([
            'Chicken (Missing 263.158 gm)',
            'Egg (Missing 1 pcs)',
        ]);

        $this->assertSame(
            "We're sorry, this order can't be completed right now because some items are currently unavailable. Please try again shortly or choose a different item.",
            $message
        );
        $this->assertStringNotContainsString('Chicken', $message);
        $this->assertStringNotContainsString('263.158', $message);
    }
}
