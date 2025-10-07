<?php
namespace App\Service\Vendor;
use App\Entity\Event\Event;
use App\Entity\Vendor\Vendor;
use App\Entity\Vendor\VendorConversationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Vendor\ConversationMessageSentEvent;
use App\DTO\Vendor\ConversationMessageDTO;
class VendorConversationService {
  public function __construct(private EntityManagerInterface $em, private EventDispatcherInterface $dispatcher) {}
  public function sendMessage(int $conversationId, ConversationMessageDTO $dto): VendorConversationMessage {
    $msg = new VendorConversationMessage($conversationId, $dto->senderId, $dto->content);
    $this->em->persist($msg);
    $this->dispatcher->dispatch(new ConversationMessageSentEvent($msg, $dto->senderId, new \DateTimeImmutable()));
    return $msg;
  }
}
