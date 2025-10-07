<?php

namespace App\Tests\Integration;

use App\Entity\Vendor\VendorMedia;
use App\Service\FileStorageService;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineEventSubscriberIntegrationTest extends KernelTestCase
{
    use FixturesTrait;

    private FileStorageService $fileStorage;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->fileStorage = static::getContainer()->get(FileStorageService::class);
    }

    public function testFileDeletedOnEntityRemove(): void
    {
        // Загружаем фикстуры
        $objects = $this->loadFixtures([
            \App\Tests\Fixtures\VendorMediaFixtures::class,
        ])->getReferenceRepository();

        /** @var VendorMedia $media */
        $media = $objects->getReference('vendor_media_sample');

        $em = static::getContainer()->get('doctrine')->getManager();

        // Подменим FileStorageService для проверки вызова delete()
        $mockStorage = $this->createMock(FileStorageService::class);
        static::getContainer()->set(FileStorageService::class, $mockStorage);

        $mockStorage
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('/tmp/test_media.jpg'));

        // Удаляем сущность
        $em->remove($media);
        $em->flush();
    }
}
