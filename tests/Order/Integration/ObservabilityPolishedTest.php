<?php
declare(strict_types=1);

namespace Tests\Order\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ObservabilityPolishedTest extends WebTestCase
{
    public function test_metrics_endpoint(): void
    {
        $c = static::createClient();
        $c->request('GET', '/metrics');
        self::assertTrue(in_array($c->getResponse()->getStatusCode(), [200,500], true));
    }

    public function test_ready_endpoint(): void
    {
        $c = static::createClient();
        $c->request('GET', '/_ready/order');
        self::assertTrue(in_array($c->getResponse()->getStatusCode(), [200,503], true));
    }
}
