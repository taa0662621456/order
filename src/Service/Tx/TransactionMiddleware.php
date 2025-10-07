<?php
declare(strict_types=1);
namespace OrderComponent\Service\Tx;
use Doctrine\ORM\EntityManagerInterface;
final class TransactionMiddleware
{
    public function __construct(private readonly EntityManagerInterface $em){}
    /**
     * @template T
     * @param callable(EntityManagerInterface):T $fn
     * @return T
     */
    public function run(callable $fn)
    {
        $this->em->beginTransaction();
        try {
            $result = $fn($this->em);
            $this->em->flush();
            $this->em->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
