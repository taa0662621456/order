<?php
namespace App\Tests\Controller\Admin;

use App\Entity\Event\EventEvent;
use App\Entity\EventMember;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventMemberCrudControllerTest extends WebTestCase
{
    public function testAddRemoveMember(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $event = new EventEvent(); $event->setTitle('Event C');
        $em->persist($event); $em->flush();

        $mem = new EventMember(); $mem->setName('User1')->setEvent($event);
        $em->persist($mem); $em->flush();

        $client->request('GET', '/admin?crudAction=removeMember&entityFqcn=App\\Entity\\EventMember&entityId='.$mem->getId());
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
