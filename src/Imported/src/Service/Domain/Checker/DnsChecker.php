<?php

namespace App\Service\Domain\Checker;

class DnsChecker implements CheckerInterface
{
    public function __construct(
        private readonly int $timeoutMs = 1500
    ) {}

    public function isAvailable(string $domain): bool
    {
        $domain = $this->normalize($domain);

        // Fast A/AAAA/CNAME/MX/NS lookup
        $types = DNS_A | DNS_AAAA | DNS_CNAME | DNS_MX | DNS_NS;
        $records = @dns_get_record($domain, $types);
        if (is_array($records) && count($records) > 0) {
            // Has DNS -> likely registered (not guaranteed)
            return false;
        }

        // Fallback: gethostbyname (avoids PHP bug on some platforms)
        $host = @gethostbyname($domain);
        if ($host && $host !== $domain) {
            return false;
        }

        // No DNS footprint -> possibly free
        return true;
    }

    private function normalize(string $domain): string
    {
        $domain = trim(strtolower($domain));
        if (function_exists('idn_to_ascii')) {
            $idn = @idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
            if ($idn) return $idn;
        }
        return $domain;
    }
}
