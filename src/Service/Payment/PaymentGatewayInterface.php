<?php
declare(strict_types=1);
namespace OrderComponent\Service\Payment;
use OrderComponent\Entity\Order;
interface PaymentGatewayInterface { public function charge(Order $order, int $amount): string; }
