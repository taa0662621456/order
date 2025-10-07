<?php
namespace OrderComponent\Api\State;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Api\DTO\{OrderInput, OrderOutput};
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\ValueObject\Money\Currency;
use OrderComponent\ValueObject\Order\{Sku, Quantity};

final class OrderDataPersister implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if(!$data instanceof OrderInput) return $data;
        $o=new Order(); $o->setCurrency(new Currency($data->currency)); $this->em->persist($o);
        foreach($data->items as $i){ $it=new OrderItem($o,new Sku($i['sku']), new Quantity((int)$i['quantity']), (int)$i['unitPrice']); $this->em->persist($it); }
        $this->em->flush();
        return OrderOutput::fromEntity($o);
    }
}
