<?php
namespace OrderComponent\Api\Dto;
use Symfony\Component\Validator\Constraints as Assert;
final class OrderPayInput {
  public function __construct(
    #[Assert\NotBlank] #[Assert\Positive] public string $amount = '0.00',
    #[Assert\NotBlank] public string $externalRef = 'api'
  ) {}
}
