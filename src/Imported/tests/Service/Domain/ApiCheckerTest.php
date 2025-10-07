<?php

namespace App\Tests\Service\Domain;

use App\Service\Domain\Checker\ApiChecker;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ApiCheckerTest extends TestCase
{
    public function testWithoutKeysReturnsTaken(): void
    {
        $checker = new ApiChecker('godaddy', null, null);
        $this->assertFalse($checker->isAvailable('example.com'));
    }

    public function testLogsOnCurlError(): void
    {
        // Подставим невалидный baseUrl, чтобы гарантировать ошибку подключения
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $checker = new ApiChecker('godaddy', 'key', 'secret', 50, $logger, 'https://127.0.0.1:1');
        $this->assertFalse($checker->isAvailable('example.com'));
    }
}
