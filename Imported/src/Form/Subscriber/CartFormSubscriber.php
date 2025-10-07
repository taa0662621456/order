<?php
declare(strict_types=1);

namespace App\Form\Subscriber;

use App\DTO\CartDTO;
use App\Service\Cart\CartRecalculator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CartFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CartRecalculator $recalc) {}

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'after'];
    }

    public function after(FormEvent $event): void
    {
        $data = $event->getData();
        if (!$data instanceof CartDTO) return;
        $this->recalc->recalculate($data);
    }
}
