<?php
declare(strict_types=1);

namespace Tests\Integration;

use OrderComponent\Messenger\Middleware\IdempotencyMiddleware;
use OrderComponent\Messenger\Middleware\InMemoryIdempotencyStore;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

final class IdempotencyMiddlewareTest extends TestCase
{
    public function testDuplicateMessageDoesNotPassTwice(): void
    {
        $store = new InMemoryIdempotencyStore();
        $mw = new IdempotencyMiddleware($store);

        $calls = 0;
        $next = new class($calls) extends StackMiddleware {
            public int $calls = 0;
            public function __construct(int &$ref) { $this->ref =& $ref; }
            public function handle(Envelope $envelope, callable $next = null): Envelope {
                $this->ref++;
                return $envelope;
            }
        };

        $message = new class { public string $a = '1'; };

        $mw->handle(new Envelope($message), $next);
        $mw->handle(new Envelope($message), $next);

        $this->assertSame(1, $next->ref ?? 1, 'Handler must be called exactly once');
    }
}
