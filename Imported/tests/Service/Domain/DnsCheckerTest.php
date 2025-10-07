<?php

namespace App\Tests\Service\Domain;

use App\Service\Domain\Checker\DnsChecker;
use PHPUnit\Framework\TestCase;

class DnsCheckerTest extends TestCase
{
    public function testNormalizeDoesNotCrash(): void
    {
        $checker = new DnsChecker();
        $this->assertIsBool($checker->isAvailable('тест-домен.рф')); // IDN
    }
}
