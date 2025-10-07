<?php

namespace App\Entity\Order;

use App\Entity\Payment\Payment;
use App\EntityInterface\Order\OrderPaymentInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderPayment implements OrderPaymentInterface
{
    #[ORM\OneToMany(mappedBy: 'orderPayment', targetEntity: Payment::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    /** @return Collection<int,Payment> */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
        }
        return $this;
    }
}
