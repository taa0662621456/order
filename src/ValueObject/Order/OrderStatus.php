<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
enum OrderStatus: string { case Draft='draft'; case Placed='placed'; case Paid='paid'; case Shipped='shipped'; case Completed='completed'; case Cancelled='cancelled'; case Refunded='refunded'; }
