<?php
namespace OrderComponent\Api\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OrderComponent\Entity\Order\Order;
use OrderComponent\Api\Dto\OrderShipInput;

#[Route('/orders/{id}/ship', name: 'api_orders_ship', methods: ['POST'])]
final class OrderShipAction
{
    public function __construct(private EntityManagerInterface $em, private ValidatorInterface $validator){}
    public function __invoke(string $id, Request $request): JsonResponse
    {
        $o = $this->em->getRepository(Order::class)->findOneBy(['id'=>$id]);
        if (!$o) { return new JsonResponse(['error'=>'Not found'],404); }
        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new OrderShipInput((int)($data['count'] ?? 1), $data['note'] ?? null);
        $err = $this->validator->validate($dto);
        if (count($err) > 0) return new JsonResponse(['errors'=>(string)$err], 422);
        try { $o->shipItems($dto->count, $dto->note); $this->em->flush(); }
        catch (\DomainException $e) { return new JsonResponse(['error'=>$e->getMessage()], 400); }
        return new JsonResponse(['id'=>$o->getId(),'status'=>$o->getStatus()]);
    }
}
