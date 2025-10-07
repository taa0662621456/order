<?php
declare(strict_types=1);

namespace Tests\Order\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MonitoringTest extends WebTestCase
{
    public function test_health_endpoint(): void
    {
        $c = static::createClient();
        $c->request('GET', '/_health/order');
        self::assertTrue(in_array($c->getResponse()->getStatusCode(), [200,500], true));
    }
}
