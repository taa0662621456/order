<?php
namespace App\Controller\Api;

use App\Entity\Address\Address;
use App\Entity\Address\AddressCity;
use App\Entity\Address\AddressCountry;
use App\Entity\Address\AddressStreet;
use App\Entity\Address\AddressZipcode;
use App\Entity\Order\OrderStorage;
use App\Service\Order\OrderManager;
use App\Service\Order\OrderMapper;
use App\Service\Order\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

#[Route('/api/order')]
final class OrderApiController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrderManager $orderManager,
        private readonly EventDispatcherInterface $dispatcher,
        private OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }

    // Общая функция для поиска заказа по ID, чтобы избежать повторяющегося кода
    private function findOrder(int $id): ?OrderStorage
    {
        return $this->em->getRepository(OrderStorage::class)->find($id);
    }

    // Общая функция для обработки ошибок, если заказ не найден
    private function handleOrderNotFound(): JsonResponse
    {
        return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('', name: 'api_order_create', methods: ['POST'])]
    public function create(Request $request, OrderMapper $orderMapper, AddressFormatStrategyFactory $addressFormatFactory): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['items']) || !isset($data['billing']) || !isset($data['shipping'])) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Извлекаем данные для биллинга и шиппинга
            $billingData = $data['billing'];
            $shippingData = $data['shipping'];

            // Создаем объекты Address
            $billingAddress = new Address(
                new AddressCity($billingData['city']),
                new AddressZipcode($billingData['zipcode']),
                new AddressCountry($billingData['country']),
                new AddressStreet($billingData['street'])
            );

            $shippingAddress = new Address(
                new AddressCity($shippingData['city']),
                new AddressZipcode($shippingData['zipcode']),
                new AddressCountry($shippingData['country']),
                new AddressStreet($shippingData['street'])
            );

            // Получаем стратегию форматирования для каждой страны
            $billingFormatter = $addressFormatFactory->forCountry($billingAddress->country()->value());
            $shippingFormatter = $addressFormatFactory->forCountry($shippingAddress->country()->value());

            // Форматируем адреса
            $formattedBillingAddress = $billingFormatter->format($billingAddress);
            $formattedShippingAddress = $shippingFormatter->format($shippingAddress);

            // Логика маппинга заказа
            $order = $orderMapper->mapOrderStorage($data);

            // Добавляем отформатированные адреса к заказу
            $order->setOrderBillingAddress($formattedBillingAddress);
            $order->setOrderShipmentAddress($formattedShippingAddress);

            // Завершаем и сохраняем заказ
            $this->orderManager->finalize($order);

            $this->em->persist($order);
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'id' => $order->getId(),
            'status' => $order->getOrderStatus(),
            'total' => $order->getOrderTotal(),
            'billing_address' => $formattedBillingAddress, // Возвращаем отформатированный адрес
            'shipping_address' => $formattedShippingAddress, // Возвращаем отформатированный адрес
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_order_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $order = $this->findOrder($id);

        if (!$order) {
            return $this->handleOrderNotFound();
        }

        return $this->json([
            'id' => $order->getId(),
            'status' => $order->getOrderStatus(),
            'total' => $order->getOrderTotal(),
        ]);
    }

    #[Route('/{id}/pay', name: 'api_order_pay', methods: ['POST'])]
    public function pay(int $id): JsonResponse
    {
        $order = $this->findOrder($id);

        if (!$order) {
            return $this->handleOrderNotFound();
        }

        try {
            $this->orderManager->finalize($order);
            $this->dispatcher->dispatch(new GenericEvent($order), 'order.paid');
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Payment failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Order paid', 'id' => $order->getId()]);
    }

    #[Route('/{id}/complete', name: 'api_order_complete', methods: ['POST'])]
    public function complete(int $id): JsonResponse
    {
        $order = $this->findOrder($id);

        if (!$order) {
            return $this->handleOrderNotFound();
        }

        try {
            $this->orderManager->finalize($order);
            $this->dispatcher->dispatch(new GenericEvent($order), 'order.completed');
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Completion failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Order completed', 'id' => $order->getId()]);
    }

    #[Route('/{id}/item', name: 'api_order_item', methods: ['GET'])]
    public function item(int $id): JsonResponse
    {
        $order = $this->findOrder($id);

        if (!$order) {
            return $this->handleOrderNotFound();
        }

        $items = array_map(fn($item) => [
            'id' => $item->getId(),
            'product' => $item->getProduct()?->getName(),
            'quantity' => $item->getQuantity(),
            'subtotal' => $item->getSubtotal(),
        ], $order->getOrderItem()->toArray());

        return $this->json($items);
    }

    #[Route('/{id}/payment', name: 'api_order_payment', methods: ['GET'])]
    public function payment(int $id): JsonResponse
    {
        $order = $this->findOrder($id);

        if (!$order) {
            return $this->handleOrderNotFound();
        }

        $payments = array_map(fn($payment) => [
            'id' => $payment->getId(),
            'amount' => $payment->getAmount(),
            'status' => $payment->getStatus(),
        ], $order->getOrderPayment()->toArray());

        return $this->json($payments);
    }

    #[Route('/{id}/shipment', name: 'api_order_shipment', methods: ['GET'])]
    public function shipment(int $id): JsonResponse
    {
        $order = $this->findOrder($id);

        if (!$order) {
            return $this->handleOrderNotFound();
        }

        $shipment = array_map(fn($shipment) => [
            'id' => $shipment->getId(),
            'method' => $shipment->getMethod(),
            'status' => $shipment->getStatus(),
        ], $order->getOrderShipmentAddress()->toArray());

        return $this->json($shipment);
    }
}
