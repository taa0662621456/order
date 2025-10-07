<?php
namespace App\Tests\Unit\Entity;
use App\Service\Entity\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTestUnitUnitTest extends TestCase
{
    private EntityRepository $repository;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->repository = new EntityRepository($managerRegistry, 'App\\Repository');
    }

    public function testGetRepositoryNamespace(): void
    {
        $namespace = $this->repository->getEntityRepositoryNamespace('User');
        $this->assertSame('App\\Repository\\UserRepository', $namespace);
    }
}

