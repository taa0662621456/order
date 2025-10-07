<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\AuditLogDTO;
final class AuditLogger {
    /** @return string logId */
    public function logAction(AuditLogDTO $dto): string {
        return 'log_'.uniqid();
    }
    /** @return array<AuditLogDTO> */
    public function listByUser(string $userId): array { return []; }
    /** @return array<AuditLogDTO> */
    public function listByEntity(string $entity): array { return []; }
}