<?php
declare(strict_types=1);

namespace App\Service\Monitoring;

use Redis;

final class RedisIdempotencyMonitor
{
    public function __construct(private readonly Redis $redis) {}

    public function stats(): array
    {
        $keys = $this->redis->keys('*');
        $ttlStats = [];
        foreach (array_slice($keys, 0, 100) as $k) {
            $ttlStats[$k] = $this->redis->ttl($k);
        }
        return ['count' => count($keys), 'sample_ttls' => $ttlStats];
    }
}
