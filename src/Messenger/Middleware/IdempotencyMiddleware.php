<?php
declare(strict_types=1);

namespace OrderComponent\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

interface IdempotencyStoreInterface {
    public function has(string $key): bool;
    public function put(string $key): void;
}

final class InMemoryIdempotencyStore implements IdempotencyStoreInterface {
    /** @var array<string,bool> */
    private array $store = [];
    public function has(string $key): bool { return isset($this->store[$key]); }
    public function put(string $key): void { $this->store[$key] = true; }
}

final class IdempotencyMiddleware implements MiddlewareInterface
{
    public function __construct(private IdempotencyStoreInterface $store) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $key = $this->makeKey($envelope->getMessage());
        if ($this->store->has($key)) {
            return $envelope;
        }
        $this->store->put($key);
        return $stack->next()->handle($envelope, $stack);
    }

    private function makeKey(object $message): string
    {
        $data = ['class' => $message::class, 'props' => get_object_vars($message)];
        return hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));
    }
}
