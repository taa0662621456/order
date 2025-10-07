<?php
declare(strict_types=1);

namespace Tests\Order\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Service\Order\TransactionalEventPublisher;
use OrderComponent\Service\Order\OutboxRelay;
use OrderComponent\Repository\Order\OutboxRepository;

final class OrderOutboxTest extends KernelTestCase
{
    public function test_publish_and_relay(): void
    {
        self::bootKernel();
        /** @var TransactionalEventPublisher $pub */
        $pub = self::getContainer()->get(TransactionalEventPublisher::class);
        /** @var OutboxRelay $relay */
        $relay = self::getContainer()->get(OutboxRelay::class);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $pub->publish('order.placed', ['orderId' => 'test-order', 'number' => 'ORD-1']);
        $em->flush(); // имитация общей транзакции

        $processed = $relay->runOnce(10);
        self::assertGreaterThanOrEqual(1, $processed);
    }
}
