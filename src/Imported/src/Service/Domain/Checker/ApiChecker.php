<?php

namespace App\Service\Domain\Checker;

use Psr\Log\LoggerInterface;

class ApiChecker implements CheckerInterface
{
    public function __construct(
        private readonly string $provider = 'godaddy',
        private readonly ?string $apiKey = null,
        private readonly ?string $apiSecret = null,
        private readonly int $timeoutMs = 800,
        private readonly ?LoggerInterface $logger = null,
        private readonly string $baseUrl = '' // кастомизация при необходимости
    ) {}

    public function isAvailable(string $domain): bool
    {
        $domain = $this->normalize($domain);
        $p = strtolower($this->provider);

        return match ($p) {
            'godaddy' => $this->checkGoDaddy($domain),
            default   => $this->fail('unknown_provider', ['provider' => $this->provider]),
        };
    }

    private function checkGoDaddy(string $domain): bool
    {
        if (!$this->apiKey || !$this->apiSecret) {
            return $this->fail('missing_api_keys');
        }

        $url = rtrim($this->baseUrl ?: 'https://api.godaddy.com', '/') . '/v1/domains/available?domain=' . rawurlencode($domain);

        $h = curl_init($url);
        curl_setopt_array($h, [
            CURLOPT_RETURNTRANSFER     => true,
            CURLOPT_FOLLOWLOCATION     => false,
            CURLOPT_MAXREDIRS          => 0,
            CURLOPT_TIMEOUT_MS         => $this->timeoutMs,
            CURLOPT_CONNECTTIMEOUT_MS  => min(300, $this->timeoutMs),
            CURLOPT_NOSIGNAL           => true,
            CURLOPT_FAILONERROR        => false,
            CURLOPT_SSL_VERIFYPEER     => true,
            CURLOPT_SSL_VERIFYHOST     => 2,
            CURLOPT_HTTPHEADER         => [
                'Accept: application/json',
                'User-Agent: domain-availability/1.0 (+https://yourapp.example)',
                'Authorization: sso-key ' . $this->apiKey . ':' . $this->apiSecret,
            ],
        ]);

        $body = curl_exec($h);
        $errno = curl_errno($h);
        $code  = curl_getinfo($h, CURLINFO_HTTP_CODE);
        $err   = $errno ? curl_error($h) : null;
        curl_close($h);

        if ($errno !== 0) {
            return $this->fail('curl_error', ['errno' => $errno, 'error' => $err]);
        }
        if ($code >= 500 || $code === 429) {
            return $this->fail('provider_unavailable', ['http' => $code]);
        }
        if ($code < 200 || $code >= 300) {
            return $this->fail('bad_status', ['http' => $code]);
        }
        if (!$body) {
            return $this->fail('empty_body');
        }

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return $this->fail('bad_json', ['json_error' => json_last_error_msg()]);
        }

        // GoDaddy: {"domain":"example.com","available":true,"definitive":false}
        if (!array_key_exists('available', $data)) {
            return $this->fail('no_available_field');
        }

        return (bool) $data['available'];
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

    private function fail(string $reason, array $context = []): bool
    {
        if ($this->logger) {
            $this->logger->warning('domain_api_check_fail', ['reason' => $reason] + $context);
        }
        // Безопасное поведение: считаем домен занятым при ошибке
        return false;
    }
}
