<?php

namespace App\EventListener;

use App\Entity\VendorSecurity;
use App\Service\Notification\NotificationService;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CustomerEmailUpdaterListener
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function postUpdate(VendorSecurity $user, LifecycleEventArgs $args): void
    {
        if (!$user->isEmailVerified()) {
            $this->notificationService->sendEmail(
                $user->getEmail(),
                'Verify your email',
                'Hello ' . $user->getUsername() . ', please verify your email by clicking the link.'
            );
        }
    }
}
