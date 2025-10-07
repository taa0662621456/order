<?php
declare(strict_types=1);

namespace App\Service\Order;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

final class OrderSequentialNumberGenerator
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly LoggerInterface $logger,
        private readonly string $orderClass = 'App\\Entity\\Order\\Order',
        private readonly string $numberField = 'number',
        private readonly string $prefixFormat = 'Ymd',
        private readonly int $pad = 4,
    ) {}

    public function generate(): string
    {
        $prefix = date($this->prefixFormat);
        $repo = $this->registry->getRepository($this->orderClass);

        $lastIndex = 0;
        try {
            // Try to find max index for today by scanning few recent entries
            $candidates = method_exists($repo, 'findBy') ? $repo->findBy([], [$this->numberField => 'DESC'], 50) : [];
            foreach ($candidates as $row) {
                $num = $this->readNumber($row);
                if (str_starts_with($num, $prefix.'-')) {
                    $idx = (int) substr($num, strlen($prefix) + 1);
                    if ($idx > $lastIndex) { $lastIndex = $idx; }
                }
            }
        } catch (\Throwable $e) {
            $this->logger->warning('Failed to query last order number', ['error' => $e->getMessage()]);
        }

        $next = $lastIndex + 1;
        return $prefix . '-' . str_pad((string)$next, $this->pad, '0', STR_PAD_LEFT);
    }

    private function readNumber(object $entity): string
    {
        // Prefer getter
        $getter = 'get' . ucfirst($this->numberField);
        if (method_exists($entity, $getter)) {
            return (string) $entity->{$getter}();
        }
        // Try public property
        if (property_exists($entity, $this->numberField)) {
            /** @var mixed $v */
            $v = $entity->{$this->numberField};
            return (string) $v;
        }
        return '';
    }
}
