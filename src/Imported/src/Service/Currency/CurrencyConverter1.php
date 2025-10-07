<?php
declare(strict_types=1);
namespace App\Service\Currency;
use App\Entity\Exchange\Exchange;
use App\Entity\Exchange\ExchangeRate;

use Doctrine\ORM\EntityManagerInterface;

final class CurrencyConverter
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function convert(int $amountMinor, string $from, string $to): int
    {
        if ($from === $to) return $amountMinor;
        $rate = $this->em->getRepository(\App\Entity\ExchangeRate::class)->findOneBy(['base'=>$from,'quote'=>$to]);
        if (!$rate) return $amountMinor;
        return (int) round($amountMinor * $rate->getRate());
    }
}
