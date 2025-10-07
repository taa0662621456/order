<?php

namespace App\Service;

use GeoIp2\Database\Reader;
use Psr\Log\LoggerInterface;

class GeoIpService
{
    public function __construct(
        private readonly Reader $reader,
        private readonly LoggerInterface $logger
    ) {}

    public function getLocation(string $ip): array
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return [
                'ip'      => $ip,
                'country' => 'local',
                'city'    => 'localhost',
            ];
        }

        try {
            $record = $this->reader->city($ip);

            return [
                'ip'      => $ip,
                'country' => $record->country->isoCode ?? 'unknown',
                'city'    => $record->city->name ?? 'unknown',
                'lat'     => $record->location->latitude ?? null,
                'lon'     => $record->location->longitude ?? null,
            ];
        } catch (\Throwable $e) {
            $this->logger->warning('GeoIP lookup failed', [
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);

            return [
                'ip'      => $ip,
                'country' => 'unknown',
                'city'    => 'unknown',
            ];
        }
    }

    public function getCountry(string $ip): string
    {
        return $this->getLocation($ip)['country'] ?? 'unknown';
    }

    public function getCity(string $ip): string
    {
        return $this->getLocation($ip)['city'] ?? 'unknown';
    }

    /**
     * Проверяет, разрешена ли страна для работы API.
     *
     * @param string $ip
     * @param array $allowedCountries Список ISO-кодов (например ['US','CA','UA'])
     */
    public function isAllowedCountry(string $ip, array $allowedCountries = []): bool
    {
        $country = $this->getCountry($ip);

        if ($country === 'unknown' || $country === 'local') {
            return true; // localhost и неизвестные — не блокируем
        }

        return in_array($country, $allowedCountries, true);
    }
}
