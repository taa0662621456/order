<?php
namespace OrderComponent\Api\Dto;
use Symfony\Component\Validator\Constraints as Assert;
final class OrderShipInput {
  public function __construct(
    #[Assert\Positive] public int $count = 1,
    public ?string $note = null
  ) {}
}
