<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\HealthCheckDTO;
final class HealthService {
    private \DateTimeImmutable $startedAt;
    public function __construct() { $this->startedAt = new \DateTimeImmutable(); }
    public function check(): HealthCheckDTO {
        $dto = new HealthCheckDTO();
        $dto->uptime = (new \DateTimeImmutable())->diff($this->startedAt)->format('%ad %hh %im %ss');
        $dto->checks = [
            'db' => true,
            'cache' => true,
            'queue' => true
        ];
        $dto->ok = !in_array(false, $dto->checks, true);
        $dto->status = $dto->ok ? 'ok' : 'degraded';
        return $dto;
    }
}
