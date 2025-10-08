<?php
declare(strict_types=1);
namespace OrderComponent\Service\Tx;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

final readonly class TransactionMiddleware
{
    public function __construct(private EntityManagerInterface $em){}

    /**
     * @template T
     * @param callable(EntityManagerInterface):T $fn
     * @return T
     * @throws \Throwable
     * @throws \Throwable
     * @throws \Throwable
     */
    public function run(callable $fn)
    {
        $this->em->beginTransaction();
        try {
            $result = $fn($this->em);
            $this->em->flush();
            $this->em->commit();
            return $result;
        } catch (Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
