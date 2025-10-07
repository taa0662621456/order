<?php

namespace App\Tests\Service\Domain;

use App\Service\Domain\Checker\CheckerInterface;
use App\Service\Domain\DomainAvailabilityService;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class DomainAvailabilityServiceTest extends TestCase
{
    public function testUsesSelectedStrategy(): void
    {
        $dns = $this->createMock(CheckerInterface::class);
        $dns->method('isAvailable')->willReturn(false);

        $svc = new DomainAvailabilityService('dns', $dns, null, null, null, 0);
        $this->assertFalse($svc->isAvailable('example.com'));
    }

    public function testHybridFallsBack(): void
    {
        $dns = $this->createMock(CheckerInterface::class);
        $dns->method('isAvailable')->willReturn(true);

        $whois = $this->createMock(CheckerInterface::class);
        $whois->method('isAvailable')->willReturn(true);

        $svc = new DomainAvailabilityService('hybrid', $dns, $whois, null, null, 0);
        $this->assertTrue($svc->isAvailable('maybe-free.com'));
    }

    public function testCacheIsUsed(): void
    {
        $dns = $this->createMock(CheckerInterface::class);
        $dns->expects($this->once())->method('isAvailable')->willReturn(true);

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturnOnConsecutiveCalls(false, true);
        $cacheItem->method('get')->willReturn(true);
        $cacheItem->expects($this->once())->method('set')->with(true);
        $cacheItem->expects($this->once())->method('expiresAfter')->with(600);

        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')->willReturn($cacheItem);
        $cache->expects($this->once())->method('save')->with($cacheItem);

        $svc = new DomainAvailabilityService('dns', $dns, null, null, $cache, 600);
        $this->assertTrue($svc->isAvailable('cached.com'));
        $this->assertTrue($svc->isAvailable('cached.com')); // from cache
    }
}
