<?php
declare(strict_types=1);

namespace App\Service\Metrics;

use Redis;

final class RedisIdempotencyMetricsCollector
{
    public function __construct(private readonly Redis $redis) {}

    public function collect(): array
    {
        $keys = $this->redis->keys('*');
        $count = count($keys);
        $maxTtl = 0;
        $sumTtl = 0;
        $n = 0;
        foreach (array_slice($keys, 0, 100) as $k) {
            $ttl = $this->redis->ttl($k);
            if ($ttl > 0) {
                $maxTtl = max($maxTtl, $ttl);
                $sumTtl += $ttl;
                $n++;
            }
        }
        $avgTtl = $n > 0 ? (int)($sumTtl / $n) : 0;
        return [
            '# HELP idempotency_keys_total Number of Redis idempotency keys',
            '# TYPE idempotency_keys_total gauge',
            'idempotency_keys_total ' . $count,
            '# HELP idempotency_ttl_seconds TTL stats of sample keys',
            '# TYPE idempotency_ttl_seconds gauge',
            'idempotency_ttl_seconds{type="avg"} ' . $avgTtl,
            'idempotency_ttl_seconds{type="max"} ' . $maxTtl,
        ];
    }
}
