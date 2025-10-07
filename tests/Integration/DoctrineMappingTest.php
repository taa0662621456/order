<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineMappingTest extends TestCase
{
    private static KernelInterface $kernel;

    public static function setUpBeforeClass(): void
    {
        self::$kernel = new TestKernel('test', true);
        self::$kernel->boot();
    }

    public static function tearDownAfterClass(): void
    {
        self::$kernel->shutdown();
    }

    public function testMappingIsValid(): void
    {
        $em = self::$kernel->getContainer()->get(EntityManagerInterface::class);
        $validator = new SchemaValidator($em);
        $errors = $validator->validateMapping();
        self::assertSame([], $errors, 'Doctrine mapping has errors: '.print_r($errors, true));
    }
}
