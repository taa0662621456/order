<?php

namespace App\Controller\Api;

use App\DTO\CartSnapshot;
use App\Entity\Order\OrderStatus;
use App\Service\Order\OrderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/orders', name: 'api_orders_')]
final readonly class OrderController
{
    public function __construct(
        private OrderService        $orders,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var CartSnapshot $cart */
        try {
            $cart = $this->serializer->deserialize($request->getContent(), CartSnapshot::class, 'json');
        } catch (ExceptionInterface $e) {
        }
        $order = $this->orders->createOrderFromCartSnapshot($cart);

        $data = [
            'id' => method_exists($order, 'getId') ? $order->getId() : null,
            'grandTotal' => method_exists($order, 'getGrandTotal') ? $order->getGrandTotal()->asDecimal() : null,
            'currency' => method_exists($order, 'getGrandTotal') ? $order->getGrandTotal()->currency() : ($cart->currency ?? 'USD'),
            'status' => method_exists($order, 'getStatus') ? $order->getStatus() : OrderStatus::NEW->value,
        ];
        return new JsonResponse($data, 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $order = $this->orders->getOrderWithItems($id);
        $data = [
            'id' => method_exists($order, 'getId') ? $order->getId() : null,
            'itemsSubtotal' => method_exists($order, 'getItemsSubtotal') ? $order->getItemsSubtotal()->asDecimal() : null,
            'discountTotal' => method_exists($order, 'getDiscountTotal') ? $order->getDiscountTotal()->asDecimal() : null,
            'shippingTotal' => method_exists($order, 'getShippingTotal') ? $order->getShippingTotal()->asDecimal() : null,
            'taxTotal'      => method_exists($order, 'getTaxTotal') ? $order->getTaxTotal()->asDecimal() : null,
            'grandTotal'    => method_exists($order, 'getGrandTotal') ? $order->getGrandTotal()->asDecimal() : null,
            'status'        => method_exists($order, 'getStatus') ? $order->getStatus() : null,
        ];
        return new JsonResponse($data, 200);
    }

    #[Route('/{id}/status', name: 'status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $order = $this->orders->getOrderWithItems($id);
        try {
            $payload = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
        }
        $to = OrderStatus::from($payload['status'] ?? OrderStatus::NEW->value);
        $order = $this->orders->updateOrderStatus($order, $to);
        return new JsonResponse(['id' => $id, 'status' => $to->value], 200);
    }
}
