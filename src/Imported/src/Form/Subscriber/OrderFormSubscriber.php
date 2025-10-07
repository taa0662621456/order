<?php
declare(strict_types=1);

namespace App\Form\Subscriber;

use App\DTO\OrderDTO;
use App\Service\Order\OrderTotalsRecalculator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class OrderFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly OrderTotalsRecalculator $recalc) {}

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'after'];
    }

    public function after(FormEvent $event): void
    {
        $data = $event->getData();
        if (!$data instanceof OrderDTO) return;
        $this->recalc->recalculate($data);
    }
}
