<?php
namespace Tests\Api;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderResourceTest extends WebTestCase
{
  public function test_create_and_flow(): void
  {
    $c=static::createClient();
    $c->request('POST','/orders',server:['CONTENT_TYPE'=>'application/json'],content:json_encode(['currency'=>'USD','grandTotal'=>'100.00']));
    self::assertResponseStatusCodeSame(201);
    $id=json_decode($c->getResponse()->getContent(),true)['id'];
    $c->request('POST',"/orders/$id/pay",server:['CONTENT_TYPE'=>'application/json'],content:json_encode(['amount'=>'40.00','externalRef'=>'api']));
    self::assertResponseIsSuccessful();
  }
}
