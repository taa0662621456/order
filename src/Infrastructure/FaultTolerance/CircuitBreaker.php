<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\FaultTolerance;

use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class CircuitBreaker
{
    private int $failures = 0;
    private bool $open = false;
    private ?DateTimeImmutable $openedAt = null;

    public function __construct(
        private readonly int $threshold = 5,
        private readonly int $cooldownSec = 30
    ) {}

    /**
     * @throws \Throwable
     */
    public function call(callable $fn)
    {
        if ($this->open && $this->openedAt && $this->openedAt->modify("+{$this->cooldownSec} seconds") > new DateTimeImmutable()) {
            throw new RuntimeException('Circuit open');
        }
        try {
            $result = $fn();
            $this->failures = 0;
            $this->open = false;
            return $result;
        } catch (Throwable $e) {
            $this->failures++;
            if ($this->failures >= $this->threshold) {
                $this->open = true;
                $this->openedAt = new DateTimeImmutable();
            }
            throw $e;
        }
    }
}
