<?php
declare(strict_types=1);

namespace OrderComponent\Messenger\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\IdempotencyKey;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class IdempotencyMiddleware implements MiddlewareInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $key = $this->makeKey($message);

        $found = $this->em->getRepository(IdempotencyKey::class)->find($key);
        if ($found) {
            return $envelope->with(new HandledStamp(null, static::class));
        }

        $this->em->persist(new IdempotencyKey($key));
        $this->em->flush();

        return $stack->next()->handle($envelope, $stack);
    }

    private function makeKey(object $message): string
    {
        $data = ['class' => $message::class, 'props' => get_object_vars($message)];
        return hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));
    }
}
