<?php
declare(strict_types=1);

namespace App\Service\Metrics;

use Doctrine\DBAL\Connection;

final class MessengerMetricsCollector
{
    public function __construct(private readonly Connection $conn) {}

    public function collect(): array
    {
        try {
            $failed = (int) $this->conn->fetchOne('SELECT COUNT(*) FROM messenger_messages WHERE queue_name = "failed"');
        } catch (\Throwable $e) {
            $failed = 0;
        }
        return [
            '# HELP messenger_failed_messages_total Number of failed Messenger messages',
            '# TYPE messenger_failed_messages_total gauge',
            'messenger_failed_messages_total ' . $failed,
        ];
    }
}
