<?php
declare(strict_types=1);

namespace App\Service\Vendor;
use App\Entity\Message\Message;
use App\Entity\Vendor\Vendor;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Entity\Vendor\VendorDocument;
use App\Entity\Vendor\VendorConversationMessage;
use App\Entity\Vendor\VendorCommission;
use App\Message\Vendor\DocumentUploadedMessage;
use App\Message\Vendor\ConversationMessageSentMessage;
use App\Message\Vendor\CommissionChangedMessage;

final class AsyncEventDispatcherService
{
    public function __construct(private readonly MessageBusInterface $bus) {}

    public function dispatchDocumentUploaded(VendorDocument $doc, int $userId): void
    {
        $this->bus->dispatch(new DocumentUploadedMessage($doc->getId(), $userId));
    }

    public function dispatchConversationMessageSent(VendorConversationMessage $msg): void
    {
        $this->bus->dispatch(new ConversationMessageSentMessage($msg->getId(), $msg->getSenderId()));
    }

    public function dispatchCommissionChanged(VendorCommission $com): void
    {
        $this->bus->dispatch(new CommissionChangedMessage($com->getId()));
    }
}
