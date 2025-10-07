<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\FaultTolerance;

final class CircuitBreaker
{
    private int $failures = 0;
    private bool $open = false;
    private ?\DateTimeImmutable $openedAt = null;

    public function __construct(
        private int $threshold = 5,
        private int $cooldownSec = 30
    ) {}

    public function call(callable $fn)
    {
        if ($this->open && $this->openedAt && $this->openedAt->modify("+{$this->cooldownSec} seconds") > new \DateTimeImmutable()) {
            throw new \RuntimeException('Circuit open');
        }
        try {
            $result = $fn();
            $this->failures = 0;
            $this->open = false;
            return $result;
        } catch (\Throwable $e) {
            $this->failures++;
            if ($this->failures >= $this->threshold) {
                $this->open = true;
                $this->openedAt = new \DateTimeImmutable();
            }
            throw $e;
        }
    }
}
