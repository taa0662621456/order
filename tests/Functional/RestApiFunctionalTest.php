<?php
namespace OrderComponent\Tests\Functional;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;

final class RestApiFunctionalTest extends TestCase
{
    private static KernelInterface $kernel; private static KernelBrowser $client;
    public static function setUpBeforeClass(): void { self::$kernel=new TestKernel('test', true); self::$kernel->boot(); self::$client=new KernelBrowser(self::$kernel); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    /**
     * @throws \JsonException
     */
    public function testCreatePayShipFlow(): void
    {
        $c=self::$kernel->getContainer(); $em=$c->get(EntityManagerInterface::class);
        $tool=new SchemaTool($em); $tool->dropDatabase(); $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $payload=json_encode(['currency'=>'USD','items'=>[['sku'=>'SKU-1','quantity'=>2,'unitPrice'=>1000],['sku'=>'SKU-2','quantity'=>1,'unitPrice'=>5000]]], JSON_THROW_ON_ERROR);
        self::$client->request('POST','/orders',[],[],['CONTENT_TYPE'=>'application/json'],$payload);
        $this->assertSame(201,self::$client->getResponse()->getStatusCode());
        $data=json_decode(self::$client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $orderId=$data['id']??null; $this->assertNotNull($orderId);

        self::$client->request('POST',"/orders/$orderId/pay",[],[],['CONTENT_TYPE'=>'application/json'],json_encode(['amount'=>7000]));
        $this->assertSame(200,self::$client->getResponse()->getStatusCode());

        self::$client->request('POST',"/orders/$orderId/ship");
        $this->assertSame(200,self::$client->getResponse()->getStatusCode());
    }
}
