<?php
declare(strict_types=1);

namespace App\Service\Order\OrderSession;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class OrderSession
{
    public function __construct(
        private readonly SessionInterface $session,
        private readonly ManagerRegistry $registry,
        private readonly string $sessionKeyName = 'current_order_id',
        private readonly string $orderClass = 'App\\Entity\\Order\\Order',
    ) {}

    public function setCurrentId(int $orderId): void
    {
        $this->session->set($this->sessionKeyName, $orderId);
    }

    public function clear(): void
    {
        $this->session->remove($this->sessionKeyName);
    }

    public function getCurrent(): object
    {
        if (!$this->session->has($this->sessionKeyName)) {
            throw new \RuntimeException('Cart not found in session');
        }
        $id = (int) $this->session->get($this->sessionKeyName);
        $repo = $this->registry->getRepository($this->orderClass);
        $cart = method_exists($repo, 'find') ? $repo->find($id) : null;
        if (!$cart) {
            $this->session->remove($this->sessionKeyName);
            throw new \RuntimeException('Cart not found');
        }
        return $cart;
    }
}
