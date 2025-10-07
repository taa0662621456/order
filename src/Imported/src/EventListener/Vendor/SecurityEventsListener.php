<?php
namespace App\EventListener\Vendor;
use App\Service\Vendor\AuditLoggerService;
use App\Event\Vendor\UserLoggedInEvent;
use App\Event\Vendor\UserRoleChangedEvent;
use App\Event\Vendor\InvitationSentEvent;
class SecurityEventsListener {
  public function __construct(private AuditLoggerService $logger) {}
  public function onUserLoggedIn(UserLoggedInEvent $event): void {
    $this->logger->log('user.login',['userId'=>$event->getUserId()],$event->getUserId());
  }
  public function onUserRoleChanged(UserRoleChangedEvent $event): void {
    $this->logger->log('user.role_change',['old'=>$event->getOldRole(),'new'=>$event->getNewRole()],$event->getUserId());
  }
  public function onInvitationSent(InvitationSentEvent $event): void {
    $this->logger->log('user.invitation',['email'=>$event->getEmail(),'vendorId'=>$event->getVendorId()],null,$event->getVendorId());
  }
}
