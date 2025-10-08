<?php
declare(strict_types=1);

namespace Tests\Integration\Billing;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrderComponent\Service\Order\Billing\IdempotencyGuard;

final class IdempotencyGuardTest extends KernelTestCase
{
    /**
     * @throws \JsonException
     */
    public function test_check_and_persist_behaviour(): void
    {
        self::bootKernel();
        $guard = static::getContainer()->get(IdempotencyGuard::class);

        $payload = json_encode(['a' => 1], JSON_THROW_ON_ERROR);
        $ok1 = $guard->checkAndPersist('mock', 'evt_1', $payload);
        $ok2 = $guard->checkAndPersist('mock', 'evt_1', $payload); // duplicate
        self::assertTrue($ok1);
        self::assertFalse($ok2);
    }
}
