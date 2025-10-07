<?php

namespace App\DTO\Order;

use Symfony\Component\Validator\Constraints as Assert;

class OrderCreateDTO
{
    // New properties for customerId and amount
    public int $customerId;
    public float $amount;

    // Existing properties
    private string $product;
    private int $quantity;
    private \DateTime $orderDate;

    // Constructor including customerId and amount
    public function __construct(int $customerId, float $amount, string $product, int $quantity, \DateTime $orderDate)
    {
        $this->customerId = $customerId;
        $this->amount = $amount;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->orderDate = $orderDate;
    }

    // Getter and Setter for customerId
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    // Getter and Setter for amount
    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    // Getter and Setter for product
    public function getProduct(): string
    {
        return $this->product;
    }

    public function setProduct(string $product): void
    {
        $this->product = $product;
    }

    // Getter and Setter for quantity
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    // Getter and Setter for orderDate
    public function getOrderDate(): \DateTime
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTime $orderDate): void
    {
        $this->orderDate = $orderDate;
    }
}
