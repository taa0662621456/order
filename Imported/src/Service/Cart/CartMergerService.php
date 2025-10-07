<?php
declare(strict_types=1);

namespace App\Service\Cart;

use App\Entity\Order\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CartMergerService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function merge(Order $guestCart, Order $userCart, User $user): Order
    {
        if (method_exists($guestCart, 'getItems') && method_exists($userCart, 'addItem')) {
            foreach ($guestCart->getItems() as $item) {
                $userCart->addItem($item);
            }
        }
        if (method_exists($userCart, 'setUser')) {
            $userCart->setUser($user);
        }
        $this->em->remove($guestCart);
        $this->em->flush();
        return $userCart;
    }
}
