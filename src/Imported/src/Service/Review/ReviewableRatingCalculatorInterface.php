<?php
declare(strict_types=1);

namespace App\Service\Review;

interface ReviewableRatingCalculatorInterface
{
    public function calculate(ReviewableInterface $reviewSubject): int;
}
