<?php
declare(strict_types=1);

namespace OrderComponent\\Controller;

use OrderComponent\\DTO\\Order\\OrderCreateDTO;
use OrderComponent\\DTO\\Order\\OrderPaymentDTO;
use OrderComponent\\DTO\\Order\\OrderShipmentDTO;
use OrderComponent\\Entity\\Order\\Order;
use Symfony\\Component\\HttpFoundation\\JsonResponse;
use Symfony\\Component\\HttpFoundation\\Request;
use Symfony\\Component\\Routing\\Attribute\\Route;
use Symfony\\Component\\Validator\\Validator\\ValidatorInterface;
use Doctrine\\ORM\\EntityManagerInterface;

#[Route('/order')]
final class OrderController
{
    public function __construct(private EntityManagerInterface $em, private ValidatorInterface $validator) {}

    #[Route('', name: 'order_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new OrderCreateDTO($data['currency'] ?? 'USD', $data['grandTotal'] ?? '0.00');
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string)$errors], 422);
        }
        $order = new Order($dto->currency, $dto->grandTotal);
        $this->em->persist($order);
        $this->em->flush();

        return new JsonResponse(['id' => $order->id(), 'status' => $order->status()], 201);
    }

    #[Route('/{id}', name: 'order_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $o = $this->em->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$o) { return new JsonResponse(['error' => 'Not found'], 404); }
        return new JsonResponse([
            'id' => $o->id(),
            'status' => $o->status(),
            'grandTotal' => $o->grandTotal(),
            'paidTotal' => $o->paidTotal(),
            'refundedTotal' => $o->refundedTotal(),
            'currency' => $o->currency(),
        ]);
    }

    #[Route('/{id}/pay', name: 'order_pay', methods: ['POST'])]
    public function pay(string $id, Request $request): JsonResponse
    {
        $o = $this->em->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$o) { return new JsonResponse(['error' => 'Not found'], 404); }

        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new OrderPaymentDTO($data['amount'] ?? '0.00', $data['externalRef'] ?? 'unknown');
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) { return new JsonResponse(['errors' => (string)$errors], 422); }

        try {
            $o->applyPartialPayment($dto->amount, $dto->externalRef, true);
            $this->em->flush();
        } catch (\\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
        return new JsonResponse(['id' => $o->id(), 'status' => $o->status(), 'paidTotal' => $o->paidTotal()]);
    }

    #[Route('/{id}/ship', name: 'order_ship', methods: ['POST'])]
    public function ship(string $id, Request $request): JsonResponse
    {
        $o = $this->em->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$o) { return new JsonResponse(['error' => 'Not found'], 404); }

        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new OrderShipmentDTO((int)($data['count'] ?? 1), $data['note'] ?? null);
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) { return new JsonResponse(['errors' => (string)$errors], 422); }

        try {
            $o->shipItems($dto->count, $dto->note);
            $this->em->flush();
        } catch (\\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
        return new JsonResponse(['id' => $o->id(), 'status' => $o->status()]);
    }

    #[Route('/{id}/refund', name: 'order_refund', methods: ['POST'])]
    public function refund(string $id, Request $request): JsonResponse
    {
        $o = $this->em->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$o) { return new JsonResponse(['error' => 'Not found'], 404); }

        $data = json_decode($request->getContent(), true) ?? [];
        $amount = (string)($data['amount'] ?? '0.00');
        $reason = $data['reason'] ?? null;

        try {
            $o->refundPartial($amount, $reason);
            $this->em->flush();
        } catch (\\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
        return new JsonResponse(['id' => $o->id(), 'status' => $o->status(), 'refundedTotal' => $o->refundedTotal()]);
    }
}
