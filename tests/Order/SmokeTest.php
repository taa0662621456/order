<?php
declare(strict_types=1);

namespace OrderComponent\Tests\Order;

use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function testAutoloadsAndHasBundle(): void
    {
        $this->assertTrue(class_exists(\OrderComponent\OrderComponentBundle::class));
    }
}
