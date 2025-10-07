<?php

namespace App\Tests\Service\Domain;

use App\Service\Domain\Checker\WhoisChecker;
use Iodev\Whois\Whois;
use PHPUnit\Framework\TestCase;

class WhoisCheckerTest extends TestCase
{
    public function testHandlesExceptions(): void
    {
        $whois = $this->createMock(Whois::class);
        $whois->method('loadDomainInfo')->willThrowException(new \Exception('WHOIS blocked'));

        $checker = new WhoisChecker($whois, true);
        $this->assertFalse($checker->isAvailable('example.com'));
    }
}
