<?php
namespace App\Tests\Controller\Admin;

use App\Entity\Event\EventEvent;
use App\Entity\EventCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventCategoryCrudControllerTest extends WebTestCase
{
    public function testLinkCategory(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $ec = new EventCategory(); $ec->setName('Conf');
        $em->persist($ec);
        $event = new EventEvent(); $event->setTitle('Event B');
        $em->persist($event);
        $em->flush();

        $client->request('GET', '/admin?crudAction=linkCategory&entityFqcn=App\\Entity\\EventCategory&entityId='.$ec->getId());
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
