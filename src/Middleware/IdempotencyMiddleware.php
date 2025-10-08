<?php
declare(strict_types=1);
namespace OrderComponent\Middleware;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Envelope;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Outbox\IdempotencyKey;
use OrderComponent\Message\OrderMessage;

final readonly class IdempotencyMiddleware implements MiddlewareInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $msg = $envelope->getMessage();
        if ($msg instanceof OrderMessage) {
            $key = sha1($msg->eventName.':'.$msg->orderId);
            if ($this->em->find(IdempotencyKey::class, $key)) {
                return $envelope; // skip duplicates
            }
            $this->em->persist(new IdempotencyKey($key));
            $this->em->flush();
        }
        return $stack->next()->handle($envelope, $stack);
    }
}
