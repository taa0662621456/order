<?php

namespace App\Service\Domain\Checker;

use Iodev\Whois\Whois;

class WhoisChecker implements CheckerInterface
{
    public function __construct(
        private readonly ?Whois $whois = null,
        private readonly bool $softFailAsTaken = true
    ) {}

    public function isAvailable(string $domain): bool
    {
        $domain = $this->normalize($domain);
        $whois = $this->whois ?? Whois::createDefault();

        try {
            $info = $whois->loadDomainInfo($domain);
            // If WHOIS info exists -> domain is registered
            return $info === null;
        } catch (\Throwable $e) {
            // WHOIS not supported / throttled / error
            return $this->softFailAsTaken ? false : true;
        }
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
