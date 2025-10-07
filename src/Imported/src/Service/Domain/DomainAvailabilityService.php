<?php

namespace App\Service\Domain;

use App\Service\Domain\Checker\CheckerInterface;
use App\Service\Domain\Checker\DnsChecker;
use App\Service\Domain\Checker\WhoisChecker;
use App\Service\Domain\Checker\ApiChecker;
use Psr\Cache\CacheItemPoolInterface;

class DomainAvailabilityService
{
    public function __construct(
        private readonly string $strategy = 'dns', // dns|whois|api|hybrid
        private readonly ?CheckerInterface $dnsChecker = null,
        private readonly ?CheckerInterface $whoisChecker = null,
        private readonly ?CheckerInterface $apiChecker = null,
        private readonly ?CacheItemPoolInterface $cache = null,
        private readonly int $ttlSeconds = 600,
    ) {}

    public function isAvailable(string $domain): bool
    {
        $domain = trim($domain);
        if ($domain === '') return false;

        // Cache layer
        $cacheKey = 'domain_avail_' . md5(strtolower($domain) . '|' . $this->strategy);
        if ($this->cache) {
            $item = $this->cache->getItem($cacheKey);
            if ($item->isHit()) {
                return (bool)$item->get();
            }
        }

        $result = match (strtolower($this->strategy)) {
            'dns'    => $this->getDns()->isAvailable($domain),
            'whois'  => $this->getWhois()->isAvailable($domain),
            'api'    => $this->getApi()->isAvailable($domain),
            'hybrid' => $this->hybridCheck($domain),
            default  => $this->getDns()->isAvailable($domain),
        };

        if ($this->cache) {
            $item->set($result);
            $item->expiresAfter($this->ttlSeconds);
            $this->cache->save($item);
        }

        return $result;
    }

    private function hybridCheck(string $domain): bool
    {
        // 1) быстрый фильтр DNS
        if (!$this->getDns()->isAvailable($domain)) {
            return false;
        }
        // 2) подтверждение через WHOIS (если доступен)
        if ($this->whoisChecker) {
            return $this->whoisChecker->isAvailable($domain);
        }
        // 3) или API-провайдер (если настроен)
        if ($this->apiChecker) {
            return $this->apiChecker->isAvailable($domain);
        }
        // если ничего нет, верим DNS
        return true;
    }

    private function getDns(): CheckerInterface
    {
        return $this->dnsChecker ?? new DnsChecker();
    }
    private function getWhois(): CheckerInterface
    {
        return $this->whoisChecker ?? new WhoisChecker();
    }
    private function getApi(): CheckerInterface
    {
        return $this->apiChecker ?? new ApiChecker();
    }
}
